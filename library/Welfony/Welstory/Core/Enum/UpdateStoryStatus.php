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

namespace Welfony\Welstory\Core\Enum;

use Welfony\Core\Enum;

class UpdateStoryStatus extends Enum
{

    const Report = 1;
    const View = 2;
    const Download = 3;
    const Play = 4;
    const Share = 5;

}