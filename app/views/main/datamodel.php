<?php
$plugin = $GLOBALS["plugin"];
/**
 * @var ScormPlugin $plugin
 */

if (!empty($id)) {
    $scorm = $plugin->getLearningUnitAsObject($id);
    if (!$scorm) {
        print_error('invalidcoursemodule');
    }
} else if (!empty($a)) {
    $scorm = $plugin->getLearningUnitAsObject($a);
    if (!$scorm) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('missingparameter');
}

if (!empty($scoid)) {
    $result = true;
    $request = null;
    foreach (data_submitted() as $element => $value) {
        $element = str_replace('__', '.', $element);
        if (substr($element, 0, 3) == 'cmi') {
            $netelement = preg_replace('/\.N(\d+)\./', "\.\$1\.", $element);
            $result = scorm_insert_track($user->id, $scorm->id, $scoid, $attempt, $element, $value, $scorm->forcecompleted) && $result;
        }
        if (substr($element, 0, 15) == 'adl.nav.request') {
            // SCORM 2004 Sequencing Request
            require_once(__DIR__.'/../../../datamodels/sequencinglib.php');

            $search = array('@continue@', '@previous@', '@\{target=(\S+)\}choice@', '@exit@', '@exitAll@', '@abandon@', '@abandonAll@');
            $replace = array('continue_', 'previous_', '\1', 'exit_', 'exitall_', 'abandon_', 'abandonall');
            $action = preg_replace($search, $replace, $value);

            if ($action != $value) {
                // Evaluating navigation request
                $valid = scorm_seq_overall ($scoid, $USER->id, $action, $attempt);
                $valid = 'true';

                // Set valid request
                $search = array('@continue@', '@previous@', '@\{target=(\S+)\}choice@');
                $replace = array('true', 'true', 'true');
                $matched = preg_replace($search, $replace, $value);
                if ($matched == 'true') {
                    $request = 'adl.nav.request_valid["'.$action.'"] = "'.$valid.'";';
                }
            }
        }
        // Log every datamodel update requested
        if (substr($element, 0, 15) == 'adl.nav.request' || substr($element, 0, 3) == 'cmi') {
            if (scorm_debugging($scorm)) {
                add_to_log($course->id, 'scorm', 'trk: scoid/'.$scoid.' at: '.$attempt, 'view.php?id='.$cm->id, "$element => $value", $cm->id);
            }
        }
    }
    if ($result) {
        echo "true\n0";
    } else {
        echo "false\n101";
    }
    if ($request != null) {
        echo "\n".$request;
    }
}
