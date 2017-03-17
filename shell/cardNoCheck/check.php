<?php
// autoload
$libList = ['Disp', 'NcccCheck', 'PhpCheck'];
foreach ($libList as $name) {
	include('libs/' . $name . '.php');
}

$fileName = $argv[1];
$rows = file('files/' . $fileName, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

$result = [];
foreach ($rows as $row) {
	if (NcccCheck::check($row)) {
		$result[$row] = ' backup files';
		continue;
	}

	if (PhpCheck::check($row)) {
		$result[$row] = ' PHP remark';
		continue;
	}
	Disp::dispString($row . '  unknown');
}