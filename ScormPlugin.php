<?php

# Copyright (c)  2012 Christian Flothmann <christian.flothmann@iais.fraunhofer.de>
#
# ScormPlugin
#
# Enables usage of SCORM packages.
#
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.

/**
 * Description of ScormPlugin
 *
 * @author Christian Flothmann <christian.flothmann@iais.fraunhofer.de>
 */
class ScormPlugin extends StudIPPlugin implements StandardPlugin
{
    public function __construct()
    {
        global $perm;
        
        parent::__construct();
        
        $url = PluginEngine::getURL($this, array(), "main");
        $scormItem = new Navigation(_("Scorm"), $url);
        Navigation::addItem("/course/scorm", $scormItem);
        
        $scormOverviewItem = new Navigation(_("Übersicht"), $url);
        Navigation::addItem("/course/scorm/overview", $scormOverviewItem);
        
        // allow to add learning units if the user has "dozent" permission
        // for the current course
        $cid = Request::get("cid");
        if($cid && $perm->have_studip_perm("dozent", $cid)) {
            $url = PluginEngine::getURL($this, array(), "main/add");
            $addScormItem = new Navigation(_("Hinzufügen"), $url);
            Navigation::addItem("/course/scorm/add", $addScormItem);
        }
    }
    
    public function getTabNavigation($course_id)
    {
        return null;
    }
    
    public function getIconNavigation($course_id, $last_visit)
    {
        return null;
    }
    
    public function getNotificationObjects($course_id, $since, $user_id)
    {
        return null;
    }
    
    public function getInfoTemplate($course_id)
    {
        return null;
    }
    
    public function perform($unconsumed_path)
    {
        $trails_root = $this->getPluginPath() . "/app";
        $dispatcher = new Trails_Dispatcher($trails_root,
                PluginEngine::getUrl('scormplugin'),
                'index');
        $dispatcher->dispatch($unconsumed_path);
    }
    
    /**
     * Fetch a SCORM module from the database as an associative array.
     * 
     * @param int $id The id of the module being retrieved
     * @return array The module's data
     */
    public function getLearningUnit($id)
    {
        $stmt = $this->getSqlStatement($id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Fetch a SCORM module from the database as an anonymous object.
     * 
     * @param int $id The id of the module being retrieved
     * @return \stdClass The module's data
     */
    public function getLearningUnitAsObject($id)
    {
        $stmt = $this->getSqlStatement($id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Prepare a sql statement to retrieve a SCORM module from the database.
     * 
     * @param int $id The id of the module being retrieved
     * @return \PDOStatement The prepared statement
     */
    private function getSqlStatement($id)
    {
        $db = DBManager::get();
        $stmt = $db->prepare("SELECT `id`, `cid`, `name`, `introduction_text`,
            `scormtype`, `filename`, `scorm_version`, `starttime`, `endtime`,
            `auto`, `popup`, `grademethod`, `maxgrade`, `maxattempt`, `whatgrade`
            FROM `scorm_learning_units` WHERE `id` = :id");
        $stmt->bindValue(":id", $id);
        return $stmt;
    }
    
    /**
     * Returns the absoloute path to the packages directory.
     * 
     * @return string The path to the packages
     */
    public function getPackagesPath()
    {
        return $GLOBALS["ABSOLUTE_PATH_STUDIP"] . $this->getPluginPath() . "/packages";
    }
    
    /**
     * Returns the directory for a learning where its extract files are stored.
     * 
     * @param int $id The id of the learning unit
     * @return string The absolute path
     */
    public function getContentsDirectoryPath($id)
    {
        return $this->getPackagesPath() . "/$id/contents";
    }
}

require_once(__DIR__.'/config.php');
require_once __DIR__ . "/lib.php";
require_once __DIR__ . "/locallib.php";
