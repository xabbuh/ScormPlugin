<?php

class CreateGradeTables extends DBMigration
{
    public function description()
    {
        return "create grading related tables";
    }

    public function up()
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `grade_categories` (
              `id` bigint(10) NOT NULL AUTO_INCREMENT,
              `courseid` varchar(32) NOT NULL,
              `parent` bigint(10) DEFAULT NULL,
              `depth` bigint(10) NOT NULL DEFAULT '0',
              `path` varchar(255) DEFAULT NULL,
              `fullname` varchar(255) NOT NULL DEFAULT '',
              `aggregation` bigint(10) NOT NULL DEFAULT '0',
              `keephigh` bigint(10) NOT NULL DEFAULT '0',
              `droplow` bigint(10) NOT NULL DEFAULT '0',
              `aggregateonlygraded` tinyint(1) NOT NULL DEFAULT '0',
              `aggregateoutcomes` tinyint(1) NOT NULL DEFAULT '0',
              `aggregatesubcats` tinyint(1) NOT NULL DEFAULT '0',
              `timecreated` bigint(10) NOT NULL,
              `timemodified` bigint(10) NOT NULL,
              `hidden` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY (`courseid`),
              KEY (`parent`)
            )"
        );
        $this->db->query(
            "CREATE TABLE `grade_categories_history` (
              `id` bigint(10) NOT NULL AUTO_INCREMENT,
              `action` bigint(10) NOT NULL DEFAULT '0',
              `oldid` bigint(10) NOT NULL,
              `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `timemodified` bigint(10) DEFAULT NULL,
              `loggeduser` varchar(32) DEFAULT NULL,
              `courseid` varchar(32) NOT NULL,
              `parent` bigint(10) DEFAULT NULL,
              `depth` bigint(10) NOT NULL DEFAULT '0',
              `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `fullname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `aggregation` bigint(10) NOT NULL DEFAULT '0',
              `keephigh` bigint(10) NOT NULL DEFAULT '0',
              `droplow` bigint(10) NOT NULL DEFAULT '0',
              `aggregateonlygraded` tinyint(1) NOT NULL DEFAULT '0',
              `aggregateoutcomes` tinyint(1) NOT NULL DEFAULT '0',
              `aggregatesubcats` tinyint(1) NOT NULL DEFAULT '0',
              `hidden` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY (`action`),
              KEY (`oldid`),
              KEY (`courseid`),
              KEY (`parent`),
              KEY (`loggeduser`)
            )"
        );
        $this->db->query(
            "CREATE TABLE `grade_grades` (
              `id` bigint(10) NOT NULL AUTO_INCREMENT,
              `itemid` bigint(10) NOT NULL,
              `userid` varchar(32) NOT NULL,
              `rawgrade` decimal(10,5) DEFAULT NULL,
              `rawgrademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
              `rawgrademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `rawscaleid` bigint(10) DEFAULT NULL,
              `usermodified` bigint(10) DEFAULT NULL,
              `finalgrade` decimal(10,5) DEFAULT NULL,
              `hidden` bigint(10) NOT NULL DEFAULT '0',
              `locked` bigint(10) NOT NULL DEFAULT '0',
              `locktime` bigint(10) NOT NULL DEFAULT '0',
              `exported` bigint(10) NOT NULL DEFAULT '0',
              `overridden` bigint(10) NOT NULL DEFAULT '0',
              `excluded` bigint(10) NOT NULL DEFAULT '0',
              `feedback` longtext COLLATE utf8_unicode_ci,
              `feedbackformat` bigint(10) NOT NULL DEFAULT '0',
              `information` longtext COLLATE utf8_unicode_ci,
              `informationformat` bigint(10) NOT NULL DEFAULT '0',
              `timecreated` bigint(10) DEFAULT NULL,
              `timemodified` bigint(10) DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY (`userid`,`itemid`),
              KEY (`locked`,`locktime`),
              KEY (`itemid`),
              KEY (`userid`),
              KEY (`rawscaleid`),
              KEY (`usermodified`)
            )"
        );
        $this->db->query(
            "CREATE TABLE `grade_grades_history` (
              `id` bigint(10) NOT NULL AUTO_INCREMENT,
              `action` bigint(10) NOT NULL DEFAULT '0',
              `oldid` bigint(10) NOT NULL,
              `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `timemodified` bigint(10) DEFAULT NULL,
              `loggeduser` bigint(10) DEFAULT NULL,
              `itemid` bigint(10) NOT NULL,
              `userid` varchar(32) NOT NULL,
              `rawgrade` decimal(10,5) DEFAULT NULL,
              `rawgrademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
              `rawgrademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `rawscaleid` bigint(10) DEFAULT NULL,
              `usermodified` bigint(10) DEFAULT NULL,
              `finalgrade` decimal(10,5) DEFAULT NULL,
              `hidden` bigint(10) NOT NULL DEFAULT '0',
              `locked` bigint(10) NOT NULL DEFAULT '0',
              `locktime` bigint(10) NOT NULL DEFAULT '0',
              `exported` bigint(10) NOT NULL DEFAULT '0',
              `overridden` bigint(10) NOT NULL DEFAULT '0',
              `excluded` bigint(10) NOT NULL DEFAULT '0',
              `feedback` longtext COLLATE utf8_unicode_ci,
              `feedbackformat` bigint(10) NOT NULL DEFAULT '0',
              `information` longtext COLLATE utf8_unicode_ci,
              `informationformat` bigint(10) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY (`action`),
              KEY (`oldid`),
              KEY (`itemid`),
              KEY (`userid`),
              KEY (`rawscaleid`),
              KEY (`usermodified`),
              KEY (`loggeduser`)
            )"
        );
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `grade_items` (
              `id` bigint(10) NOT NULL AUTO_INCREMENT,
              `courseid` varchar(32) DEFAULT NULL,
              `categoryid` bigint(10) DEFAULT NULL,
              `itemname` varchar(255) DEFAULT NULL,
              `itemtype` varchar(30) NOT NULL DEFAULT '',
              `itemmodule` varchar(30) DEFAULT NULL,
              `iteminstance` bigint(10) DEFAULT NULL,
              `itemnumber` bigint(10) DEFAULT NULL,
              `iteminfo` longtext,
              `idnumber` varchar(255) DEFAULT NULL,
              `calculation` longtext,
              `gradetype` smallint(4) NOT NULL DEFAULT '1',
              `grademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
              `grademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `scaleid` bigint(10) DEFAULT NULL,
              `outcomeid` bigint(10) DEFAULT NULL,
              `gradepass` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `multfactor` decimal(10,5) NOT NULL DEFAULT '1.00000',
              `plusfactor` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `aggregationcoef` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `sortorder` bigint(10) NOT NULL DEFAULT '0',
              `display` bigint(10) NOT NULL DEFAULT '0',
              `decimals` tinyint(1) DEFAULT NULL,
              `hidden` bigint(10) NOT NULL DEFAULT '0',
              `locked` bigint(10) NOT NULL DEFAULT '0',
              `locktime` bigint(10) NOT NULL DEFAULT '0',
              `needsupdate` bigint(10) NOT NULL DEFAULT '0',
              `timecreated` bigint(10) DEFAULT NULL,
              `timemodified` bigint(10) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY (`locked`,`locktime`),
              KEY (`itemtype`,`needsupdate`),
              KEY (`gradetype`),
              KEY (`idnumber`,`courseid`),
              KEY (`courseid`),
              KEY (`categoryid`),
              KEY (`scaleid`),
              KEY (`outcomeid`)
            )"
        );
        $this->db->query(
            "CREATE TABLE `grade_items_history` (
              `id` bigint(10) NOT NULL AUTO_INCREMENT,
              `action` bigint(10) NOT NULL DEFAULT '0',
              `oldid` bigint(10) NOT NULL,
              `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `timemodified` bigint(10) DEFAULT NULL,
              `loggeduser` varchar(32) DEFAULT NULL,
              `courseid` varchar(32) DEFAULT NULL,
              `categoryid` bigint(10) DEFAULT NULL,
              `itemname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `itemtype` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `itemmodule` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
              `iteminstance` bigint(10) DEFAULT NULL,
              `itemnumber` bigint(10) DEFAULT NULL,
              `iteminfo` longtext COLLATE utf8_unicode_ci,
              `idnumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `calculation` longtext COLLATE utf8_unicode_ci,
              `gradetype` smallint(4) NOT NULL DEFAULT '1',
              `grademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
              `grademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `scaleid` bigint(10) DEFAULT NULL,
              `outcomeid` bigint(10) DEFAULT NULL,
              `gradepass` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `multfactor` decimal(10,5) NOT NULL DEFAULT '1.00000',
              `plusfactor` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `aggregationcoef` decimal(10,5) NOT NULL DEFAULT '0.00000',
              `sortorder` bigint(10) NOT NULL DEFAULT '0',
              `hidden` bigint(10) NOT NULL DEFAULT '0',
              `locked` bigint(10) NOT NULL DEFAULT '0',
              `locktime` bigint(10) NOT NULL DEFAULT '0',
              `needsupdate` bigint(10) NOT NULL DEFAULT '0',
              `display` bigint(10) NOT NULL DEFAULT '0',
              `decimals` tinyint(1) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY (`action`),
              KEY (`oldid`),
              KEY (`courseid`),
              KEY (`categoryid`),
              KEY (`scaleid`),
              KEY (`outcomeid`),
              KEY (`loggeduser`)
            )"
        );
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `grade_categories`");
        $this->db->query("DROP TABLE IF EXISTS `grade_categories_history`");
        $this->db->query("DROP TABLE IF EXISTS `grade_grades`");
        $this->db->query("DROP TABLE IF EXISTS `grade_grades_history`");
        $this->db->query("DROP TABLE IF EXISTS `grade_items`");
        $this->db->query("DROP TABLE IF EXISTS `grade_items_history`");
    }
}
