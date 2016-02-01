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

class FollowRepository extends AbstractRepository
{

    public static function getFollowerByUserAndSender($userId, $senderId)
    {
        $strSql = 'SELECT
                       F.*
                   FROM Follow F
                   WHERE F.ReceiverId = ? AND F.SenderId = ?
                   LIMIT 1';

        return self::$app->conn->fetchAssoc($strSql, array($userId, $senderId));
    }

    public static function getAllFollowersCountByUser($userId)
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Follow F
                   WHERE F.ReceiverId = ?
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array($userId));

        return $row['Total'];
    }

    public static function getAllFollowersByUser($userId, $page, $pageSize, $currentUserId)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       F.CreatedDate,
                       CASE
                       WHEN EXISTS(SELECT 1 FROM Follow FCU WHERE FCU.SenderId = ? AND FCU.ReceiverId = U.UserId) THEN 1
                       ELSE 0
                       END Followed
                   FROM Follow F
                   INNER JOIN Users U ON U.UserId = F.SenderId
                   WHERE F.ReceiverId = ?
                   ORDER BY F.CreatedDate DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array($currentUserId, $userId));
    }

    public static function getAllFollowingsCountByUser($userId)
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Follow F
                   WHERE F.SenderId = ?
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array($userId));

        return $row['Total'];
    }

    public static function getAllFollowingsByUser($userId, $page, $pageSize, $currentUserId)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       F.CreatedDate,
                       CASE
                       WHEN EXISTS(SELECT 1 FROM Follow FCU WHERE FCU.SenderId = ? AND FCU.ReceiverId = U.UserId) THEN 1
                       ELSE 0
                       END Followed
                   FROM Follow F
                   INNER JOIN Users U ON U.UserId = F.ReceiverId
                   WHERE F.SenderId = ?
                   ORDER BY F.CreatedDate DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array($currentUserId, $userId));
    }

    public static function save($data)
    {
        try {
            if (self::$app->conn->insert('Follow', $data)) {
                return self::$app->conn->lastInsertId();
            }
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return false;
    }

    public static function removeBySenderAndReceiver($userId, $receiverId)
    {
        try {
            self::$app->conn->delete('Follow', array('SenderId' => $userId, 'ReceiverId' => $receiverId));
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return true;
    }

}