<?php 

/**
 * @package   local_christest
 * @author    2023, Chrik_B
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/* 
 reading sources
 
 https://moodledev.io/docs/apis/commonfiles

 https://moodledev.io/docs/apis/commonfiles/version.php

 https://docs.moodle.org/dev/version.php 
 
*/


defined('MOODLE_INTERNAL') || die();

$plugin->version   = 20230206;         
$plugin->requires  = 2022112803;  // works:2016052300 //
$plugin->component = 'local_christest';
$plugin->cron      = 0;