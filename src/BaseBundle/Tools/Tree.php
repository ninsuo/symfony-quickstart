<?php

namespace BaseBundle\Tools;

use BaseBundle\Api\TreeInterface;

class Tree
{
    /**
     * Takes an array of nodes having a path property defining their position in the tree.
     * Path is composed of strings separated by $separator.
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
     * @param string $separator
     * @param bool $strictMode
     * @return array
     * @throws \LogicException
     * @throws \RuntimeException
     */
    static public function createTree($nodes, $separator = "\n", $strictMode = true)
    {
        $tree = [];

        foreach ($nodes as $node) {
            if (!($node instanceof TreeInterface)) {
                throw new \LogicException("Tree nodes should implement TreeInterface class.");
            }
            $ref = &$tree;
            $path = [];
            foreach ($arr = explode($separator, $node->getPath()) as $key => $elem) {
                if (empty($elem)) {
                    continue;
                }
                $path[] = $key;
                if ($strictMode && isset($ref[$elem]) && (count($arr) == $key + 1 || is_object($ref[$elem]))) {
                    throw new \RuntimeException("Node is overwriting another one at ".implode($separator, $path));
                }
                if (count($arr) == $key + 1) {
                    $ref[$elem] = $node;
                } else if (!array_key_exists($elem, $ref)) {
                    $ref[$elem] = [];
                }
                $ref = &$ref[$elem];
            }
        }

        return $tree;
    }
}
