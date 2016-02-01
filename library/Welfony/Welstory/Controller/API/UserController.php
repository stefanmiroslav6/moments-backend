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
use Welfony\Core\Enum\SocialType;
use Welfony\Image\ImageUtility;
use Welfony\Utility\Validation;
use Welfony\Utility\Util;
use Welfony\Welstory\Controller\Base\AbstractAPIController;
use Welfony\Welstory\Repository\SocialRepository;
use Welfony\Welstory\Repository\UserRepository;

class UserController extends AbstractAPIController
{

    public function index()
    {
        $page = intval($this->app->request->get('page'));
        $page = $page <= 0 ? 1 : $page;
        $pageSize = intval($this->app->request->get('pageSize'));
        $pageSize = $pageSize <= 0 ? 20 : $pageSize;

        $totalUsersCount = UserRepository::getAllUsersCount();
        $users = UserRepository::getAllUsers($page, $pageSize);

        $this->sendResponse(array('total' => $totalUsersCount, 'users' => $users));
    }

    public function getUserDetail($userId)
    {
        $userDetail = UserRepository::findUserDetailById($userId);
        if (!$userDetail) {
            $this->sendResponse(array('success' => false, 'message' => 'User is not existed!'));
        }

        $this->sendResponse($userDetail);
    }

    public function signInWithEmail()
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();

        $usernameOrEmail = htmlspecialchars($reqData['Email']);
        $password = $reqData['Password'];

        $isUserValid = false;
        $msg = '';

        $user = UserRepository::findUserByUsernameOrEmail($usernameOrEmail);
        if ($user) {
            if ($this->app->passHash->checkPassword($password, $user['Password'])) {
                $isUserValid = true;
            } else {
                $msg = 'Invalid Email or Password!';
            }
        } else {
            $msg = 'User is not found!';
        }

        $rst = array('success' => $isUserValid);
        if (!$isUserValid) {
            $rst['message'] = $msg;
        } else {
            $rst['user'] = UserRepository::findUserById($user['UserId']);
        }

