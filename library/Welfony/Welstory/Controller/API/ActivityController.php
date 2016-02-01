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
use Welfony\Welstory\Repository\ActivityRepository;

class ActivityController extends AbstractAPIController
{

    public function listActivitiesByUser($userId)
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 10 : $pageSize;

        $totalActivitiesCount = ActivityRepository::getAllActivitiesCountByUser($userId);

        $tempRst = ActivityRepository::getAllActivitiesByUser($userId, $page, $pageSize);
        $activities = $this->assembleActivityList($tempRst);

        $this->sendResponse(array('total' => $totalActivitiesCount, 'activities' => $activities));
    }

    private function assembleActivityList($tempRst)
    {
        $activities = array();
        foreach ($tempRst as $row) {
            $activity = array(
                'ActivityId' => $row['ActivityId'],
                'Body' => htmlspecialchars_decode($row['Body']),
                'CreatedDate' => $row['CreatedDate'],
                'User' => array(
                    'UserId' => $row['UserId'],
                    'Username' => $row['Username'],
                    'Email' => $row['Email'],
                    'AvatarUrl' => $row['AvatarUrl'],
                    'ProfileBackgroundUrl' => $row['ProfileBackgroundUrl']
                )
            );

            $activities[] = $activity;
        }

        return $activities;
    }

}