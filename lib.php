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
 * S3 logs lib
 *
 * @package     local
 * @subpackage  s3logs
 * @author      Marcus Boon<marcus@catalyst-au.net>
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Helper function to set config for the plugin.
 *
 * @param stdClass $config Object containing the configuration.
 *
 * @return void
 */
function set_local_s3logs_config($config) {

    foreach ($config as $key => $value) {

        set_config($key, $value, 'local_s3logs');
    }
}

/**
 * Helper function to get existing config for the plugin.
 *
 * @return stdClass Object containing the configuration.
 */
function get_local_s3logs_config() {

    $config = new stdClass;
    $config->enabletasks = 0;
    $config->maxtaskruntime = 60;
    $config->maximumage = 18;
    $config->rotate = 86400;
    $config->usesdkcreds = false;
    $config->key = '';
    $config->secret = '';
    $config->bucket = '';
    $config->region = 'us-east-1';

    $storedconfig = get_config('local_s3logs');

    // Override the defaults if already set.
    foreach ($storedconfig as $key => $value) {

        $config->$key = $value;
    }
    return $config;
}

/**
 * Function to determine if the scheduled task should run.
 *
 * @return bool
 */
function local_s3logs_should_tasks_run() {

    $config = get_local_s3logs_config();

    if (isset($config->enabletasks) && $config->enabletasks) {

        return true;
    }

    return false;
}

/**
 * Legacy cron function.
 *
 * @return bool
 */
function local_s3logs_cron() {
    global $CFG;

    mtrace('RUNNING legacy cron for local_s3logs');

    if ($CFG->branch <= 26) {

        \local_s3logs\archiver::archive();
        mtrace('Moodle to Amazon S3 logs archiver successfully exectued!');
    }

    return true;
}
