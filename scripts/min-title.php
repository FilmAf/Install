#!/usr/local/bin/php -c /etc/apache2/php-cli.ini

<?php
// vi: ft=php noet ai ts=4 sw=4 cindent

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

	// ---------------------------------------------------------------------------------
	// DVD Title
	// ---------------------------------------------------------------------------------
	$ss = "DROP TABLE IF EXISTS search_dvd_tmp";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "TRUNCATE TABLE search_dvd_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "SELECT dvd_title FROM dvd";
	$rr = CSql::query($ss, 0,__FILE__,__LINE__);

	$k  = 0;
	echo 'DVD Title '; ob_flush(); flush();
	if ( $rr )
	{
		while ( $ln = CSql::fetch($rr) )
		{
			$x = explode('<br />', str_replace(' & ', ' and ', str_replace(' &amp; ', ' and ', $ln['dvd_title'])));
			$n = count($x);

			if ( $n )
			{
				$s = strtolower($x[0]);
				if ( strpos($s, 'season') || strpos($s, 'series') || strpos($s, 'episode') )
					$n = 1;
			}

			for ( $i = 0 ; $i < $n ; $i++ )
			{
				$s_name = trim($x[$i]);
				if ( $s_name )
				{
					if ( $s_name{0} == '+' ) continue;
					if ( $s_name{0} == '-' ) $s_name = trim(substr($s_name,1));
					if ( $s_name{0} == '(' ) $s_name = trim(substr($s_name,1));
					$n_p1	= strpos($s_name, '-'); if ( $n_p1 === false ) $n_p1 = 1000;
					$n_p2	= strpos($s_name, '('); if ( $n_p2 !== false ) $n_p1 = min($n_p1, $n_p2);
					$n_p2	= strpos($s_name, ')'); if ( $n_p2 !== false ) $n_p1 = min($n_p1, $n_p2);
					$n_p2	= strpos($s_name, '+'); if ( $n_p2 !== false ) $n_p1 = min($n_p1, $n_p2);
					$n_p2	= strpos($s_name, ','); if ( $n_p2 !== false ) $n_p1 = min($n_p1, $n_p2);
					$n_p1	= min($n_p1, 100);
					$s_name	= trim(substr($s_name,0, $n_p1));
					if ( $s_name )
					{
						$s_nocase = trim(str_replace('  ', ' ', str_replace('  ', ' ', substr(dvdaf_translatestring($s_name, DVDAF_SEARCH,0),0,100))));
						if ( $s_nocase ) CSql::query_and_free("INSERT INTO search_dvd_new (nocase, name) VALUES('/ {$s_nocase} /','{$s_name}')", CSql_IGNORE_ERROR,__FILE__,__LINE__);
						if ( ++$k % 1000 == 0 ) echo '.'; ob_flush(); flush();
					}
				}
			}
		}
	}
	CSql::free($rr);

	$ss = "RENAME TABLE search_dvd TO search_dvd_tmp, ".
					   "search_dvd_new TO search_dvd, ".
					   "search_dvd_tmp TO search_dvd_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "TRUNCATE TABLE search_dvd_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "OPTIMIZE TABLE search_dvd";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	echo "\nDone.\n\n"; ob_flush(); flush();
}

main();

?>

