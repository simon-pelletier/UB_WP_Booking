<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';
$rooms_table = $table_prefix . 'hb_rooms';

$resaManager = new ResaManager($wpdb);
$roomManager = new RoomManager($wpdb);

if (isset($_GET['supprimerResa'])){
    $messageResa = 'La réservation a bien été supprimée';
    $wpdb->delete( 'wp_hb_resa', array( 'ID' => $_GET['supprimerResa'] ) );
}

if (isset($_POST['ajouterResa'])){
    $confirmclient = 1;



    if(!empty($_POST['nom'])){
      if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

      $chambreid = $resaManager->quelIdDeChambre($_POST['chambre']);

      $dateA = strtotime($_POST['datearrivee']);
      $dateB = strtotime($_POST['datedepart']);

      $nuitsTimestamp = $dateB - $dateA;
      $nuits = intval($nuitsTimestamp / 86400); //60*60*24
      if(isset($_POST['supp'])){
          $supp = 1;
          $tarif = $resaManager->calculTarif($nuits, (int) $chambreid,  (int) $_POST['nombrep'], $supp);
      } else {
          $supp = 0;
          $tarif = $resaManager->calculTarif($nuits, (int) $chambreid,  (int) $_POST['nombrep'], $supp);
      }


      $resaManager->addResaManuel($_POST['nom'], $_POST['email'], $_POST['tel'], $_POST['nombrep'], $_POST['chambre'], $chambreid, $_POST['datearrivee'], $_POST['datedepart'], $_POST['infos'], $tarif, $nuits, $confirmclient, 0, $supp);

      $messageResa = 'La réservation a bien été ajoutée !';

    } else {
      $messageResaError = 'L\'email entré n\'est pas correct.';
    }
    } else {
      $messageResaError = 'Merci de renseigner le Nom du client';
    }

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
          <th><img src="../wp-content/plugins/ub_hotelbooking/web/img/max.png" class="resaImg"/></th>
          <th>Chambre</th>
          <th>Date Arrivée</th>
          <th>Date Départ</th>
          <th>Infos</th>
          <th>2Lits</th>
          <th>€</th>
          <th><img src="../wp-content/plugins/ub_hotelbooking/web/img/nuit.png" class="resaImg"/></th>
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
                <td><input type="checkbox" value="1" name="supp"/></td>
                <td></td>
                <td></td>
                <td></td>
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
            echo '<td>', $resa->datearrivee, '</td>';
            echo '<td>', $resa->datedepart, '</td>';
            echo '<td>', $resa->infos, '</td>';
            if($resa->supp == 1)
            {
                echo '<td>oui</td>';
            }else{
                echo '<td>non</td>';
            }
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
