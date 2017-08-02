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
 * S3 Log archiver status page
 *
 * @package     local
 * @subpackage  s3logs
 * @author      Marcus Boon<marcus@catalyst-au.net>
 */

use local_s3logs\form\settings_form;

require_once(__DIR__ . '/../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('local_s3logs_settings');

$output = $PAGE->get_renderer('local_s3logs');

$config = get_local_s3logs_config();

$form = new settings_form(null, array('config' => $config));

if ($data = $form->get_data()) {

    set_local_s3logs_data($data);

    redirect(new moodle_url('/local/s3logs/index.php'));
}

echo $output->header();
echo $output->heading(get_string('pluginname', 'local_s3logs'));
$form->display();
echo $output->footer();
