<?php

namespace BaseBundle\Tools;

class Math
{
    public function rand($expected = 10)
    {
        return openssl_random_pseudo_bytes($expected);
    }

    public function uuid()
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
