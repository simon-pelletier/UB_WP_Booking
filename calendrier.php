<?php

//Voir s'il y a plus sûr
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

$mois = $_GET['mois'];
$anne = $_GET['anne'];

function mois($nb){
	$key = $nb - 1;
	$ap = array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
	return $ap[$key];
}
?>
<h1><center><?php echo mois($mois)."  ".$anne;?></center></h1>
<?php
global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';

$resas = $wpdb->get_results("SELECT nom, chambre, datearrivee, datedepart, nuits FROM $resa_table");
//print_r($resas);

//$resaManager->resaMonthCalendar();


echo '<br/><br/>';
//echo $resas[0]->datearrivee;
/*
foreach ($resas as $resa){
	echo $resa->datearrivee;
	echo '<br/>';
}

*/





$nbjour = cal_days_in_month( CAL_GREGORIAN, $mois, $anne); // nombre de jour dans le mois

echo "<table class='ub-calendar' >";

for($i = 1; $nbjour >= $i; $i++){
  $p = cal_to_jd(CAL_GREGORIAN, $mois, $i, $anne); // formater jour
  $jourweek = jddayofweek($p); // jour de la semaine

  if($i == $nbjour){

    if($jourweek == 1){
      echo '<tr class="ub-calendar-row">';
    }
    echo "<td class='ub-calendar-case'>". str_pad($i, 2, "0", STR_PAD_LEFT) . "</td></tr>";

  } else if($i == 1) {

    echo '<tr class="ub-calendar-row">';

    if($jourweek == 0){
      $jourweek = 7;
    }

    for($b = 1 ;$b != $jourweek; $b++){
    echo "<td></td>";
    }

    echo "<td class='ub-calendar-case'>". str_pad($i, 2, "0", STR_PAD_LEFT) . "</td>";

    if($jourweek == 7){
    echo "</tr>";
    }

  } else {

    if($jourweek == 1){
      echo '<tr class="ub-calendar-row">';
    }

		$dateForCalendar = $anne . "-" . $mois . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);

		if (date('Y-m-d') == $dateForCalendar){
				echo "<td class='ub-calendar-case ub-calendar-case-today'>". str_pad($i, 2, "0", STR_PAD_LEFT) . "<br/>";
		} else {
				echo "<td class='ub-calendar-case'>". str_pad($i, 2, "0", STR_PAD_LEFT) . "<br/>";
		}


		foreach ($resas as $resa){
			if($resa->datearrivee == $dateForCalendar){
				echo '<div class="ub-arrival"><div class="ub-arrival-chambre">' . $resa->chambre . '</div><div class="ub-arrival-name">' . $resa->nom . '</div></div>';
			} else {
			}
			if($resa->datedepart == $dateForCalendar){
				echo '<div class="ub-departure"><div class="ub-arrival-chambre">' . $resa->chambre . '</div><div class="ub-arrival-name">' . $resa->nom . '</div></div>';
			} else {
			}
		}

		echo "</td>";



    if($jourweek == 0){
      echo "</tr>";
    }

  }
}
echo "</table>";


?>
