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
 * s3logs settings form
 *
 * @package     local
 * @subpackage  s3logs
 * @author      Marcus Boon<marcus@catalyst-au.net>
 */

namespace local_s3logs\form;

defined('MOODLE_INTERNAL') || die();

use local_s3logs\client\s3_client;

require_once($CFG->libdir . '/formslib.php');

class settings_form extends \moodleform {

	/**
	 * {@inheritDoc}
	 * @see moodleform::definition()
	 */
	public function definition() {
		global $OUTPUT;

		$mform = $this->_form;
		$config = $this->_customdata['config'];

		$mform = $this->define_general_section($mform, $config);
		$mform = $this->define_archive_settings_section($mform, $config);
		$mform = $this->define_amazon_s3_section($mform, $config);

		foreach ($config as $key => $value) {

			$mform->setDefault($key, $value);
		}

		$this->add_action_buttons();
	}

	public function define_general_section($mform, $config) {

		$mform->addElement('header', 'generalheader', get_string('settings:generalheader', 'local_s3logs'));
		$mform->setExpanded('generalheader');

		$mform->addElement('advcheckbox', 'enabletasks', get_string('settings:enabletasks', 'local_s3logs'));
		$mform->addHelpButton('enabletasks', 'settings:enabletasks', 'local_s3logs');

        $mform->addElement('duration', 'maxtaskruntime', get_string('settings:maxtaskruntime', 'local_s3logs'));
        $mform->addHelpButton('maxtaskruntime', 'settings:maxtaskruntime', 'local_s3logs');
        $mform->disabledIf('maxtaskruntime', 'enabletasks');
        $mform->setType('maxtaskruntime', PARAM_INT);

		return $mform;
	}

    public function define_archive_settings_section($mform, $config) {

        $mform->addElement('header', 'archivesettingsheader', get_string('settings:archivesettingsheader', 'local_s3logs'));
        $mform->setExpanded('archivesettingsheader');

        $mform->addElement('text', 'maximumage', get_string('settings:maximumage', 'local_s3logs'));
        $mform->addHelpButton('maximumage', 'settings:maximumage', 'local_s3logs');
        $mform->setType('maximumage', PARAM_INT);

        $rotateoptions = array(
            '86400'   => 'daily',
            '604800'  => 'weekly',
        );
        $mform->addElement('select', 'rotate', get_string('settings:rotate', 'local_s3logs'), $rotateoptions);
        $mform->addHelpButton('rotate', 'settings:rotate', 'local_s3logs');

        return $mform;
    }

	public function define_amazon_s3_check($mform, $config) {
		global $OUTPUT;
		$connection = false;

		$client = new s3_client($config);
		$connection = $client->test_connection();

		if ($connection->success) {
			$mform->addElement('html', $OUTPUT->notification($connection->message, 'notifysuccess'));

			// Check permissions if we can connect.
			$permissions = $client->test_permissions();
			if ($permissions->success) {
				$mform->addElement('html', $OUTPUT->notification($permissions->messages[0], 'notifysuccess'));
			} else {
				foreach ($permissions->messages as $message) {
					$mform->addElement('html', $OUTPUT->notification($message, 'notifyproblem'));
				}
			}

		} else {
			$mform->addElement('html', $OUTPUT->notification($connection->message, 'notifyproblem'));
			$permissions = false;
		}

		return $mform;
	}

	public function define_amazon_s3_section($mform, $config) {

		$mform->addElement('header', 'awsheader', get_string('settings:awsheader', 'local_s3logs'));
		$mform->setExpanded('awsheader');

		$mform = $this->define_amazon_s3_check($mform, $config);

        $regionoptions = array(
            'us-east-1'      => 'us-east-1',
			'us-east-2'      => 'us-east-2',
			'us-west-1'      => 'us-west-1',
			'us-west-2'      => 'us-west-2',
			'ap-northeast-2' => 'ap-northeast-2',
			'ap-southeast-1' => 'ap-southeast-1',
			'ap-southeast-2' => 'ap-southeast-2',
			'ap-northeast-1' => 'ap-northeast-1',
			'eu-central-1'   => 'eu-central-1',
            'eu-west-1'      => 'eu-west-1'
        );

		$mform->addElement('text', 'key', get_string('settings:key', 'local_s3logs'));
		$mform->addHelpButton('key', 'settings:key', 'local_s3logs');
		$mform->setType("key", PARAM_TEXT);

		$mform->addElement('passwordunmask', 'secret', get_string('settings:secret', 'local_s3logs'), array('size' => 40));
		$mform->addHelpButton('secret', 'settings:secret', 'local_s3logs');
		$mform->setType("secret", PARAM_TEXT);

		$mform->addElement('text', 'bucket', get_string('settings:bucket', 'local_s3logs'));
		$mform->addHelpButton('bucket', 'settings:bucket', 'local_s3logs');
		$mform->setType("bucket", PARAM_TEXT);

		$mform->addElement('select', 'region', get_string('settings:region', 'local_s3logs'), $regionoptions);
		$mform->addHelpButton('region', 'settings:region', 'local_s3logs');

		return $mform;
	}
}
