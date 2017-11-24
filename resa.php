<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';
$rooms_table = $table_prefix . 'hb_rooms';
$config_table = $table_prefix . 'hb_config';

$config = $wpdb->get_results("SELECT * FROM $config_table");

$resaManager = new ResaManager();
$roomManager = new RoomManager();

if (isset($_GET['supprimerResa'])){
    $messageResa = 'La réservation a bien été supprimée';
    $wpdb->delete( 'wp_hb_resa', array( 'ID' => $_GET['supprimerResa'] ) );
}

if (isset($_POST['ajouterResa'])){
    $confirmclient = 3;



    if(!empty($_POST['nom'])){
      if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

      $chambreid = $resaManager->quelIdDeChambre($_POST['chambre']);

      $dateA = strtotime($_POST['datearrivee']);

      $dateB = date("Y-m-d", strtotime($_POST['datearrivee'] . ' +' . (int)$_POST['nuits'] . ' day'));

      $nuits = $_POST['nuits'];

      $tarif = $resaManager->calculTarif($nuits, (int) $chambreid,  (int) $_POST['nombrep'], $_POST['litsupp']);

      $resaManager->addResaManuel($_POST['nom'], $_POST['email'], $_POST['tel'], $_POST['nombrep'], $_POST['chambre'], $chambreid, $_POST['datearrivee'], $dateB, $_POST['infos'], $tarif, $nuits, $confirmclient, 0, $_POST['litsupp'], $_POST['tidej'], $_POST['divers']);

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
       <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css"/>
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
          <th>Name</th>
          <th>@</th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/tel.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="resaImg"/></th>
          <th>Room</th>
          <th>Arrival</th>
          <th>Departure</th>
          <th>Comment</th>
          <th>2<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/lits.svg" class="resaImg"/></th>
          <th><?php echo $config[0]->devise ?></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/nuit.svg" class="resaImg"/></th>
          <th>Confirmed</th>
          <th></th>
          </tr>
          <tr>
             <form method="POST" action="admin.php?page=UBHBRESA">
              <td></td>
              <td><input type="text" style="max-width:100px;" name="nom"/></td>
                <td><input type="text" style="max-width:100px;" name="email"/></td>
                <td><input type="text" style="max-width:100px;" name ="tel"/></td>

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
                <!--<td><input type="date" name="datedepart" value="<?php echo $selectedB; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></td>-->
                <td></td>
                <td><input type="text" style="max-width:100px;" name="infos"/></td>
                <td><input type="checkbox" value="1" name="litsupp"/></td>
                <td></td>
                <td><input type="text" style="max-width:30px;" name="nuits" value="1"/></td>
                <td></td>
                <td><input type="submit" name="ajouterResa" value="Add"/></td>
              </form></tr>

        <?php
        $resa = $wpdb->get_results("SELECT * FROM $resa_table");
        foreach ($resaManager->resaList() as $resa)
          {

            $rdA = date("d-m-Y", strtotime($resa->datearrivee));
            $rdB = date("d-m-Y", strtotime($resa->datedepart));

            echo '<tr>';
            echo '<td>', $resa->id, '</td>';
            echo '<td>', $resa->nom, '</td>';
            echo '<td>', $resa->email, '</td>';
            echo '<td>', $resa->tel, '</td>';
            echo '<td>', $resa->nombrep, '</td>';
            echo '<td>', $resa->chambre, '</td>';
            echo '<td>', $rdA, '</td>';
            echo '<td>', $rdB, '</td>';
            echo '<td>', $resa->infos, '</td>';
            if($resa->litsupp == 1)
            {
                echo '<td class="confirmoui">Yes</td>';
            }else{
                echo '<td>No</td>';
            }
            echo '<td>', $resa->tarif, ' ', $config[0]->devise, '</td>';
            echo '<td>', $resa->nuits, '</td>';
            if ($resa->confirmclient == 1){
                echo '<td class="confirmoui">oui</td>';
            }else if ($resa->confirmclient == 2){
                echo '<td class="confirmnon">non</td>';
            }else if ($resa->confirmclient == 3){
              echo '<td class="confirmmanuel">manuel</td>';
            }
            echo '<td><a href="admin.php?page=UBHBRESA&supprimerResa=', $resa->id, '">Delete</a></td>';
            echo '</tr>';
          }
        ?>
        </table></center></div>
    </body>
</html>
