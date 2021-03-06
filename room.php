<?php

require 'src/autoload.php';

global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';
$rooms_table = $table_prefix . 'hb_rooms';
$config_table = $table_prefix . 'hb_config';

$roomManager = new RoomManager();
$configManager = new ConfigManager();

if (isset($_GET['supprimerRoom'])){
    $messageRoom = 'La chambre a été supprimée.';

    $roomManager->deleteRoom($_GET['supprimerRoom'], $_GET['photo']);

    ?><meta http-equiv="refresh" content="0; url=admin.php?page=UBHBADMIN"><?php
}

if (isset($_POST['ajouterChambre'])){

  if($roomManager->chambreExistance($_POST['chambre']) > 0){
    $messageRoomError = 'Ce nom de chambre existe déjà !';
  } else {
    if (!empty($_FILES['photo']['name'])){
        $tailleMax = 2097152;
         if ($_FILES['photo']['size'] <= $tailleMax){
            $dimensions = getimagesize($_FILES['photo']['tmp_name']);
            $longueur = $dimensions[0];
            $largeur = $dimensions[1];
             if ($longueur==300 && $largeur==300) {
               $roomManager->addRoom($_POST['chambre'], $_POST['max'], $_POST['lits'], $_POST['douche'], $_POST['wc'], $_POST['tel'], $_POST['tv'], $_POST['baignoire'], $_POST['wifi'], $_POST['clim'], $_POST['photo'], $_POST['for1'], $_POST['for2'], $_POST['for3'], $_POST['for4'], $_POST['infosupen'], $_POST['infosupfr']);
               $messageRoom = 'La chambre a été ajoutée.';

                $roomManager->ajoutPhoto($_FILES['photo']['name'], $_FILES['photo']['tmp_name']);
            } else {
                 $messageRoomError = 'Votre photo doit être de 300 * 300px<br/>';
             }
         } else {
             $messageRoomError = 'Votre photo ne doit pas dépasser les 2Mo<br/>';
         }
    } else {
      $roomManager->addRoom($_POST['chambre'], $_POST['max'], $_POST['lits'], $_POST['douche'], $_POST['wc'], $_POST['tel'], $_POST['tv'], $_POST['baignoire'], $_POST['wifi'], $_POST['clim'], $_POST['photo'], $_POST['for1'], $_POST['for2'], $_POST['for3'], $_POST['for4'], $_POST['infosupen'], $_POST['infosupfr']);
      $messageRoom = 'La chambre a été ajoutée.';
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
      $_POST['infoscompen'],
      $_POST['infoscompfr'],
      $_POST['supplitsstatus'],
      $_POST['supplits'],
      $_POST['suppsaisonstatus'],
      $_POST['suppsaison'],
      $_POST['supptidejstatus'],
      $_POST['supptidej'],
      $_POST['tidejcompris'],
      $_POST['suppdiversstatus'],
      $_POST['suppdiverstexten'],
      $_POST['suppdiverstextfr'],
      $_POST['suppdivers']
    );
    $messageConfig = 'Configuration sauvegardée';
    ?><meta http-equiv="refresh" content="0; url=admin.php?page=UBHBADMIN"><?php
  } else {
    $messageRoomError = 'Merci de renseigner une adresse e-mail valide.';
  }
}
?>

