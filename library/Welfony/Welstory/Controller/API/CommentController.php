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
use Welfony\Welstory\Repository\ActivityRepository;
use Welfony\Welstory\Repository\CommentRepository;
use Welfony\Welstory\Repository\StoryRepository;

class CommentController extends AbstractAPIController
{

    public function listCommentsByStory($storyId)
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $totalCommentsCount = CommentRepository::getAllCommentCountByStory($storyId);

        $tempRst = CommentRepository::getAllCommentsByStory($storyId, $page, $pageSize);
        $comments = $this->assembleCommentList($tempRst);

        $this->sendResponse(array('total' => $totalCommentsCount, 'comments' => $comments));
    }

    public function add($storyId)
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();

        if (!isset($reqData['UserId']) || intval($reqData['UserId']) <= 0) {
            $this->sendResponse(array('success' => false, 'message' => 'Invalid user!'));
        }
        $userId = intval($reqData['UserId']);

        if (!isset($reqData['Body']) || empty($reqData['Body'])) {
            $this->sendResponse(array('success' => false, 'message' => 'Invalid comment content!'));
        }
        $body = htmlspecialchars($reqData['Body']);

        $comment = array(
            'UserId' => $userId,
            'StoryId' => $storyId,
            'Deep' => 0,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'Body' => $body
        );

        if (isset($reqData['ParentId'])) {
            $comment['ParentId'] = intval($reqData['ParentId']);
            $parent = CommentRepository::findCommentById($comment['ParentId']);
            if ($parent) {
                $comment['Deep'] = $parent['Deep'] + 1;
            }
        }

        $newId = CommentRepository::save($comment);
        if ($newId) {
            $story = StoryRepository::findStoryById($storyId);
            $activity = array(
                'SenderId' => $userId,
                'ReceiverId' => $story['UserId'],
                'Type' => ActivityType::CommentStory,
                'StoryId' => $storyId,
                'CommentId' => $newId,
                'CreatedDate' => date('Y-m-d H:i:s')
            );

            ActivityRepository::save($activity);

            $comment['CommentId'] = $newId;
            $this->sendResponse(array('success' => true, 'comment' => $comment));
        } else {
            $this->sendOperationFailedResult('Comment');
        }
    }

    private function assembleCommentList($tempRst)
    {
        $comments = array();
        foreach ($tempRst as $row) {
            $comment = array(
                'CommentId' => $row['CommentId'],
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

            $comments[] = $comment;
        }

        return $comments;
    }

}