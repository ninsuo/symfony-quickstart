<?php

namespace BaseBundle\Tools;

use BaseBundle\Api\TreeInterface;

class Tree
{
    /**
     * Takes an array of nodes having a path property defining their position in the tree.
     * Path is composed of strings separated by $separator.
     *
     * The $pathCallback callback will be used on each node, it will receive a node in
     * arguments and should return the path of this node as a string.
     *
     * Example:
     *
     * If you have objects having the following paths:
     * $objectA have path app/AppCache.php
     * $objectB have path app/AppKernel.php
     * $objectC have path app/config/config.yml
     * $objectD have path app/config/config_dev.yml
     *
     * With "/" separator, you will end up with:
     *
     * app
     *  |
     *  |-- AppCache.php => $objectA
     *  |-- appKernel.php => $objectB
     *  |-- config
     *        |-- config.yml => $objectC
     *        |-- config_dev.yml => $objectC
     *
     * @param TreeInterface[] $nodes
     * @param callable        $pathCallback
     * @param string          $separator
     * @param bool            $strictMode
     * @param string          $trim
     *
     * @return array
     *
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public static function createTree($nodes, $pathCallback, $separator = "\n", $strictMode = true, $trim = null)
    {
        $tree = [];

        foreach ($nodes as $node) {
            $ref  = &$tree;
            $path = [];
            $stringPath = call_user_func($pathCallback, $node);
            foreach ($arr = explode($separator, $stringPath) as $key => $elem) {
                if (!is_null($trim)) {
                    $key  = trim($key, $trim);
                    $elem = trim($elem, $trim);
                }

                if (empty($elem)) {
                    continue;
                }

                $path[] = $key;

                if ($strictMode && isset($ref[$elem]) && (count($arr) == $key + 1 || is_object($ref[$elem]))) {
                    throw new \RuntimeException('Node is overwriting another one at '.implode($separator, $path));
                }

                if (count($arr) == $key + 1) {
                    $ref[$elem] = $node;
                } elseif (!array_key_exists($elem, $ref)) {
                    $ref[$elem] = [];
                }

                $ref = &$ref[$elem];
            }
        }

        return $tree;
    }
}
