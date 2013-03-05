<?php
class AddReferenceColumn extends DBMigration
{
    public function description()
    {
        return "adds column to store a reference to the original package file";
    }
    
    public function up()
    {
        $this->db->query("ALTER TABLE `scorm_learning_units`
            ADD COLUMN `reference` VARCHAR(255)");
    }
    
    public function down()
    {
        $this->db->query("ALTER TABLE `scorm_learning_units`
            DROP COLUMN `reference`");
    }
}
