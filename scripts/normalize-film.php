#!/usr/local/bin/php -c /etc/apache2/php-cli.ini

<?php
# vi: ft=php noet ai ts=4 sw=4 cindent

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

	$ss = "SELECT dvd_id FROM dvd WHERE media_type in ('F','S','L','E','N') and (".
//				 "publisher != '-' or ".
				 "country != ',us,' or ".
				 "region_mask != 1 or ".
				 "num_titles != 1 or ".
				 "num_disks != 1 or ".
				 "source != 'T' or ".
				 "dvd_rel_dd != '-' or ".
				 "dvd_oop_dd != '-' or ".
				 "list_price != 0 or ".
				 "sku != '-' or ".
				 "upc != '-' or ".
				 "asin != '-' or ".
				 "amz_country != '-')";
//	echo "$ss\n";

	$a = array();
    if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		while ( ($ln = CSql::fetch($rr)) )
			$a[] = $ln['dvd_id'];

	for ( $i = 0 ; $i < count($a) ; $i++ )
	{
		$n_dvd_id = $a[$i];

		$ss = "INSERT INTO dvd_hist ".
					"(dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, ".
					 "media_type, num_titles, num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, ".
					 "pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, dvd_updated_tm, dvd_updated_by, dvd_id_merged, ".
					 "last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id) ".
			  "SELECT dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, ".
					 "media_type, num_titles, num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, ".
					 "pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, dvd_updated_tm, dvd_updated_by, dvd_id_merged, ".
					 "last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id ".
				"FROM dvd a ".
			   "WHERE not exists (SELECT 1 FROM dvd_hist b WHERE a.dvd_id = b.dvd_id and a.version_id = b.version_id) ".
				 "and a.dvd_id = {$n_dvd_id} and media_type in ('F','S','L','E','N')";
		CSql::query_and_free($ss, 0,__FILE__,__LINE__);

		$ss = "UPDATE dvd ".
				 "SET country = ',us,', ".
					 "country_block = ',us,', ".
//					 "publisher = '-', ".
//					 "publisher_nocase = '-', ".
					 "region_mask = 1, ".
					 "num_titles = 1, ".
					 "num_disks = 1, ".
					 "source = 'T', ".
					 "dvd_rel_dd = '-', ".
					 "dvd_oop_dd = '-', ".
					 "list_price = 0, ".
					 "sku = '-', ".
					 "upc = '-', ".
					 "asin = '-', ".
					 "amz_country = '-', ".
					 "version_id = version_id + 1, ".
					 "dvd_updated_tm = now(), ".
					 "dvd_updated_by = 'ash', ".
					 "last_justify = 'Theatrical and broadcast auto adjustment' ".
			   "WHERE dvd_id = {$n_dvd_id} and media_type in ('F','S','L','E','N')";
		CSql::query_and_free($ss, 0,__FILE__,__LINE__);

		CSql::query_and_free("CALL update_dvd_search_index({$n_dvd_id},1)",0,__FILE__,__LINE__);
	}

	$ss = "UPDATE dvd ".
			 "SET rel_status = IF(film_rel_dd = '-', '-', IF(film_rel_dd < DATE_FORMAT(now(),'%Y%m%d'),'C','A')), ".
				 "best_price = 0, ".
				 "amz_rank = 9999999, ".
				 "collection_rank = 0 ".
		   "WHERE media_type in ('F','S','L','E','N') ".
			 "and (".
				  "rel_status != IF(film_rel_dd = '-', '-', IF(film_rel_dd < DATE_FORMAT(now(),'%Y%m%d'),'C','A')) or ".
				  "best_price != 0 or ".
				  "amz_rank != 9999999 or ".
				  "collection_rank != 0".
				 ")";
//	echo "$ss\n";
	CSql::query_and_free($ss, 0,__FILE__,__LINE__);
}

main();

?>
