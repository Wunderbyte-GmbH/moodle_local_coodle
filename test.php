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
 * @package    local_coodle
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use core_files\archive_writer;
use ZipArchive;
require_once('../../config.php');

$context = \context_system::instance();
$PAGE->set_context($context);

require_login();

// TODO delete before production.

$PAGE->set_pagelayout('standard');
$title = "COOdLe Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);


echo $OUTPUT->header();

echo '  <input type="color" id="head" name="head" value="#e66465" />';
// // Create a new ZipArchive object

// $pathtofileinzip = '/some/made/up/name.txt';

// // $zipwriter = archive_writer::get_file_writer('test.zip', archive_writer::ZIP_WRITER);
// // $zipwriter->add_file_from_stored_file($pathtofileinzip, $storedfile);
// // $zipwriter->finish();

// // $zip = new ZipArchive();

// // Specify the name and path of the output zip file.
// // Create a new ZipArchive object.
// $zip = new ZipArchive();

// // Create a temporary file to hold the zip archive.
// $tempfile = tempnam(sys_get_temp_dir(), 'zip');

// // Open the temporary file for writing.
// if ($zip->open($tempfile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
//     // Array of files to add to the zip archive.
//     $files = [
//         '/path/to/file1.txt',
//         '/path/to/file2.jpg',
//         '/path/to/file3.pdf',
//     ];

//     // Add each file to the zip archive.
//     foreach ($files as $file) {
//         // Read the file contents.
//         $filecontents = file_get_contents($file);

//         // Add the file to the zip archive with a custom name (optional).
//         $zip->addFromString(basename($file), $filecontents);
//     }

//     // Close the zip archive.
//     $zip->close();

//     // Set appropriate headers for the download.
//     header('Content-Type: application/zip');
//     header('Content-Disposition: attachment; filename="archive.zip"');
//     header('Content-Length: ' . filesize($tempfile));

//     // Read and output the zip file.
//     readfile($tempfile);

//     // Delete the temporary file.
//     unlink($tempfile);
// } else {
//     // Failed to create the zip archive.
//     echo 'Failed to create the zip file.';
// }
