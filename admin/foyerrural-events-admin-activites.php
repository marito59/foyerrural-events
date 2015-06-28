<?php
function activites_menu_page ()	{
	global $wpdb;
	$tableprefix = $wpdb->prefix . "fr_";
	$debug = false;
	
	if (isset($REQUEST['action'])) {
		if (!check_admin_referer('fr_activity_menu', 'fr_nonce_field')) {
			return;
		}
	}
	
    $id = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : -1;
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
	
	$name  = isset($_REQUEST['activity-name']) ? wp_kses($_REQUEST['activity-name'], wp_kses_allowed_html('strip')) : null;
	$date  = isset($_REQUEST['activity-date']) ? $_REQUEST['activity-date'] : null;
	$truck = isset($_REQUEST['activity-truck']) ? $_REQUEST['activity-truck'] : 0;
	$order = isset($_REQUEST['activity-order']) ? $_REQUEST['activity-order'] : 1;
	
	$format = 'Y-m-d H:i:s';
	$ladate = DateTime::createFromFormat('d/m/Y', $date);

	if ($debug) $wpdb->show_errors();

	if ((!isset($name) && $action != "") || ($name=="" && $action!="")) {
		$message = "<strong>Erreur</strong> : nom de l'activité non définie.";
	} elseif ($action == "add") {
		$wpdb->insert( 
			$tableprefix . 'activite', 
			array( 
				'nom' => $name, 
				'date' => $ladate->format('Y-m-d H:i:s'), 
				'Camion' => $truck, 
				'order' => $order, 
			), 
			array( 
				'%s', 
				'%s',
				'%d',
				'%d'
			) 
		);
		$message = "Activité ajoutée !";		
	} elseif ($action == "edit") {
		$wpdb->update( 
			$tableprefix . 'activite', 
			array( 
				'nom' => $name, 
				'date' => $ladate->format('Y-m-d H:i:s'), 
				'Camion' => $truck, 
				'order' => $order, 
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
		$message = "Activité modifiée !";		
	} elseif ($action == 'delete')	{
		$wpdb->delete( $tableprefix . 'activite', array( 'ID' => $id ) );
		$message = "Activité supprimée !";
	}
		
		$query = 'SELECT ' . $tableprefix . 'activite.ID AS activite_id, ' . $tableprefix . 'activite.nom AS activite_name,  DATE_FORMAT(' . $tableprefix . 'activite.date, "%d/%m/%Y") AS activite_date,
					' . $tableprefix . 'activite.camion AS activite_truck, ' . $tableprefix . 'activite.order AS activite_order,
					(SELECT count(*) FROM ' . $tableprefix . 'occurrence_activite where id_activite=' . $tableprefix . 'activite.ID)  AS nbre_horaires
			FROM ' . $tableprefix . 'activite 
			ORDER BY ' . $tableprefix . 'activite.date, ' . $tableprefix . 'activite.order';
		$results = $wpdb->get_results( $query );
		
if ($debug) echo $wpdb->print_error();

if (isset($message)){
?>
<div class="updated"><p><?php echo $message;?></p></div>
<?php } ?>

<div class="wrap">
	<h2>Gestion des activités
<a href="<?php echo wp_nonce_url('?page=activity_edit&amp;action=add', 'fr_edit_activity_action', 'fr_nonce_url_check');?>" class="add-new-h2">Nouvelle activité</a>
</h2>	
<table class="wp-list-table widefat fixed striped tags">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1">Select All</label>
			<input id="cb-select-all-1" type="checkbox"></th>
		<th scope="col" id="name" class="manage-column column-name sortable desc" style="">
				<span>Nom</span>
		</th>
		<th scope="col" id="date" class="manage-column column-date sortable desc" style="">
				<span>Date</span>
		</th>
		<th scope="col" id="truck" class="manage-column column-truck bool sortable desc" style="">
				<span>Camion ?</span>
		</th>
		<th scope="col" id="ordre" class="manage-column column-order num sortable desc" style="">
				<span>Ordre</span>
		</th>
		<th scope="col" id="horaire" class="manage-column column-horaire num sortable desc" style="">
				<span>Nbre d'horaires définis</span>
		</th>
		<th scope="col" id="ssid" class="manage-column column-ssid sortable desc" style="">ID</th>	
	</tr>
	</thead>

	<tbody id="the-list" data-wp-lists="list:tag">
<?php
	foreach ( $results as $result ) 
	{
		if ($debug) echo var_dump($result) ."\n";
		$id = $result->activite_id;

?>
		<tr id="tag-2">
			<th scope="row" class="check-column">
				<label class="screen-reader-text" for="cb-select-<?echo $id;?>">Select <?php echo $result->activite_name;?></label>
				<input type="checkbox" name="delete_tags[]" value="<?echo $id;?>" id="cb-select-<?echo $id;?>"></th>
			<td class="name column-name">
				<strong><a class="row-title" href="<?php echo wp_nonce_url('?page=activity_edit&amp;action=edit&amp;ID=' . $id, 'fr_edit_activity_action', 'fr_nonce_url_check'); ?>" title="Edit <?php echo $result->activite_name;?>">
					<?php echo $result->activite_name;?></a></strong><br>
				<div class="row-actions">
					<span class="edit"><a href="<?php echo wp_nonce_url('?page=activity_edit&amp;action=edit&amp;ID=' . $id, 'fr_edit_activity_action', 'fr_nonce_url_check'); ?>">Edit</a> | </span>
					<span class="delete"><a class="delete-tag" href="<?php echo wp_nonce_url('?page=activity_edit&amp;action=delete&amp;ID=' . $id, 'fr_edit_activity_action', 'fr_nonce_url_check'); ?>">Delete</a> | </span>
					<span class="occurrence"><a class="occurrence-tag" href="<?php echo wp_nonce_url('?page=occurrences_list&amp;action=list&amp;ACTIVITY_ID=' . $id . "&amp;name=". $result->activite_name, 'fr_edit_activity_action', 'fr_nonce_url_check'); ?>">Gérer les horaires</a></span>
			</td>
			<td class="date column-date"><?php echo $result->activite_date;?></td>
			<td class="truck column-truck"><?php echo $result->activite_truck;?></td>
			<td class="order column-order"><?php echo $result->activite_order;?></td>
			<td class="horaire column-horaire"><?php echo $result->nbre_horaires;?></td>
			<td class="ssid column-ssid"><?php echo $id;?></td>
		</tr>
<?php } ?>
	</tbody>
	
	<tfoot>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1">Select All</label>
			<input id="cb-select-all-1" type="checkbox"></th>
		<th scope="col" id="name" class="manage-column column-name sortable desc" style="">
				<span>Nom</span>
		</th>
		<th scope="col" id="date" class="manage-column column-date sortable desc" style="">
				<span>Date</span>
		</th>
		<th scope="col" id="truck" class="manage-column column-truck bool sortable desc" style="">
				<span>Camion ?</span>
		</th>
		<th scope="col" id="ordre" class="manage-column column-order num sortable desc" style="">
				<span>Ordre</span>
		</th>
		<th scope="col" id="horaire" class="manage-column column-horaire num sortable desc" style="">
				<span>Nbre d'horaires définis</span>
		</th>
		<th scope="col" id="ssid" class="manage-column column-ssid sortable desc" style="">ID</th>	
	</tr>
	</tfoot>

</table>
</div>
<p><a href="<?php echo wp_nonce_url(plugin_dir_url( __FILE__ ) . 'save-data.php?action=noparticipants', 'fr_save_data', 'fr_nonce_field');?>" class="button secondary" target="_blank">Télécharger les activitiés au format CSV</a></p>

<?php	
}
?>