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

use Welfony\Welstory\Controller\API\ErrorController;
use Welfony\Welstory\Controller\API\IndexController;

$app->hook('slim.before.router', function() use($app)
{
    $app->response()->header('Content-Type', 'application/json');
});

$app->get('/', function() use($app)
{
    $controller = new IndexController();
    $controller->index();
});

$app->notFound(function() use($app)
{
    $ctrl = new ErrorController();
    $ctrl->notFound();
});

$app->error(function(\Exception $e) use($app)
{
    $ctrl = new ErrorController();
    $ctrl->error($e);
});