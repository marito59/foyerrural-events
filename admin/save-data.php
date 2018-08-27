<?php
define( 'SHORTINIT', true );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

/*if (!check_admin_referer('fr_save_data', 'fr_nonce_field')) {
	return;
}
*/

/*if ( !is_user_logged_in() ) {
	die('not allowed');
} */

    global $wpdb;
    $tableprefix = $wpdb->prefix . "fr_";
    $debug = false;  
    
    $noparticipants = 0;
    
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'noparticipants') {
        $header = "Date;Activité;Heure;Nbre participants\n";
        $format = "%s;%s;%s-%s;%d\n";
        $file = "activites.csv";
        $noparticipants = 1;
    } else {
        $header = "Date;Activité;Heure;Nom;Fixe;Mobile;Email;Date enregistrement\n";
        $format = "%s;%s;%s-%s;%s;=\"%s\";=\"%s\";%s;%s\n";
        $file = "participants.csv";
    }
    
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=' . $file);
echo "\xEF\xBB\xBF"; // UTF-8 BOM


      
	$sql = "SELECT"
        . " DATE_FORMAT(". $tableprefix."activite.date, '%d/%m/%Y') AS activite_date, ".$tableprefix."activite.nom AS activite_nom, TIME_FORMAT(".$tableprefix."occurrence_activite.heure_debut, '%H:%i') AS heure_debut, TIME_FORMAT(".$tableprefix."occurrence_activite.heure_fin, '%H:%i') AS heure_fin,"
        . $tableprefix."occurrence_activite.nbre_participants AS nbre_participants, concat(".$tableprefix."occurrence_personne.prenom, \" \", ".$tableprefix."occurrence_personne.nom) AS participant_nom,"
        . $tableprefix."occurrence_personne.tel_fixe, ".$tableprefix."occurrence_personne.tel_mobile, ".$tableprefix."occurrence_personne.email, ".$tableprefix."occurrence_personne.agree,"
        . " DATE_FORMAT(". $tableprefix."occurrence_personne.change_date, '%d/%m/%Y %H:%i') AS change_date"
        . " FROM ".$tableprefix."activite"
        . " LEFT JOIN ".$tableprefix."occurrence_activite ON ".$tableprefix."occurrence_activite.id_activite = ".$tableprefix."activite.ID"
        . " LEFT JOIN ".$tableprefix."occurrence_personne ON id_occurrence = ".$tableprefix."occurrence_activite.ID"
        . " ORDER BY ".$tableprefix."activite.date, ".$tableprefix."activite.order, ".$tableprefix."occurrence_activite.heure_debut,".$tableprefix."occurrence_personne.change_date";

    if ($debug) $wpdb->show_errors($debug);	
	$results = $wpdb->get_results( $sql );
    
    if ($debug) echo $wpdb->print_error();
    
    // header
    echo $header;

    $row = false;
    $prev_date = "";
    $prev_activite = "";
    
	foreach ( $results as $result ) 
	{
        if ($debug) echo var_dump($result);
               
        $ladate = $result->activite_date;
        if ($prev_date == $ladate) {
            $ladate = "";
            $line = false;
        } else {
            $prev_date = $ladate;
            $line = true;
       }
        if ($prev_activite == $result->activite_nom) {
            $activite = "";
        } else {
            $prev_activite = $result->activite_nom;
            $activite = $result->activite_nom;
        }
                
        if ($result->change_date) {
            $date_enreg = $result->change_date;
        } else {
            $date_enreg = "";
        }
        if ($noparticipants == 1) {
            echo sprintf($format, $ladate, $activite, $result->heure_debut, $result->heure_fin, $result->nbre_participants);
        } else {
            echo sprintf($format, $ladate, $activite, $result->heure_debut, $result->heure_fin, $result->participant_nom, $result->tel_fixe, $result->tel_mobile, $result->email, $date_enreg);
        }
  }
?>