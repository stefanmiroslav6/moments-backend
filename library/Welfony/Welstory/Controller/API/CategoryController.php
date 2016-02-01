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
use Welfony\Welstory\Repository\CategoryRepository;

class CategoryController extends AbstractAPIController
{

    public function listAllCategories()
    {
        $this->sendResponse(CategoryRepository::getAllCategories());
    }

    public function listAllQuestionsInCategory($categoryId)
    {
        $this->sendResponse(CategoryRepository::getAllQuestionsByCategory($categoryId));
    }

}