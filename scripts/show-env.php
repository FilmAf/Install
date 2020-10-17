#!/usr/local/bin/php -c /etc/apache2/php-cli.ini

<?php
// vi: ft=php noet ai ts=8 sw=4 cindent

define('HOST_UNKNOWN'		 , 0);
define('HOST_FILMAF_COM'	 , 1);
define('HOST_FILMAF_EDU'	 , 2);

$gn_host = HOST_FILMAF_COM;
$gs_root = '/var/www/html';
date_default_timezone_set( $gn_host != HOST_FILMAF_COM ? 'America/New_York' : 'America/Chicago');

require '/var/www/html/lib/CTrace.php';
require '/var/www/html/lib/CSqlMysql.php';

echo "_SERVER\n";
print_r($_SERVER);
// echo "\n_GET\n";
// print_r($_GET);
// echo "\n_POST\n";
// print_r($_POST);
// echo "\n_FILES\n";
// print_r($_FILES);
// echo "\n_REQUEST\n";
// print_r($_REQUEST);
// echo "\n_SESSION\n";
// print_r($_SESSION);
echo "\n_ENV\n";
print_r($_ENV);
// echo "\n_COOKIE\n";
// print_r($_COOKIE);

?>

