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
 * English language strings for local_s3logs
 *
 * @package     local
 * @subpackage  s3logs
 * @author      Marcus Boon<marcus@catalyst-au.net>
 */

$string['pluginname'] = 'Moodle to Amazon S3 Log Archiver';
$string['settings:archivesettingsheader'] = 'Log Archive Settings';
$string['settings:awsheader'] = 'Amazon S3 Settings';
$string['settings:bucket'] = 'Bucket';
$string['settings:bucket_help'] = 'Amazon S3 bucket to store files in.';
$string['settings:connectionfailure'] = 'Could not establish connection to the AWS S3 bucket.';
$string['settings:connectionsuccess'] = 'Could establish connection to the AWS S3 bucket.';
$string['settings:deletesuccess'] = 'Could delete object from the S3 bucket - It is not recommended for the AWS user to have delete permissions. ';
$string['settings:enabletasks'] = 'Enable log archive tasks';
$string['settings:enabletasks_help'] = 'Enable or disable the log archiving system tasks that move logs after a certain threshold to Amazon S3';
$string['settings:generalheader'] = 'General Settings';
$string['settings:key'] = 'Key';
$string['settings:key_help'] = 'Amazon S3 key credential.';
$string['settings:maximumage'] = 'Maximum age of log entries (months)';
$string['settings:maximumage_help'] = 'Specifies the maximum age of log entriesi (in months) before the archiver starts archiving it to Amazon S3';
$string['settings:maxtaskruntime'] = 'Maximum log archive task runtime';
$string['settings:maxtaskruntime_help'] = 'Background tasks handle the archiving and truncating of the Moodle log table. This setting controlls the maximum runtime for all S3 logs related tasks.';
$string['settings:permissioncheckpassed'] = 'Permissions check passed.';
$string['settings:readfailure'] = 'Could not read object from the S3 bucket.';
$string['settings:region'] = 'region';
$string['settings:region_help'] = 'Amazon S3 API gateway region.';
$string['settings:rotate'] = 'Rotate';
$string['settings:rotate_help'] = 'Specifies how to group the Moodle logs before archiving it to Amazon S3';
$string['settings:secret'] = 'Secret';
$string['settings:secret_help'] = 'Amazon S3 secret credential.';
$string['settings:writefailure'] = 'Could not write object to the S3 bucket.';
