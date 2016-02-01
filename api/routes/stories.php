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

use Welfony\Welstory\Controller\API\StoryController;
use Welfony\Welstory\Controller\API\CommentController;

$app->get('/stories/featured', function() use($app)
{
    $ctrl = new StoryController();
    $ctrl->getFeaturedStories();
});

$app->get('/stories/category/:categoryId', function($categoryId) use($app)
{
    $ctrl = new StoryController();
    $ctrl->getStoriesByCategory($categoryId);
});

$app->post('/stories', function() use($app)
{
    $ctrl = new StoryController();
    $ctrl->addStory();
});

$app->put('/stories/:storyId', function($storyId) use($app)
{
    $ctrl = new StoryController();
    $ctrl->updateStory($storyId);
})->conditions(array('storyId' => '\d{1,10}'));

$app->put('/stories/:storyId/status', function($storyId) use($app)
{
    $ctrl = new StoryController();
    $ctrl->updateStoryStatus($storyId);
})->conditions(array('storyId' => '\d{1,10}'));

$app->get('/stories/:storyId/remove', function($storyId) use($app)
{
    $ctrl = new StoryController();
    $ctrl->removeStory($storyId);
})->conditions(array('storyId' => '\d{1,10}'));

$app->get('/users/:userId/stories', function($userId) use($app)
{
    $ctrl = new StoryController();
    $ctrl->getStoriesByUser($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->get('/users/:userId/feeds', function($userId) use($app)
{
    $ctrl = new StoryController();
    $ctrl->getFeedStoriesByUser($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->get('/users/:userId/liked', function($userId) use($app)
{
    $ctrl = new StoryController();
    $ctrl->getLikedStoriesByUser($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->post('/users/:userId/liked', function($userId) use($app)
{
    $ctrl = new StoryController();
    $ctrl->addStoryLike($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->get('/stories/:storyId/comments', function($storyId) use($app)
{
    $ctrl = new CommentController();
    $ctrl->listCommentsByStory($storyId);
})->conditions(array('storyId' => '\d{1,10}'));

$app->post('/stories/:storyId/comments', function($storyId) use($app)
{
    $ctrl = new CommentController();
    $ctrl->add($storyId);
})->conditions(array('storyId' => '\d{1,10}'));