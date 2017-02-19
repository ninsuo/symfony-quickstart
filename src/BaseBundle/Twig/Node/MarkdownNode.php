<?php

namespace BaseBundle\Twig\Node;

class MarkdownNode extends \Twig_Node
{
    public function __construct(\Twig_NodeInterface $body, $lineno, $tag = 'markdown')
    {
        parent::__construct(['body' => $body], [], $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("echo (new \Parsedown())->text(\BaseBundle\Twig\Extension\MarkdownExtension::removeMargin(ob_get_clean()));\n")
        ;
    }
}