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

class Util
{

    public static function genRandomNum($length, $strength = 6)
    {
        $vowels = 'aeuy';
        $consonants = 'bdghjmnpqrstvz';
        if ($strength >= 1) {
            $consonants .= 'BDGHJLMNPQRSTVWXZ';
        }
        if ($strength >= 2) {
            $vowels .= "AEUY";
        }
        if ($strength >= 4) {
            $consonants .= '23456789';
        }
        if ($strength >= 8) {
            $consonants .= '@#$%';
        }

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }

        return $password;
    }

    public static function genRandomEmail($prefix = 'ws')
    {
        static $emailFormat = '%s-%s@welstory.com';
        return sprintf($emailFormat, $prefix, strtolower(self::genRandomNum(8)));
    }

    public static function genRandomPassword()
    {
        return self::genRandomNum(6);
    }

}