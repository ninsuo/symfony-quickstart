<?php

namespace BaseBundle\Tools;

class Gd
{
    public static function load($file)
    {
        $content = file_get_contents($file);
        $img     = @imagecreatefromstring($content);

        return $img;
    }

    public static function save($img, $file = null)
    {
        if (!is_null($file)) {
            imagepng($img, $file);

            return;
        }

        ob_start();
        imagepng($img);

        return ob_get_clean();
    }

    public static function resize($img, $targetWidth, $targetHeight)
    {
        $srcWidth  = imagesx($img);
        $srcHeight = imagesy($img);

        $srcRatio    = $srcWidth / $srcHeight;
        $targetRatio = $targetWidth / $targetHeight;
        if (($srcWidth <= $targetWidth) && ($srcHeight <= $targetHeight)) {
            $imgTargetWidth  = $srcWidth;
            $imgTargetHeight = $srcHeight;
        } elseif ($targetRatio > $srcRatio) {
            $imgTargetWidth  = (int) ($targetHeight * $srcRatio);
            $imgTargetHeight = $targetHeight;
        } else {
            $imgTargetWidth  = $targetWidth;
            $imgTargetHeight = (int) ($targetWidth / $srcRatio);
        }

        $targetImg   = imagecreatetruecolor($targetWidth, $targetHeight);
        $transparent = imagecolorallocate($targetImg, 255, 255, 255);
        imagefill($targetImg, 0, 0, $transparent);
        imagecolortransparent($targetImg, $transparent);

        imagecopyresampled(
           $targetImg, $img, ($targetWidth - $imgTargetWidth) / 2, ($targetHeight - $imgTargetHeight) / 2, 0, 0,
           $imgTargetWidth, $imgTargetHeight, $srcWidth, $srcHeight
        );

        return $targetImg;
    }
}
