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
 * local_s3logs tasks
 *
 * @package     local
 * @subpackage  s3logs
 * @author      Marcus Boon<marcus@catalyst-au.net>
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'local_s3logs\task\archive',
        'blocking'  => 0,
        'minute'    => '1',
        'hour '     => '30',
        'day'       => '*',
        'dayofweek' => '6',
        'month'     => '*'
    ),
);
