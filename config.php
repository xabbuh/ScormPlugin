<?php
define('MOODLE_INTERNAL', true);
$plugin = PluginEngine::getPlugin("ScormPlugin");
/**
* @var StudipPlugin $plugin
*/

global $CFG;
$CFG = new stdClass();
$CFG->libdir = __DIR__."/lib";
$CFG->yui2version = "2.9.0";
$CFG->wwwroot = $plugin ? $plugin->getPluginURL() : '';
$CFG->plugin = $plugin;
