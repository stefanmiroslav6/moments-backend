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

class CommentRepository extends AbstractRepository
{

    public static function getAllCommentCountByStory($storyId)
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Comment C
                   WHERE C.StoryId = ?
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array($storyId));

        return $row['Total'];
    }

    public static function getAllCommentsByStory($storyId, $page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       C.CommentId,
                       C.Body,
                       C.CreatedDate,

                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       U.ProfileBackgroundUrl
                   FROM Comment C
                   INNER JOIN Users U ON U.UserId = C.UserId
                   WHERE C.StoryId = ?
                   ORDER BY C.CommentId DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array($storyId));
    }

    public static function findCommentById($id)
    {
        $strSql = 'SELECT
                       C.CommentId,
                       C.UserId,
                       C.StoryId,
                       C.Body,
                       C.Deep,
                       C.ParentId,
                       C.CreatedDate
                   FROM Comment C
                   WHERE C.CommentId = ?
                   LIMIT 1';

        return self::$app->conn->fetchAssoc($strSql, array($id));
    }

    public static function save($data)
    {
        try {
            if (self::$app->conn->insert('Comment', $data)) {
                return self::$app->conn->lastInsertId();
            }
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return false;
    }

    public static function update($commentId, $data)
    {
        try {
            return self::$app->conn->update('Comment', $data, array('CommentId' => $commentId));
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }
    }

}