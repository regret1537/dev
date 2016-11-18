<?php
include('config/ZipConfig.php');

include(LIB_PATH . '/ClassZip.php');
include(LIB_PATH . '/ClassPassword.php');
include(LIB_PATH . '/ClassDate.php');
include(LIB_PATH . '/ClassDisp.php');

// Set zip file name
if (isset($argv[1]) === false) {
	$zip_name = ClassDate::now('Ymd');
} else {
	$zip_name = $argv[1];
}

// Generate a 8 bits password
$zip_password = ClassPassword::randomKey();

// Zip the files
$zip = new ClassZip();
$zip->zipDir(SRC_PATH, $zip_name, $zip_password);

// Chang to zip directory
$result = rename($zip_name, ZIP_PATH . '/' . $zip_name);
if ($result === false) {
	ClassDisp::dispString('Move zip file failed');
}

// Display the messages
ClassDisp::dispString('Zip: ' . $zip_name);
ClassDisp::dispString('Password: ' . $zip_password);
ClassDisp::dispArray($zip->getOutput());

