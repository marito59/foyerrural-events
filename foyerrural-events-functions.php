<?php
/* admin functions
 *
 */

function fr_getactivitelist_func( $atts ){
	global $wpdb;
	$tableprefix = $wpdb->prefix . "fr_";
	$debug = false;
	
	$step = (isset($_POST["step"])?htmlspecialchars($_POST["step"]):0);
	$step += 1;
	$activite_id = (isset($_POST["activite_id"])?htmlspecialchars($_POST["activite_id"]):"");
	$activite_name = (isset($_POST["activite_name"])?htmlspecialchars($_POST["activite_name"]):"");
	$occurrence_id = (isset($_POST["occurrence_id"])?htmlspecialchars($_POST["occurrence_id"]):"") ;
	$occurrence_name = (isset($_POST["occurrence_name"])?htmlspecialchars($_POST["occurrence_name"]):"") ;
	$personne_full = (isset($_POST["personne_full"])?htmlspecialchars($_POST["personne_full"]):"") ;
	if ($personne_full != "") {
		$personne = explode("|", $personne_full);
		$personne_name = $personne[0]." ".$personne[1];
	} else { 
		$personne_name = "";
		$personne = array("", "", "", "", "");
	}
	if ($occurrence_name != "") {
		$occurrence_list = explode ("|", $occurrence_name);
	} else $occurrence_list = array();
?>	
	<script>
		function process_step(arg) {
			if (arg==1) {
				jQuery("input[name=activite_id]").val(jQuery('input[name=activite]:checked').val());
				jQuery("input[name=activite_name]").val(jQuery('input[name=activite]:checked').next('label:first').html());
			} else if (arg==2) {
				var selected = [];
				var occurrence_name = [];
				jQuery('input[name=occurrence]:checked').each(function() {
					selected.push(jQuery(this).val());
					occurrence_name.push(jQuery(this).next('label:first').html());
				})
				var selected_joined = selected.join('|');
				var occurrence_name_joined = occurrence_name.join('|');
				jQuery("input[name=occurrence_id]").val(selected_joined);
				jQuery("input[name=occurrence_name]").val(occurrence_name_joined);
				//jQuery('input[name=occurrence]:checked').next('label:first').html());
			} else if (arg==3) {
				$personne = jQuery('input[id=personne_prenom]').val();
				$personne += "|"+jQuery('input[id=personne_nom]').val();
				$personne += "|"+jQuery('input[id=personne_fixe]').val();
				$personne += "|"+jQuery('input[id=personne_portable]').val();
				$personne += "|"+jQuery('input[id=personne_email]').val();

				jQuery("input[name=personne_full]").val($personne);
			} else if (arg==5) {
				jQuery("input[name=activite_id]").val(0);
				jQuery("input[name=activite_name]").val("");
				jQuery("input[name=occurrence_id]").val(0);
				jQuery("input[name=occurrence_name]").val("");
			
				jQuery("input[name=step]").val(0);
			}			
		}
		
		function goBack() {
			window.history.back();
		}
	</script>
	<div id="phase">
		<span id="phase_1" style="font-weight: <?php echo ($step==1?"bold":"normal")?>;">1 - Activit&eacute;</span>
		<span id="phase_2" style="font-weight: <?php echo ($step==2?"bold":"normal")?>;">2 - Horaire</span>
		<span id="phase_3" style="font-weight: <?php echo ($step==3?"bold":"normal")?>;">3 - Coordonn&eacute;es</span>
		<span id="phase_4" style="font-weight: <?php echo ($step==4?"bold":"normal")?>;">4 - Confirmation</span>
		<span id="phase_5" style="font-weight: <?php echo ($step==5?"bold":"normal")?>;">5 - Fin</span>
	</div>
	<div id="header_summary">
		<p style='<?php echo ($personne_name == ""?"display:none;":"")?> margin-bottom:3px'>Vous : <span id="header_nom" style="font-weight:bold; visibility:block;"><?php echo $personne_name ?></span></p>
		<p style='<?php echo ($activite_name == ""?"display:none;":"")?> margin-bottom:3px'>Votre activit&eacute; : <span id="header_activite" style="font-weight:bold; visibility:block;"><?php echo $activite_name ?></span></p>
		<p style='<?php echo ($occurrence_name == ""?"display:none;":"")?> margin-bottom:3px'>Vos horaires : <span id="header_occurrence" style="font-weight:bold; visibility:block;"><ul>
<?php
	if ($occurrence_name != "") {foreach ($occurrence_list as $occurrence) {	echo "<li>".$occurrence."</li>"; } } 
?>
		</ul></span></p>
	</div>
	<form name="aout" action="#" method="POST" autocomplete="off" onsubmit="process_step(<?php echo $step?>)">
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
		$format = 'Y-m-d H:i:s';
		
		$query = 'SELECT ' . $tableprefix . 'activite.ID as activite_id, ' . $tableprefix . 'activite.nom as activite_name,   ' . $tableprefix . 'activite.date as activite_date,
					' . $tableprefix . 'occurrence_activite.ID as occurrence_id, 
					' . $tableprefix . 'occurrence_activite.heure_debut as occurrence_heure_debut, ' . $tableprefix . 'occurrence_activite.heure_fin as occurrence_heure_fin,
					' . $tableprefix . 'occurrence_activite.nbre_participants as occurrence_nbre_participants,
   			(select count(*) from ' . $tableprefix . 'occurrence_personne where id_occurrence=' . $tableprefix . 'occurrence_activite.ID)  as occurrence_nbre_inscrits
   			FROM ' . $tableprefix . 'activite LEFT JOIN ' . $tableprefix . 'occurrence_activite ON ' . $tableprefix . 'occurrence_activite.id_activite = ' . $tableprefix . 'activite.ID 
   			order by ' . $tableprefix . 'activite.date, ' . $tableprefix . 'occurrence_activite.heure_debut';
		$results = $wpdb->get_results( $query );

		foreach ( $results as $result ) 
		{
			echo "<tr>";
			$date = DateTime::createFromFormat($format, $result->activite_date);
			//$date = date_parse($result->date);
			//$date = explode('-',$result->date);
			$ladate = $date->format("d/m/Y");
			//$ladate = $date["day"].'/'.$date["month"].'/'.$date["year"];
			if ($ladate <> $prevdate) {
				echo "<td>".$ladate."</td>";
				$prevdate = $ladate;
			} else { echo "<td>&nbsp;</td>";}
			echo "<td name='activite_".$result->activite_id."'><input name='activite' type='radio' value='".$result->activite_id."'>&nbsp;<label>".$result->activite_name."</label></td>";
			echo "</tr>";
		}
	}		
