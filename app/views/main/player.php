<?php
require_once __DIR__ . '/../../../locallib.php';

if(Request::get("display") == "popup") {
    PageLayout::disableHeader();
    $displaymode = "popup";
}

// TODO: remove
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", true);
echo "<pre>";

//$id = optional_param('cm', '', PARAM_INT);       // Course Module ID, or
//$a = optional_param('a', '', PARAM_INT);         // scorm ID
$scoid = Request::get("scoid");  // sco ID
$mode = Request::get("mode");
if(!$mode) {
    $mode = "normal";
}
$currentorg = Request::get($displaymode); // selected organization
if(!$currentorg) {
    $currentorg = "";
}
$newattempt = Request::get("newattempt");
if(!$newattempt) {
    $newattempt = "off";
}
$displaymode = Request::get("display");
if(!$displaymode) {
    $displaymode = "";
}

// IE 9 workaround for Flash bug: MDL-29213
// Note that it's not clear if appending the meta tag via $CFG->additionalhtmlhead
// is correct at all, both because of the mechanism itself and because MS says
// the tag must be used *before* including other stuff. See the issue for more info.
// TODO: Once we implement some way to inject meta tags, change this to use it. MDL-30039
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9') !== false) {
    if (!isset($CFG->additionalhtmlhead)) { //check to make sure set first - that way we can use .=
        $CFG->additionalhtmlhead = '';
    }
    $CFG->additionalhtmlhead .= '<meta http-equiv="X-UA-Compatible" content="IE=8" />';
}

$forcejs = get_config('scorm', 'forcejavascript');
if (!empty($forcejs)) {
    $PAGE->add_body_class('forcejavascript');
}

$strscorms = "SCORMS";
$strscorm  = "SCORM";
$strpopup = "POPUP";
$strexit = "beenden";

//check if scorm closed
$timenow = time();
if (!is_null($scorm->endtime)) {
    if (strtotime($scorm->starttime) > $timenow) {
        echo $OUTPUT->header();
        echo $OUTPUT->box(get_string("notopenyet", "scorm", userdate($scorm->timeopen)), "generalbox boxaligncenter");
        echo $OUTPUT->footer();
        die;
    } else if ($timenow > strtotime($scorm->endtime)) {
        echo $OUTPUT->header();
        echo $OUTPUT->box(get_string("expired", "scorm", userdate($scorm->timeclose)), "generalbox boxaligncenter");
        echo $OUTPUT->footer();

        die;
    }
}

// TOC processing
if (!file_exists(__DIR__ . '/../../../'.$scorm->scorm_version.'lib.php')) {
    $scorm->scorm_version = 'scorm_12';
}
require_once(__DIR__ . '/../../../'.$scorm->scorm_version.'lib.php');
//$attempt = scorm_get_last_attempt($scorm->id, $USER->id);
$attempt = 0;
if (($newattempt=='on') && (($attempt < $scorm->maxattempt) || ($scorm->maxattempt == 0))) {
    $attempt++;
    $mode = 'normal';
}
$attemptstr = '&amp;attempt=' . $attempt;

$result = scorm_get_toc($USER, $scorm, $cm->id, TOCJSLINK, $currentorg, $scoid, $mode, $attempt, true, true);
$sco = $result->sco;

if (($mode == 'browse') && ($scorm->hidebrowse == 1)) {
    $mode = 'normal';
}
if ($mode != 'browse') {
    if ($trackdata = scorm_get_tracks($sco->id, $USER->id, $attempt)) {
        if (($trackdata->status == 'completed') || ($trackdata->status == 'passed') || ($trackdata->status == 'failed')) {
            $mode = 'review';
        } else {
            $mode = 'normal';
        }
    } else {
        $mode = 'normal';
    }
}

$scoidstr = '&amp;scoid='.$sco->id;
$scoidpop = '&scoid='.$sco->id;
$modestr = '&amp;mode='.$mode;
if ($mode == 'browse') {
    $modepop = '&mode='.$mode;
} else {
    $modepop = '';
}
$orgstr = '&currentorg='.$currentorg;

$SESSION->scorm = new stdClass();
$SESSION->scorm->scoid = $sco->id;
$SESSION->scorm->scormstatus = 'Not Initialized';
$SESSION->scorm->scormmode = $mode;
$SESSION->scorm->attempt = $attempt;

// Print the page header
if (empty($scorm->popup) || $displaymode=='popup') {
    $exitlink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$scorm->course.'" title="'.$strexit.'">'.$strexit.'</a> ';
    // TODO: fix button
//    $PAGE->set_button($exitlink);
    echo $exitlink;
}

