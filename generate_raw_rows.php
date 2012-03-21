<?php

// seed randomizer to gain determinism
srand(0);

// available characters
$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz -_0123456789';
$maxchar = strlen($chars) - 1;

// where to store the sql
$dir = dirname(__FILE__);
$normal = $dir.'/normal.sql';
$denormal = $dir.'/denormal.sql';
$hnorm = fopen($normal, 'w');
$hdenorm = fopen($denormal, 'w');
	
// generate me some sqls!

// rows
for ($assetid = 0; $assetid < 10000; $assetid++) {
	$name = gen_random_col(rand(1, 255));
	$short_name = gen_random_col(rand(1, 255));
	$description = gen_random_col(rand(0, 4096));
	$html = gen_random_col(rand(0, 4096));

	// normal form
	fwrite($hnorm, 
		"INSERT INTO nt_attr_norm (assetid, name, short_name, description, html) "
		."VALUES ('$assetid', '$name', '$short_name', '$description', '$html');\n"
	);
	
	// denormal form
	// we've hard coded:
	//   0 - name
	//   1 - short_name
	//   2 - description
	//   3 - html
	// in init.sql
	fwrite($hdenorm, 
		"INSERT INTO nt_attr_val (assetid, attrid, value) "
		."VALUES ('$assetid', 0, '$name');\n"
		."INSERT INTO nt_attr_val (assetid, attrid, value) "
		."VALUES ('$assetid', 1, '$short_name');\n"
		."INSERT INTO nt_attr_val (assetid, attrid, value) "
		."VALUES ('$assetid', 2, '$description');\n"
		."INSERT INTO nt_attr_val (assetid, attrid, value) "
		."VALUES ('$assetid', 3, '$html');\n"
	);
}

/**
 * Helper to generate random chars for input in to the table
 */
function gen_random_col($width) {
	global $chars, $maxchar;
	
	$str = '';
	for ($k = 0; $k < $width; $k++) {
		$str .= $chars[rand(0,$maxchar)];
	}
	return $str;
}