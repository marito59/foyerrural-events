<?php
function attendees_list_page ()	{
    global $wpdb;
    $tableprefix = $wpdb->prefix . "fr_";
	$debug = false;  
      
	// $sql = "SELECT\n"
    // . $tableprefix."activite.date, ".$tableprefix."activite.nom AS activite_nom, TIME_FORMAT(".$tableprefix."occurrence_activite.heure_debut, '%H:%i') AS heure_debut, TIME_FORMAT(".$tableprefix."occurrence_activite.heure_fin, '%H:%i') AS heure_fin,\n"
    // . " concat(".$tableprefix."occurrence_personne.prenom, \" \", ".$tableprefix."occurrence_personne.nom) AS participant_nom, \n"
    // . $tableprefix."occurrence_personne.tel_fixe, ".$tableprefix."occurrence_personne.tel_mobile, ".$tableprefix."occurrence_personne.email, ".$tableprefix."occurrence_personne.agree, \n"
    // . $tableprefix."occurrence_personne.change_date\n"
    // . "FROM ".$tableprefix."activite\n"
    // . "LEFT JOIN ".$tableprefix."occurrence_activite ON ".$tableprefix."occurrence_activite.id_activite = ".$tableprefix."activite.ID\n"
    // . "LEFT JOIN ".$tableprefix."occurrence_personne ON id_occurrence = ".$tableprefix."occurrence_activite.ID\n"
    // . "ORDER BY ".$tableprefix."activite.date, ".$tableprefix."activite.order, ".$tableprefix."occurrence_activite.heure_debut, wp_fr_occurrence_personne.change_date";
	$sql = "SELECT"
        . " DATE_FORMAT(". $tableprefix."activite.date, '%d/%m/%Y') AS activite_date, ".$tableprefix."activite.nom AS activite_nom, TIME_FORMAT(".$tableprefix."occurrence_activite.heure_debut, '%H:%i') AS heure_debut, TIME_FORMAT(".$tableprefix."occurrence_activite.heure_fin, '%H:%i') AS heure_fin,"
        . " concat(".$tableprefix."occurrence_personne.prenom, \" \", ".$tableprefix."occurrence_personne.nom) AS participant_nom,"
        . $tableprefix."occurrence_personne.tel_fixe, ".$tableprefix."occurrence_personne.tel_mobile, ".$tableprefix."occurrence_personne.email, ".$tableprefix."occurrence_personne.agree,"
        . " DATE_FORMAT(". $tableprefix."occurrence_personne.change_date, '%d/%m/%Y %H:%i') AS change_date"
        . " FROM ".$tableprefix."activite"
        . " LEFT JOIN ".$tableprefix."occurrence_activite ON ".$tableprefix."occurrence_activite.id_activite = ".$tableprefix."activite.ID"
        . " LEFT JOIN ".$tableprefix."occurrence_personne ON id_occurrence = ".$tableprefix."occurrence_activite.ID"
        . " ORDER BY ".$tableprefix."activite.date, ".$tableprefix."activite.order, ".$tableprefix."occurrence_activite.heure_debut, ".$tableprefix."occurrence_personne.change_date";

    if ($debug) $wpdb->show_errors($debug);	
	$results = $wpdb->get_results( $sql );
    
    if ($debug) echo $wpdb->print_error();
?>
<h2>Liste des participants</h2>
<p><a href="<?php echo wp_nonce_url(plugin_dir_url( __FILE__ ) . 'save-data.php', 'fr_save_data', 'fr_nonce_field');?>" class="button secondary" target="_blank">Télécharger au format CSV</a></p>

    <table id='participants_list'>
        <tr>
            <th>Date</th><th>Activit&eacute;</th><th>Heure</th><th>Nom</th><th>Fixe</th><th>Mobile</th><th>Email</th><th>Date enregistrement</th>
        </tr>
<?php
    $row = false;
    $prev_date = "";
    $prev_activite = "";
    
	foreach ( $results as $result ) 
	{
        if ($debug) echo var_dump($result) . "\n";
        $ladate = $result->activite_date;
        if ($prev_date == $ladate) {
            $ladate = "&nbsp;";
            $line = false;
        } else {
            $prev_date = $ladate;
             $line = true;
       }
        if ($prev_activite == $result->activite_nom) {
            $activite = "&nbsp;";
        } else {
            $prev_activite = $result->activite_nom;
            $activite = $result->activite_nom;
        }
        $date = $result->change_date; 
        if ($date) {
            $date_enreg = $date;
        } else {
            $date_enreg = "";
        }
        
?>
        <tr class='<?php echo ($row?"odd ":"even ");$row = !$row;echo ($line?"sep ":" ");?>'>
            <td><?php echo $ladate; ?></td>
            <td><?php echo $activite; ?></td>
            <td><?php echo $result->heure_debut . "-" . $result->heure_fin; ?></td>
            <td><?php echo $result->participant_nom ?></td>
            <td><?php echo $result->tel_fixe; ?></td>
            <td><?php echo $result->tel_mobile; ?></td>
            <td><?php echo $result->email; ?></td>
            <td><?php echo $date_enreg ?></td>
        </tr>
<?php        
    }
?>
    </table>
<?php
}
?>