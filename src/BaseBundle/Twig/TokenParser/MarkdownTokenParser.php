<?php

namespace BaseBundle\Twig\TokenParser;

use BaseBundle\Twig\Node\MarkdownNode;

class MarkdownTokenParser extends \Twig_TokenParser
{
    // {% markdown
    public function getTag()
    {
        return 'markdown';
    }

    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        // %}
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        // ~> {% endmarkdown
        $body = $this->parser->subparse(function(\Twig_Token $token) {
            return $token->test('endmarkdown');
        }, true);

        // %}
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new MarkdownNode($body, $lineno, $this->getTag());
    }
}
