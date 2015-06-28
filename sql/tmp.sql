$sql = "select\n"
    . "wp_fr_activite.date, wp_fr_activite.nom, concat (wp_fr_occurrence_activite.heure_debut, \"-\", wp_fr_occurrence_activite.heure_fin), concat(wp_fr_occurrence_personne.prenom, \" \", wp_fr_occurrence_personne.nom), wp_fr_occurrence_personne.tel_fixe, wp_fr_occurrence_personne.tel_mobile, wp_fr_occurrence_personne.email, wp_fr_occurrence_personne.agree, wp_fr_occurrence_personne.change_date\n"
    . "from wp_fr_activite\n"
    . "left join wp_fr_occurrence_activite on wp_fr_occurrence_activite.id_activite = wp_fr_activite.ID\n"
    . "left join wp_fr_occurrence_personne on id_occurrence = wp_fr_occurrence_activite.ID\n"
    . "order by wp_fr_activite.date, wp_fr_activite.order, wp_fr_occurrence_activite.heure_debut, wp_fr_occurrence_personne.change_date";