<!DOCTYPE HTML>
<html>
   <head>
       <title>Chambres</title>
       <meta charset="utf-8"/>
       <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css"/>
   </head>
    <body>
      <div>
        <h1><center>Configuration : </center></h1>
        <br/>
        <center>
          <?php
          $config = $wpdb->get_results("SELECT * FROM $config_table");
          if (isset($messageConfig)){
          echo '<div class="ub-messageRoom">' . $messageConfig . '</div><br/>';
          }
          ?>
        <form method="POST" action="admin.php?page=UBHBADMIN">
          <label>E-mail de réservation : <input type="text" style="max-width:250px;" name="adminmail" value="<?php echo $config[0]->adminmail ?>"/></label>
          <label>Personnes maximum : <select name="persmax" size="1" class="ub-nbpersonnes">
             <?php
              for ($i=1; $i <= 4; $i++){
                if ($config[0]->personnesmax == $i){
                  echo '<option selected>' . $i . '</option>';
                } else {
                  echo '<option>' . $i . '</option>';
                }

              }
              ?></select></label>
              <label>Devise : <input type="text" style="max-width:30px;" name="devise" value="<?php echo $config[0]->devise ?>"/></label>
              <br/><br/>
              <?php
              if ($configManager->checkedOrNot($config[0]->fumeur) == 'checked'){
                $fumeurIcon = 'fumeur.svg';
              } else {
                $fumeurIcon = 'nofumeur.svg';
              }
              ?>
              <td><input type="checkbox" name="fumeur" value="1" <?php echo $configManager->checkedOrNot($config[0]->fumeur); ?>/><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/<?php echo $fumeurIcon ?>" class="ub-resaImgList"/>Fumeur</td> -
              <?php
              if ($configManager->checkedOrNot($config[0]->animaux) == 'checked'){
                $animauxIcon = 'animaux.svg';
              } else {
                $animauxIcon = 'noanimaux.svg';
              }
              ?>
              <td><input type="checkbox" name="animaux" value="1" <?php echo $configManager->checkedOrNot($config[0]->animaux); ?>/><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/<?php echo $animauxIcon ?>" class="ub-resaImgList"/>Animaux</td> -
              <?php
              if ($configManager->checkedOrNot($config[0]->parking) == 'checked'){
                $parkingIcon = 'parking.svg';
              } else {
                $parkingIcon = 'noparking.svg';
              }
              ?>
              <td><input type="checkbox" name="parking" value="1" <?php echo $configManager->checkedOrNot($config[0]->parking); ?>/><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/<?php echo $parkingIcon ?>" class="ub-resaImgList"/>Parking</td> -
              <?php
              if ($configManager->checkedOrNot($config[0]->cb) == 'checked'){
                $cbIcon = 'cb.svg';
              } else {
                $cbIcon = 'nocb.svg';
              }
              ?>
              <td><input type="checkbox" name="cb" value="1" <?php echo $configManager->checkedOrNot($config[0]->cb); ?>/><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/<?php echo $cbIcon ?>" class="ub-resaImgList"/>Carte de crédit</td> -
              <?php
              if ($configManager->checkedOrNot($config[0]->cvac) == 'checked'){
                $cvacIcon = 'cvac.svg';
              } else {
                $cvacIcon = 'nocvac.svg';
              }
              ?>
              <td><input type="checkbox" name="cvac" value="1" <?php echo $configManager->checkedOrNot($config[0]->cvac); ?>/><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/<?php echo $cvacIcon ?>" class="ub-resaImgList"/>Chèques vacances</td>
              <br/><br/>
              <td>EN - Informations générales : <input type="text" name="infoscompen" value="<?php echo $config[0]->infoscompen ?>" class="ub-infoscomp"/></td><br/>
              <td>FR - Informations générales : <input type="text" name="infoscompfr" value="<?php echo $config[0]->infoscompfr ?>" class="ub-infoscomp"/></td>
              <br/>
              <td><input type="checkbox" name="supplitsstatus" value="1" <?php echo $configManager->checkedOrNot($config[0]->supplitsstatus); ?>/>Option lits séparés</td> -
              <td>Montant : <input type="text" name="supplits" value="<?php echo $config[0]->supplits ?>" class="ub-supp"/></td> <?php echo $config[0]->devise ?>
              <br/>
              <td><input type="checkbox" name="suppsaisonstatus" value="1" <?php echo $configManager->checkedOrNot($config[0]->suppsaisonstatus); ?>/>Supplément saison</td> -
              <td>Montant : <input type="text" name="suppsaison" value="<?php echo $config[0]->suppsaison ?>" class="ub-supp"/></td> <?php echo $config[0]->devise ?>
              <br/>
              <td><input type="checkbox" name="supptidejstatus" value="1" <?php echo $configManager->checkedOrNot($config[0]->supptidejstatus); ?>/>Supplément petit déjeuner</td> -
              <td>Montant : <input type="text" name="supptidej" value="<?php echo $config[0]->supptidej ?>" class="ub-supp"/></td> <?php echo $config[0]->devise ?>
              -
              <td><input type="checkbox" name="tidejcompris" value="1" <?php echo $configManager->checkedOrNot($config[0]->tidejcompris); ?>/>Petit déjeuner inclus</td>
              <br/>
              <td><input type="checkbox" name="suppdiversstatus" value="1" <?php echo $configManager->checkedOrNot($config[0]->suppdiversstatus); ?>/>Autre supplément</td> -
              <td>EN-Intitulé : <input type="text" name="suppdiverstexten" value="<?php echo $config[0]->suppdiverstexten ?>" class="suppdiverstexten"/></td>
              <td>FR-Intitulé : <input type="text" name="suppdiverstextfr" value="<?php echo $config[0]->suppdiverstextfr ?>" class="suppdiverstextfr"/></td>
              <td>Montant : <input type="text" name="suppdivers" value="<?php echo $config[0]->suppdivers ?>" class="ub-supp"/></td> <?php echo $config[0]->devise ?>
              <br/><br/>
          <input type="submit" name="update" value="Sauvegarder" class="ub-btn"/>
        </form>
        </center>
      </div>