?>
		</table>
	</div>
	<div id="occurrence" style="<?php echo ($step==2?"visibility:visible":"display:none") ?>;">
		<div id="occurrence_activite">
<?php
	if ($step == 2) {
	
	$wpdb->show_errors();
		//$results = $wpdb->get_results( 'SELECT * FROM ' . $tableprefix . 'occurrence_activite, (select count(*) from '.$tableprefix . 'occurrence_personne where id_occurrence='.$tableprefix . 'occurrence_activite.ID) as inscrits WHERE id_activite = '.$activite_id.' ORDER BY heure_debut' );*
		$results = $wpdb->get_results( 'SELECT *,
 (select count(*) from ' . $tableprefix . 'occurrence_personne where id_occurrence=' . $tableprefix . 'occurrence_activite.ID)  as inscrits from ' . $tableprefix . 'occurrence_activite WHERE id_activite = 19 ORDER BY heure_debut');
		echo $wpdb->print_error();
		//, (select count(*) from occurrence_personne where test.occurrence_personne_assoc.id_occurrence=test.occurrence_activite.ID) as inscrits
		foreach ( $results as $result ) 
		{
			$places = $result->nbre_participants - $result->inscrits;
			echo "<input name='occurrence' type='checkbox' value='".$result->ID."'>&nbsp;<label>".$result->heure_debut."-".$result->heure_fin."</label> (nbre de places restants : ".$places.")<br />";
		}
	}
?>
		</div>
	</div>
	<div id="personne" style="<?php echo ($step==3?"visibility:visible":"display:none") ?>;">
		<table id="personne">
		<tr><td><label>Pr&eacute;nom : </label><input id="personne_prenom" type="text" value="<?php echo $personne[0] ?>"></td></tr>
		<tr><td><label>Nom : </label><input id="personne_nom" type="text" value="<?php echo $personne[1] ?>"></td></tr>
		<tr><td><label>T&eacute;l&eacute;phone fixe : </label><input id="personne_fixe" type="text" value="<?php echo $personne[2] ?>"></td></tr>
		<tr><td><label>T&eacute;l&eacute;phone portable : </label><input id="personne_portable" type="text" value="<?php echo $personne[3] ?>"></td></tr>
		<tr><td><label>email : </label><input id="personne_email" type="text" value="<?php echo $personne[4] ?>"></td></tr>
		</table>
	</div>
	<div id="recap" style="<?php echo ($step==4?"visibility:visible":"display:none") ?>;">
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
	<div id="fin" style="<?php echo ($step==5?"visibility:visible":"display:none") ?>;">
<?php
	if ($step == 5) {
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
		<td><input style="visibility: <?php echo ($step==1|$step==5?'hidden':'visible')?>;" type="button" value="Revenir" onclick="goBack();" /></td><td><input type="submit" value="Continuer<?php echo ($step==5?' avec une autre activit&eacute;':'')?>" /></td>
	</tr>
	<tr style="visibility:<?php echo ($step==5?'visible':'collapse')?>;"><td>&nbsp;</td><td><a href="/"><input type="button" value="Terminer" /></td></tr>
	</table>
	</form>
<?php	
	return ;
}
add_shortcode( 'get_activite_list', 'fr_getactivitelist_func' );
function my_init() {
	if (!is_admin()) {
		wp_enqueue_script('jquery');
		wp_register_style( 'custom-style', plugins_url( '/css/custom-style.css', __FILE__ ), array(), 'v0.1', 'all' );
		wp_enqueue_style( 'custom-style' );
	}
}
add_action('init', 'my_init', 9999);

?>