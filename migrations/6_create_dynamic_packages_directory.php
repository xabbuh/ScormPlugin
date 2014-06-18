<?php

require_once __DIR__.'/../ScormPlugin.php';

/**
 * Creates the packages directory in the dynamic contents directory of the
 * Stud.IP LMS.
 *
 * @author Christian Flothmann <christian.flothmann@uos.de>
 */
class CreateDynamicPackagesDirectory extends Migration
{
    /**
     * @var ScormPlugin The plugin
     */
    private $plugin;

    public function __construct()
    {
        $this->plugin = new ScormPlugin();
    }
    /**
     * {@inheritDoc}
     */
    function description()
    {
        return 'Creates the Scorm packages directory';
    }

    /**
     * {@inheritDoc}
     */
    function up()
    {
        mkdir($this->plugin->getPackagesPath(), 0777, true);
        chmod($this->plugin->getPackagesPath(), 0777);
    }

    /**
     * {@inheritDoc}
     */
    function down()
    {
        if (is_dir($this->plugin->getPackagesPath())) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->plugin->getPackagesPath()),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                /** @var SplFileInfo $file */
                if (in_array($file->getBasename(), array('.', '..'))) {
                    continue;
                }

                if ($file->isFile() || $file->isLink()) {
                    unlink($file->getRealPath());
                } else if ($file->isDir()) {
                    rmdir($file->getRealPath());
                }
            }

            rmdir($this->plugin->getPackagesPath());
        } else if (is_file($this->plugin->getPackagesPath()) || is_link($this->plugin->getPackagesPath())) {
            unlink($this->plugin->getPackagesPath());
        }
    }
}
