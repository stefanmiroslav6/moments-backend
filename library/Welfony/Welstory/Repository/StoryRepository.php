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

use Welfony\Welstory\Core\Enum\FollowStatus;
use Welfony\Welstory\Repository\Base\AbstractRepository;

class StoryRepository extends AbstractRepository
{

    public static function getAllStoriesCountByCategory($categoryId)
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Story S
                   WHERE S.CategoryId = ?
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array($categoryId));

        return $row['Total'];
    }

    public static function getAllStoriesByCategory($userId, $categoryId, $page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       S.StoryId,
                       S.Title StoryTitle,
                       S.Description,
                       S.Type StoryType,
                       S.CoverUrl,
                       S.CoverThumbUrl,
                       S.MediaUrl,
                       S.MediaLength,
                       S.ViewCount,
                       S.PlayCount,
                       S.DownloadCount,
                       S.SharedCount,
                       (SELECT COUNT(1) FROM Comment CMT WHERE CMT.StoryId = S.StoryId) CommentCount,
                       (SELECT COUNT(1) FROM StoryLike SL WHERE SL.StoryId = S.StoryId) LikeCount,
                       CASE
                       WHEN EXISTS(SELECT 1
                                   FROM StoryLike SLU
                                   WHERE SLU.UserId = ? AND SLU.StoryId = S.StoryId) THEN 1
                       ELSE 0
                       END IsLiked,
                       S.IsRecommended,
                       S.CreatedDate,
                       S.LastModifiedDate,
                       S.PublishDate,

                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       U.ProfileBackgroundUrl,

                       C.CategoryId,
                       C.Title CategoryTitle,

                       Q.QuestionId,
                       Q.Title QuestionTitle
                   FROM Story S
                   INNER JOIN Users U ON U.UserId = S.UserId
                   LEFT OUTER JOIN Category C ON C.CategoryId = S.CategoryId
                   LEFT OUTER JOIN Question Q ON Q.QuestionId = S.QuestionId
                   WHERE S.CategoryId = ?
                   ORDER BY S.StoryId DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array($userId, $categoryId));
    }

    public static function getAllFeaturedStoriesCount()
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Story S
                   WHERE S.StoryId > 0
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array());

        return $row['Total'];
    }

    public static function getAllFeaturedStories($page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       S.StoryId,
                       S.Title StoryTitle,
                       S.Description,
                       S.Type StoryType,
                       S.CoverUrl,
                       S.CoverThumbUrl,
                       S.MediaUrl,
                       S.MediaLength,
                       S.ViewCount,
                       S.PlayCount,
                       S.DownloadCount,
                       S.SharedCount,
                       (SELECT COUNT(1) FROM Comment CMT WHERE CMT.StoryId = S.StoryId) CommentCount,
                       (SELECT COUNT(1) FROM StoryLike SL WHERE SL.StoryId = S.StoryId) LikeCount,
                       0 IsLiked,
                       S.IsRecommended,
                       S.CreatedDate,
                       S.LastModifiedDate,
                       S.PublishDate,

                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       U.ProfileBackgroundUrl,

                       C.CategoryId,
                       C.Title CategoryTitle,

                       Q.QuestionId,
                       Q.Title QuestionTitle
                   FROM Story S
                   INNER JOIN Users U ON U.UserId = S.UserId
                   LEFT OUTER JOIN Category C ON C.CategoryId = S.CategoryId
                   LEFT OUTER JOIN Question Q ON Q.QuestionId = S.QuestionId
                   WHERE S.StoryId > 0
                   ORDER BY S.StoryId DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array());
    }

    public static function getAllLikedStoriesCountByUser($userId)
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Story S
                   INNER JOIN StoryLike SL ON SL.StoryId = S.StoryId
                   WHERE SL.UserId = ?
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array($userId));

        return $row['Total'];
    }

    public static function getAllLikedStoriesByUser($currentUserId, $userId, $page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       S.StoryId,
                       S.Title StoryTitle,
                       S.Description,
                       S.Type StoryType,
                       S.CoverUrl,
                       S.CoverThumbUrl,
                       S.MediaUrl,
                       S.MediaLength,
                       S.ViewCount,
                       S.PlayCount,
                       S.DownloadCount,
                       S.SharedCount,
                       (SELECT COUNT(1) FROM Comment CMT WHERE CMT.StoryId = S.StoryId) CommentCount,
                       (SELECT COUNT(1) FROM StoryLike SL WHERE SL.StoryId = S.StoryId) LikeCount,
                       CASE
                       WHEN EXISTS(SELECT 1
                                   FROM StoryLike SLU
                                   WHERE SLU.UserId = ? AND SLU.StoryId = S.StoryId) THEN 1
                       ELSE 0
                       END IsLiked,
                       S.IsRecommended,
                       S.CreatedDate,
                       S.LastModifiedDate,
                       S.PublishDate,

                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       U.ProfileBackgroundUrl,

                       C.CategoryId,
                       C.Title CategoryTitle,

                       Q.QuestionId,
                       Q.Title QuestionTitle
                   FROM Story S
                   INNER JOIN StoryLike SL ON SL.StoryId = S.StoryId
                   INNER JOIN Users U ON U.UserId = S.UserId
                   LEFT OUTER JOIN Category C ON C.CategoryId = S.CategoryId
                   LEFT OUTER JOIN Question Q ON Q.QuestionId = S.QuestionId
                   WHERE SL.UserId = ?
                   ORDER BY S.StoryId DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array($currentUserId, $userId));
    }

    public static function getAllStoriesCountByUser($userId)
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Story S
                   WHERE S.UserId = ?
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array($userId));

        return $row['Total'];
    }

    public static function getAllStoriesByUser($currentUserId, $userId, $page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       S.StoryId,
                       S.Title StoryTitle,
                       S.Description,
                       S.Type StoryType,
                       S.CoverUrl,
                       S.CoverThumbUrl,
                       S.MediaUrl,
                       S.MediaLength,
                       S.ViewCount,
                       S.PlayCount,
                       S.DownloadCount,
                       S.SharedCount,
                       (SELECT COUNT(1) FROM Comment CMT WHERE CMT.StoryId = S.StoryId) CommentCount,
                       (SELECT COUNT(1) FROM StoryLike SL WHERE SL.StoryId = S.StoryId) LikeCount,
                       CASE
                       WHEN EXISTS(SELECT 1
                                   FROM StoryLike SLU
                                   WHERE SLU.UserId = ? AND SLU.StoryId = S.StoryId) THEN 1
                       ELSE 0
                       END IsLiked,
                       S.IsRecommended,
                       S.CreatedDate,
                       S.LastModifiedDate,
                       S.PublishDate,

                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       U.ProfileBackgroundUrl,

                       C.CategoryId,
                       C.Title CategoryTitle,

                       Q.QuestionId,
                       Q.Title QuestionTitle
                   FROM Story S
                   INNER JOIN Users U ON U.UserId = S.UserId
                   LEFT OUTER JOIN Category C ON C.CategoryId = S.CategoryId
                   LEFT OUTER JOIN Question Q ON Q.QuestionId = S.QuestionId
                   WHERE S.UserId = ?
                   ORDER BY S.StoryId DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array($currentUserId, $userId));
    }

    public static function getAllFeedStoriesCountByUser($userId)
    {
        $strSql = "SELECT
                       COUNT(1) `Total`
                   FROM Story S
                   WHERE S.UserId = ? OR S.UserId IN (SELECT F.ReceiverId
                                                      FROM Follow F
                                                      WHERE F.SenderId = ?)
                   LIMIT 1";

        $row = self::$app->conn->fetchAssoc($strSql, array($userId, $userId));

        return $row['Total'];
    }

    public static function getAllFeedStoriesByUser($userId, $page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $strSql = "SELECT
                       S.StoryId,
                       S.Title StoryTitle,
                       S.Description,
                       S.Type StoryType,
                       S.CoverUrl,
                       S.CoverThumbUrl,
                       S.MediaUrl,
                       S.MediaLength,
                       S.ViewCount,
                       S.PlayCount,
                       S.DownloadCount,
                       S.SharedCount,
                       (SELECT COUNT(1) FROM Comment CMT WHERE CMT.StoryId = S.StoryId) CommentCount,
                       (SELECT COUNT(1) FROM StoryLike SL WHERE SL.StoryId = S.StoryId) LikeCount,
                       CASE
                       WHEN EXISTS(SELECT 1
                                   FROM StoryLike SLU
                                   WHERE SLU.UserId = ? AND SLU.StoryId = S.StoryId) THEN 1
                       ELSE 0
                       END IsLiked,
                       S.IsRecommended,
                       S.CreatedDate,
                       S.LastModifiedDate,
                       S.PublishDate,

                       U.UserId,
                       U.Username,
                       U.Email,
                       U.AvatarUrl,
                       U.ProfileBackgroundUrl,

                       C.CategoryId,
                       C.Title CategoryTitle,

                       Q.QuestionId,
                       Q.Title QuestionTitle
                   FROM Story S
                   INNER JOIN Users U ON U.UserId = S.UserId
                   LEFT OUTER JOIN Category C ON C.CategoryId = S.CategoryId
                   LEFT OUTER JOIN Question Q ON Q.QuestionId = S.QuestionId
                   WHERE S.UserId = ? OR S.UserId IN (SELECT F.ReceiverId
                                                      FROM Follow F
                                                      WHERE F.SenderId = ?)
                   ORDER BY S.StoryId DESC
                   LIMIT $offset, $pageSize";

        return self::$app->conn->fetchAll($strSql, array($userId, $userId, $userId));
    }

    public static function findStoryById($id)
    {
        $strSql = 'SELECT
                       S.StoryId,
                       S.UserId,
                       S.ViewCount,
                       S.SharedCount,
                       S.DownloadCount,
                       S.PlayCount
                   FROM Story S
                   WHERE S.StoryId = ?
                   LIMIT 1';

        return self::$app->conn->fetchAssoc($strSql, array($id));
    }

    public static function save($data)
    {
        try {
            if (self::$app->conn->insert('Story', $data)) {
                return self::$app->conn->lastInsertId();
            }
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return false;
    }

    public static function update($storyId, $data)
    {
        try {
            return self::$app->conn->update('Story', $data, array('StoryId' => $storyId));
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }
    }

    public static function remove($storyId)
    {
        try {
            self::$app->conn->delete('Comment', array('StoryId' => $storyId));
            self::$app->conn->delete('StoryLike', array('StoryId' => $storyId));
            self::$app->conn->delete('Story', array('StoryId' => $storyId));
        } catch (\Exception $e) {
            self::$app->log->getWriter()->write($e, \Slim\Log::ERROR);
            return false;
        }

        return true;
    }

}