<?php

namespace BaseBundle\Tools;

class Math
{
    public function rand($expected = 10)
    {
        $allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
        $handle = fopen('/dev/urandom', 'r');
        $current = 0;
        $result = '';
        while ($current < $expected) {
            $chars = fread($handle, 1024);
            for ($i = 0; $i < 1024; $i++) {
                $char = $chars[$i];
                if (false !== strpos($allowed, $char)) {
                    $result .= $char;
                    $current++;
                }
            }
        }
        fclose($handle);

        return $result;
    }
}
