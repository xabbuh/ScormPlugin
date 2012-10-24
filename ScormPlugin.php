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
    
    public function getIconNavigation($course_id, $last_visit, $user_id)
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
    
    public function getLearningUnit($id)
    {
        $db = DBManager::get();
        $stmt = $db->prepare("SELECT `id`, `cid`, `name`, `introduction_text`,
            `scormtype`, `filename`, `scorm_version`, `starttime`, `endtime`,
            `auto`, `popup`, `grademethod`, `maxgrade`, `maxattempt`, `whatgrade`
            FROM `scorm_learning_units` WHERE `id` = ? AND (`starttime` IS NULL OR
            `starttime` <= NOW()) AND (`endtime` IS NULL OR `endtime` >= NOW())");
        $stmt->execute(array($id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getLearningUnitAsObject($id)
    {
        $learningUnit = $this->getLearningUnit($id);
        $learningUnitObject = new stdClass();
        foreach($learningUnit as $key => $value) {
            $learningUnitObject->{$key} = $value;
        }
        return $learningUnitObject;
    }
}