<br/>

<br/>
        <div class="ub-Tab">
        <h1><center>Chambres : </center></h1>
        <br/>
        <?php
        if (isset($messageRoom)){
        echo '<div class="ub-messageRoom">' . $messageRoom . '</div><br/>';
        }
        if (isset($messageRoomError)){
        echo '<div class="ub-messageRoomError">' . $messageRoomError . '</div><br/>';
        }
        ?>
        <center>
        <table>
          <tr>
          <th>Chambre</th>
          <th>Nb<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/lits.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/douche.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/wc.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/tel.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/tv.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/baignoire.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/wifi.svg" class="ub-resaImg"/></th>
          <th><img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/clim.svg" class="ub-resaImg"/></th>
          <th>Photo 300*300px</th>
          <th>1<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="ub-resaImg"/></th>
          <th>2<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="ub-resaImg"/></th>
          <th>3<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="ub-resaImg"/></th>
          <th>4<img src="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="ub-resaImg"/></th>
          <th>EN-Info</th>
          <th>FR-Info</th>
          <th></th>
          </tr>
        <tr>
           <form method="POST" action="admin.php?page=UBHBADMIN" enctype="multipart/form-data">

            <td><input type="text" style="max-width:50px;" name="chambre"/></td>

            <td><select name="max" size="1" class="ub-nbpersonnes">
               <?php
                for ($i=1; $i <= $config[0]->personnesmax; $i++){
                    echo '<option>' . $i . '</option>';
                }
                ?></select></td>

            <td><select name="lits" size="1" class="ub-nbpersonnes">
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
            <td><input type="text" style="max-width:100px;" name="infosupen"/></td>
            <td><input type="text" style="max-width:100px;" name="infosupfr"/></td>
            <td><input type="submit" name="ajouterChambre" value="Ajouter" class="ub-add"/></td>
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
          echo '<td>', $room->infosupen, '</td>';
          echo '<td>', $room->infosupfr, '</td>';
          echo '<td><a href="admin.php?page=UBHBADMIN&supprimerRoom=', $room->id, '&photo=', $room->photo, '"><img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/delete.svg" class="ub-icon"/></a></td>';
          echo '</tr>';
        }
        ?>
            </table></center>
        </div>
        <br/><br/><br/><br/><br/><br/><br/>
    </body>
</html>
