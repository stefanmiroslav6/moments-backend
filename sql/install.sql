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
-- CREATE DATABASE IF NOT EXISTS welstory DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- GRANT ALL PRIVILEGES ON welstory.* TO wsusr@'%' IDENTIFIED BY 'password123' WITH GRANT OPTION;
-- GRANT ALL PRIVILEGES ON welstory.* TO wsusr@localhost IDENTIFIED BY 'password123' WITH GRANT OPTION;

-- USE welstory;

CREATE TABLE IF NOT EXISTS `Users` (
  `UserId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(50) NOT NULL,
  `Email` VARCHAR(255) NOT NULL,
  `Password` VARCHAR(130) NOT NULL,
  `Birthday` DATETIME NULL,
  `Gender` SMALLINT(1) NOT NULL DEFAULT 1,
  `AvatarUrl` VARCHAR(255) NOT NULL DEFAULT '',
  `ProfileBackgroundUrl` VARCHAR(255) NOT NULL DEFAULT '',
  `ForgotPasswordEmailToken` VARCHAR(50) NULL,
  `ForgotPasswordEmailCreateDate` DATETIME NULL,
  `IsRecommended` SMALLINT(1) NOT NULL DEFAULT 0,
  `CreatedDate` DATETIME NOT NULL,
  `LastModifiedDate` DATETIME NULL,
  PRIMARY KEY (`UserId`),
  INDEX `IX_Users_Username` (`Username` ASC),
  INDEX `IX_Users_Email` (`Email` ASC),
  CONSTRAINT `UK_Users_Username` UNIQUE (`Username`),
  CONSTRAINT `UK_Users_Email` UNIQUE (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `Users`
AUTO_INCREMENT = 1001;

CREATE TABLE IF NOT EXISTS `Social` (
  `SocialId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserId` INT UNSIGNED NOT NULL,
  `Type` SMALLINT(1) NOT NULL DEFAULT 1,
  `ExternalId` VARCHAR(255) NOT NULL,
  `Email` VARCHAR(200) NOT NULL DEFAULT '',
  `DisplayName` VARCHAR(150) NOT NULL DEFAULT '',
  `Firstname` VARCHAR(100) NOT NULL DEFAULT '',
  `Lastname` VARCHAR(100) NOT NULL DEFAULT '',
  `ProfileUrl` VARCHAR(300) NOT NULL DEFAULT '',
  `PhotoUrl` VARCHAR(300) NOT NULL DEFAULT '',
  `WebsiteUrl` VARCHAR(300) NOT NULL DEFAULT '',
  `CreatedDate` DATETIME NOT NULL,
  `LastModifiedDate` DATETIME NULL,
  PRIMARY KEY (`SocialId`),
  CONSTRAINT `FK_Social_UserId` FOREIGN KEY (`UserId`) REFERENCES `Users`(`UserId`),
  INDEX `IX_Social_UserId` (`UserId` ASC),
  INDEX `IX_Social_ExternalId` (`ExternalId` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Category` (
  `CategoryId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(120) NOT NULL,
  `Description` VARCHAR(1000) NOT NULL DEFAULT '',
  `Sort` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`CategoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Question` (
  `QuestionId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(255) NOT NULL,
  `Description` VARCHAR(1000) NOT NULL DEFAULT '',
  `CategoryId` INT UNSIGNED NOT NULL,
  `CreatedDate` DATETIME NOT NULL,
  `LastModifiedDate` DATETIME NULL,
  PRIMARY KEY (`QuestionId`),
  CONSTRAINT `FK_Question_CategoryId` FOREIGN KEY (`CategoryId`) REFERENCES `Category`(`CategoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Story` (
  `StoryId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(200) NOT NULL,
  `Description` VARCHAR(1000) NOT NULL DEFAULT '',
  `CoverUrl` VARCHAR(500) NOT NULL DEFAULT '',
  `CoverThumbUrl` VARCHAR(500) NOT NULL DEFAULT '',
  `MediaUrl` VARCHAR(500) NOT NULL DEFAULT '',
  `MediaLength` INT UNSIGNED NOT NULL DEFAULT 0,
  `Type` SMALLINT(1) NOT NULL DEFAULT 1,
  `IsRecommended` SMALLINT(1) NOT NULL DEFAULT 0,
  `UserId` INT UNSIGNED NOT NULL,
  `CategoryId` INT UNSIGNED NULL,
  `QuestionId` INT UNSIGNED NULL,
  `ViewCount` INT UNSIGNED NOT NULL DEFAULT 0,
  `PlayCount` INT UNSIGNED NOT NULL DEFAULT 0,
  `DownloadCount` INT UNSIGNED NOT NULL DEFAULT 0,
  `SharedCount` INT UNSIGNED NOT NULL DEFAULT 0,
  `CreatedDate` DATETIME NOT NULL,
  `LastModifiedDate` DATETIME NULL,
  `PublishDate` DATETIME NULL,
  PRIMARY KEY (`StoryId`),
  CONSTRAINT `FK_Story_UserId` FOREIGN KEY (`UserId`) REFERENCES `Users`(`UserId`),
  CONSTRAINT `FK_Story_CategoryId` FOREIGN KEY (`CategoryId`) REFERENCES `Category`(`CategoryId`),
  CONSTRAINT `FK_Story_QuestionId` FOREIGN KEY (`QuestionId`) REFERENCES `Question`(`QuestionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Follow` (
  `FollowId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `SenderId` INT UNSIGNED NOT NULL,
  `ReceiverId` INT UNSIGNED NOT NULL,
  `Status` SMALLINT(1) NOT NULL DEFAULT 1,
  `CreatedDate` DATETIME NOT NULL,
  PRIMARY KEY (`FollowId`),
  CONSTRAINT `FK_Follow_SenderId` FOREIGN KEY (`SenderId`) REFERENCES `Users`(`UserId`),
  CONSTRAINT `FK_Follow_ReceiverId` FOREIGN KEY (`ReceiverId`) REFERENCES `Users`(`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Comment` (
  `CommentId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Body` TEXT NOT NULL,
  `ParentId` INT UNSIGNED NULL,
  `Deep` INT UNSIGNED NOT NULL DEFAULT 0,
  `StoryId` INT UNSIGNED NOT NULL,
  `UserId` INT UNSIGNED NOT NULL,
  `CreatedDate` DATETIME NOT NULL,
  `LastModifiedDate` DATETIME NULL,
  PRIMARY KEY (`CommentId`),
  CONSTRAINT `FK_Comment_ParentId` FOREIGN KEY (`ParentId`) REFERENCES `Comment`(`CommentId`),
  CONSTRAINT `FK_Comment_StoryId` FOREIGN KEY (`StoryId`) REFERENCES `Story`(`StoryId`),
  CONSTRAINT `FK_Comment_UserId` FOREIGN KEY (`UserId`) REFERENCES `Users`(`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `StoryLike` (
  `StoryLikeId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `StoryId` INT UNSIGNED NOT NULL,
  `UserId` INT UNSIGNED NOT NULL,
  `CreatedDate` DATETIME NOT NULL,
  PRIMARY KEY (`StoryLikeId`),
  CONSTRAINT `FK_StoryLike_StoryId` FOREIGN KEY (`StoryId`) REFERENCES `Story`(`StoryId`),
  CONSTRAINT `FK_StoryLike_UserId` FOREIGN KEY (`UserId`) REFERENCES `Users`(`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Feedback` (
  `FeedbackId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserId` INT UNSIGNED NULL,
  `Content` TEXT NOT NULL,
  `CreatedDate` DATETIME NOT NULL,
  PRIMARY KEY (`FeedbackId`),
  CONSTRAINT `FK_Feedback_UserId` FOREIGN KEY (`UserId`) REFERENCES `Users`(`UserId`),
  INDEX `IX_Feedback_UserId` (`UserId` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Email` (
  `EmailId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `SenderEmail` VARCHAR(100) NOT NULL,
  `ReceiverEmail` VARCHAR(100) NOT NULL,
  `Title` VARCHAR(255) NOT NULL,
  `Body` TEXT NOT NULL,
  `Status` SMALLINT(1) NOT NULL DEFAULT 1,
  `CreatedDate` DATETIME NOT NULL,
  `LastModifiedDate` DATETIME NULL,
  PRIMARY KEY (`EmailId`),
  INDEX `IX_Email_Status` (`Status` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Activity` (
  `ActivityId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `SenderId` INT UNSIGNED NOT NULL,
  `ReceiverId` INT UNSIGNED NOT NULL,
  `StoryId` INT UNSIGNED NULL,
  `CommentId` INT UNSIGNED NULL,
  `Type` SMALLINT(1) NOT NULL DEFAULT 1,
  `CreatedDate` DATETIME NOT NULL,
  PRIMARY KEY (`ActivityId`),
  CONSTRAINT `FK_Activity_SenderId` FOREIGN KEY (`SenderId`) REFERENCES `Users`(`UserId`),
  CONSTRAINT `FK_Activity_ReceiverId` FOREIGN KEY (`ReceiverId`) REFERENCES `Users`(`UserId`),
  CONSTRAINT `FK_Activity_StoryId` FOREIGN KEY (`StoryId`) REFERENCES `Story`(`StoryId`),
  CONSTRAINT `FK_Activity_CommentId` FOREIGN KEY (`CommentId`) REFERENCES `Comment`(`CommentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;