<?php

namespace AdminBundle\Form\Type;

use BaseBundle\Base\BaseType;
use BaseBundle\Entity\Permission;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;

class PermissionType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $action = $options['id'] ? 'update' : 'create';

        $builder
           ->add('role', Type\TextType::class, [
               'label'       => "admin.permissions.{$action}_label",
               'constraints' => [
                   new Constraints\NotBlank(),
               ],
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => "admin.permissions.{$action}_submit",
               'attr'  => [
                   'class'           => 'domajax',
                   'data-endpoint'   => $this->get('router')->generate('admin_permissions_list', [
                       'selectedId' => $options['selected_id'],
                   ]),
                   'data-input-attr' => 'id',
                   'data-id'         => $options['id'],
                   'data-output'     => '#permissions',
               ],
           ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => Permission::class,
            'selected_id'   => null,
            'id'            => null,
        ]);
    }
}
