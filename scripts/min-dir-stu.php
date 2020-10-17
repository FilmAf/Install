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
	// Director
	// ---------------------------------------------------------------------------------
	echo 'Director '; ob_flush(); flush();

	$ss = "DROP TABLE IF EXISTS search_director_tmp";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "TRUNCATE TABLE search_director_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "SELECT distinct director FROM dvd";
	$rr = CSql::query($ss, 0,__FILE__,__LINE__);

	$k  = 0;
	if ( $rr )
	{
		while ( $ln = CSql::fetch($rr) )
		{
			$x = explode(',', $ln['director']);
			$n = count($x);
			for ( $i = 0 ; $i < $n ; $i++ )
			{
				$s_name = trim($x[$i]);
				if ( $s_name )
				{
					$s_nocase = dvdaf_translatestring($s_name, DVDAF_SEARCH,0);
					if ( $s_nocase ) CSql::query_and_free("INSERT INTO search_director_new (nocase, name) VALUES('/ {$s_nocase} /','{$s_name}')", CSql_IGNORE_ERROR,__FILE__,__LINE__);
					if ( ++$k % 1000 == 0 ) echo '.'; ob_flush(); flush();
				}
			}
		}
	}
	CSql::free($rr);

	$ss = "RENAME TABLE search_director TO search_director_tmp, ".
					   "search_director_new TO search_director, ".
					   "search_director_tmp TO search_director_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "TRUNCATE TABLE search_director_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "OPTIMIZE TABLE search_director";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	echo "\nDone.\n\n"; ob_flush(); flush();

	// ---------------------------------------------------------------------------------
	// Publisher
	// ---------------------------------------------------------------------------------
	echo 'Publisher '; ob_flush(); flush();
	
	$ss = "DROP TABLE IF EXISTS search_publisher_tmp";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "TRUNCATE TABLE search_publisher_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "SELECT distinct publisher FROM dvd";
	$rr = CSql::query($ss, 0,__FILE__,__LINE__);

	$k  = 0;
	if ( $rr )
	{
		while ( $ln = CSql::fetch($rr) )
		{
			$x = explode(',', $ln['publisher']);
			$n = count($x);
			for ( $i = 0 ; $i < $n ; $i++ )
			{
				$s_name = trim($x[$i]);
				if ( $s_name )
				{
					$s_nocase = dvdaf_translatestring($s_name, DVDAF_SEARCH,0);
					if ( $s_nocase ) CSql::query_and_free("INSERT INTO search_publisher_new (nocase, name) VALUES('/ {$s_nocase} /','{$s_name}')", CSql_IGNORE_ERROR,__FILE__,__LINE__);
					if ( ++$k % 1000 == 0 ) echo '.'; ob_flush(); flush();
				}
			}
		}
	}
	CSql::free($rr);

	$ss = "RENAME TABLE search_publisher TO search_publisher_tmp, ".
					   "search_publisher_new TO search_publisher, ".
					   "search_publisher_tmp TO search_publisher_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "TRUNCATE TABLE search_publisher_new";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	$ss = "OPTIMIZE TABLE search_publisher";
	CSql::query_and_free($ss,0,__FILE__,__LINE__);

	echo "\nDone.\n\n"; ob_flush(); flush();
}

main();

?>

