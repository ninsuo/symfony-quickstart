<?php

namespace BaseBundle\Twig\Extension;

use BaseBundle\Base\BaseTwigExtension;
use BaseBundle\Twig\TokenParser\MarkdownTokenParser;

class MarkdownExtension extends BaseTwigExtension
{
    public function getTokenParsers()
    {
        return [
            new MarkdownTokenParser(),
        ];
    }

    static public function removeMargin($text)
    {
        $lines = explode("\n", str_replace(["\r", "\t"], ['', '    '], $text));
        $margin = null;
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue ;
            }
            $sp = 0;
            for ($i = 0; $i < $len = strlen($line); $i++) {
                if ($line[$i] == ' ') {
                    $sp++;
                } else {
                    break ;
                }
            }
            if (is_null($margin) || $sp < $margin) {
                $margin = $sp;
            }
        }
        foreach ($lines as $key => $line) {
            $lines[$key] = substr($line, $margin);
        }

        return implode("\n", $lines);
    }

    public function getName()
    {
        return 'markdown';
    }
}
