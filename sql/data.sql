-- ==============================================================================
--
--  This file is part of the WelStory.
--
--  Copyright (c) 2012-2014 welfony.com
--
--  For the full copyright and license information, please view the LICENSE
--  file that was distributed with this source code.
--
-- ==============================================================================

USE welstory;

DELETE FROM `Activity`;
DELETE FROM `Comment`;
DELETE FROM `Follow`;
DELETE FROM `StoryLike`;
DELETE FROM `Story`;
DELETE FROM `Social`;
DELETE FROM `Users`;
DELETE FROM `Question`;
DELETE FROM `Category`;

INSERT INTO `Users` (`UserId`, `Username`, `Email`, `Password`, `AvatarUrl`, `CreatedDate`, `LastModifiedDate`) VALUES
(1, 'System', 'admin@welstory.com', '$pbkdf2-sha512$15000$SIqEsjtkvTqOWX.0l/VbLg$2BwoHPeGoBsY.MZVg/xoqwyPXE8xJ/vP1DlGizKGVvTUFLPuuMmniACDrDNeU9cw0/JzbraJm8QqLjNYlI7gGQ', 'http://2.gravatar.com/avatar/90b84f2d8db07fdcd507919ed50ee589?s=180', '2014-01-01', '2014-01-01');

INSERT INTO `Category` (`CategoryId`, `Title`, `Description`, `Sort`) VALUES
(1, 'Cats', 'Cats description', 1);
INSERT INTO `Category` (`CategoryId`, `Title`, `Description`, `Sort`) VALUES
(2, 'Dogs', 'Dogs description', 2);

INSERT INTO `Question` (`QuestionId`, `Title`, `Description`, `CategoryId`, `CreatedDate`, `LastModifiedDate`) VALUES
(1, 'What is your favourite food?', 'Question description 1', 1, '2014-01-01', '2014-01-01');
INSERT INTO `Question` (`QuestionId`, `Title`, `Description`, `CategoryId`, `CreatedDate`, `LastModifiedDate`) VALUES
(2, 'What is your favourite color?', 'Question description 2', 2, '2014-01-01', '2014-01-01');