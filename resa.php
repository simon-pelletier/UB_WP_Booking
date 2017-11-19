<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';
$rooms_table = $table_prefix . 'hb_rooms';

$resaManager = new ResaManager($wpdb);
$roomManager = new RoomManager($wpdb);

if (isset($_GET['supprimerResa'])){
    //$message = 'La chambre a bien été supprimée';
    $wpdb->delete( 'wp_hb_resa', array( 'ID' => $_GET['supprimerResa'] ) );
}

if (isset($_POST['ajouterResa'])){

    if(isset($_POST['confirmclient'])){
        $confirmclient = 1;
    } else {
        $confirmclient = 0;
    }

    $dateA = strtotime($_POST['datearrivee']);
    $dateB = strtotime($_POST['datedepart']);

    $nuitsTimestamp = $dateB - $dateA;

    $nuits = intval($nuitsTimestamp / 86400); //60*60*24


    $chambreid = $resaManager->quelIdDeChambre($_POST['chambre']);

    $tarif = $resaManager->calculTarif($nuits, (int) $chambreid,  (int) $_POST['nombrep']);

    $wpdb->insert(
      'wp_hb_resa',
      array(
        'nom' => $_POST['nom'],
         'email' => $_POST['email'],
         'tel' => $_POST['tel'],
         'nombrep' => $_POST['nombrep'],
         'chambre' => $_POST['chambre'],
         'chambreid' => $chambreid,
         'datearrivee' => $_POST['datearrivee'],
         'datedepart' => $_POST['datedepart'],
         'infos' => $_POST['infos'],
         'tarif' => $tarif,
         'nuits' => $nuits,
         'confirmclient' => $confirmclient,
         'cleconfirm' => 0
      ),
      array(
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%s'
      )
    );
/*
    if ($resaManuel->isValid()){
        $messageResa = 'La réservation a bien été ajoutée !';
        $resaManager->addResa($resaManuel);
    } else {
        $erreurs = $resaManuel->erreurs();
    }
    if (isset($erreurs) && in_array(Resa::NOM_INVALIDE, $erreurs)) {
        $messageResaError = 'Merci de renseigner votre nom.<br/>';
    }
    if (isset($erreurs) && in_array(Resa::EMAIL_INVALIDE, $erreurs)) {
        $messageResaError = 'Cet e-mail n\'est pas valide.<br/>';
    }
    if (isset($erreurs) && in_array(Resa::TEL_INVALIDE, $erreurs)) {
        $messageResaError = 'Ce numéro de téléphone n\'est pas valide.<br/>';
    }
*/

}
?>


<!DOCTYPE HTML>
<html>
   <head>
       <title>Hotel Booking Admin</title>
       <meta charset="utf-8"/>
       <link rel="stylesheet" type="text/css" href="../wp-content/plugins/ub_hotelbooking/web/css/style.css"/>
   </head>

    <body>
       <div>
        <h1><center>Booking</center></h1>
        <?php
        if (isset($messageResa)){
        echo '<div class="messageResa">' . $messageResa . '</div><br/>';
        }
        if (isset($messageResaError)){
        echo '<div class="messageResaError">' . $messageResaError . '</div><br/>';
        }

        ?>
        <center>
        <table>
          <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>@</th>
          <th>TEL</th>
          <th><img src="web/img/max.png" class="resaImg"/></th>
          <th>Chambre</th>
          <th>Date Arrivée</th>
          <th>Date Départ</th>
          <th>Infos</th>
          <th>€</th>
          <th><img src="web/img/nuit.png" class="resaImg"/></th>
          <th>Confirmé</th>
          <th>Actions</th>
          </tr>


          <tr>
             <form method="POST" action="admin.php?page=UBHBRESA">
              <td></td>
              <td><input type="text" style="max-width:100px;" name="nom"/></td>
                <td><input type="text" style="max-width:100px;" name="email"/></td>
                <td><input type="text" style="max-width:100px;" name ="tel"/></td>
                <!--<td><input type="text" style="max-width:30px;" name="nombrep"/></td>-->

                <td><select name="nombrep" size="1" class="nbpersonnes">
               <?php
                for ($i=1; $i <= 4; $i++){
                    echo '<option>' . $i . '</option>';
                }
                ?></select></td>


                <td><select name="chambre" size="1" class="nbpersonnes">
               <?php
               $room = $wpdb->get_results("SELECT * FROM $rooms_table");
               foreach ($room as $room) {
                 echo '<option>' . $room->chambre . '</option>';
               }
               /*
                foreach ($roomManager->roomAdminList() as $aroom){
                    echo '<option>' . $aroom->chambre() . '</option>';
                }
                */
                ?></select></td>








                <?php
                $timezone = "Europe/Paris";
                date_default_timezone_set($timezone);
                $mindaya = date("Y-m-d");
                $mindayd = date("Y-m-d", strtotime($mindaya . ' +1 day'));
                $maxday = date("Y-m-d", strtotime($mindaya . ' +1 YEAR'));


                $selectedA = $mindaya;
                $selectedB = $mindayd;
                 ?>

                <td><input type="date" name="datearrivee" value="<?php echo $selectedA; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></td>
                <td><input type="date" name="datedepart" value="<?php echo $selectedB; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></td>

                <td><input type="text" style="max-width:100px;" name="infos"/></td>
                <td></td>
                <td></td>
                <td><input type="checkbox" value="1" name="confirmclient"/></td>
                <td><input type="submit" name="ajouterResa" value="Ajouter"/></td>

              </form></tr>




        <?php

        $resa = $wpdb->get_results("SELECT * FROM $resa_table");
        foreach ($resaManager->resaList() as $resa)
          {
            echo '<tr>';

            echo '<td>', $resa->id, '</td>';
            echo '<td>', $resa->nom, '</td>';
            echo '<td>', $resa->email, '</td>';
            echo '<td>', $resa->tel, '</td>';
            echo '<td>', $resa->nombrep, '</td>';
            echo '<td>', $resa->chambre, '</td>';
            //echo '<td>', $resa->datearrivee->format('d/m/Y'), '</td>';
            //echo '<td>', $resa->datedepart->format('d/m/Y'), '</td>';
            echo '<td>', $resa->datearrivee, '</td>';
            echo '<td>', $resa->datedepart, '</td>';
            echo '<td>', $resa->infos, '</td>';
            echo '<td>', $resa->tarif, ' €</td>';
            echo '<td>', $resa->nuits, '</td>';
            if($resa->confirmclient == 1)
            {
                echo '<td class="confirmoui">oui</td>';
            }else{
                echo '<td class="confirmnon">non</td>';
            }

            echo '<td><a href="admin.php?page=UBHBRESA&supprimerResa=', $resa->id, '">Supprimer</a></td>';


            echo '</tr>';
          }

        ?>

        </table></center></div>
    </body>
</html>
