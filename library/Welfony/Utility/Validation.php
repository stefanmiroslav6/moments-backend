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

namespace Welfony\Utility;

class Validation
{

    public static function isValidDate($str, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $str);
        return $d && $d->format($format) == $str;
    }

    public static function isValidUrl($str)
    {
        return filter_var($str, FILTER_VALIDATE_URL);
    }

    public static function isValidEmail($str)
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public static function isValidUsername($str)
    {
        return preg_match('/^[A-Za-z0-9-]{4,}$/',$str);
    }

    public static function isValidPassword($str)
    {
        return preg_match('/^\w{6,}$/',$str);
    }

    public static function isValidFindPasswordToken($str)
    {
        return preg_match('/^\w{6}$/',$str);
    }

    public static function isValidImage($file)
    {
        return true;
    }

    public static function isValidVideo($file)
    {
        return true;
    }

    public static function isValidAudio($file)
    {
        return true;
    }

    public static function uploadErrorToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

}