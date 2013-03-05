<?php

class AddRevisionMetadata extends DBMigration
{
    public function description()
    {
        return "add revision number and sha1 hash as versioning metadata";
    }
    
    public function up()
    {
        $this->db->query("ALTER TABLE `scorm_learning_units`
            ADD COLUMN `sha1hash` VARCHAR(40) DEFAULT NULL,
            ADD COLUMN `revision` BIGINT NOT NULL DEFAULT 0");
    }
    
    public function down()
    {
        $this->db->query("ALTER TABLE `scorm_learning_units`
            DROP COLUMN `sha1hash`,
            DROP COLUMN `revision`");
    }
}
