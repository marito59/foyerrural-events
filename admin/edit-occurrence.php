<?php
function occurrence_edit_page() {		
        if (isset ($_GET['action'])) {
               if (!check_admin_referer('fr_edit_occurrence_action', 'fr_nonce_url_check')) {return;} 
        }
        
        global $wpdb;
	$tableprefix = $wpdb->prefix . "fr_";
	$debug = false;
        $id = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : -1;
        $activityid = $_REQUEST['ACTIVITY_ID'];
        $activityname = $_REQUEST['name'];
        $action = $_REQUEST['action'];

if ($action == 'edit' || $action == 'delete') {
        if ($debug) $wpdb->show_errors();		
	$query = 'SELECT ' . $tableprefix . 'occurrence_activite.ID AS activite_id, TIME_FORMAT(' . $tableprefix . 'occurrence_activite.heure_debut, "%H:%i") AS occurrence_debut,  
				TIME_FORMAT(' . $tableprefix . 'occurrence_activite.heure_fin, "%H:%i") AS occurrence_fin, ' . $tableprefix . 'occurrence_activite.nbre_participants AS occurrence_nbre
		FROM ' . $tableprefix . 'occurrence_activite 
		WHERE ID =' . $id . '
                ORDER BY '. $tableprefix . 'occurrence_activite.heure_debut';
	$result = $wpdb->get_row( $query );
        if ($debug) echo $wpdb->print_error();

       $heure_debut = $result->occurrence_debut;
       $heure_fin = $result->occurrence_fin;
       $nbre = $result->occurrence_nbre;

} else {
       $heure_debut = "";
       $heure_fin = "";
       $nbre = 1;
       $id =-1;
}
?>

<div class="wrap">
<h2>
<?php
        $readonlyclass = "";

        if ($action == 'edit') {
                echo "Modifier un horaire";
                $button = "Mettre à jour";
                
        } elseif ($action == "add") {
        	echo "Ajouter un horaire";
                $button = "Ajouter";
        } elseif ($action == "delete") {
        	echo "Supprimer un horaire";
                $button = "Confirmer la suppression";
                $readonlyclass = "readonly";
        }
?>     
</h2>
<h3> pour l'activité <?php echo $activityname;?></h3>	
<div class="form-wrap">
<form id="editactivity" method="post" action="?page=occurrences_list" class="validate">
        <?php wp_nonce_field('fr_occurrence_menu', 'fr_nonce_field'); ?>
        <input type="hidden" name="action" value="<?php echo $action;?>">
        <input type="hidden" name="ID" value="<?php echo $id;?>">
        <input type="hidden" name="ACTIVITY_ID" value="<?php echo $activityid;?>">
        <input type="hidden" name="name" value="<?php echo $activityname;?>">

        <div class="form-field form-required term-name-wrap">
        	<label for="occurrence_debut">Heure de début : </label>
        	<input name="occurrence_debut" id="occurrence_debut" type="time" value="<?php echo $heure_debut;?>" size="10" aria-required="true" <?php echo $readonlyclass;?>>
        	<p>L'heure de début de l'activité (au format hh:mm).</p>
        </div>
        <div class="form-field form-required term-name-wrap">
        	<label for="occurrence_fin">Heure de fin :</label>
        	<input name="occurrence_fin" id="occurrence_fin" type="time" value="<?php echo $heure_fin;?>" size="10" <?php echo $readonlyclass;?>>
        	<p>L'heure de fin de l'activité (au format hh:mm).</p>
        </div>
        <div class="form-field form-required term-ordre-wrap">
        	<label for="occurrence_nbre">Nombre de participants souhaités :</label>
        	<input name="occurrence_nbre" id="occurrence_nbre" type="number" value="<?php echo $nbre;?>" size="2" <?php echo $readonlyclass;?>>
        	<p>Nombre de personnes souhaités pour l'activité.</p>
        </div>        
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $button;?>"></p>
</form>
</div>
</div>
<?php
}
?>	