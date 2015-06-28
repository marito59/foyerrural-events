<?php
/* admin functions
 *
 */
 
add_action ('admin_menu', 'fr_create_menu');

function fr_create_menu() {
	// create top level menu
	add_menu_page ('Foyer Rural - 15 août - Administration', 'Foyer Rural', 'manage_options', 'fr_admin_menu', 'activites_menu_page', 'dashicons-groups');
	
	// liste des participants
	add_submenu_page ('fr_admin_menu', 'Liste des participants', 'Participants', 'manage_options', 'attendees_list', 'attendees_list_page');
	
	// paramétrage des activités
	add_submenu_page ('fr_admin_menu', 'Paramétrage des Activités', 'Activités', 'manage_options', 'activity_menu', 'activites_menu_page');
	
	// paramétrage des occurrence d'une activité'
	add_submenu_page (null, 'Paramétrage des Occurrences', 'Occurrences', 'manage_options', 'occurrences_list', 'occurrences_menu_page');
	
	add_submenu_page(null, 'Edition d\'une activité', 'Edition d\'une activité', 'manage_options', 'activity_edit', 'activity_edit_page');
	add_submenu_page(null, 'Edition des horaires d\'une activité', 'Edition des horaires d\'une activité', 'manage_options', 'occurrence_edit', 'occurrence_edit_page');
	add_submenu_page(null, 'Export CSV', 'Export CSV', 'manage_options', 'export_csv', 'export_csv_page');
	
	// register page option
	//add_action ('admin_init', 'fr_register_settings');
}

function fr_admin_page () {}

function fr_register_settings ()
{
	//register_settings('fr_settings', 'fr_options', 'fr_sanitize_options');
}
?>