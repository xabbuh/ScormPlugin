<?php
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

/**
 * This page loads the correct JS file based on package type
 *
 * @package mod_scorm
 * @copyright 1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $user;

require_once(__DIR__ . '/../../../locallib.php');

$a = (int)Request::get("a", 0);
$scoid = (int)Request::get("scoid");
$mode = (int)Request::get("mode", 0);
$attempt = (int)Request::get("attempt");

if (!empty($a)) {
    $scorm = $plugin->getLearningUnitAsObject($a);
//    $course = $DB->get_record('course', array('id'=>$scorm->course), '*', MUST_EXIST);
//    $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id, false, MUST_EXIST);
} else {
    print_error('missingparameter');
}

$userdata = new stdClass();
if ($usertrack = scorm_get_tracks($scoid, $USER->id, $attempt)) {
    // According to SCORM 2004 spec(RTE V1, 4.2.8), only cmi.exit==suspend should allow previous datamodel elements on re-launch.
    if (!scorm_version_check($scorm->version, SCORM_13) ||
        (isset($usertrack->{'cmi.exit'}) && ($usertrack->{'cmi.exit'} == 'suspend'))) {
        foreach ($usertrack as $key => $value) {
            // TODO: implement addslashes_js
            // $userdata->$key = addslashes_js($value);
            $userdata->$key = $value;
        }
    } else {
        $userdata->status = '';
        $userdata->score_raw = '';
    }
} else {
    $userdata->status = '';
    $userdata->score_raw = '';
}
// TODO: implement addslashes_js
//$userdata->student_id = addslashes_js($USER->username);
//$userdata->student_name = addslashes_js($USER->lastname .', '. $USER->firstname);
$userdata->student_id = $USER->username;
$userdata->student_name = $USER->lastname .', '. $USER->firstname;
$userdata->mode = 'normal';
if (!empty($mode)) {
    $userdata->mode = $mode;
}
if ($userdata->mode == 'normal') {
    $userdata->credit = 'credit';
} else {
    $userdata->credit = 'no-credit';
}
if ($scodatas = scorm_get_sco($scoid, SCO_DATA)) {
    foreach ($scodatas as $key => $value) {
        // TODO: implement addslashes_js
        //$userdata->$key = addslashes_js($value);
        $userdata->$key = $value;
    }
} else {
    print_error('cannotfindsco', 'scorm');
}
if (!$sco = scorm_get_sco($scoid)) {
    print_error('cannotfindsco', 'scorm');
}
if (scorm_version_check($scorm->version, SCORM_13)) {
    $userdata->{'cmi.scaled_passing_score'} = $DB->get_field('scorm_seq_objective', 'minnormalizedmeasure', array('scoid'=>$scoid));
}

header('Content-Type: text/javascript; charset=iso-8859-15');

$scorm->version = strtolower($scorm->version);   // Just to be safe.
if (file_exists(__DIR__ . '/../../../datamodels/'.$scorm->version.'.js.php')) {
    include_once(__DIR__ . '/../../../datamodels/'.$scorm->version.'.js.php');
} else {
    include_once(__DIR__ . '/../../../datamodels/scorm_12.js.php');
}
// Set the start time of this SCO.
scorm_insert_track($user->id, $scorm->id, $scoid, $attempt, 'x.start.time', time());
?>

var errorCode = "0";
function underscore(str) {
    str = String(str).replace(/.N/g,".");
    return str.replace(/\./g,"__");
}