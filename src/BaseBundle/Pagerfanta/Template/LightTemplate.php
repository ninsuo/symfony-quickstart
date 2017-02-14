<?php

namespace BaseBundle\Pagerfanta\Template;

use Pagerfanta\View\Template\Template;
use Symfony\Component\Translation\TranslatorInterface;

class LightTemplate extends Template
{
    protected static $defaultOptions = [
        'prev_message'        => 'base.pager.prev',
        'next_message'        => 'base.pager.next',
        'dots_message'        => '&hellip;',
        'active_suffix'       => '<span class="sr-only">(%%active_suffix%%)</span>',
        'css_container_class' => 'pagination',
        'css_prev_class'      => 'prev',
        'css_next_class'      => 'next',
        'css_disabled_class'  => 'disabled',
        'css_dots_class'      => 'disabled',
        'css_active_class'    => 'active',
        'rel_previous'        => 'prev',
        'rel_next'            => 'next',
        'hash'                => '',
    ];
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct();

        $this->translator = $translator;
    }

    public function container()
    {
        return sprintf('<ul class="%s">%%pages%%</ul>', $this->option('css_container_class'));
    }

    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    public function pageWithText($page, $text)
    {
        $class = null;

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function previousDisabled()
    {
        $class = $this->previousDisabledClass();
        $text  = $this->translator->trans($this->option('prev_message'));

        return $this->spanLi($class, $text);
    }

    public function previousEnabled($page)
    {
        $text  = $this->translator->trans($this->option('prev_message'));
        $class = $this->option('css_prev_class');
        $rel   = $this->option('rel_previous');

        return $this->pageWithTextAndClass($page, $text, $class, $rel);
    }

    public function nextDisabled()
    {
        $class = $this->nextDisabledClass();
        $text  = $this->translator->trans($this->option('next_message'));

        return $this->spanLi($class, $text);
    }

    public function nextDisabledClass()
    {
        return $this->option('css_next_class').' '.$this->option('css_disabled_class');
    }

    public function nextEnabled($page)
    {
        $text  = $this->translator->trans($this->option('next_message'));
        $class = $this->option('css_next_class');
        $rel   = $this->option('rel_next');

        return $this->pageWithTextAndClass($page, $text, $class, $rel);
    }

    public function first()
    {
        return $this->page(1);
    }

    public function last($page)
    {
        return $this->page($page);
    }

    public function current($page)
    {
        $current = $this->translator->trans('base.pager.current');
        $text    = trim($page.' '.str_replace('%%active_suffix%%', $current, $this->option('active_suffix')));
        $class   = $this->option('css_active_class');

        return $this->spanLi($class, $text);
    }

    public function separator()
    {
        $class = $this->option('css_dots_class');
        $text  = $this->option('dots_message');

        return $this->spanLi($class, $text);
    }

    public function spanLi($class, $text)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<li%s><span>%s</span></li>', $liClass, $text);
    }

    private function pageWithTextAndClass($page, $text, $class, $rel = null)
    {
        $href = $this->generateRoute($page);

        return $this->linkLi($class, $href, $text, $rel);
    }

    private function previousDisabledClass()
    {
        return $this->option('css_prev_class').' '.$this->option('css_disabled_class');
    }

    private function linkLi($class, $href, $text, $rel = null)
    {
        $href = $href . ($this->option('hash') ? '#'.$this->option('hash') : '');
        $liClass = $class ? sprintf(' class="%s"', $class) : '';
        $rel     = $rel ? sprintf(' rel="%s"', $rel) : '';

        return sprintf('<li%s><a href="%s"%s>%s</a></li>', $liClass, $href, $rel, $text);
    }
}
