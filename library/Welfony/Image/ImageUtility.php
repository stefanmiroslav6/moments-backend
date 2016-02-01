<?php

// ==============================================================================
//
// This file is part of the WelStory.
//
// Create by Welfony Support <support@welfony.com>
// Copyright (c) 2012-2014 welfony.com
//
// For the full copyright and license information, please view the LICENSE
// file that was distributed with this source code.
//
// ==============================================================================

namespace Welfony\Image;

class ImageUtility
{

    public static function getImageFileType($fileContent)
    {
        $format = '';

        if (substr($fileContent, 0, 3) == "\xFF\xD8\xFF") {
            $format = 'jpg';
        } elseif (substr($fileContent, 0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") {
            $format = 'png';
        }

        return $format;
    }

    public static function getImageFromUrl($url, $targetPath = null)
    {
        $ch = curl_init($url);

        if ($targetPath) {
            $fp = fopen($targetPath, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
        }

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $return = curl_exec($ch);
        curl_close($ch);

        if ($targetPath) {
            fclose($fp);
        }

        return $return;
    }

}