<?php

namespace BaseBundle\Value;

class Markdown
{
    protected $markdown;
    protected $html;

    public function getMarkdown()
    {
        return $this->markdown;
    }

    public function setMarkdown($markdown)
    {
        $this->markdown = $markdown;

        return $this;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }
}