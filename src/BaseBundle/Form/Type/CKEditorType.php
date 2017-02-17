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
            'language'     => 'en',
            'toolbar'      => [
                ['name' => 'clipboard', 'items' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']],
                ['name' => 'links', 'items' => ['Link', 'Unlink', 'Anchor']],
                ['name' => 'insert', 'items' => ['Image', 'Embed', 'Table', 'HorizontalRule', 'SpecialChar', 'Emojione']],
                ['name' => 'paragraph', 'items' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']],
                ['name' => 'tools', 'items' => ['Maximize']],
                ['name' => 'document', 'items' => ['Preview', 'Source']],
                '/',
                ['name' => 'basicstyles', 'items' => ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']],
                ['name' => 'justify', 'items' => ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']],
                ['name' => 'colors', 'items' => ['TextColor', 'BGColor']],
                ['name' => 'styles', 'items' => ['Styles', 'Format', 'Font', 'FontSize']],
            ],
            'extraPlugins' => 'embed,embedbase,emojione,notification,notificationaggregator,preview,widget,lineutils,widgetselection',
        ];

        if ($this->isGranted($this->getParameter('role_file_upload'))) {
            $config = array_merge($config, [
                'filebrowserBrowseRoute' => 'ckeditor_browse',
                'filebrowserUploadRoute' => 'ckeditor_upload',
            ]);
        }

        $resolver->setDefault('config', $config);

        $resolver->setDefault('plugins', [
            'embed'                  => [
                'path'     => '/bundles/base/ckeditor/plugins/embed/',
                'filename' => 'plugin.js',
            ],
            'embedbase'              => [
                'path'     => '/bundles/base/ckeditor/plugins/embedbase/',
                'filename' => 'plugin.js',
            ],
            'emojione'               => [
                'path'     => '/bundles/base/ckeditor/plugins/emojione/',
                'filename' => 'plugin.js',
            ],
            'lineutils'              => [
                'path'     => '/bundles/base/ckeditor/plugins/lineutils/',
                'filename' => 'plugin.js',
            ],
            'notification'           => [
                'path'     => '/bundles/base/ckeditor/plugins/notification/',
                'filename' => 'plugin.js',
            ],
            'notificationaggregator' => [
                'path'     => '/bundles/base/ckeditor/plugins/notificationaggregator/',
                'filename' => 'plugin.js',
            ],
            'preview' => [
                'path'     => '/bundles/base/ckeditor/plugins/preview/',
                'filename' => 'plugin.js',
            ],
            'widget'                 => [
                'path'     => '/bundles/base/ckeditor/plugins/widget/',
                'filename' => 'plugin.js',
            ],
            'widgetselection'        => [
                'path'     => '/bundles/base/ckeditor/plugins/widgetselection/',
                'filename' => 'plugin.js',
            ],
        ]);
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
