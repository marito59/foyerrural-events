<?php
/* admin functions
 *
 */

/**
 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
 * 
 */
function plugin_activation( $network_wide ) {

	global $wpdb; 
	$frevents_db_version = defined( 'FOYERRURAL-EVENTS__VERSION' ) ? FOYERRURAL_EVENTS__VERSION : false;
	$tableprefix = $wpdb->prefix . "fr_";
	

	$charset_collate = '';
	if ( ! empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( ! empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";

	//Events table
	$sql_activites_table = "CREATE TABLE IF NOT EXISTS " . $tableprefix . "activite (
 		`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  		`nom` varchar(45) NOT NULL,
  		`date` datetime NOT NULL,
 		 `Camion` tinyint(1) NOT NULL,
  		PRIMARY KEY (`ID`)
		)" . $charset_collate . " ROW_FORMAT=COMPACT AUTO_INCREMENT=21" ;

	$sql_occurrence_table = "CREATE TABLE IF NOT EXISTS " . $tableprefix . "occurrence (
		  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `id_activite` int(10) unsigned NOT NULL,
		  `heure_debut` time NOT NULL,
		  `heure_fin` time NOT NULL,
		  `nbre_participants` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`ID`),
		  KEY `FK_id_activite_1` (`id_activite`)		
	  	)" . $charset_collate . " ROW_FORMAT=COMPACT AUTO_INCREMENT=8" ;
	
	$sql_personne_table = "CREATE TABLE IF NOT EXISTS " . $tableprefix . "occurrence_personne (
		 `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `nom` varchar(45) NOT NULL,
		  `prenom` varchar(45) NOT NULL,
		  `email` varchar(128) DEFAULT NULL,
		  `tel_mobile` varchar(20) DEFAULT NULL,
		  `tel_fixe` varchar(20) DEFAULT NULL,
		  `id_occurrence` int(10) unsigned NOT NULL,
		  `agree` tinyint(1) NOT NULL DEFAULT '0',
		  `change_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`ID`)
		)" . $charset_collate . " ROW_FORMAT=COMPACT AUTO_INCREMENT=10" ;
	
	

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql_activite_table);
	dbDelta($sql_occurrence_table);
	dbDelta($sql_personne_table);


	//Flush rewrite rules only on activation, and after CPT/CTs has been registered.
	flush_rewrite_rules();
}

/**
 * Deactivate routine
 *
 * Flushes rewrite rules. Don't clear cron jobs, as these won't be re-added.
 *
 *@since 1.5
 *@access private
 *@ignore
*/
function plugin_deactivate(){
	flush_rewrite_rules();
    }

?>