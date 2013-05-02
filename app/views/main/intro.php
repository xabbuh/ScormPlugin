<?php
$id = $learningUnit["id"];

if (!empty($id)) {
    // TODO: check for replacements
//    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
//        print_error('coursemisconf');
//    }
//    if (! $scorm = $DB->get_record("scorm", array("id"=>$cm->instance))) {
//        print_error('invalidcoursemodule');
//    }
} else if (!empty($a)) {
    if (! $scorm = $DB->get_record("scorm", array("id"=>$a))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$scorm->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('missingparameter');
}

$forcejs = get_config('scorm', 'forcejavascript');
if (!empty($forcejs)) {
    $PAGE->add_body_class('forcejavascript');
}

if (!empty($scorm->popup)) {
    $PAGE->requires->data_for_js('scormplayerdata', Array('cwidth'=>$scorm->width,
        'cheight'=>$scorm->height,
        'popupoptions' => $scorm->options), true);
    $PAGE->requires->js('/mod/scorm/view.js', true);
}

$strscorms = get_string("modulenameplural", "scorm");
$strscorm  = get_string("modulename", "scorm");

// TODO
//$shortname = format_string($course->shortname, true, array('context' => $context));
//$pagetitle = strip_tags($shortname.': '.format_string($scorm->name));

if (!empty($action) && confirm_sesskey() && has_capability('mod/scorm:deleteownresponses', $contextmodule)) {
    if ($action == 'delete') {
        $confirmurl = new moodle_url($PAGE->url, array('action'=>'deleteconfirm'));
        echo $OUTPUT->confirm(get_string('deleteuserattemptcheck', 'scorm'), $confirmurl, $PAGE->url);
        exit;
    } else if ($action == 'deleteconfirm') {
        //delete this users attempts.
        $DB->delete_records('scorm_scoes_track', array('userid' => $USER->id, 'scormid' => $scorm->id));
        scorm_update_grades($scorm, $USER->id, true);
        echo $OUTPUT->notification(get_string('scormresponsedeleted', 'scorm'), 'notifysuccess');
    }
}

$currenttab = 'info';
// TODO: Übersicht
//require($CFG->dirroot . '/mod/scorm/tabs.php');

// Print the main part of the page
// TODO: heading
//echo $OUTPUT->heading(format_string($scorm->name));

// TODO: displayattemptstatus is never written
$attemptstatus = '';
if (true || $scorm->displayattemptstatus == 1) {
    $attemptstatus = scorm_get_attempt_status($USER, $scorm, $cm);
}
echo "<div>";
//echo format_module_intro('scorm', $scorm, $cm->id);
echo $attemptstatus;
echo "</div>";

$scormopen = true;
$timenow = time();
if (!empty($scorm->timeopen) && $scorm->timeopen > $timenow) {
    echo $OUTPUT->box(get_string("notopenyet", "scorm", userdate($scorm->timeopen)), "generalbox boxaligncenter");
    $scormopen = false;
}
if (!empty($scorm->timeclose) && $timenow > $scorm->timeclose) {
    echo $OUTPUT->box(get_string("expired", "scorm", userdate($scorm->timeclose)), "generalbox boxaligncenter");
    $scormopen = false;
}
if ($scormopen) {
    scorm_view_display(
        $USER,
        $scorm,
        PluginEngine::getUrl($GLOBALS["plugin"], array(), "main/player/{$learningUnit["id"]}"),
        $cm
    );
}
if (!empty($forcejs)) {
    echo $OUTPUT->box(get_string("forcejavascriptmessage", "scorm"), "generalbox boxaligncenter forcejavascriptmessage");
}

if (!empty($scorm->popup)) {
    $PAGE->requires->js_init_call('M.mod_scormform.init');
}
