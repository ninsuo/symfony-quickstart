<?php

namespace Fuz\QuickStartBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Translation\TranslatorInterface;

class FOSUserBundleListener implements EventSubscriberInterface
{
    protected $mailer;
    protected $tokenGenerator;
    protected $router;
    protected $session;
    protected $security;
    protected $userManager;
    protected $translator;

    public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator,
       UrlGeneratorInterface $router, SessionInterface $session, TokenStorageInterface $security,
       UserManagerInterface $userManager, TranslatorInterface $translator)
    {
        $this->mailer         = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router         = $router;
        $this->session        = $session;
        $this->security       = $security;
        $this->userManager    = $userManager;
        $this->translator     = $translator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN      => 'onLoginSuccess',
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onLoginSuccess',
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS    => 'onRegistrationSuccess',
//                FOSUserEvents::PROFILE_EDIT_INITIALIZE => 'onProfileEditInitialize',
//                FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onProfileEditSuccess',
        );
    }

    public function onLoginSuccess($event)
    {
        if ($event instanceof UserEvent) {
            $user = $event->getUser();
        }
        if ($event instanceof InteractiveLoginEvent) {
            $user = $event->getAuthenticationToken()->getUser();
        }
        $user->setSigninCount($user->getSigninCount() + 1);
        $this->userManager->updateUser($user);
    }

    public function onRegistrationInitialize(GetResponseUserEvent $event)
    {
        $user = $event->getUser();
        $user->setEmail(null);
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();
        $user->setNickname($user->getUsername());
        $user->setUsername($user->getEmail());
    }

    public function onProfileEditInitialize(GetResponseUserEvent $event)
    {
        // required, because when Success's event is called, session already contains new email
        $this->email = $this->security->getToken()->getUser()->getEmail();
    }

    public function onProfileEditSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();
        if ($user->getEmail() !== $this->email) {
            // disable user
            $user->setEnabled(false);

            // send confirmation token to new email
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $this->mailer->sendConfirmationEmailMessage($user);

            // force user to log-out
            $this->security->setToken();

            // redirect user to check email page
            $this->session->set('fos_user_send_confirmation_email/email', $user->getEmail());
            $url = $this->router->generate('fos_user_registration_check_email');
            $event->setResponse(new RedirectResponse($url));
        }
    }

}