// TODO: load js
//$PAGE->requires->data_for_js('scormplayerdata', Array('cwidth'=>$scorm->width,
//                                                      'cheight'=>$scorm->height,
//                                                      'popupoptions' => $scorm->options), true);
//$PAGE->requires->js('/mod/scorm/request.js', true);
//$PAGE->requires->js('/lib/cookies.js', true);
//$PAGE->requires->css('/mod/scorm/styles.css');
//echo $OUTPUT->header();
//
//// NEW IMS TOC
//$PAGE->requires->string_for_js('navigation', 'scorm');
//$PAGE->requires->string_for_js('toc', 'scorm');
//$PAGE->requires->string_for_js('hide', 'moodle');
//$PAGE->requires->string_for_js('show', 'moodle');
//$PAGE->requires->string_for_js('popupsblocked', 'scorm');

$name = false;

?>
    <div id="scormpage">

      <div id="tocbox">
        <div id='scormapi-parent'>
            <script id="external-scormapi" type="text/JavaScript"></script>
        </div>
<?php
if ($scorm->hidetoc == SCORM_TOC_POPUP or $mode=='browse' or $mode=='review') {
    echo '<div id="scormtop">';
    echo $mode == 'browse' ? '<div id="scormmode" class="scorm-left">durchführen' . "</div>\n" : '';
    echo $mode == 'review' ? '<div id="scormmode" class="scorm-left">Review'."</div>\n" : '';
    if ($scorm->hidetoc == SCORM_TOC_POPUP) {
        echo '<div id="scormnav" class="scorm-right">'.$result->tocmenu.'</div>';
    }
    echo '</div>';
}
?>
            <div id="toctree">
                <?php
                if (empty($scorm->popup) || $displaymode == 'popup') {
                    echo $result->toc;
                }?>
            </div> <!-- toctree -->
        </div> <!--  tocbox -->
                <noscript>
                    <div id="noscript">
                        Um den SCORM-Player nutzen zu können, müssen Sie JavaScript aktivieren.
                    </div>
                </noscript>
<?php
if ($result->prerequisites) {
    if ($scorm->popup != 0 && $displaymode !=='popup') {
        // Clean the name for the window as IE is fussy
        $name = preg_replace("/[^A-Za-z0-9]/", "", $scorm->name);
        if (!$name) {
            $name = 'DefaultPlayerWindow';
        }
        $name = 'scorm_'.$name;
        
        $url = PluginEngine::getURL($GLOBALS["plugin"],
                array("scoid" => $sco->id, "display" => "popup"),
                "main/player/{$scorm->id}");
        echo '<script type="text/javascript">';
        printf("scorm_openpopup('%s', '%s', '%s', '%s', '%s');",
                $url,
                $name,
                $scorm->options,
                $scorm->width,
                $scorm->height);
        echo '</script>';
        
        $url = PluginEngine::getURL($GLOBALS["plugin"], array("id" => $cm->id),
                "main/load_sco");
        $url .= "&{$scoidstr}{$modestr}";
        ?>
            <!--[if IE]>
                <iframe id="main" class="scoframe" name="main" src="<?php echo $url; ?>"></iframe>
            <![endif]-->
            <!--[if !IE]>
                <object id="main" class="scoframe" type="text/html" data="<?php echo $url; ?>"></object>
            <![endif]-->
            </noscript>
        <?php
    }
} else {
    echo $OUTPUT->box(get_string('noprerequisites', 'scorm'));
}
?>
    </div> <!-- SCORM page -->
<?php
// NEW IMS TOC
if (empty($scorm->popup) || $displaymode == 'popup') {
    if (!isset($result->toctitle)) {
        $result->toctitle = "Menü";
    }
    
    echo '<script type="text/javascript">';
    echo "$(document).ready(function() {";
    printf("    M.mod_scorm.init(YUI(), '%s', '%s', '%s', '%s', '%s');",
            $scorm->hidenav,
            $scorm->hidetoc,
            $result->toctitle,
            $name,
            $sco->id);
    echo "});";
    echo "if(!M.cfg) {";
    echo "    M.cfg = {}";
    echo "}";
    echo "M.cfg.pluginurl = '" . $GLOBALS["plugin"]->getPluginUrl() . "';";
    echo "M.cfg.loadscourl = '" . PluginEngine::getUrl($GLOBALS["plugin"], array(), "main/load_sco") . "';";
    echo '</script>';
}
if (!empty($forcejs)) {
    echo $OUTPUT->box(get_string("forcejavascriptmessage", "scorm"), "generalbox boxaligncenter forcejavascriptmessage");
}