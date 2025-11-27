<?php

$rootDir = dirname(dirname(dirname(__DIR__)));
$envFile = $rootDir . DIRECTORY_SEPARATOR . '.env';

if (file_exists($envFile)) {
	$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		$line = trim($line);
		if ($line === '' || strpos($line, '#') === 0) {
			continue;
		}
		[$name, $value] = array_pad(explode('=', $line, 2), 2, '');
		$name = trim($name);
		$value = trim($value, " \t\n\r\0\x0B\"'");
		if ($name === '') {
			continue;
		}
		if (!array_key_exists($name, $_ENV)) {
			putenv("$name=$value");
			$_ENV[$name] = $value;
			$_SERVER[$name] = $value;
		}
	}
}

define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'hms');

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Gemini API configuration
if (!defined('GEMINI_API_KEY')) {
	define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');
}
if (!defined('GEMINI_MODEL')) {
	define('GEMINI_MODEL', getenv('GEMINI_MODEL') ?: 'gemini-2.5-flash-latest');
}
?>