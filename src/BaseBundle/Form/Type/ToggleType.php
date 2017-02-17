<?php

namespace BaseBundle\Form\Type;

use BaseBundle\Base\BaseType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ToggleType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'data-off'    => $this->trans('base.button.off'),
                'data-on'     => $this->trans('base.button.on'),
                'data-style'  => 'ios',
                'data-toggle' => 'toggle',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CheckboxType::class;
    }
}