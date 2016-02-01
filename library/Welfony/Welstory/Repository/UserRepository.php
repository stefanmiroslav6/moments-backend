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

class UserRepository extends AbstractRepository
{

    public static function getAllUsersCount()
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Users
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql);

        return $row['Total'];
    }

    public static function getAllUsers($page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       U.UserId,
                       U.Username,
                       U.Email,
                       U.Gender,
                       U.Birthday,
                       U.AvatarUrl,
                       U.CreatedDate
                   FROM Users U
                   ORDER BY U.UserId DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql);
    }

    public static function findUserDetailById($id)
    {
        $strSql = 'SELECT
                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       (SELECT COUNT(1)
                        FROM Story US
                        WHERE US.UserId = U.UserId) StoryCount,
                       (SELECT COUNT(1)
                        FROM StoryLike SL
                        WHERE SL.UserId = U.UserId) LikeCount,
                       (SELECT COUNT(1)
                        FROM Follow FER
                        WHERE FER.ReceiverId = U.UserId) FollowerCount,
                       (SELECT COUNT(1)
                        FROM Follow FING
                        WHERE FING.SenderId = U.UserId) FollowingCount
                   FROM Users U
                   WHERE U.UserId = ?
                   LIMIT 1';

        return self::$app->conn->fetchAssoc($strSql, array($id));
    }

    public static function findUserById($id)
    {
        $strSql = 'SELECT
                       U.UserId,
                       U.Username,
                       U.Email,
                       U.Gender,
                       U.Birthday,
                       U.AvatarUrl,
                       U.CreatedDate,
                       U.LastModifiedDate
                   FROM Users U
                   WHERE U.UserId = ?
                   LIMIT 1';

        return self::$app->conn->fetchAssoc($strSql, array($id));
    }

    public static function findUserByUsernameOrEmail($usernameOrEmail)
    {
        $strSql = 'SELECT
                       U.UserId,
                       U.Username,
                       U.Email,
                       U.Gender,
                       U.Birthday,
                       U.AvatarUrl,
                       U.Password,
                       U.ForgotPasswordEmailToken,
                       U.ForgotPasswordEmailCreateDate,
                       U.CreatedDate,
                       U.LastModifiedDate
                   FROM Users U
                   WHERE U.Username = ? OR U.Email = ?
                   LIMIT 1';

        return self::$app->conn->fetchAssoc($strSql, array($usernameOrEmail, $usernameOrEmail));
    }

    public static function findUserBySocialExternalId($socialExternalId, $socialType)
    {
        $strSql = 'SELECT
                       U.UserId,
                       U.Username,
                       U.Email,
                       U.Gender,
                       U.Birthday,
                       U.AvatarUrl,
                       U.CreatedDate,
                       U.LastModifiedDate
                   FROM Users U
                   INNER JOIN Social S ON S.UserId = U.UserId
                   WHERE S.ExternalId = ? AND S.Type = ?
                   LIMIT 1';

        $stmt = self::$app->conn->prepare($strSql);
        $stmt->bindValue(1, $socialExternalId);
        $stmt->bindValue(2, $socialType);
        $stmt->execute();

        return $stmt->fetch();
    }

    public static function save($data)
    {
        try {
            if (self::$app->conn->insert('Users', $data)) {
                return self::$app->conn->lastInsertId();
            }
        } catch (\Exception $e) {
            $app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return false;
    }

    public static function update($userId, $data)
    {
        try {
            return self::$app->conn->update('Users', $data, array('UserId' => $userId));
        } catch (\Exception $e) {
            $app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }
    }

}