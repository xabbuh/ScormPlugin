<?php
require_once __DIR__ . '/../../../locallib.php';

ini_set("display_errors", true);
error_reporting(E_ALL ^ E_NOTICE);
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

$delayseconds = 2;  // Delay time before sco launch, used to give time to browser to define API
$db = DBManager::get();

if (!empty($a)) {
    $scorm = $plugin->getLearningUnitAsObject($a);
} else {
    print_error('missingparameter');
}

//check if scorm closed
$timenow = time();
if ($scorm->timeclose !=0) {
    if ($scorm->timeopen > $timenow) {
        print_error('notopenyet', 'scorm', null, userdate($scorm->timeopen));
    } else if ($timenow > $scorm->timeclose) {
        print_error('expired', 'scorm', null, userdate($scorm->timeclose));
    }
}

if (!empty($scoid)) {
    //
    // Direct SCO request
    //
    if ($sco = scorm_get_sco($scoid)) {
        if ($sco->launch == '') {
            // Search for the next launchable sco
            $stmt = $db->prepare('SELECT * FROM `scorm_scos` WHERE `learning_unit_id` = :id AND `launch` != "" AND `id` > :scoid ORDER BY id');
            $stmt->bindValue(':id', $scorm->id);
            $stmt->bindValue(':scoid', $sco->id);
            $stmt->execute();
            $sco = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
//
// If no sco was found get the first of SCORM package
//
if (!isset($sco)) {
    $stmt = $db->prepare('SELECT * FROM `scorm_scos` WHERE `learning_unit_id` = :id AND `launch` != "" ORDER BY id');
    $stmt->bindValue(':id', $scorm->id);
    $stmt->execute();
    $sco = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($sco->scormtype == 'asset') {
    $attempt = scorm_get_last_attempt($scorm->id, $USER->id);
    $element = (scorm_version_check($scorm->version, SCORM_13)) ? 'cmi.completion_status':'cmi.core.lesson_status';
    $value = 'completed';
    $result = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $element, $value);
}

//
// Forge SCO URL
//
$connector = '';
$version = substr($scorm->scorm_version, 0, 4);
if ((isset($sco->parameters) && (!empty($sco->parameters))) || ($version == 'AICC')) {
    if (stripos($sco->launch, '?') !== false) {
        $connector = '&';
    } else {
        $connector = '?';
    }
    if ((isset($sco->parameters) && (!empty($sco->parameters))) && ($sco->parameters[0] == '?')) {
        $sco->parameters = substr($sco->parameters, 1);
    }
}

if ($version == 'AICC') {
    require_once("$CFG->dirroot/mod/scorm/datamodels/aicclib.php");
    $aicc_sid = scorm_aicc_get_hacp_session($scorm->id);
    if (empty($aicc_sid)) {
        $aicc_sid = sesskey();
    }
    $sco_params = '';
    if (isset($sco->parameters) && (!empty($sco->parameters))) {
        $sco_params = '&'. $sco->parameters;
    }
    $launcher = $sco->launch.$connector.'aicc_sid='.$aicc_sid.'&aicc_url='.$CFG->wwwroot.'/mod/scorm/aicc.php'.$sco_params;
} else {
    if (isset($sco->parameters) && (!empty($sco->parameters))) {
        $launcher = $sco->launch.$connector.$sco->parameters;
    } else {
        $launcher = $sco->launch;
    }
}

if (scorm_external_link($sco->launch)) {
    //TODO: does this happen?
    $result = $launcher;
} else if ($scorm->scormtype === SCORM_TYPE_EXTERNAL) {
    // Remote learning activity
    $result = dirname($scorm->reference).'/'.$launcher;
} else if ($scorm->scormtype === SCORM_TYPE_IMSREPOSITORY) {
    // Repository
    $result = $CFG->repositorywebroot.substr($scorm->reference, 1).'/'.$sco->launch;
} else if ($scorm->scormtype === SCORM_TYPE_LOCAL or $scorm->scormtype === SCORM_TYPE_LOCALSYNC) {
    //note: do not convert this to use get_file_url() or moodle_url()
    //SCORM does not work without slasharguments and moodle_url() encodes querystring vars
    $plugin = PluginEngine::getPlugin("ScormPlugin");
    $result = $GLOBALS['DYNAMIC_CONTENT_URL'].'/Scorm/'.$scorm->id.'/contents/'.$launcher;
}

// which API are we looking for
$LMS_api = (scorm_version_check($scorm->version, SCORM_12) || empty($scorm->version)) ? 'API' : 'API_1484_11';

header('Content-Type: text/html');

?>
<html>
    <head>
        <title>LoadSCO</title>
        <script type="text/javascript">
        //<![CDATA[
        var myApiHandle = null;
        var myFindAPITries = 0;

        function myGetAPIHandle() {
           myFindAPITries = 0;
           if (myApiHandle == null) {
              myApiHandle = myGetAPI();
           }
           return myApiHandle;
        }

        function myFindAPI(win) {
           while ((win.<?php echo $LMS_api; ?> == null) && (win.parent != null) && (win.parent != win)) {
              myFindAPITries++;
              // Note: 7 is an arbitrary number, but should be more than sufficient
              if (myFindAPITries > 7) {
                 return null;
              }
              win = win.parent;
           }
           return win.<?php echo $LMS_api; ?>;
        }

        // hun for the API - needs to be loaded before we can launch the package
        function myGetAPI() {
           var theAPI = myFindAPI(window);
           if ((theAPI == null) && (window.opener != null) && (typeof(window.opener) != "undefined")) {
              theAPI = myFindAPI(window.opener);
           }
           if (theAPI == null) {
              return null;
           }
           return theAPI;
        }

       function doredirect() {
            if (myGetAPIHandle() != null) {
                location = "<?php echo $result ?>";
            }
            else {
                document.body.innerHTML = "<p>Lädt in <span id='countdown'><?php echo $delayseconds ?></span> Sekunden neu. &nbsp; <img src='<?php echo icon_url('wait', 'scorm') ?>'><p>";
                var e = document.getElementById("countdown");
                var cSeconds = parseInt(e.innerHTML);
                var timer = setInterval(function() {
                                                if( cSeconds && myGetAPIHandle() == null ) {
                                                    e.innerHTML = --cSeconds;
                                                } else {
                                                    clearInterval(timer);
                                                    document.body.innerHTML = "<p>Bitte warten.</p>";
                                                    location = "<?php echo $result ?>";
                                                }
                                            }, 1000);
            }
        }
        //]]>
        </script>
        <noscript>
            <meta http-equiv="refresh" content="0;url=<?php echo $result ?>" />
        </noscript>
    </head>
    <body onload="doredirect();">
        <p>Bitte warten.</p>
    </body>
</html>