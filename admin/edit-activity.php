<?php
function activity_edit_page() {		
        if (isset ($_GET['action'])) {
               if (!check_admin_referer('fr_edit_activity_action', 'fr_nonce_url_check')) {return;} 
        }
        
	global $wpdb;
	$tableprefix = $wpdb->prefix . "fr_";
	$debug = false;
        if (isset ($_REQUEST['ID'])) {
                $id = $_REQUEST['ID'];
        }
        $action = $_REQUEST['action'];

if ($action == 'edit' || $action == 'delete') {
        if ($debug) $wpdb->show_errors();		
	$query = 'SELECT ' . $tableprefix . 'activite.ID AS activite_id, ' . $tableprefix . 'activite.nom AS activite_name,  DATE_FORMAT(' . $tableprefix . 'activite.date, "%d/%m/%Y") AS activite_date,
				' . $tableprefix . 'activite.camion AS activite_truck, ' . $tableprefix . 'activite.order AS activite_order
		FROM ' . $tableprefix . 'activite 
		WHERE ID =' . $id;
	$result = $wpdb->get_row( $query );
        if ($debug) echo $wpdb->print_error();

       $nom = $result->activite_name;
       $date = $result->activite_date;
       $camion = $result->activite_truck;
       $ordre = $result->activite_order;
} else {
       $nom = "";
       $date = "";
       $camion = 0;
       $ordre = 1;
       $id =-1;
}
?>
<div class="wrap">
<h2>
<?php
        $readonlyclass = "";

        if ($action == 'edit') {
                echo "Modifier une activité";
                $button = "Mettre à jour";
                
        } elseif ($action == "add") {
        	echo "Ajouter une activité";
                $button = "Ajouter";
        } elseif ($action == "delete") {
        	echo "Supprimer une activité";
                $button = "Confirmer la suppression";
                $readonlyclass = "readonly";
        }
?>     
</h2>
	
<div class="form-wrap">
<form id="editactivity" method="post" action="?page=activity_menu" class="validate">
        <?php wp_nonce_field('fr_activity_menu', 'fr_nonce_field'); ?>
        <input type="hidden" name="action" value="<?php echo $action;?>">
        <input type="hidden" name="ID" value="<?php echo $id;?>">
        <div class="form-field form-required term-name-wrap">
        	<label for="activity-name">Activit&eacute; : </label>
        	<input name="activity-name" id="activity-name" type="text" value="<?php echo $nom;?>" size="40" aria-required="true" <?php echo $readonlyclass;?>>
        	<p>Le nom de l'activité.</p>
        </div>
        <div class="form-field form-required term-date-wrap">
        	<label for="activity-date">Date :</label>
        	<input name="activity-date" id="activity-date" type="text" value="<?php echo $date;?>" size="10" <?php echo $readonlyclass;?>>
         	<p>La date de l'évènement au format jj/mm/aaaa.</p>
        </div>
        <div class="form-field term-camion-wrap">
                <input type="checkbox" name="activity-truck" id="activity-truck" value="<?php echo $camion;?>" <?php echo $readonlyclass;?>>
        	<label for="activity-truck">Camion nécessaire ?</label>
        	<p>Si le camion est nécessaire, sélectionner cette option.</p>
        </div>
        <div class="form-field form-required term-ordre-wrap">
        	<label for="activity-order">Ordre d'apparition :</label>
        	<input name="activity-order" id="activity-order" type="number" value="<?php echo $ordre;?>" size="2" <?php echo $readonlyclass;?>>
        	<p>Ordre d'apparition de l'activité lorsqu'il y a plusieurs activités le même jour.</p>
        </div>        
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $button;?>"></p>
</form>
</div>
</div>
<script>
        jQuery(document).ready(function() {
                jQuery('#activity-date').datepicker({
                dateFormat : 'dd/mm/yy',
                })
        });
</script>
<?php
}
?>	