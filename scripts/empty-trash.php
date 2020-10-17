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

function main()
{
    CSql::connect(__FILE__,__LINE__);
    CSql::query_and_free("DELETE FROM my_dvd WHERE folder = 'trash-can' and my_dvd_expire_tm <= now()", 0,__FILE__,__LINE__);
    CSql::query_and_free("DELETE FROM my_dvd_2 WHERE not exists (SELECT * FROM my_dvd WHERE my_dvd_2.dvd_id = my_dvd.dvd_id and my_dvd_2.user_id = my_dvd.user_id)", 0,__FILE__,__LINE__);
}

main();

?>

