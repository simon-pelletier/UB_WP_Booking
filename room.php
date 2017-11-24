<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$rooms_table = $table_prefix . 'hb_rooms';
$config_table = $table_prefix . 'hb_config';

$roomManager = new RoomManager();
$configManager = new ConfigManager();

if (isset($_GET['supprimerRoom'])){
    $messageRoom = 'The room has been removed.';

    $roomManager->deleteRoom($_GET['supprimerRoom'], $_GET['photo']);

    ?><meta http-equiv="refresh" content="0; url=admin.php?page=UBHBADMIN"><?php
}

if (isset($_POST['ajouterChambre'])){

  if($roomManager->chambreExistance($_POST['chambre']) > 0){
    $messageRoomError = 'This room name already exists.';
  } else {
    if (!empty($_FILES['photo']['name'])){
        $tailleMax = 2097152;
         if ($_FILES['photo']['size'] <= $tailleMax){
            $dimensions = getimagesize($_FILES['photo']['tmp_name']);
            $longueur = $dimensions[0];
            $largeur = $dimensions[1];
             if ($longueur==300 && $largeur==300) {
               $roomManager->addRoom($_POST['chambre'], $_POST['max'], $_POST['lits'], $_POST['douche'], $_POST['wc'], $_POST['tel'], $_POST['tv'], $_POST['baignoire'], $_POST['wifi'], $_POST['clim'], $_POST['photo'], $_POST['for1'], $_POST['for2'], $_POST['for3'], $_POST['for4'], $_POST['infosup']);
               $messageRoom = 'The room has been added.';

                $roomManager->ajoutPhoto($_FILES['photo']['name'], $_FILES['photo']['tmp_name']);
            } else {
                 $messageRoomError = 'Your photo must be 300 * 300px<br/>';
             }
         } else {
             $messageRoomError = 'Your photo must not exceed 2MB<br/>';
         }
    } else {
      $roomManager->addRoom($_POST['chambre'], $_POST['max'], $_POST['lits'], $_POST['douche'], $_POST['wc'], $_POST['tel'], $_POST['tv'], $_POST['baignoire'], $_POST['wifi'], $_POST['clim'], $_POST['photo'], $_POST['for1'], $_POST['for2'], $_POST['for3'], $_POST['for4'], $_POST['infosup']);
      $messageRoom = 'The room has been added.';
    }
  }
}

