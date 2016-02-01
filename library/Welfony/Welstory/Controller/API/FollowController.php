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
use Welfony\Welstory\Core\Enum\ActivityType;
use Welfony\Welstory\Core\Enum\FollowStatus;
use Welfony\Welstory\Repository\ActivityRepository;
use Welfony\Welstory\Repository\FollowRepository;

class FollowController extends AbstractAPIController
{

    public function getFollowerByUserAndSender($userId, $senderId)
    {
        $follow = FollowRepository::getFollowerByUserAndSender($userId, $senderId);
        $this->sendResponse(array('followed' => $follow ? true : false));
    }

    public function getAllFollowersByUser($userId)
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $currentUserId = intval($this->app->request->get('currentUser'));

        $totalStoriesCount = FollowRepository::getAllFollowersCountByUser($userId);
        $users = FollowRepository::getAllFollowersByUser($userId, $page, $pageSize, $currentUserId);

        $this->sendResponse(array('total' => $totalStoriesCount, 'users' => $users));
    }

    public function getAllFollowingsByUser($userId)
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $currentUserId = intval($this->app->request->get('currentUser'));

        $totalStoriesCount = FollowRepository::getAllFollowingsCountByUser($userId);
        $users = FollowRepository::getAllFollowingsByUser($userId, $page, $pageSize, $currentUserId);

        $this->sendResponse(array('total' => $totalStoriesCount, 'users' => $users));
    }

    public function followUser($userId)
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();

        if (!isset($reqData['ReceiverId']) || intval($reqData['ReceiverId']) <= 0) {
            $this->sendResponse(array('success' => false, 'message' => 'Invalid receiver!'));
        }
        $receiverId = intval($reqData['ReceiverId']);

        $follow = array(
            'SenderId' => $userId,
            'ReceiverId' => $receiverId,
            'CreatedDate' => date('Y-m-d H:i:s')
        );

        $newId = FollowRepository::save($follow);
        if ($newId) {
            $activity = array(
                'SenderId' => $userId,
                'ReceiverId' => $receiverId,
                'Type' => ActivityType::Follow,
                'CreatedDate' => date('Y-m-d H:i:s')
            );

            ActivityRepository::save($activity);

            $follow['FollowId'] = $newId;
            $this->sendResponse(array('success' => true, 'follow' => $follow));
        } else {
            $this->sendOperationFailedResult('Follow');
        }
    }

    public function unfollowUser($userId)
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();

        if (!isset($reqData['ReceiverId']) || intval($reqData['ReceiverId']) <= 0) {
            $this->sendResponse(array('success' => false, 'message' => 'Invalid receiver!'));
        }
        $receiverId = intval($reqData['ReceiverId']);

        $success = FollowRepository::removeBySenderAndReceiver($userId, $receiverId);
        if ($success) {
            $this->sendResponse(array('success' => true));
        } else {
            $this->sendOperationFailedResult('Follow');
        }
    }

}