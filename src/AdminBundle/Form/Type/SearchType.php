<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('criteria', Type\SearchType::class, array(
               'label' => '&nbsp;',
           ))
           ->add('submit', Type\SubmitType::class, array(
               'label' => 'Search',
           ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'search_filter';
    }
}
