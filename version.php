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
 * Version information for local_s3logs
 *
 * @package     local
 * @subpackage  s3logs
 * @author      Marcus Boon<marcus@catalyst-au.net>
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2021042700;     // The current plugin version (Date: YYYYMMDDXX).
$plugin->release   = 2021042700;     // Same as version.
$plugin->requires  = 2013111811;     // Requires Moodle 2.6.11 and above.
$plugin->component = 'local_s3logs'; // Internal plugin name.
$plugin->maturity  = MATURITY_ALPHA; // Not suitable for Production environments.
$plugin->dependencies = array(
    'local_aws' => 2017030100        // Depends on local_aws for S3 connections.
);
