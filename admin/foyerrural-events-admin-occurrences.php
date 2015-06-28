<?php
function occurrences_menu_page ()	{
	global $wpdb;
	$tableprefix = $wpdb->prefix . "fr_";
	$debug = false;
	
	if (isset($REQUEST['action'])) {
		if (!check_admin_referer('fr_occurrence_menu', 'fr_nonce_field')) {
			return;
		}
	}
	
    $id = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : -1;
    $activityid = isset($_REQUEST['ACTIVITY_ID']) ? $_REQUEST['ACTIVITY_ID'] : -1;
	$activityname = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";;
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
	$debut = isset($_REQUEST['occurrence_debut']) ? $_REQUEST['occurrence_debut'] : null;
	$fin = isset($_REQUEST['occurrence_fin']) ? $_REQUEST['occurrence_fin'] : null;
	$nbre = isset($_REQUEST['occurrence_nbre']) ? $_REQUEST['occurrence_nbre'] : 0;
	
	
	$format = 'H:i';
	$ledebut = DateTime::createFromFormat($format, $debut);
	$lafin = DateTime::createFromFormat($format, $fin);
	
	if ($debug) $wpdb->show_errors();

	
	if (($debut==null && $action != "list") || ($debut=="" && $action!="")) {
		$message = "<strong>Erreur</strong> : horaire de l'activité non définie.";
	} elseif ($action == "add") {
		$wpdb->insert( 
			$tableprefix . 'occurrence_activite', 
			array( 
				'heure_debut' => $ledebut->format('H:i'), 
				'heure_fin' => $lafin->format('H:i'), 
				'nbre_participants' => $nbre, 
				'id_activite' => $activityid, 
			), 
			array( 
				'%s', 
				'%s',
				'%d',
				'%d'
			) 
		);
		$message = "Horaire ajouté !";		
	} elseif ($action == "edit") {
		$wpdb->update( 
			$tableprefix . 'occurrence_activite', 
			array( 
				'heure_debut' => $ledebut->format('H:i'), 
				'heure_fin' => $lafin->format('H:i'), 
				'nbre_participants' => $nbre, 
				'id_activite' => $activityid, 
			), 
			array ( 'ID' => $id),
			array( 
				'%s', 
				'%s',
				'%d',
				'%d'
			) ,
			array ('%d')
		);
		$message = "Horaire modifié !";		
	} elseif ($action == 'delete')	{
		$wpdb->delete( $tableprefix . 'occurrence_activite', array( 'ID' => $id ) );
		$message = "Horaire supprimé !";
	}


		$query = 'SELECT ' . $tableprefix . 'activite.ID AS activite_id, ' . $tableprefix . 'activite.nom AS activite_name, 
					' . $tableprefix . 'occurrence_activite.ID AS occurrence_id, TIME_FORMAT(' . $tableprefix . 'occurrence_activite.heure_debut, "%H:%i") AS occurrence_debut,
					TIME_FORMAT(' . $tableprefix . 'occurrence_activite.heure_fin, "%H:%i") AS occurrence_fin, ' . $tableprefix . 'occurrence_activite.nbre_participants AS occurrence_nbre
			FROM ' . $tableprefix . 'activite 
			JOIN '.$tableprefix.'occurrence_activite ON '.$tableprefix.'occurrence_activite.id_activite = '.$tableprefix.'activite.ID
			WHERE '.$tableprefix.'occurrence_activite.id_activite='.$activityid.'
			ORDER BY ' . $tableprefix . 'activite.date, ' . $tableprefix . 'activite.order,' . $tableprefix . 'occurrence_activite.heure_debut';
		$results = $wpdb->get_results( $query );
		
if ($debug) echo $wpdb->print_error();

if ($wpdb->num_rows == 0) {
	$message = "Aucun horaire défini pour cette activité. <strong>Ajoutez un horaire.</strong>";
} 
if (isset($message)){
?>
<div class="updated"><p><?php echo $message;?></p></div>
<?php } ?>
<div class="wrap">
	<h2>Gestion des Horaire
<a href="<?php echo wp_nonce_url('?page=occurrence_edit&amp;action=add&amp;ACTIVITY_ID='.$activityid.'&amp;name='.$activityname, 'fr_edit_occurrence_action', 'fr_nonce_url_check');?>" class="add-new-h2">Nouvel horaire</a>
</h2>	
<h3>pour l'activité "<?php echo $activityname;?>"</h3>
<table class="wp-list-table widefat fixed striped tags">
	<thead>
	<tr>
		<th scope="col" id="name" class="manage-column column-name sortable desc" style="">
				<span>Heure début</span>
		</th>
		<th scope="col" id="date" class="manage-column column-date sortable desc" style="">
				<span>Heure fin</span>
		</th>
		<th scope="col" id="truck" class="manage-column column-truck bool sortable desc" style="">
				<span>Nbre de participants</span>
		</th>
		<th scope="col" id="ssid" class="manage-column column-ssid sortable desc" style="">ID</th>	
	</tr>
	</thead>

	<tbody id="the-list" data-wp-lists="list:tag">
<?php
	foreach ( $results as $result ) 
	{
		if ($debug) echo var_dump($result) ."\n";
		$id = $result->occurrence_id;

?>
		<tr id="tag-2">
			<td class="name column-name">
				<strong><a class="row-title" href="<?php echo wp_nonce_url('?page=occurrence_edit&amp;action=edit&amp;ID=' . $id. '&amp;ACTIVITY_ID='.$activityid.'&amp;name='.$activityname, 'fr_edit_occurrence_action', 'fr_nonce_url_check'); ?>" title="Edit <?php echo $result->occurrence_debut;?>">
					<?php echo $result->occurrence_debut;?></a></strong><br>
				<div class="row-actions">
					<span class="edit"><a href="<?php echo wp_nonce_url('?page=occurrence_edit&amp;action=edit&amp;ID=' . $id . '&amp;ACTIVITY_ID='.$activityid.'&amp;name='.$activityname, 'fr_edit_occurrence_action', 'fr_nonce_url_check'); ?>">Edit</a> | </span>
					<span class="delete"><a class="delete-tag" href="<?php echo wp_nonce_url('?page=occurrence_edit&amp;action=delete&amp;ID=' . $id . '&amp;ACTIVITY_ID='.$activityid.'&amp;name='.$activityname, 'fr_edit_occurrence_action', 'fr_nonce_url_check'); ?>">Delete</a> | </span>
			</td>
			<td class="truck column-fin"><?php echo $result->occurrence_fin;?></td>
			<td class="order column-nbre"><?php echo $result->occurrence_nbre;?></td>
			<td class="ssid column-ssid"><?php echo $id;?></td>
		</tr>
<?php } ?>
	</tbody>
	
	<tfoot>
<tr>
		<th scope="col" id="name" class="manage-column column-name sortable desc" style="">
				<span>Heure début</span>
		</th>
		<th scope="col" id="date" class="manage-column column-date sortable desc" style="">
				<span>Heure fin</span>
		</th>
		<th scope="col" id="truck" class="manage-column column-truck bool sortable desc" style="">
				<span>Nbre de participants</span>
		</th>
		<th scope="col" id="ssid" class="manage-column column-ssid sortable desc" style="">ID</th>	
	</tr>
	</tfoot>

</table>
</div>
<?php	
}
?>