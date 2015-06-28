<?php 
/* 
  Plugin Name: Foyer Rural - pr&eacute;paration 15 ao&ucirc;t 
  Plugin URI: https://github.com/marito59/foyerrural-events
  Description: Ce plugin permet de r&eacute;server une activit&eacute;. 
  Version: 0.3 
  Author: Christian Maritorena
  Author URI: http://www.lechevabignien.com/ 
  License: GPLv3 
*/
/*
    Copyright (C) 2015  Christian Maritorena

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Contact : Christian Maritorena, maritore59(at)yahoo.fr
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'FOYERRURAL_EVENTS__MINIMUM_WP_VERSION', '4.0' );

define( 'FOYERRURAL_EVENTS__VERSION',		'0.2' );
define( 'FOYERRURAL_EVENTS__PLUGIN_DIR',	plugin_dir_path( __FILE__ ) );
define( 'FOYERRURAL_EVENTS__PLUGIN_FILE',	__FILE__ );

require_once( FOYERRURAL_EVENTS__PLUGIN_DIR . 'foyerrural-events-functions.php'    );
require_once( FOYERRURAL_EVENTS__PLUGIN_DIR . 'foyerrural-events-plugin.php'    );

if ( is_admin() ) {
	require_once( FOYERRURAL_EVENTS__PLUGIN_DIR . 'foyerrural-events-admin.php'     );
	require_once( FOYERRURAL_EVENTS__PLUGIN_DIR . 'admin/foyerrural-events-admin-activites.php'     );
	require_once( FOYERRURAL_EVENTS__PLUGIN_DIR . 'admin/foyerrural-events-admin-occurrences.php'     );
	require_once( FOYERRURAL_EVENTS__PLUGIN_DIR . 'admin/foyerrural-events-admin-participants.php'     );
  require_once( FOYERRURAL_EVENTS__PLUGIN_DIR . 'admin/edit-activity.php'     );
  require_once( FOYERRURAL_EVENTS__PLUGIN_DIR . 'admin/edit-occurrence.php'     );
}

register_activation_hook( __FILE__, 'plugin_activation' );
register_deactivation_hook( __FILE__, 'plugin_deactivation' );



?>