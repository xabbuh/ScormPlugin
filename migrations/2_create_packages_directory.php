<?php
class CreatePackagesDirectory extends Migration
{
    public function description()
    {
        return "create directory to store packages files";
    }
    
    public function up()
    {
        $path = __DIR__ . "/../packages";
        if (!file_exists($path)) {
            mkdir($path);
            chmod($path, 0777);
        }
    }
}
