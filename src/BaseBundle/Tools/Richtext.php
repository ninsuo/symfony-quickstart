<?php

namespace BaseBundle\Tools;

class Richtext
{
    public static function getCKEditorConfig()
    {
        return [
            'toolbar' => [
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
    }
}
