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
    echo "\nUpdating:\n".date("F j, Y, H:i:s")."\n"; ob_flush(); flush();
    if ( ($rr = CSql::query("SELECT dvd_id FROM dvd WHERE dvd_indexed_tm IS NULL or dvd_indexed_tm <= dvd_updated_tm ORDER BY dvd_id", 0,__FILE__,__LINE__)) )
//  if ( ($rr = CSql::query("SELECT dvd_id FROM dvd ORDER BY dvd_id", 0,__FILE__,__LINE__)) )
    {
		for ( $k = 1 ; $ln = CSql::fetch($rr) ; $k++ )
		{
			CSql::query_and_free("update dvd set dvd_indexed_tm = now() where dvd_id = {$ln['dvd_id']}", 0,__FILE__,__LINE__);
			CSql::query_and_free("call update_dvd_search_index({$ln['dvd_id']},1)", 0,__FILE__,__LINE__);
			if ( $k % 100 == 0 ) echo '.'; ob_flush(); flush();
		}
    }
    CSql::free($rr);
/*
	echo "\nOptimizing:\n".date("F j, Y, H:i:s")."\n"; ob_flush(); flush();
	CSql::query_and_free("optimize table search_all_1"		,0,__FILE__,__LINE__);
	CSql::query_and_free("optimize table search_all_2"		,0,__FILE__,__LINE__);
	CSql::query_and_free("optimize table stats_dvd_country"	,0,__FILE__,__LINE__);
	CSql::query_and_free("optimize table stats_dvd_dir"		,0,__FILE__,__LINE__);
	CSql::query_and_free("optimize table stats_dvd_language",0,__FILE__,__LINE__);
	CSql::query_and_free("optimize table stats_dvd_pub"		,0,__FILE__,__LINE__);
	CSql::query_and_free("optimize table stats_dvd_region"	,0,__FILE__,__LINE__);
*/
	echo "\nDone.\n".date("F j, Y, H:i:s")."\n"; ob_flush(); flush();
}

main();

?>

