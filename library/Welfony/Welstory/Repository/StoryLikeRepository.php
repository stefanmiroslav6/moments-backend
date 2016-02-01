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

class StoryLikeRepository extends AbstractRepository
{

    public static function findByUserAndStoryId($userId, $storyId)
    {
        $strSql = 'SELECT
                       SL.StoryLikeId,
                       SL.UserId,
                       SL.StoryId,
                       SL.CreatedDate
                   FROM StoryLike SL
                   WHERE SL.UserId = ? AND SL.StoryId = ?
                   LIMIT 1';

        return self::$app->conn->fetchAssoc($strSql, array($userId, $storyId));
    }

    public static function save($data)
    {
        try {
            if (self::$app->conn->insert('StoryLike', $data)) {
                return self::$app->conn->lastInsertId();
            }
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return false;
    }

    public static function remove($userId, $storyId)
    {
        try {
            self::$app->conn->delete('StoryLike', array('UserId' => $userId, 'StoryId' => $storyId));
            return true;
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }
    }

}