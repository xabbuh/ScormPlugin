<?php

class ChangeGradeColumns extends DBMigration
{
    public function description()
    {
        return "change column type of columns grademethod and whatgrade from enum to integer";
    }

    public function up()
    {
        // change column grademethod (use 0, 1, 2, 3 instead of count, max, average and sum
        $this->db->query("ALTER TABLE `scorm_learning_units` ADD COLUMN `grademethod2` TINYINT AFTER `grademethod`");
        $this->db->query("UPDATE `scorm_learning_units` SET `grademethod2` = 0 WHERE `grademethod` = 'count'");
        $this->db->query("UPDATE `scorm_learning_units` SET `grademethod2` = 1 WHERE `grademethod` = 'max'");
        $this->db->query("UPDATE `scorm_learning_units` SET `grademethod2` = 2 WHERE `grademethod` = 'average'");
        $this->db->query("UPDATE `scorm_learning_units` SET `grademethod2` = 3 WHERE `grademethod` = 'sum'");
        $this->db->query("ALTER TABLE `scorm_learning_units` DROP COLUMN `grademethod`");
        $this->db->query("ALTER TABLE `scorm_learning_units` CHANGE COLUMN `grademethod2` `grademethod` TINYINT");

        // change column whatgrade (use 0, 1, 2, 3 instead of best, average, first and last
        $this->db->query("ALTER TABLE `scorm_learning_units` ADD COLUMN `whatgrade2` TINYINT AFTER `whatgrade`");
        $this->db->query("UPDATE `scorm_learning_units` SET `whatgrade2` = 0 WHERE `whatgrade` = 'best'");
        $this->db->query("UPDATE `scorm_learning_units` SET `whatgrade2` = 1 WHERE `whatgrade` = 'average'");
        $this->db->query("UPDATE `scorm_learning_units` SET `whatgrade2` = 2 WHERE `whatgrade` = 'first'");
        $this->db->query("UPDATE `scorm_learning_units` SET `whatgrade2` = 3 WHERE `whatgrade` = 'last'");
        $this->db->query("ALTER TABLE `scorm_learning_units` DROP COLUMN `whatgrade`");
        $this->db->query("ALTER TABLE `scorm_learning_units` CHANGE COLUMN `whatgrade2` `whatgrade` TINYINT");
    }

    public function down()
    {
        // change column grademethod (use count, max, average and sum instead of 0, 1, 2, 3
        $this->db->query("ALTER TABLE `scorm_learning_units`
            ADD COLUMN `grademethod2` ENUM('count', 'max', 'average', 'sum') AFTER `grademethod`");
        $this->db->query("UPDATE `scorm_learning_units` SET `grademethod2` = 'count' WHERE `grademethod` = 0");
        $this->db->query("UPDATE `scorm_learning_units` SET `grademethod2` = 'max' WHERE `grademethod` = 1");
        $this->db->query("UPDATE `scorm_learning_units` SET `grademethod2` = 'average' WHERE `grademethod` = 2");
        $this->db->query("UPDATE `scorm_learning_units` SET `grademethod2` = 'sum' WHERE `grademethod` = 3");
        $this->db->query("ALTER TABLE `scorm_learning_units` DROP COLUMN `grademethod`");
        $this->db->query("ALTER TABLE `scorm_learning_units`
            CHANGE COLUMN `grademethod2` `grademethod` ENUM('count', 'max', 'average', 'sum')");

        // change column whatgrade (use best, average, first and last instead of 0, 1, 2, 3
        $this->db->query("ALTER TABLE `scorm_learning_units`
            ADD COLUMN `whatgrade2` ENUM('best', 'average', 'first', 'last') AFTER `whatgrade`");
        $this->db->query("UPDATE `scorm_learning_units` SET `whatgrade2` = 'best' WHERE `whatgrade` = 0");
        $this->db->query("UPDATE `scorm_learning_units` SET `whatgrade2` = 'average' WHERE `whatgrade` = 1");
        $this->db->query("UPDATE `scorm_learning_units` SET `whatgrade2` = 'first' WHERE `whatgrade` = 2");
        $this->db->query("UPDATE `scorm_learning_units` SET `whatgrade2` = 'last' WHERE `whatgrade` = 3");
        $this->db->query("ALTER TABLE `scorm_learning_units` DROP COLUMN `whatgrade`");
        $this->db->query("ALTER TABLE `scorm_learning_units`
            CHANGE COLUMN `whatgrade2` `whatgrade` ENUM('best', 'average', 'first', 'last')");
    }
}
