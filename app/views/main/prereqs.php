<?php
$plugin = $GLOBALS["plugin"];
/**
 * @var ScormPlugin $plugin
 */

if (!empty($a)) {
    $scorm = $plugin->getLearningUnitAsObject($a);
    if (!$scorm) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('missingparameter');
}

$scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe
if (!file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php')) {
    $scorm->version = 'scorm_12';
}

require_once($CFG->libdir.'/../datamodels/'.$scorm->version.'lib.php');

if (!empty($scoid)) {
    $result = true;
    $request = null;
    $result = scorm_get_toc($USER, $scorm, $cm->id, TOCJSLINK, $currentorg, $scoid, $mode, $attempt, true, false);
    echo $result->toc;
}
