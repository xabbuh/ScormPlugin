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
            `grademethod`, `maxgrade`, `maxattempt`, `whatgrade` FROM `scorm_learning_units`");
        $stmt->execute();
        $this->learningUnits = $stmt->fetchAll();
    }
    
    function player_action($id)
    {
        $plugin = $GLOBALS["plugin"];
        $this->learningUnit = $plugin->getLearningUnit($id);
        $this->scorm = $plugin->getLearningUnitAsObject($id);
        
        $url = PluginEngine::getURL($plugin, array(),
                "main/player/$id");
        $playerItem = new Navigation($this->learningUnit["name"], $url);
        Navigation::addItem("/course/scorm/player", $playerItem);
        Navigation::activateItem("/course/scorm/player");
            
        if(Request::get("intro") == "yes") {
            $this->render_action("intro");
        } else {
            PageLayout::addScript("http://yui.yahooapis.com/3.7.2/build/yui/yui-min.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/yui/build/yahoo-dom-event/yahoo-dom-event.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/yui/build/element/element.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/yui/build/connection/connection.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/yui/build/treeview/treeview.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/yui/build/layout/layout.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/yui/build/container/container.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/yui/build/button/button.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/module.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/request.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/cookies.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/player.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/view.js");
            PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/script.js");

            PageLayout::addStylesheet($plugin->getPluginUrl() . "/assets/css/styles.css");
            
            $this->scorm = new stdClass();
            foreach($this->learningUnit as $key => $value) {
                $this->scorm->{$key} = $value;
            }
        }
    }
    
    function load_sco_action()
    {
        // SCORM id
        $this->a = Request::get("a", "");
        
        // SCO id
        $this->scoid = Request::get("scoid");
        
        // load the plugin's frame layout
        $this->plugin = $GLOBALS["plugin"];
        $pluginManager = PluginManager::getInstance();
        $pluginInfo = $pluginManager->getPluginInfo(get_class($this->plugin));
        $layoutPath = $GLOBALS["PLUGINS_PATH"] . "/" . $pluginInfo["path"]
                . "/layouts/frame";
        $layout = $GLOBALS['template_factory']->open($layoutPath);
        $this->set_layout($layout);
    }
    
    function load_data_action()
    {
        // load the plugin's frame layout
        $this->plugin = $GLOBALS["plugin"];
        $pluginManager = PluginManager::getInstance();
        $pluginInfo = $pluginManager->getPluginInfo(get_class($this->plugin));
        $layoutPath = $GLOBALS["PLUGINS_PATH"] . "/" . $pluginInfo["path"]
                . "/layouts/frame";
        $layout = $GLOBALS['template_factory']->open($layoutPath);
        $this->set_layout($layout);
    }
    
    function add_action()
    {
        Navigation::activateItem("/course/scorm/add");
        $plugin = $GLOBALS["plugin"];
        PageLayout::addScript($plugin->getPluginUrl() . "/assets/js/form.js");
    }
    
    function save_action()
    {
        Navigation::activateItem("/course/scorm/add");
        
        if (Request::get("action") == "save" && Request::get("accept") == "yes") {
            // check form values
            $this->errors = array();
            if (strlen(Request::get("name")) < 3) {
                $this->errors[] = _("Kein Name für das Lernmodul angegeben.");
            }
            switch ($_FILES["packagefilechoose"]["error"]) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $this->errors[] = _("Die ausgewählte Lernmoduldatei ist zu groß.");
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->errors[] = _("Bitte eine Lernmoduldatei auswählen.");
                    break;
                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                    $this->errors[] = _("Fehler beim Hochladen der Lernmoduldatei.");
                    break;
            }
            
            // save the module if not errors occured
            if (count($this->errors) == 0) {
                $scorm = new stdClass();
                $scorm->name = Request::get("name");
                $scorm->introduction_text = Request::get("");
                $scorm->scormtype = SCORM_TYPE_LOCAL;
                $scorm->popup = Request::int("popup");
                switch(Request::int("grademethod")) {
                    case GRADESCOES:
                        $scorm->grademethod = "count";
                        break;
                    case GRADEHIGHEST:
                        $scorm->grademethod = "max";
                        break;
                    case GRADEAVERAGE:
                        $scorm->grademethod = "average";
                        break;
                    case GRADESUM:
                        $scorm->grademethod = "sum";
                        break;
                }
                $scorm->maxgrade = Request::get("maxgrade");
                $scorm->maxattempt = Request::get("maxattempt");
                switch(Request::int("whatgrade")) {
                    case HIGHESTATTEMPT:
                        $scorm->whatgrade = "best";
                        break;
                    case AVERAGEATTEMPT:
                        $scorm->whatgrade = "average";
                        break;
                    case FIRSTATTEMPT:
                        $scorm->whatgrade = "first";
                        break;
                    case LASTATTEMPT:
                        $scorm->whatgrade = "last";
                        break;
                }
                $scorm->course = Request::get("cid");

                $timeopen = Request::getArray("timeopen");
                if (is_array($timeopen) && isset($timeopen["enabled"])) {
                    $scorm->timeopen = sprintf(
                        "%d-%02d-%02d %02d:%02d:00",
                        $timeopen["year"],
                        $timeopen["month"],
                        $timeopen["day"],
                        $timeopen["hour"],
                        $timeopen["minute"]
                    );
                } else {
                    $scorm->timeopen = null;
                }
                $timeclose = Request::getArray("timeclose");
                if (is_array($timeclose) && isset($timeclose["enabled"])) {
                    $scorm->timeclose = sprintf(
                        "%d-%02d-%02d %02d:%02d:00",
                        $timeclose["year"],
                        $timeclose["month"],
                        $timeclose["day"],
                        $timeclose["hour"],
                        $timeclose["minute"]
                    );
                } else {
                    $scorm->timeclose = null;
                }

                scorm_add_instance(
                    $scorm,
                    $_FILES["packagefilechoose"]["name"],
                    $_FILES["packagefilechoose"]["tmp_name"]);
                $this->redirect(PluginEngine::getURL($GLOBALS["plugin"], array(), "main/index"));
            }
        } else if (Request::get("action") == "save" && Request::get("cancel") == "yes") {
            $this->redirect(PluginEngine::getURL($GLOBALS["plugin"], array(), "main/index"));
        } else {
            $this->redirect(PluginEngine::getURL($GLOBALS["plugin"], array(), "main/add"));
        }
    }

    /**
     * Controller for deleting SCORM modules.
     * 
     * @param int $id The id of the module being removed
     */
    public function delete_action($id)
    {
        Navigation::activateItem("/course/scorm/overview");
        
        if (Request::get("accept") == "yes") {
            scorm_delete_instance($id);
            $this->redirect(PluginEngine::getUrl($GLOBALS["plugin"], array(), "main/index"));
        } else if (Request::get("cancel") == "yes") {
            $this->redirect(PluginEngine::getUrl($GLOBALS["plugin"], array(), "main/index"));
        } else {
            $this->id = $id;
            $this->cid = Request::get("cid");
        }
    }
}
