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
    $messageResa = 'The reservation has been deleted.';
    $wpdb->delete( 'wp_hb_resa', array( 'ID' => $_GET['supprimerResa'] ) );

}

if (isset($_POST['deletePastConfirm'])){
    $messageResa = $resaManager->deletePast() . ' obsolete bookings have been removed.';
    //$resaManager->deletePast;
    ?><meta http-equiv="refresh" content="1; url=admin.php?page=UBHBRESA"><?php
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

      $messageResa = 'The reservation has been added!';

    } else {
      $messageResaError = 'The email entered is not correct.';
    }
    } else {
      $messageResaError = 'Please fill in the Customer Name';
    }

}
?>

<?php

if(isset($_POST['deletePast'])){
  ?>

 <!DOCTYPE HTML>
 <html>
    <head>
        <title>Booking</title>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css"/>
    </head>

     <body>
       <center>
         <div class="ub-messageRoomError">
       ! WARNING ! <br/><br/>
       This action is irreversible.<br/>
       Do you really want to delete past bookings?<br/><br/>
       </div>
       <form method="POST" action="admin.php?page=UBHBRESA">
         <input type="submit" name="cancel" value="Cancel" class="ub-retour"/><input type="submit" name="deletePastConfirm" value="Confirm cleaning" class="ub-deletePast"/>
       </form>
       </center>
     </body>
  </html>

<?php

} else {

 ?>

<!DOCTYPE HTML>
<html>
   <head>
       <title>Booking</title>
       <meta charset="utf-8"/>
       <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css"/>
   </head>

    <body>
       <div>
        <h1><center>Booking</center></h1>



        <?php
        if (isset($messageResa)){
        echo '<div class="ub-messageResa">' . $messageResa . '</div><br/>';
        }
        if (isset($messageResaError)){
        echo '<div class="ub-messageResaError">' . $messageResaError . '</div><br/>';
        }

        ?>
        <center>
        <table>
          <tr>
          <th>ID</th>
          <th>Name</th>
          <th>@</th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/tel.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="ub-resaImg"/></th>
          <th>Room</th>
          <th>Arrival</th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/nuit.svg" class="ub-resaImg"/></th>
          <th>Departure</th>
          <th>Comment</th>
          <th>2<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/lits.svg" class="ub-resaImg"/></th>
          <th><?php echo $config[0]->devise ?></th>

          <th>Confirmed</th>
          <th></th>
          </tr>
          <tr>
             <form method="POST" action="admin.php?page=UBHBRESA">
              <td></td>
              <td><input type="text" style="max-width:100px;" name="nom"/></td>
                <td><input type="text" style="max-width:100px;" name="email"/></td>
                <td><input type="text" style="max-width:100px;" name ="tel"/></td>

                <td><select name="nombrep" size="1" class="ub-nbpersonnes">
               <?php
                for ($i=1; $i <= 4; $i++){
                    echo '<option>' . $i . '</option>';
                }
                ?></select></td>

                <td><select name="chambre" size="1" class="ub-nbpersonnes">
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
                <td><input type="date" name="datearrivee" value="<?php echo $selectedA; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="ub-date"/></td>
                <td><input type="text" style="max-width:30px;" name="nuits" value="1"/></td>
                <td></td>
                <td><input type="text" style="max-width:100px;" name="infos"/></td>
                <td><input type="checkbox" value="1" name="litsupp"/></td>
                <td></td>

                <td></td>
                <td><input type="submit" name="ajouterResa" value="Add"/></td>
              </form></tr>

        <?php
        //$resa = $wpdb->get_results("SELECT * FROM $resa_table ORDER BY tarif");
        foreach ($resaManager->resaList() as $resa)
          {

            $rdA = date("d-m-Y", strtotime($resa->datearrivee));
            $rdB = date("d-m-Y", strtotime($resa->datedepart));
            $ajourdhui = date("Y-m-d");

            if ($resa->datedepart < $ajourdhui){
              echo '<tr class="yesteday">';
            } else if ($resa->datearrivee > $ajourdhui){
              echo '<tr class="tomorrow">';
            } else {
              echo '<tr class="today">';
            }


            echo '<td>', $resa->id, '</td>';
            echo '<td>', $resa->nom, '</td>';
            echo '<td>', $resa->email, '</td>';
            echo '<td>', $resa->tel, '</td>';
            echo '<td>', $resa->nombrep, '</td>';
            echo '<td>', $resa->chambre, '</td>';
            echo '<td>', $rdA, '</td>';
            echo '<td>', $resa->nuits, '</td>';
            echo '<td>', $rdB, '</td>';
            echo '<td>', $resa->infos, '</td>';
            if($resa->litsupp == 1)
            {
                echo '<td>Yes</td>';
            }else{
                echo '<td>No</td>';
            }
            echo '<td>', $resa->tarif, ' ', $config[0]->devise, '</td>';

            if ($resa->confirmclient == 1){
                echo '<td class="ub-confirmoui">Yes</td>';
            }else if ($resa->confirmclient == 2){
                echo '<td class="ub-confirmnon">No</td>';
            }else if ($resa->confirmclient == 3){
              echo '<td class="ub-confirmmanuel">Manual</td>';
            }
            echo '<td><a href="admin.php?page=UBHBRESA&supprimerResa=', $resa->id, '"><img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/delete.svg" class="ub-icon"/></a></td>';
            echo '</tr>';
          }
        ?>
        </table></center></div>
        <br/>
        <form method="POST" action="admin.php?page=UBHBRESA">
          <center><input type="submit" name="deletePast" value="! Cleaning past bookings !" class="ub-deletePast"/></center>
        </form>
        <br/>
    </body>
</html>
<?php
}
?>
