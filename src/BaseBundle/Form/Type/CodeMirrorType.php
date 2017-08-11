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
 * Provide code mirror (a code editor) with line numbers, syntax highlighting,
 * dozens of supported languages...
 *
 * Put the mode in "mode" attribute, this will help to import required javascript
 * dependancies. If you require several modes, set them in an array.
 *
 * Put all required CodeMirror options in the "codemirror" form type option.
 *
 * See the documentation at: http://codemirror.net
 */
class CodeMirrorType extends BaseType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['codemirror'] = $options['codemirror'];
        $view->vars['modes'] = is_array($options['mode']) ? $options['mode'] : [$options['mode']];
    }

    public function getParent()
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['codemirror', 'mode']);

        $resolver->setDefaults([
            'mode' => 'jinja2',
            'codemirror' => [
                'lineNumbers' => true,
                'indentUnit' => 4,
                'indentWithTabs' => false,
                'smartIndent' => true,
                'fixedGutter' => true,
                'mode' => [
                    'name' => 'jinja2',
                    'htmlMode' => true,
                ],
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'codemirror';
    }
}
