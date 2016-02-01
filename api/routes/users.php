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

use Welfony\Welstory\Controller\API\ActivityController;
use Welfony\Welstory\Controller\API\FollowController;
use Welfony\Welstory\Controller\API\UserController;

$app->get('/users/:userId', function($userId) use($app)
{
    $ctrl = new UserController();
    $ctrl->getUserDetail($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->get('/users', function() use($app)
{
    $ctrl = new UserController();
    $ctrl->index();
});

$app->post('/users/signup', function() use($app)
{
    $ctrl = new UserController();
    $ctrl->signUpWithEmail();
});

$app->post('/users/signin', function() use($app)
{
    $ctrl = new UserController();
    $ctrl->signInWithEmail();
});

$app->post('/users/:userId/avatar', function($userId) use($app)
{
    $ctrl = new UserController();
    $ctrl->changeAvatar($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->put('/users/:userId', function($userId) use($app)
{
    $ctrl = new UserController();
    $ctrl->updateProfile($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->get('/users/:userId/follows/sender/:senderId', function($userId, $senderId) use($app)
{
    $ctrl = new FollowController();
    $ctrl->getFollowerByUserAndSender($userId, $senderId);
})->conditions(array('userId' => '\d{1,10}', 'senderId' => '\d{1,10}'));

$app->get('/users/:userId/followers', function($userId) use($app)
{
    $ctrl = new FollowController();
    $ctrl->getAllFollowersByUser($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->get('/users/:userId/followings', function($userId) use($app)
{
    $ctrl = new FollowController();
    $ctrl->getAllFollowingsByUser($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->post('/users/:userId/follows', function($userId) use($app)
{
    $ctrl = new FollowController();
    $ctrl->followUser($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->put('/users/:userId/follows', function($userId) use($app)
{
    $ctrl = new FollowController();
    $ctrl->unfollowUser($userId);
})->conditions(array('userId' => '\d{1,10}'));

$app->get('/users/:userId/activities', function($userId) use($app)
{
    $ctrl = new ActivityController();
    $ctrl->listActivitiesByUser($userId);
})->conditions(array('userId' => '\d{1,10}'));