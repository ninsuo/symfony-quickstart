<?php

namespace BaseBundle\Form\Type;

use BaseBundle\Base\BaseType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType as BaseCKEditorType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CKEditorType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $config = [
            'language' => 'fr',
            'toolbar'  => [
                ['name' => 'clipboard', 'items' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']],
                ['name' => 'links', 'items' => ['Link', 'Unlink', 'Anchor']],
                ['name' => 'insert', 'items' => ['Image', 'Table', 'HorizontalRule', 'SpecialChar']],
                ['name' => 'paragraph', 'items' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']],
                ['name' => 'tools', 'items' => ['Maximize']],
                ['name' => 'document', 'items' => ['Source']],
                '/',
                ['name' => 'basicstyles', 'items' => ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']],
                ['name' => 'justify', 'items' => ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']],
                ['name' => 'colors', 'items' => ['TextColor', 'BGColor']],
                ['name' => 'styles', 'items' => ['Styles', 'Format', 'Font', 'FontSize']],
            ],
        ];

        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('GROUP_EDITOR')) {
            $config = array_merge($config, [
                'filebrowserBrowseRoute' => "ckeditor_browse",
                'filebrowserUploadRoute' => "ckeditor_upload",
            ]);
        }

        $resolver->setDefault('config', $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return BaseCKEditorType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'light_ckeditor';
    }
}