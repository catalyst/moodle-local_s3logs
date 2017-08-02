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
 * Task that archives the logs and pushes to S3
 *
 * @package     local
 * @subpackage  s3logs
 * @author      Marcus Boon<marcus@catalyst-au.net>
 */

namespace local_s3logs\task;

defined('MOODLE_INTERNAL') || die();

use local_s3logs\client\s3_client;

require_once(__DIR__ . '/../../lib.php');

class archive extends \core_task\scheduled_task {

    /**
     * Get task name
     */
    public function get_name() {
        return get_string('archive_task', 'local_s3logs');
    }

    /**
     * Execute task.
     */
    public function execute() {
        global $DB;

        // Instantiate an S3 client.
        $config = get_local_s3logs_config();
        $s3client = new s3_client($config);

        // Find logs, as long as they are more than maximumage we archive them.
        $maxage = 60 * 60 * 24 * 30 * $config->maximumage; // We standardise on a month having 30 days.
        $threshold = time() - $maxage;

        // We process about a months worth of logs in 1000 rows of data.
        $sql = "SELECT *
                  FROM {logstore_standard_log}
              ORDER BY timecreated ASC LIMIT 1000";
        $logs = $DB->get_records_sql($sql);

        // Package and push logs.

        // We need a way to track which rows we have processed and delete them from the logs.
        $processedids = array();
        $filename = '';
        $datastring = '';

        foreach ($logs as $id => $data) {

            // Process the logs while timecreated is more than the threshold.
            while ($data->timecreated > $threshold) {

                $columns = array();
                $values = array();

                foreach ((array)$data as $key => $value) {
                    $columns[] = $key;
                    $values[]  = $value;
                }

                // Add this to the array of processed ids.
                $processedids[] = $id;

                // Set the filename.
                $currentfilename = date('Ymd', $timecreated);

                if (empty($filename) || $currentfilename == $filename) {

                    // Set the filename to be the current file name.
                    $filename = $currentfilename;


                    $datastring .= "INSERT INTO {logstore_standard_log} (" . implode(',', $columns) . ") values (" . implode(',', $values) . ");" . PHP_EOL;

                } else if ($currentfilename != $filename) {

                    // New day of logs.
                    $localpath = "/tmp/{$filename}";
                    file_put_contents($localpath, $datastring);

                    // Push the file to S3.
                    $externalpath = $s3client->get_fullpath($filename);
                    copy($localpath, $externalpath);

                    // Set datastring to current values and set the filename to be the new filename.
                    $datastring = "INSERT INTO {logstore_standard_log} (" . implode(',', $columns) . ") values (" . implode(',', $values) . ");" . PHP_EOL;
                    $filename = $currentfilename;
                }
            }
        }

        // Delete the processed records from the log table.
        $todelete = implode(',', $processedids);

        $truncatesql = "DELETE FROM {logstore_standard_log}
        WHERE id IN ({$todelete})";
        $DB->execute($truncatesql);
    }
}
