<?php
require_once 'app/controllers/studip_controller.php';

class MainController extends StudipController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $GLOBALS['CURRENT_PAGE'] = 'Scorm';
        PageLayout::setTitle(_('Scorm'));
        
        # $this->flash = Trails_Flash::instance();
        
        # set default layout
        $layout = $GLOBALS['template_factory']->open('layouts/base');
        $this->set_layout($layout);
    }
    
    function index_action()
    {
        Navigation::activateItem("/course/scorm/overview");
        
        $db = DBManager::get();
        $stmt = $db->prepare("SELECT `id`, `cid`, `name`, `introduction_text`,
            `filename`, `scorm_version`, `starttime`, `endtime`, `popup`,
            `grademethod`, `maxgrade`, `maxattempt`, `whatgrade` FROM `scorm_learning_units` WHERE
            (`starttime` IS NULL OR `starttime` <= NOW()) AND (`endtime` IS NULL
            OR `endtime` >= NOW())");
        $stmt->execute();
        $this->learningUnits = $stmt->fetchAll();
    }

}