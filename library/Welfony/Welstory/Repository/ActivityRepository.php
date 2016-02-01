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

class ActivityRepository extends AbstractRepository
{

    public static function getAllActivitiesCountByUser($userId)
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Activity A
                   WHERE A.ReceiverId = ?
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array($userId));

        return $row['Total'];
    }

    public static function getAllActivitiesByUser($userId, $page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       A.ActivityId,
                       A.CreatedDate,

                       CASE
                       WHEN A.Type = 1 THEN (SELECT
                                               CONCAT('@', SLU.Username, ' like your story \"', SLS.Title, '\".')
                                             FROM StoryLike SL
                                             INNER JOIN Users SLU ON SLU.UserId = SL.UserId
                                             INNER JOIN Story SLS ON SLS.StoryId = SL.StoryId
                                             WHERE SL.StoryId = A.StoryId AND SL.UserId = A.SenderId)
                       WHEN A.Type = 2 THEN (SELECT
                                               CONCAT('@', FU.Username, ' followed you.')
                                             FROM Users FU
                                             WHERE FU.UserId = A.SenderId)
                       WHEN A.Type = 3 THEN (SELECT
                                               CONCAT('@', CU.Username, ' comment your story \"', CS.Title, '\".')
                                             FROM Comment C
                                             INNER JOIN Users CU ON CU.UserId = C.UserId
                                             INNER JOIN Story CS ON CS.StoryId = C.StoryId
                                             WHERE C.CommentId = A.CommentId AND C.StoryId = A.StoryId AND C.UserId = A.SenderId)
                       ELSE 'System Message'
                       END Body,

                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       U.ProfileBackgroundUrl
                   FROM Activity A
                   INNER JOIN Users U ON U.UserId = A.SenderId
                   WHERE A.ReceiverId = ?
                   ORDER BY A.ActivityId DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array($userId));
    }

    public static function save($data)
    {
        try {
            if (self::$app->conn->insert('Activity', $data)) {
                return self::$app->conn->lastInsertId();
            }
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return false;
    }

}