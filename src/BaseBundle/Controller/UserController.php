<?php

namespace BaseBundle\Controller;

use BaseBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    /**
     * Login failure action or login direct access.
     *
     * @Method({"GET"})
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        if ($this->getUser()) {
            return new RedirectResponse($this->generateUrl('home'));
        } else {
            return $this->forward('HWIOAuthBundle:Connect:connect');
        }
    }

    /**
     * Logout action.
     *
     * @Method({"GET"})
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
        $this->container->get('security.token_storage')->setToken(null);

        return $this->goBack($request);
    }

    /**
     * Login action.
     *
     * @Method({"GET"})
     * @Route("/connect/{service}", name="connect")
     */
    public function connectAction(Request $request, $service)
    {
        $this->get('session')->set('referer', $request->headers->get('referer'));

        return $this->forward('HWIOAuthBundle:Connect:redirectToService', ['service' => $service]);
    }

    /**
     * Login success action.
     *
     * @Method({"GET"})
     * @Route("/welcome", name="welcome")
     */
    public function welcomeAction()
    {
        $referer = $this->get('session')->get('referer');
        if (is_null($referer)) {
            return new RedirectResponse($this->generateUrl('home'));
        }

        return new RedirectResponse($referer);
    }

    /**
     * User's profile update
     *
     * Take care, this option is incompatible with user_info_auto_update = true
     *
     * @Route("/profile", name="profile")
     * @Security("has_role('ROLE_USER')")
     * @Template()
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function profileAction(Request $request)
    {
        if (!$this->getParameter('accounts_updatable')) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder($this->getUser())
            ->add('nickname', Type\TextType::class, [
                'label' => 'base.profile.nickname',
            ])
            ->add('contact', Type\EmailType::class, [
                'label' => 'base.profile.contact',
            ])
            ->add('picture', Type\UrlType::class, [
                'label' => 'base.profile.picture',
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'base.button.submit',
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getManager()->persist($this->getUser());
            $this->getManager()->flush($this->getUser());

            $this->success('base.profile.updated');

            return $this->redirectToRoute('profile');
        }

        return [
            'data' => $this->getUser(),
            'form' => $form->createView(),
        ];
    }

    /**
     * User unsubscription confirmation.
     *
     * @Route("/unsubscribe", name="unsubscribe")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function unsubscribeAction(Request $request)
    {
        if (!$this->getParameter('accounts_removable')) {
            throw $this->createNotFoundException();
        }

        // CRSF
        $form = $this
            ->createFormBuilder()
            ->add('submit', Type\SubmitType::class, [
                'label' => 'base.unsubscribe.confirm',
                'attr'  => [
                    'class' => 'btn btn-danger',
                ],
            ])
            ->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getUser();
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($user);
                $em->flush($user);

                return $this->forward('BaseBundle:User:logout');
            }

            return $this->goBack($request);
        }

        return $this->render('BaseBundle:User:unsubscribe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
