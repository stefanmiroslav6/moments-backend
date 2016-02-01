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

class CategoryRepository extends AbstractRepository
{

    public static function getAllCategories()
    {
        $strSql = "SELECT
                       C.CategoryId,
                       C.Title,
                       C.Description
                   FROM Category C
                   ORDER BY C.CategoryId ASC";

        return self::$app->conn->fetchAll($strSql);
    }

    public static function getAllQuestionsByCategory($categoryId)
    {
        $strSql = "SELECT
                       Q.QuestionId,
                       Q.Title,
                       Q.Description
                   FROM Question Q
                   WHERE Q.CategoryId = ?
                   ORDER BY Q.QuestionId ASC";

        return self::$app->conn->fetchAll($strSql, array($categoryId));
    }

}