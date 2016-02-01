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

namespace Welfony\Welstory\Repository;

use Welfony\Welstory\Repository\Base\AbstractRepository;

class SocialRepository extends AbstractRepository
{

    public static function save($data)
    {
        try {
            if (self::$app->conn->insert('Social', $data)) {
                return self::$app->conn->lastInsertId();
            }
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return false;
    }

    public static function update($externalId, $socialType, $data)
    {
        try {
            return self::$app->conn->update('Social', $data, array('ExternalId' => $externalId, 'Type' => $socialType));
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }
    }

}