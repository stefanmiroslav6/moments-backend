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

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Welfony\Utility\Validation;
use Welfony\Welstory\Controller\Base\AbstractAPIController;
use Welfony\Welstory\Core\Enum\ActivityType;
use Welfony\Welstory\Core\Enum\StoryType;
use Welfony\Welstory\Core\Enum\UpdateStoryStatus;
use Welfony\Welstory\Repository\ActivityRepository;
use Welfony\Welstory\Repository\StoryRepository;
use Welfony\Welstory\Repository\StoryLikeRepository;

class StoryController extends AbstractAPIController
{

    public function getStoriesByCategory($categoryId)
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $totalStoriesCount = StoryRepository::getAllStoriesCountByCategory($categoryId);

        $currentUserId = intval($this->app->request->get('currentUser'));

        $tempRst = StoryRepository::getAllStoriesByCategory($currentUserId, $categoryId, $page, $pageSize);
        $stories = $this->assembleStoryList($tempRst);

        $this->sendResponse(array('total' => $totalStoriesCount, 'stories' => $stories));
    }

    public function getFeaturedStories()
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $totalStoriesCount = StoryRepository::getAllFeaturedStoriesCount();

        $tempRst = StoryRepository::getAllFeaturedStories($page, $pageSize);
        $stories = $this->assembleStoryList($tempRst);

        $this->sendResponse(array('total' => $totalStoriesCount, 'stories' => $stories));
    }

    public function getStoriesByUser($userId)
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $totalStoriesCount = StoryRepository::getAllStoriesCountByUser($userId);

        $currentUserId = intval($this->app->request->get('currentUser'));
        $tempRst = StoryRepository::getAllStoriesByUser($currentUserId, $userId, $page, $pageSize);
        $stories = $this->assembleStoryList($tempRst);

        $this->sendResponse(array('total' => $totalStoriesCount, 'stories' => $stories));
    }

    public function getLikedStoriesByUser($userId)
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $totalStoriesCount = StoryRepository::getAllLikedStoriesCountByUser($userId);

        $currentUserId = intval($this->app->request->get('currentUser'));
        $tempRst = StoryRepository::getAllLikedStoriesByUser($currentUserId, $userId, $page, $pageSize);
        $stories = $this->assembleStoryList($tempRst);

        $this->sendResponse(array('total' => $totalStoriesCount, 'stories' => $stories));
    }

    public function getFeedStoriesByUser($userId)
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $totalStoriesCount = StoryRepository::getAllFeedStoriesCountByUser($userId);

        $tempRst = StoryRepository::getAllFeedStoriesByUser($userId, $page, $pageSize);
        $stories = $this->assembleStoryList($tempRst);

        $this->sendResponse(array('total' => $totalStoriesCount, 'stories' => $stories));
    }

    public function addStoryLike($userId)
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();
        if (!isset($reqData['StoryId']) || intval($reqData['StoryId']) <= 0) {
            $this->sendInvalidFieldResult('StoryLike', 'StoryId', 'missing_field');
        }

        $storyId = intval($reqData['StoryId']);
        $story = StoryRepository::findStoryById($storyId);
        if (!$story) {
            $this->sendInvalidFieldResult('StoryLike', 'StoryId', 'missing_field');
        }

        $isLike = intval($reqData['IsLike']);
        if ($isLike > 0) {
            $storyLike = StoryLikeRepository::findByUserAndStoryId($userId, $storyId);
            if ($storyLike) {
                $this->sendResponse(array('success' => true));
            }

            $storyLikeData = array(
                'UserId' => $userId,
                'StoryId' => $storyId,
                'CreatedDate' => date('Y-m-d H:i:s')
            );

            $newId = StoryLikeRepository::save($storyLikeData);
            if ($newId) {
                $activity = array(
                    'SenderId' => $userId,
                    'ReceiverId' => $story['UserId'],
                    'Type' => ActivityType::LikeStory,
                    'StoryId' => $storyId,
                    'CreatedDate' => date('Y-m-d H:i:s')
                );

                ActivityRepository::save($activity);

                $storyLikeData['StoryLikeId'] = $newId;
                $this->sendResponse(array('success' => true));
            } else {
                $this->sendOperationFailedResult('StoryLike');
            }
        } else {
            $this->sendResponse(array('success' => StoryLikeRepository::remove($userId, $storyId)));
        }
    }

    public function addStory()
    {
        $title = $this->app->request->post('Title');
        if (!$title) {
            $this->sendInvalidFieldResult('Story', 'Title', 'missing_field');
        }

        $userId = intval($this->app->request->post('UserId'));
        if ($userId <= 0) {
            $this->sendInvalidFieldResult('Story', 'UserId', 'missing_field');
        }

        $type = intval($this->app->request->post('Type'));
        if ($type <= 0) {
            $this->sendInvalidFieldResult('Story', 'Type', 'missing_field');
        }

        $storyData = array(
            'Title' => $title,
            'Description' => $this->app->request->post('Description') ? : '',
            'Type' => $type,
            'UserId' => $userId,
            'CategoryId' => $this->app->request->post('CategoryId'),
            'QuestionId' => $this->app->request->post('QuestionId'),
            'CreatedDate' => date('Y-m-d H:i:s'),
            'LastModifiedDate' => date('Y-m-d H:i:s'),
            'PublishDate' => date('Y-m-d H:i:s')
        );

        $coverData = $this->processUploadedFile('cover', 'image');
        $storyData['CoverUrl'] = $coverData['Url'];
        $storyData['CoverThumbUrl'] = $coverData['ThumbUrl'];

        switch ($type) {
            case StoryType::Video: {
                $mediaData = $this->processUploadedFile('media', 'video');
                $storyData['MediaUrl'] = $mediaData['Url'];
                $storyData['MediaLength'] = floatval($this->app->request->post('MediaLength'));
                break;
            }
            case StoryType::Audio: {
                $mediaData = $this->processUploadedFile('media', 'audio');
                $storyData['MediaUrl'] = $mediaData['Url'];
                $storyData['MediaLength'] = floatval($this->app->request->post('MediaLength'));
                break;
            }
            case StoryType::Text: {
                if (empty($storyData['Description'])) {
                    $this->sendInvalidFieldResult('Story', 'Description', 'missing_field');
                }
                break;
            }
        }

        $newStoryId = StoryRepository::save($storyData);
        if ($newStoryId) {
            $storyData['StoryId'] = $newStoryId;
            $this->sendResponse(array('story' => $storyData));
        } else {
            $this->sendOperationFailedResult('Story');
        }
    }

    public function updateStory($storyId)
    {

    }

    public function updateStoryStatus($storyId)
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();

        if (!isset($reqData['UserId']) || intval($reqData['UserId']) <= 0) {
            $this->sendResponse(array('success' => false, 'message' => 'Invalid user!'));
        }
        $userId = intval($reqData['UserId']);

        if (!isset($reqData['Status']) || intval($reqData['Status']) <= 0) {
            $this->sendResponse(array('success' => false, 'message' => 'Invalid status!'));
        }
        $status = intval($reqData['Status']);

        $story = StoryRepository::findStoryById($storyId);

        if ($status == UpdateStoryStatus::Report) {

        }

        if ($status == UpdateStoryStatus::View) {
            $story['ViewCount'] += 1;
            StoryRepository::update($story['StoryId'], $story);
        }

        if ($status == UpdateStoryStatus::Play) {
            $story['PlayCount'] += 1;
            StoryRepository::update($story['StoryId'], $story);
        }

        if ($status == UpdateStoryStatus::Share) {
            $story['SharedCount'] += 1;
            StoryRepository::update($story['StoryId'], $story);
        }

        $this->sendResponse(array('success' => true));
    }

    public function removeStory($storyId)
    {
        $success = StoryRepository::remove($storyId);
        if ($success) {
            $this->sendResponse(array('success' => true));
        } else {
            $this->sendOperationFailedResult('Story');
        }
    }

    private function processUploadedFile($fileInputName, $type)
    {
        if (empty($_FILES) || !isset($_FILES[$fileInputName])) {
            self::sendResponse(array('success' => false, 'message' => 'No file found!'));
        }

        $file = $_FILES[$fileInputName];
        if ($file['error'] > 0) {
            self::sendResponse(array('success' => false, 'message' => $file['error']));
        }

        if ($type == 'image' && !Validation::isValidImage($file) ||
            $type == 'video' && !Validation::isValidVideo($file) ||
            $type == 'audio' && !Validation::isValidAudio($file)) {
            self::sendResponse(array('success' => false, 'message' => 'File type is invalid!'));
        }

        $temp = explode('.', $file['name']);

        $targetFolder = implode(DS, array($this->app->config['app']['media_path'], date('Y'), date('m'), date('d')));
        $fs = new Filesystem();
        try {
            $fs->mkdir($targetFolder);
        } catch (IOExceptionInterface $e) {
            $this->app->log->getWriter()->write('An error occurred while creating your directory at '. $e->getPath(), \Slim\Log::ERROR);
        }

        if (!$fs->exists($targetFolder)) {
            $this->sendOperationFailedResult('File');
        }

        $hashFileName = base64_encode(date('Ymdhis') . uniqid());
        $rtn = array(
            'Extention' => end($temp),
            'RawFileName' => $file['name'],
            'HashFileName' => $hashFileName,
            'FileTmpPath' => $file['tmp_name'],
            'Url' => implode('/', array($this->app->config['app']['asset_base_url'], 'media', date('Y'), date('m'), date('d'), $hashFileName . '.' . end($temp)))
        );

        $fileTargetPath = implode(DS, array($this->app->config['app']['media_path'], date('Y'), date('m'), date('d'), $rtn['HashFileName'] . '.' . $rtn['Extention']));
        move_uploaded_file($rtn['FileTmpPath'], $fileTargetPath);

        if ($type == 'image') {
            $imagine = new Imagine();
            $image = $imagine->open($fileTargetPath);
            $image->thumbnail(new Box(180, 180))->save(str_replace('.' . $rtn['Extention'], '_180x180.' . $rtn['Extention'], $fileTargetPath));

            $rtn['ThumbUrl'] = implode('/', array($this->app->config['app']['asset_base_url'], 'media', date('Y'), date('m'), date('d'), $rtn['HashFileName'] . '_180x180.' . $rtn['Extention']));
        }

        return $rtn;
    }

    private function assembleStoryList($tempRst)
    {
        $stories = array();
        foreach ($tempRst as $row) {
            $story = array(
                'StoryId' => $row['StoryId'],
                'Title' => $row['StoryTitle'],
                'Description' => $row['Description'],
                'Type' => $row['StoryType'],
                'CoverUrl' => $row['CoverUrl'],
                'CoverThumbUrl' => $row['CoverThumbUrl'],
                'MediaUrl' => $row['MediaUrl'],
                'MediaLength' => $row['MediaLength'],
                'ViewCount' => $row['ViewCount'],
                'PlayCount' => $row['PlayCount'],
                'DownloadCount' => $row['DownloadCount'],
                'SharedCount' => $row['SharedCount'],
                'CommentCount' => $row['CommentCount'],
                'LikeCount' => $row['LikeCount'],
                'IsLiked' => $row['IsLiked'],
                'IsRecommended' => $row['IsRecommended'],
                'CreatedDate' => $row['CreatedDate'],
                'LastModifiedDate' => $row['LastModifiedDate'],
                'PublishDate' => $row['PublishDate'],
                'User' => array(
                    'UserId' => $row['UserId'],
                    'Username' => $row['Username'],
                    'Email' => $row['Email'],
                    'AvatarUrl' => $row['AvatarUrl'],
                    'ProfileBackgroundUrl' => $row['ProfileBackgroundUrl']
                )
            );

            if (intval($row['CategoryId']) > 0) {
                $story['Category'] = array(
                    'CategoryId' => $row['CategoryId'],
                    'Title' => $row['CategoryTitle']
                );
            }

            if (intval($row['QuestionId']) > 0) {
                $story['Question'] = array(
                    'QuestionId' => $row['QuestionId'],
                    'Title' => $row['QuestionTitle']
                );
            }

            $stories[] = $story;
        }

        return $stories;
    }

}