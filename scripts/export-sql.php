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
    $ss = "SELECT dvd_id, dvd_title from dvd";
    fwrite(STDOUT, "$ss\n");

    $i = 0;
    CSql::connect(__FILE__,__LINE__);
    CSql::query_and_free("SET SESSION group_concat_max_len = 10240",0,__FILE__,__LINE__);
    if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
    {
	while ( ($ln = CSql::fetch($rr)) )
	{
	    $s = '';
	    foreach ( $ln as $val ) $s .= $val . "\t";
	    $i++;
	    fwrite(STDOUT, substr($s,0,-1) . "\n");
	}
	CSql::free($rr);
    }
    fwrite(STDOUT, "record count: $i\n");
}

main();

?>

