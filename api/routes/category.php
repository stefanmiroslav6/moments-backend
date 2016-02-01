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

use Welfony\Welstory\Controller\API\CategoryController;

$app->get('/categories', function() use($app)
{
    $ctrl = new CategoryController();
    $ctrl->listAllCategories();
});

$app->get('/categories/:categoryId/questions', function($categoryId) use($app)
{
    $ctrl = new CategoryController();
    $ctrl->listAllQuestionsInCategory($categoryId);
})->conditions(array('categoryId' => '\d{1,10}'));