<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;

/**
 * @Route("/users")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UsersController extends BaseController
{
    /**
     * @Route("/", name="admin_users")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('u')
           ->from(User::class, 'u')
        ;

        if ($filter) {
            $qb
               ->where('u.nickname LIKE :criteria OR u.contact LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'pager'  => $this->getPager($request, $qb),
            'me'     => $this->getUser()->getId(),
        ];
    }

    /**
     * @Route("/toggle/admin/{token}", name="admin_users_toggle_admin")
     * @Template()
     */
    public function toggleAdminAction(Request $request, $token)
    {
        $this->checkCsrfToken('administration', $token);

        $this->get('admin.storage.user')->toggleAdmin(
           intval($request->request->get('id'))
        );

        return new Response();
    }

    /**
     * @Route("/toggle/frozen/{token}", name="admin_users_toggle_frozen")
     * @Template()
     */
    public function toggleFrozenAction(Request $request, $token)
    {
        $this->checkCsrfToken('administration', $token);

        $this->get('admin.storage.user')->toggleFrozen(
           intval($request->request->get('id'))
        );

        return new Response();
    }

    /**
     * @Route("/edit/contact/{id}", name="admin_users_edit_contact")
     * @Template("AdminBundle::_editOnClick.html.twig")
     */
    public function _editContactAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:User');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint =  $this->generateUrl('admin_users_edit_contact', ['id' => $id]);

        $form = $this
           ->createNamedFormBuilder("edit-contact-{$id}", Type\FormType::class, $entity, [
               'action' => $endpoint,
           ])
           ->add('contact', Type\EmailType::class, [
               'label'       => "admin.users.contact",
               'constraints' => [
                   new Constraints\NotBlank(),
                   new Constraints\Email(),
               ],
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => "base.crud.action.save",
               'attr'  => [
                   'class' => 'domajax',
               ],
           ])
           ->getForm()
           ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($entity);
            $em->flush();

            return [
                'text' => $entity->getContact(),
                'endpoint' => $endpoint,
            ];
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/edit/nickname/{id}", name="admin_users_edit_nickname")
     * @Template("AdminBundle::_editOnClick.html.twig")
     */
    public function _editNicknameAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:User');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint =  $this->generateUrl('admin_users_edit_nickname', ['id' => $id]);

        $form = $this
           ->createNamedFormBuilder("edit-nickname-{$id}", Type\FormType::class, $entity, [
               'action' => $endpoint,
           ])
           ->add('nickname', Type\TextType::class, [
               'label'       => "admin.users.nickname",
               'constraints' => [
                   new Constraints\NotBlank(),
               ],
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => "base.crud.action.save",
               'attr'  => [
                   'class' => 'domajax',
               ],
           ])
           ->getForm()
           ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($entity);
            $em->flush();

            return [
                'text' => $entity->getNickname(),
                'endpoint' => $endpoint,
            ];
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