if (isset($_POST['update'])){

  if (filter_var($_POST['adminmail'], FILTER_VALIDATE_EMAIL)) {
    $configManager->update(
      $_POST['adminmail'],
      $_POST['persmax'],
      $_POST['devise'],
      $_POST['fumeur'],
      $_POST['animaux'],
      $_POST['parking'],
      $_POST['cb'],
      $_POST['cvac'],
      $_POST['infoscomp'],
      $_POST['supplitsstatus'],
      $_POST['supplits'],
      $_POST['suppsaisonstatus'],
      $_POST['suppsaison'],
      $_POST['supptidejstatus'],
      $_POST['supptidej'],
      $_POST['tidejcompris'],
      $_POST['suppdiversstatus'],
      $_POST['suppdiverstext'],
      $_POST['suppdivers']
    );
    $messageConfig = 'Saved configuration.';
    ?><meta http-equiv="refresh" content="0; url=admin.php?page=UBHBADMIN"><?php
  } else {
    $messageRoomError = 'Please enter a valid email address.';
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
        <h1><center>Configuration : </center></h1>
        <center>
          <?php
          $config = $wpdb->get_results("SELECT * FROM $config_table");
          if (isset($messageConfig)){
          echo '<div class="messageRoom">' . $messageConfig . '</div><br/>';
          }
          ?>
        <form method="POST" action="admin.php?page=UBHBADMIN">
          <label>Admin E-mail : <input type="text" style="max-width:250px;" name="adminmail" value="<?php echo $config[0]->adminmail ?>"/></label>
          <label>Max people : <select name="persmax" size="1" class="nbpersonnes">
             <?php
              for ($i=1; $i <= 4; $i++){
                if ($config[0]->personnesmax == $i){
                  echo '<option selected>' . $i . '</option>';
                } else {
                  echo '<option>' . $i . '</option>';
                }

              }
              ?></select></label>
              <label>Currency : <input type="text" style="max-width:30px;" name="devise" value="<?php echo $config[0]->devise ?>"/></label>
              <br/><br/>
              <td><input type="checkbox" name="fumeur" value="1" <?php echo $configManager->checkedOrNot($config[0]->fumeur); ?>/>Smoking</td> -
              <td><input type="checkbox" name="animaux" value="1" <?php echo $configManager->checkedOrNot($config[0]->animaux); ?>/>Animals</td> -
              <td><input type="checkbox" name="parking" value="1" <?php echo $configManager->checkedOrNot($config[0]->parking); ?>/>Parking</td> -
              <td><input type="checkbox" name="cb" value="1" <?php echo $configManager->checkedOrNot($config[0]->cb); ?>/>Credit card</td> -
              <td><input type="checkbox" name="cvac" value="1" <?php echo $configManager->checkedOrNot($config[0]->cvac); ?>/>Holiday vouchers</td>
              <br/><br/>
              <td>General info : <input type="text" name="infoscomp" value="<?php echo $config[0]->infoscomp ?>" class="infoscomp"/></td>
              <br/>
              <td><input type="checkbox" name="supplitsstatus" value="1" <?php echo $configManager->checkedOrNot($config[0]->supplitsstatus); ?>/>Extra Separated beds</td> -
              <td>Amount : <input type="text" name="supplits" value="<?php echo $config[0]->supplits ?>" class="supp"/></td> <?php echo $config[0]->devise ?>
              <br/>
              <td><input type="checkbox" name="suppsaisonstatus" value="1" <?php echo $configManager->checkedOrNot($config[0]->suppsaisonstatus); ?>/>Extra Season</td> -
              <td>Amount : <input type="text" name="suppsaison" value="<?php echo $config[0]->suppsaison ?>" class="supp"/></td> <?php echo $config[0]->devise ?>
              <br/>
              <td><input type="checkbox" name="supptidejstatus" value="1" <?php echo $configManager->checkedOrNot($config[0]->supptidejstatus); ?>/>Extra Breakfast</td> -
              <td>Amount : <input type="text" name="supptidej" value="<?php echo $config[0]->supptidej ?>" class="supp"/></td> <?php echo $config[0]->devise ?>
              -
              <td><input type="checkbox" name="tidejcompris" value="1" <?php echo $configManager->checkedOrNot($config[0]->tidejcompris); ?>/>Breakfast included</td>
              <br/>
              <td><input type="checkbox" name="suppdiversstatus" value="1" <?php echo $configManager->checkedOrNot($config[0]->suppdiversstatus); ?>/>Other supplement</td> -
              <td>Entitled : <input type="text" name="suppdiverstext" value="<?php echo $config[0]->suppdiverstext ?>" class="suppdiverstext"/></td>
              <td>Amount : <input type="text" name="suppdivers" value="<?php echo $config[0]->suppdivers ?>" class="supp"/></td> <?php echo $config[0]->devise ?>
              <br/><br/>
          <input type="submit" name="update" value="Save" class="btn"/>
        </form>
        </center>
      </div>
<br/>

<br/>
        <div>
        <h1><center>Rooms : </center></h1>
        <?php
        if (isset($messageRoom)){
        echo '<div class="messageRoom">' . $messageRoom . '</div><br/>';
        }
        if (isset($messageRoomError)){
        echo '<div class="messageRoomError">' . $messageRoomError . '</div><br/>';
        }
        ?>
        <center>
        <table>
          <tr>
          <th>Room</th>
          <th>Nb<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/lits.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/douche.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/wc.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/tel.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/tv.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/baignoire.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/wifi.svg" class="resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/clim.svg" class="resaImg"/></th>
          <th>Photo 300*300px</th>
          <th>1<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="resaImg"/></th>
          <th>2<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="resaImg"/></th>
          <th>3<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="resaImg"/></th>
          <th>4<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="resaImg"/></th>
          <th>Info</th>
          <th></th>
          </tr>
        <tr>
           <form method="POST" action="admin.php?page=UBHBADMIN" enctype="multipart/form-data">

            <td><input type="text" style="max-width:50px;" name="chambre"/></td>

            <td><select name="max" size="1" class="nbpersonnes">
               <?php
                for ($i=1; $i <= $config[0]->personnesmax; $i++){
                    echo '<option>' . $i . '</option>';
                }
                ?></select></td>

            <td><select name="lits" size="1" class="nbpersonnes">
               <?php
                for ($i=1; $i <= 4; $i++){
                    echo '<option>' . $i . '</option>';
                }
                ?></select></td>
            <td><input type="checkbox" name="douche" value="1"/></td>
            <td><input type="checkbox" name="wc" value="1"/></td>
            <td><input type="checkbox" name="tel" value="1"/></td>
            <td><input type="checkbox" name="tv" value="1"/></td>
            <td><input type="checkbox" name="baignoire" value="1"/></td>
            <td><input type="checkbox" name="wifi" value="1"/></td>
            <td><input type="checkbox" name="clim" value="1"/></td>
            <td><input type="file" name="photo"/></td>
            <td><input type="text" style="max-width:30px;" name="for1"/></td>
            <td><input type="text" style="max-width:30px;" name="for2"/></td>
            <td><input type="text" style="max-width:30px;" name="for3"/></td>
            <td><input type="text" style="max-width:30px;" name="for4"/></td>
            <td><input type="text" style="max-width:100px;" name="infosup"/></td>
            <td><input type="submit" name="ajouterChambre" value="Add"/></td>
            </form>
        </tr>
        <?php
        $room = $wpdb->get_results("SELECT * FROM $rooms_table");
        foreach ($room as $room) {
          echo '<td>', $room->chambre, '</td>';
          echo '<td>', $room->max, '</td>';
          echo '<td>', $room->lits, '</td>';

          $roomManager->imgOrNot($room->douche, 'douche');
          $roomManager->imgOrNot($room->wc, 'wc');
          $roomManager->imgOrNot($room->tel, 'tel');
          $roomManager->imgOrNot($room->tv, 'tv');
          $roomManager->imgOrNot($room->baignoire, 'baignoire');
          $roomManager->imgOrNot($room->wifi, 'wifi');
          $roomManager->imgOrNot($room->clim, 'clim');

          echo '<td><center><img src="' . esc_url( home_url( '/' ) ) . '/wp-content/plugins/ub_hotelbooking/web/img/rooms/', $room->photo,'" style="max-width:60px;"/></center></td>'; //$resAdmin->photo()
          echo '<td>', $room->for1, ' ', $config[0]->devise, '</td>';
          echo '<td>', $room->for2, ' ', $config[0]->devise, '</td>';
          echo '<td>', $room->for3, ' ', $config[0]->devise, '</td>';
          echo '<td>', $room->for4, ' ', $config[0]->devise, '</td>';
          echo '<td>', $room->infosup, '</td>';
          echo '<td><a href="admin.php?page=UBHBADMIN&supprimerRoom=', $room->id, '&photo=', $room->photo, '">Delete</a></td>';
          echo '</tr>';
        }
        ?>
            </table></center>
        </div>
        <br/><br/><br/><br/><br/><br/><br/>
    </body>
</html>
