<?php

namespace AdminBundle\Controller;

use BaseBundle\Base\BaseController;
use BaseBundle\Entity\Group;
use BaseBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            'orderBy'      => $this->orderBy($qb, User::class, 'u.nickname', 'ASC', 'test'),
            'pager'        => $this->getPager($qb),
            'me'           => $this->getUser()->getId(),
            'auto_updated' => $this->getParameter('user_info_auto_update'),
        ];
    }

    /**
     * @Route("/toggle/enabled/{token}", name="_admin_users_toggle_enabled")
     * @Template()
     */
    public function toggleEnabledAction(Request $request, $token)
    {
        $this->checkCsrfToken('administration', $token);

        $em   = $this->getManager();
        $user = $this->getEntityById('BaseBundle:User', $request->request->get('id'));

        $user->setIsEnabled(1 - intval($user->isEnabled()));

        $em->persist($user);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/toggle/admin/{token}", name="_admin_users_toggle_admin")
     * @Template()
     */
    public function toggleAdminAction(Request $request, $token)
    {
        $this->checkCsrfToken('administration', $token);

        $em   = $this->getManager();
        $user = $this->getEntityById('BaseBundle:User', $request->request->get('id'));

        $user->setIsAdmin(1 - intval($user->isAdmin()));

        $em->persist($user);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/edit/contact/{id}", name="_admin_users_edit_contact")
     * @Template("BaseBundle::editOnClick.html.twig")
     */
    public function _editContactAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:User');

        if ($this->getParameter('user_info_auto_update')) {
            throw $this->createNotFoundException();
        }

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint = $this->generateUrl('_admin_users_edit_contact', ['id' => $id]);

        $form = $this
           ->createNamedFormBuilder("edit-contact-{$id}", Type\FormType::class, $entity, [
               'action' => $endpoint,
           ])
           ->add('contact', Type\EmailType::class, [
               'label'       => 'admin.users.contact',
               'constraints' => [
                   new Constraints\NotBlank(),
                   new Constraints\Email(),
               ],
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => 'base.crud.action.save',
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
                'text'     => $entity->getContact(),
                'endpoint' => $endpoint,
            ];
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/edit/nickname/{id}", name="_admin_users_edit_nickname")
     * @Template("BaseBundle::editOnClick.html.twig")
     */
    public function _editNicknameAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:User');

        if ($this->getParameter('user_info_auto_update')) {
            throw $this->createNotFoundException();
        }

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint = $this->generateUrl('_admin_users_edit_nickname', ['id' => $id]);

        $form = $this
           ->createNamedFormBuilder("edit-nickname-{$id}", Type\FormType::class, $entity, [
               'action' => $endpoint,
           ])
           ->add('nickname', Type\TextType::class, [
               'label'       => 'admin.users.nickname',
               'constraints' => [
                   new Constraints\NotBlank(),
               ],
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => 'base.crud.action.save',
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
                'text'     => $entity->getNickname(),
                'endpoint' => $endpoint,
            ];
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/manage/{id}", name="admin_users_manage")
     * @Template()
     */
    public function manageAction(Request $request, $id)
    {
        $user = $this->getEntityById('BaseBundle:User', $id);

        return [
            'user'      => $user,
            'groupsIn'  => $this->_getUserGroups($request, $id, 'group-in'),
            'groupsOut' => $this->_getUserGroups($request, $id, 'group-out'),
        ];
    }

    protected function _getUserGroups(Request $request, $userId, $prefix)
    {
        $filter = $request->query->get("filter-{$prefix}");

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('g')
           ->from(Group::class, 'g')
           ->setParameter('userId', $userId)
        ;

        if ('group-in' == $prefix) {
            $qb->where(':userId MEMBER OF g.users');
        } else {
            $qb->where(':userId NOT MEMBER OF g.users');
        }

        if ($filter) {
            $qb
               ->andWhere('g.name LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'order' => $this->orderBy($qb, Group::class, 'g.name', 'ASC', $prefix),
            'pager' => $this->getPager($qb, $prefix),
        ];
    }
}
