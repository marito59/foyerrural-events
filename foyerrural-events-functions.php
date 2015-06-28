<?php
/* usage functions
 *
 */

/*
 * fr_getactivitelist_func
 * 		Short code function
 *
 */
function fr_getactivitelist_func( $atts ){
	global $wpdb;
	$tableprefix = $wpdb->prefix . "fr_";
	$debug = false;
	
	/* 
	 * tab cycle management
	 * step 1 : initial tab (select activity and occurrence)
	 * step 2 : enter your personal data
	 * step 3 : review your choices
	 * step 4 : data saved
	 */
	$step = (isset($_POST["step"])?htmlspecialchars($_POST["step"]):0);
	
	if ($step != 0) {
		if (!wp_verify_nonce ($_REQUEST['fr_nonce_field'], 'fr_activity_register')) {
			die( 'Action non autorisée' );
		}
	}
	
	
	if ($step == 4) {
		$step = 1;
	} else {
		$step += 1;
	}
	
	/*
	 * activity : nature of the task selected
	 */
	$activite_id = (isset($_POST["activite_id"])?htmlspecialchars($_POST["activite_id"]):"");
	$activite_name = (isset($_POST["activite_name"])?htmlspecialchars($_POST["activite_name"]):"");
	
	/*
	 * occurrence : scheduke information (time frame selected)
	 */
	$occurrence_id = (isset($_POST["occurrence_id"])?htmlspecialchars($_POST["occurrence_id"]):"") ;
	$occurrence_name = (isset($_POST["occurrence_name"])?htmlspecialchars($_POST["occurrence_name"]):"") ;

	if ($occurrence_name != "") {
		$occurrence_list = explode ("|", $occurrence_name);
	} else $occurrence_list = array();
	
	/*
	 *  personne : personal information
	 */
	$personne_full = (isset($_POST["personne_full"])?htmlspecialchars($_POST["personne_full"]):"") ;
	if ($personne_full != "") {
		$personne = explode("|", $personne_full);
		$personne_name = $personne[0]." ".$personne[1];
	} else { 
		$personne_name = "";
		$personne = array("", "", "", "", "");
	}
?>	
	<script>
		var last_activite = "";
		
		/*
		 * Function : process_step
		 *
		 * @desc Called during onsbmit for a form and manage the data related to the step (tab) in the form
		 * @param integer arg : step number
		 * @return boolean		true : submit form, false : don't submit form
		 *
		 */
		
		function process_step(arg) {
			if (arg==99) {
			} else if (arg==1) {
				var selected = [];
				var occurrence_name = [];
				var nb = 0;
				jQuery('input[type=checkbox]:checked').each(function() {
					selected.push(jQuery(this).val());
					occurrence_name.push(jQuery(this).next('label:first').html());
//					var activite_id = jQuery(this).attr('id');

					nb++;
				})
				if (nb == 0) {
					alert("Vous devez sélectionner une activité et un horaire");
					return false;
				}
				var selected_joined = selected.join('|');
				var occurrence_name_joined = occurrence_name.join('|');
				jQuery("input[name=occurrence_id]").val(selected_joined);
				jQuery("input[name=occurrence_name]").val(occurrence_name_joined);
				jQuery("input[name=activite_id]").val(jQuery('input[name=activite]:checked').val());
				jQuery("input[name=activite_name]").val(jQuery('input[name=activite]:checked').next().html());
			} else if (arg==2) {
				$personne = jQuery('input[id=personne_prenom]').val();
				$personne += "|"+jQuery('input[id=personne_nom]').val();
				$personne += "|"+jQuery('input[id=personne_fixe]').val();
				$personne += "|"+jQuery('input[id=personne_portable]').val();
				$personne += "|"+jQuery('input[id=personne_email]').val();

				jQuery("input[name=personne_full]").val($personne);
/*			else if (arg=4) {
				jQuery("input[name=activite_id]").val(0);
				jQuery("input[name=activite_name]").val("");
				jQuery("input[name=occurrence_id]").val(0);
				jQuery("input[name=occurrence_name]").val("");
				jQuery("input[name=step]").val(0);*/
			}			
			return true; // submit form
		}

		function process_input() {
			
		}

		/*
		 * Function : process_activity
		 *
		 * @Description :
		 * 		Called during step 1 on the onchange event for the radiobutton of an activity
		 *			- hide the checkmark for the occurrence of the previous activity
		 *			- unhide the checkmarks for the occurrence of the newly selected activity
		 *			- highlight the rows for the selected activity
		 * @Args : elem : pointer to the selected radiobutton
		 * @Return : nothing
		 *
		 */
		
		function process_activity(elem) {
			if (last_activite !="") {
				jQuery("input[name='"+last_activite+"']").css('visibility','hidden');
				jQuery("tr[id='"+last_activite+"']").removeClass('selected');
			}
			var activite = jQuery(elem).attr("id");
			jQuery("input[name='"+activite+"']").css('visibility','visible');
			jQuery("tr[id='"+activite+"']").addClass('selected');
			last_activite = activite;
		}
		
		/*
		 * Function : goBack
		 * @Description : Process the Go Back (previous) button in the form
		 * 
		 */
		 
		function goBack() {
			window.history.back();
		}
	</script>
	<div id="phase">
		<span id="phase_1"><img src="<?php echo ($step==1?plugin_dir_url( __FILE__ )."images/slice_s_1.png":plugin_dir_url( __FILE__ )."images/slice_1.png")?>" /></span>
		<span id="phase_2"><img src="<?php echo ($step==2?plugin_dir_url( __FILE__ )."images/slice_s_2.png":plugin_dir_url( __FILE__ )."images/slice_2.png")?>" /></span>
		<span id="phase_3"><img src="<?php echo ($step==3?plugin_dir_url( __FILE__ )."images/slice_s_3.png":plugin_dir_url( __FILE__ )."images/slice_3.png")?>" /></span>
		<span id="phase_4"><img src="<?php echo ($step==4?plugin_dir_url( __FILE__ )."images/slice_s_4.png":plugin_dir_url( __FILE__ )."images/slice_4.png")?>" /></span>
	
	</div>
	<div id="header_summary">
		<p style='<?php echo ($personne_name == ""?"display:none;":"")?> margin-bottom:3px'>Vous : <span id="header_nom" style="font-weight:bold; visibility:block;"><?php echo $personne_name ?></span></p>
		<p style='<?php echo ($activite_name == ""?"display:none;":"")?> margin-bottom:3px'>Votre activit&eacute; : <span id="header_activite" style="font-weight:bold; visibility:block;"><?php echo $activite_name ?></span></p>
		<p style='<?php echo ($occurrence_name == ""?"display:none;":"")?> margin-bottom:3px'>Vos horaires : <span id="header_occurrence" style="font-weight:bold; visibility:block;"><ul>
<?php
	if ($occurrence_name != "") {foreach ($occurrence_list as $occurrence) { echo "<li>".$occurrence."</li>"; } } 
?>
		</ul></span></p>
	</div>
	<form name="aout" action="#" method="POST" autocomplete="off" onsubmit="return process_step(<?php echo $step?>)">
		<?php wp_nonce_field('fr_activity_register', 'fr_nonce_field'); ?>
		<input type="hidden" name="activite_id" value='<?php echo $activite_id ?>' />
		<input type="hidden" name="activite_name" value="<?php echo $activite_name ?>" />
		<input type="hidden" name="occurrence_id" value="<?php echo $occurrence_id ?>" />
		<input type="hidden" name="occurrence_name" value="<?php echo $occurrence_name ?>" />
		<input type="hidden" name="personne_full" value="<?php echo $personne_full ?>" />
		<input type="hidden" name="step" value="<?php echo $step ?>" />
    
	<div id="activite" style="<?php echo ($step==1?"visibility:visible":"display:none") ?>;">
		<table>
<?php
	if ($step == 1){
		$ladate = "";
		$prevdate = "";
		$prevactiviteid = "";
		$format = 'Y-m-d H:i:s';
//$wpdb->show_errors();		
		$query = 'SELECT ' . $tableprefix . 'activite.ID AS activite_id, ' . $tableprefix . 'activite.nom AS activite_name,   DATE_FORMAT(' . $tableprefix . 'activite.date, "%d/%m/%Y") AS activite_date,
					' . $tableprefix . 'occurrence_activite.ID AS occurrence_id, 
					TIME_FORMAT(' . $tableprefix . 'occurrence_activite.heure_debut, "%H:%i") AS heure_debut, TIME_FORMAT(' . $tableprefix . 'occurrence_activite.heure_fin, "%H:%i") AS heure_fin,
					' . $tableprefix . 'occurrence_activite.nbre_participants AS nbre_participants,
   			(SELECT count(*) FROM ' . $tableprefix . 'occurrence_personne where id_occurrence=' . $tableprefix . 'occurrence_activite.ID)  AS nbre_inscrits
   			FROM ' . $tableprefix . 'activite LEFT JOIN ' . $tableprefix . 'occurrence_activite ON ' . $tableprefix . 'occurrence_activite.id_activite = ' . $tableprefix . 'activite.ID 
   			ORDER BY ' . $tableprefix . 'activite.date, ' . $tableprefix . 'activite.order, '. $tableprefix . 'occurrence_activite.heure_debut';
		$results = $wpdb->get_results( $query );
//echo $wpdb->print_error();
		foreach ( $results as $result ) 
		{
//echo var_dump($result);
			echo "<tr id='activite_".$result->activite_id."'>";
//			$date = DateTime::createFromFormat($format, $result->activite_date);
//			$ladate = $date->format("d/m/Y");
			$ladate = $result->activite_date;
			if ($ladate <> $prevdate) {
				echo "<td>".$ladate."</td>";
				$prevdate = $ladate;
			} else { 
				echo "<td>&nbsp;</td>";
			}
			if ($result->activite_id <> $prevactiviteid) {
				echo "<td name='activitef'><input name='activite' id='activite_".$result->activite_id."' value='activite_".$result->activite_id."' type='radio' onchange='process_activity(this);' /><label>".$result->activite_name."</label></td>";
				$prevactiviteid = $result->activite_id;
			} else {
				echo "<td>&nbsp;</td>";
			}
			$places = $result->nbre_participants - $result->nbre_inscrits;
			echo "<td name='occurrence'><input style='visibility:hidden;' name='activite_".$result->activite_id."' id='occurrence_".$result->occurrence_id."' type='checkbox' value='".$result->occurrence_id."' oninput='process_input();'>&nbsp;<label>".$result->heure_debut."-".$result->heure_fin."</label><br /><span class='seats'>(places restantes : ".$places.")</span></td>";

			echo "</tr>";
		}
	}		
?>
		</table>
	</div>
	<div id="occurrence" style="<?php echo ($step==99?"visibility:visible":"display:none") ?>;">
		<div id="occurrence_activite">
<?php
	if ($step == 99) {
	
//		$wpdb->show_errors();
		//$results = $wpdb->get_results( 'SELECT * FROM ' . $tableprefix . 'occurrence_activite, (select count(*) from '.$tableprefix . 'occurrence_personne where id_occurrence='.$tableprefix . 'occurrence_activite.ID) AS inscrits WHERE id_activite = '.$activite_id.' ORDER BY heure_debut' );*
		$results = $wpdb->get_results( 'SELECT *,
 (select count(*) from ' . $tableprefix . 'occurrence_personne where id_occurrence=' . $tableprefix . 'occurrence_activite.ID)  as inscrits from ' . $tableprefix . 'occurrence_activite WHERE id_activite = 19 ORDER BY heure_debut');
//		echo $wpdb->print_error();
		//, (select count(*) from occurrence_personne where test.occurrence_personne_assoc.id_occurrence=test.occurrence_activite.ID) as inscrits
		foreach ( $results as $result ) 
		{
			$places = $result->nbre_participants - $result->inscrits;
			echo "<input name='occurrence' type='checkbox' value='".$result->ID."'>&nbsp;<label>".$result->heure_debut."-".$result->heure_fin."</label><br />(places restantes : ".$places.")";
		}
	}
?>
		</div>
	</div>
	<div id="personne" style="<?php echo ($step==2?"visibility:visible":"display:none") ?>;">
		<table id="personne">
		<tr><td><label>Pr&eacute;nom : </label><input id="personne_prenom" type="text" value="<?php echo $personne[0] ?>"></td></tr>
		<tr><td><label>Nom : </label><input id="personne_nom" type="text" value="<?php echo $personne[1] ?>"></td></tr>
		<tr><td><label>T&eacute;l&eacute;phone fixe : </label><input id="personne_fixe" type="tel" value="<?php echo $personne[2] ?>"></td></tr>
		<tr><td><label>T&eacute;l&eacute;phone portable : </label><input id="personne_portable" type="tel" value="<?php echo $personne[3] ?>"></td></tr>
		<tr><td><label>email : </label><input id="personne_email" type="email" value="<?php echo $personne[4] ?>"></td></tr>
		</table>
	</div>
	<div id="recap" style="<?php echo ($step==3?"visibility:visible":"display:none") ?>;">
		<p>Merci de v&eacute;rifier les informations que vous avez s&eacute;lectionn&eacute;es</p>
		<table border="0">
			<tr>
				<td>Nom :</td><td><?php echo $personne[0]." ".$personne[1] ?></td>
			</tr>
			<tr>
				<td>T&eacute;l&eacute;phone Fixe :</td><td><?php echo $personne[2] ?></td>
			</tr>
			<tr>
				<td>T&eacute;l&eacute;phone Portable :</td><td><?php echo $personne[3] ?></td>
			</tr>
			<tr>
				<td>email :</td><td><?php echo $personne[4] ?></td>
			</tr>
			<tr>
				<td>Activit&eacute; :</td><td><?php echo $activite_name ?></td>
			</tr>
			<tr>
				<td>Horaires :</td><td><ul><?php
if ($occurrence_name != "") {foreach ($occurrence_list as $occurrence) {	echo "<li>".$occurrence."</li>"; } }
 ?></ul></td>
			</tr>
		</table>
	</div>
	<div id="fin" style="<?php echo ($step==4?"visibility:visible":"display:none") ?>;">
<?php
	if ($step == 4) {
		$occurrence_list = explode('|', $occurrence_id);
		foreach ($occurrence_list as $occurrence) {
			$data = array(
				'id_occurrence' => $occurrence,
				'prenom' => $personne[0],
				'nom' => $personne[1],
				'tel_fixe' => $personne[2],
				'tel_mobile' => $personne[3],
				'email' => $personne[4],
				'agree' => true
				);
			$format = array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%b'
				);
			$wpdb->show_errors($debug);
			$results = $wpdb->insert($tableprefix. 'occurrence_personne', $data, $format);
			echo $wpdb->print_error();
		}
	}
?>
		<p>Votre inscription est enregistr&eacute;e</p>
	</div>
	<table id="navigation">
	<tr>
		<td><input style="visibility: <?php echo ($step==1|$step==4?'hidden':'visible')?>;" type="button" value="Revenir" onclick="goBack();" /></td><td><input type="submit" value="Continuer<?php echo ($step==4?' avec une autre activit&eacute;':'')?>" /></td>
	</tr>
	<tr style="visibility:<?php echo ($step==4?'visible':'collapse')?>;"><td>&nbsp;</td><td><a href="/"><input type="button" value="Terminer" /></td></tr>
	</table>
	</form>
<?php	
	return ;
}

add_shortcode( 'get_activite_list', 'fr_getactivitelist_func' );

function my_init() {
//	if (!is_admin()) {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');		
		wp_register_style( 'custom-style', plugins_url( '/css/custom-style.css', __FILE__ ), array(), 'v0.1', 'all' );
		wp_enqueue_style( 'custom-style' );
//	}
}
add_action('init', 'my_init', 9999);

?>