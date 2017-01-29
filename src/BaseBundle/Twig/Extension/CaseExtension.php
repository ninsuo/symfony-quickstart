<?php

namespace BaseBundle\Twig\Extension;

use CaseHelper\CaseHelperFactory;

/**
 * This extension manage case conversions.
 * Requires nabil1337/case-helper
 * https://github.com/nabil1337/case-helper.
 */
class CaseExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            // space case
            new \Twig_SimpleFilter('spacecase', [$this, 'spacecase']),

            // camelCase
            new \Twig_SimpleFilter('camelcase', [$this, 'camelcase']),

            // PascalCase
            new \Twig_SimpleFilter('pascalcase', [$this, 'pascalcase']),

            // kebab-case
            new \Twig_SimpleFilter('kebabcase', [$this, 'kebabcase']),

            // snake_case
            new \Twig_SimpleFilter('snakecase', [$this, 'snakecase']),

            // SCREAMING_SNAKE_CASE
            new \Twig_SimpleFilter('screamingsnakecase', [$this, 'screamingsnakecase']),

            // Train-Case
            new \Twig_SimpleFilter('traincase', [$this, 'traincase']),
        ];
    }

    public function spacecase($string, $type = 'camelcase')
    {
        $o = CaseHelperFactory::make($this->mapInputType($type));

        return $o->toSpaceCase($string);
    }

    public function camelcase($string, $type = 'pascalcase')
    {
        $o = CaseHelperFactory::make($this->mapInputType($type));

        return $o->toCamelCase($string);
    }

    public function pascalcase($string, $type = 'camelcase')
    {
        $o = CaseHelperFactory::make($this->mapInputType($type));

        return $o->toPascalCase($string);
    }

    public function kebabcase($string, $type = 'camelcase')
    {
        $o = CaseHelperFactory::make($this->mapInputType($type));

        return $o->toKebabCase($string);
    }

    public function snakecase($string, $type = 'camelcase')
    {
        $o = CaseHelperFactory::make($this->mapInputType($type));

        return $o->toSnakeCase($string);
    }

    public function screamingsnakecase($string, $type = 'camelcase')
    {
        $o = CaseHelperFactory::make($this->mapInputType($type));

        return $o->toScreamingSnakeCase($string);
    }

    public function traincase($string, $type = 'camelcase')
    {
        $o = CaseHelperFactory::make($this->mapInputType($type));

        return $o->toTrainCase($string);
    }

    public function getName()
    {
        return 'case';
    }

    protected function mapInputType($type)
    {
        $types = [
            'spacecase'          => CaseHelperFactory::INPUT_TYPE_SPACE_CASE,
            'camelcase'          => CaseHelperFactory::INPUT_TYPE_CAMEL_CASE,
            'pascalcase'         => CaseHelperFactory::INPUT_TYPE_PASCAL_CASE,
            'kebabcase'          => CaseHelperFactory::INPUT_TYPE_KEBAB_CASE,
            'snakecase'          => CaseHelperFactory::INPUT_TYPE_SNAKE_CASE,
            'screamingsnakecase' => CaseHelperFactory::INPUT_TYPE_SCREAMING_SNAKE_CASE,
            'traincase'          => CaseHelperFactory::INPUT_TYPE_TRAIN_CASE,
        ];

        return isset($types[$type]) ?: 'camelcase';
    }
}
