CREATE TABLE IF NOT EXISTS `scorm_learning_units` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `cid` VARCHAR(32) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `introduction_text` TEXT,
    `scormtype` VARCHAR(50) NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `scorm_version` VARCHAR(9) NOT NULL,
    `starttime` DATETIME,
    `endtime` DATETIME,
    `auto` TINYINT(1) NOT NULL DEFAULT 0,
    `popup` TINYINT(1) NOT NULL DEFAULT 1,
    `grademethod` ENUM('count', 'max', 'average', 'sum'),
    `maxgrade` INTEGER NOT NULL DEFAULT 100,
    `maxattempt` INTEGER NOT NULL DEFAULT 0,
    `whatgrade` ENUM('best', 'average', 'first', 'last')
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `scorm_scos` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `learning_unit_id` INTEGER NOT NULL,
    `manifest` VARCHAR(255) NOT NULL,
    `organization` VARCHAR(255) NOT NULL,
    `parent` VARCHAR(255) NOT NULL,
    `identifier` VARCHAR(255) NOT NULL,
    `launch` VARCHAR(255) NOT NULL,
    `scormtype` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `scorm_sco_data` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `sco_id` INTEGER NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `value` TEXT NOT NULL
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `scorm_sco_tracks` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(32) NOT NULL,
    `learning_unit_id` INTEGER NOT NULL,
    `sco_id` INTEGER NOT NULL,
    `attempt` INTEGER NOT NULL,
    `element` VARCHAR(255) NOT NULL,
    `value` TEXT NOT NULL,
    `timemodified` TIMESTAMP
);