<?php

namespace BaseBundle\Form\Type;

use BaseBundle\Base\BaseType;
use BaseBundle\Value\Markdown;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provide a markdown type with live preview.
 * - to disable live preview, set preview option to false
 * - to change live preview target, set preview attribute to the new jquery container selector
 *
 * In this application page, prefer using <div class="markdown">{{ data.markdown }}</div>
 *
 * In an email (or other media that won't interpret markdown), use {{ data.html|purify }},
 * but consider the difference between marked sanitization (that will escape dangerous
 * html markup) and htmlpurifier (that will remove it instead).
 */
class MarkdownType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $textareaOptions = $options;
        $textareaOptions['compound'] = false;
        unset($textareaOptions['preview']);

        $builder
           ->add('markdown', TextareaType::class, $textareaOptions)
           ->add('html', TextareaType::class, [
               'required' => false,
           ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['preview'] = $options['preview'];
    }

    public function getParent()
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['preview']);

        $resolver->setDefaults([
            'data_class' => Markdown::class,
            'compound' => true,
            'preview' => true,
        ]);
    }
}
