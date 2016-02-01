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

namespace Welfony\Welstory\Controller\API;

use Welfony\Welstory\Controller\Base\AbstractAPIController;

class ErrorController extends AbstractAPIController
{

    public function notFound()
    {
        $this->sendResponse(array(
            'message' => 'Not Found'
        ));
    }

    public function error($e)
    {
        $this->app->log->getWriter()->write($e, \Slim\Log::ERROR);
        $this->sendResponse(array(
            'message' => 'Internal Server Error'
        ));
    }

}