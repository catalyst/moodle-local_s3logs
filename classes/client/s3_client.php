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
 * S3 client for Moodle to Amazon S3 Log archiver
 *
 * @package     local
 * @subpackage  s3logs
 * @author      Marcus Boon<marcus@catalyst-au.net>
 */

namespace local_s3logs\client;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/aws/sdk/aws-autoloader.php');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

define('AWS_API_VERSION', '2006-03-01');

define('AWS_CAN_READ_OBJECT', 0);
define('AWS_CAN_WRITE_OBJECT', 1);
define('AWS_CAN_DELETE_OBJECT', 2);

class s3_client implements object_client {

    protected $client;
    protected $bucket;

    public function __construct($config) {

        $this->bucket = $config->bucket;
        $this->set_client($config);
    }

    public function __sleep() {

        return array('bucket');
    }

    public function __wakeup() {
        // Don't store credentials in the client itself as it will be serialised.
        $config = get_local_s3logs_config();
        $this->set_client($config);
        $this->client->registerStreamWrapper();
    }

    public function set_client($config) {
        $settings = array(
            'region'      => $config->region,
            'version'     => AWS_API_VERSION
        );
        $usesdkcreds = $config->usesdkcreds;
        if (!$usesdkcreds) {
            $settings['credentials'] = array('key' => $config->key, 'secret' => $config->secret);
        }
        $this->client = S3Client::factory($settings);
    }

    public function register_stream_wrapper() {

        // Registers 's3://bucket' as a prefix for file actions.
        $this->client->registerStreamWrapper();
    }

    /**
     * Returns S3 file path to use with php file function.
     *
     * @param string $filename filename used as key in S3.
     *
     * @return string full path to S3 object.
     */
    public function get_fullpath($filename) {

        return "s3://{$this->bucket}/{$filename}";
    }

    /**
     * Tests connection to S3 and bucket since there is no check connection in the AWS API.
     * We use list buckets instead and check that the bucket is in the list.
     *
     * @return boolean true on success, false otherwise.
     */
    public function test_connection() {

        $connection = new \stdClass();
        $connection->success = true;
        $connection->message = '';

        try {

            $result = $this->client->headBucket(array('Bucket' => $this->bucket));
        } catch (S3Exception $e) {

            $connection->success = false;
            $details = $this->get_exception_details($e);
            $connection->message = get_string('settings:connectionfailure', 'local_s3logs') . $details;
        }

        return $connection;
    }

    /**
     * Tests connection to S3 and bucket since there is no check connection in the AWS API.
     * We use list buckets instead and check the bucket is in the list.
     *
     * @return boolean true on success, false otherwise.
     */
    public function test_permissions() {

        $permissions = new \stdClass();
        $permissions->success = true;
        $permissions->messages = array();

        // Check write permissions.
        try {
            $result = $this->client->putObject(
                array('Bucket' => $this->bucket, 'Key' => 'permissions_check_file', 'Body' => 'test content')
            );
        } catch (S3Exception $e) {

            $permissions->success = false;
            $details = $this->get_exception_details($e);
            $permissions->messages[] = get_string('settings:writefailure', 'local_s3logs') . $details;
        }

        // Check read permissions.
        try {

            $result = $this->client->getObject(
                array('Bucket' => $this->bucket, 'Key' => 'permissions_check_file')
            );
        } catch (S3Exception $e) {

            $errorcode = $e->getAwsErrorCode();

            // Write could have failed.
            if ($errorcode !== 'NoSuchKey') {

                $permissions->success = false;
                $details = $this->get_exception_details($e);
                $permissions->messages[] = get_string('settings:readfailure', 'local_s3logs') . $details;
            }
        }

        // Check delete permissions.
        try {

            $result = $this->client->deleteObject(
                array('Bucket' => $this->bucket, 'Key' => 'permissions_check_file')
            );

            $permissions->messages[] = get_string('settings:deletesuccess', 'local_s3logs');
            $permissions->success = false;
        } catch (S3Exception $e) {

            $errorcode = $e->getAwsErrorCode();

            // Something else went wrong.
            if ($errorcode !== 'AccessDenied') {
                $details = $this->get_exception_details($e);
                $permissions->messages[] = get_string('settings:deleteerror', 'local_s3logs') . $details;
            }
        }

        if ($permissions->success) {
            $permissions->messages[] = get_string('settings:permissioncheckpassed', 'local_s3logs');
        }

        return $permissions;
    }

    protected function get_exception_details($exception) {
        $message = $exception->getMessage();

        if (get_class($exception) !== 'S3Exception') {
            return "Not a S3 exception : $message";
        }

        $errorcode = $exception->getAwsErrorCode();

        $details = ' ';

        if ($message) {
            $details .= "ERROR MSG: " . $message . "\n";
        }

        if ($errorcode) {
            $details .= "ERROR CODE: " . $errorcode . "\n";
        }

        return $details;
    }
}
