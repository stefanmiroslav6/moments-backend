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

class IndexController extends AbstractAPIController
{

    public function index()
    {
        $this->sendResponse(array(
            'message' => sprintf('%s API service', $this->app->config['app']['name'])
        ));
    }

}