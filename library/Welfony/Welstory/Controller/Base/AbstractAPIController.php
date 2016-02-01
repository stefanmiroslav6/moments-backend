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

namespace Welfony\Welstory\Controller\Base;

use Welfony\Welstory\Repository\Base\AbstractRepository;

class AbstractAPIController
{

    protected $app;

    public function __construct()
    {
        global $app;

        $this->app = $app;
        AbstractRepository::$app = $app;
    }

    protected function sendResponse($data = array())
    {
        $this->app->response()->body(json_encode($data));
        $this->app->stop();
    }

    protected function getDataFromRequestWithJsonFormat()
    {
        $reqBody = $this->app->request->getBody();
        $arrData = json_decode($reqBody, true);
        if($arrData === null) {
            $this->app->response->setStatus(400);
            $this->sendResponse(array('message' => 'Problems parsing JSON'));
        }

        return $arrData;
    }

    protected function sendInvalidFieldResult($resource, $field, $code)
    {
        $rst = array(
            'message' => 'Validation Failed',
            'errors' => array()
        );
        if ($resource) {
            $rst['errors']['resource'] = $resource;
        }
        if ($field) {
            $rst['errors']['field'] = $field;
        }
        if ($code) {
            $rst['errors']['code'] = $code;
        }

        $this->app->response->setStatus(422);
        $this->sendResponse($rst);
    }

    protected function sendOperationFailedResult($resource)
    {
        $rst = array(
            'message' => 'Operation Failed',
            'errors' => array()
        );
        if ($resource) {
            $rst['errors']['resource'] = $resource;
        }

        $this->app->response->setStatus(500);
        $this->sendResponse($rst);
    }

}