        $this->sendResponse($rst);
    }

    public function signUpWithEmail()
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();

        if (!isset($reqData['Email']) || !Validation::isValidEmail($reqData['Email'])) {
            $this->sendResponse(array('success' => false, 'message' => 'Invalid email address!'));
        }
        $email = $reqData['Email'];

        $username = $email;

        if (!isset($reqData['Password']) || !Validation::isValidPassword($reqData['Password'])) {
            $this->sendResponse(array('success' => false, 'message' => 'Invalid password!'));
        }
        $password = $reqData['Password'];

        $user = UserRepository::findUserByUsernameOrEmail($username);
        if ($user) {
            $this->sendResponse(array('success' => false, 'message' => 'This user name has already been taken!'));
        }
        $user = UserRepository::findUserByUsernameOrEmail($email);
        if ($user) {
            $this->sendResponse(array('success' => false, 'message' => 'This email address has already been taken!'));
        }

        $user = array(
            'Username' => $username,
            'Email' => $email,
            'Password' => $this->app->passHash->hashPassword($password),
            'CreatedDate' => date('Y-m-d H:i:s')
        );

        $newId = UserRepository::save($user);
        if ($newId) {
            $user['UserId'] = $newId;
            $this->sendResponse(array('success' => true, 'user' => $user));
        } else {
            $this->sendOperationFailedResult('User');
        }
    }

    public function signInWithSocial()
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();

        if (!isset($reqData['SocialId']) || empty($reqData['SocialId'])) {
            $this->sendInvalidFieldResult('Social', 'SocialId', 'missing_field');
        }

        if (!isset($reqData['Type']) || intval($reqData['Type']) <= 0) {
            $this->sendInvalidFieldResult('Social', 'Type', 'missing_field');
        }

        $socialData = array(
            'ExternalId' => htmlspecialchars($reqData['SocialId']),
            'Type' => intval($reqData['Type']),
            'Email' => isset($reqData['Email']) ? htmlspecialchars($reqData['Email']) : '',
            'DisplayName' => isset($reqData['DisplayName']) ? htmlspecialchars($reqData['DisplayName']) : '',
            'Firstname' => isset($reqData['Firstname']) ? htmlspecialchars($reqData['Firstname']) : '',
            'Lastname' => isset($reqData['Lastname']) ? htmlspecialchars($reqData['Lastname']) : '',
            'ProfileUrl' => isset($reqData['ProfileURL']) ? htmlspecialchars($reqData['ProfileURL']) : '',
            'PhotoUrl' => isset($reqData['PhotoURL']) ? htmlspecialchars($reqData['PhotoURL']) : '',
            'WebsiteUrl' => isset($reqData['WebsiteURL']) ? htmlspecialchars($reqData['WebsiteURL']) : ''
        );

        $user = UserRepository::findUserBySocialExternalId($socialData['ExternalId'], $socialData['Type']);
        if (!$user) {
            $email = Util::genRandomEmail();
            $emailArr = explode('@', empty($socialData['Email']) ? $email : $socialData['Email']);
            $username = $emailArr[0];
            $password = Util::genRandomPassword();

            $user = UserRepository::findUserByUsernameOrEmail($username);
            if ($user) {
                $email = Util::genRandomEmail($username);
                $emailArr = explode('@', $email);
                $username = $emailArr[0];
            }

            $userData = array(
                'Username' => $username,
                'Email' => $email,
                'Password' => $this->app->passHash->hashPassword($password),
                'CreatedDate' => date('Y-m-d H:i:s')
            );

            $success = false;
            $this->app->conn->beginTransaction();

            $newUserId = UserRepository::save($userData);
            if ($newUserId) {
                $socialData['UserId'] = $newUserId;
                $socialData['CreatedDate'] = date('Y-m-d H:i:s');

                $newSocialId = SocialRepository::save($socialData);
                if ($newSocialId) {
                    $success = true;
                    $this->app->conn->commit();
                } else {
                    $this->app->conn->rollback();
                    $this->sendOperationFailedResult('Social');
                }
            } else {
                $this->sendOperationFailedResult('User');
            }

            if (!empty($socialData['PhotoUrl']) && Validation::isValidUrl($socialData['PhotoUrl'])) {
                $arrUrlSegment = parse_url($socialData['PhotoUrl']);
                $temp = explode('.', $arrUrlSegment['path']);
                $extension = end($temp);
                $hashFileName = base64_encode(date('Ymdhis') . uniqid());

                $avatarFolder = $this->app->config['app']['avatar_path'] . DS . $newUserId;
                $fs = new Filesystem();
                try {
                    $fs->mkdir($avatarFolder);
                } catch (IOExceptionInterface $e) {
                    $this->app->log->getWriter()->write('An error occurred while creating your directory at '. $e->getPath(), \Slim\Log::ERROR);
                }

                if ($fs->exists($avatarFolder)) {
                    $fileTargetPath = implode(DS, array($this->app->config['app']['avatar_path'], $newUserId, $hashFileName . '.' . $extension));

                    $rstCurlAvatar = ImageUtility::getImageFromUrl($socialData['PhotoUrl']);
                    if ($rstCurlAvatar && ImageUtility::getImageFileType($rstCurlAvatar)) {
                        file_put_contents($fileTargetPath, $rstCurlAvatar);

                        $imagine = new Imagine();
                        $image = $imagine->open($fileTargetPath);
                        $image->thumbnail(new Box(180, 180))->save($fileTargetPath);

                        $data = array(
                            'AvatarUrl' => $this->app->config['app']['asset_base_url'] . implode('/', array('avatar', $newUserId, $hashFileName . '.' . $extension)),
                            'LastModifiedDate' => date('Y-m-d H:i:s')
                        );
                        UserRepository::update($newUserId, $data);
                    }
                }
            }

            $user = UserRepository::findUserById($newUserId);
        } else {
            $socialData['LastModifiedDate'] = date('Y-m-d H:i:s');
            $newSocialId = SocialRepository::update($socialData['ExternalId'], $socialData['Type'], $socialData);
        }

        $this->sendResponse(array('user' => $user));
    }

    public function updateProfile($userId)
    {
        $reqData = $this->getDataFromRequestWithJsonFormat();

        if (!isset($reqData['Username']) || empty($reqData['Username'])) {
            $this->sendInvalidFieldResult('User', 'Username', 'missing_field');
        }

        $username = $reqData['Username'];

        $user = UserRepository::findUserById($userId);
        if (!$user) {
            $this->sendOperationFailedResult('User');
        }

        $existedUser = UserRepository::findUserByUsernameOrEmail($username);
        if ($existedUser && $existedUser['UserId'] != $user['UserId']) {
            $this->sendResponse(array('success' => false, 'message' => 'Username is taked by others'));
        }

        $updateUser = array(
            'UserId' => $userId,
            'Username' => $username,
            'LastModifiedDate' => date('Y-m-d H:i:s')
        );

        if (UserRepository::update($userId, $updateUser)) {
            $this->sendResponse(array('success' => true));
        } else {
            $this->sendOperationFailedResult('User');
        }
    }

    public function changeAvatar($userId)
    {
        $user = UserRepository::findUserById($userId);
        if (!$user) {
            $this->sendResponse(array('success' => false, 'message' => 'User is not existing!'));
        }

        if (empty($_FILES)) {
            $this->sendResponse(array('success' => false, 'message' => 'No file found!'));
        }

        $file = $_FILES["avatar"];
        if ($file['error'] > 0) {
            $this->sendResponse(array('success' => false, 'message' => $file['error']));
        }

        $fileName = $file['name'];
        $hashFileName = base64_encode(date('Ymdhis') . uniqid());

        $temp = explode('.', $file['name']);
        $extension = end($temp);

        if (!Validation::isValidImage($file)) {
            $this->sendResponse(array('success' => false, 'message' => 'File type is invalid!'));
        }

        $avatarFolder = $this->app->config['app']['avatar_path'] . DS . $userId;
        $fs = new Filesystem();
        try {
            $fs->mkdir($avatarFolder);
        } catch (IOExceptionInterface $e) {
            $this->app->log->getWriter()->write('An error occurred while creating your directory at '. $e->getPath(), \Slim\Log::ERROR);
        }

        if ($fs->exists($avatarFolder)) {
            $fileTmpPath = $file['tmp_name'];
            $fileTargetPath = implode(DS, array($this->app->config['app']['avatar_path'], $userId, $hashFileName . '.' . $extension));
            move_uploaded_file($fileTmpPath, $fileTargetPath);

            $imagine = new Imagine();
            $image = $imagine->open($fileTargetPath);
            $image->thumbnail(new Box(180, 180))->save($fileTargetPath);

            $data = array(
                'AvatarUrl' => implode('/', array($this->app->config['app']['asset_base_url'], 'avatar', $userId, $hashFileName . '.' . $extension)),
                'LastModifiedDate' => date('Y-m-d H:i:s')
            );

            $success = UserRepository::update($user['UserId'], $data);
            if (!$success) {
                $this->sendResponse(array('success' => false, 'message' => 'Operation failed!'));
            }

            $this->sendResponse(array('success' => true, 'url' => $data['AvatarUrl']));
        } else {
            $this->sendResponse(array('success' => false, 'message' => 'Operation failed!'));
        }
    }

}