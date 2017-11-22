<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$rooms_table = $table_prefix . 'hb_rooms';
$config_table = $table_prefix . 'hb_config';

$roomManager = new RoomManager($wpdb);
$configManager = new ConfigManager($wpdb);

if (isset($_GET['supprimerRoom'])){
    $messageRoom = 'La chambre a bien été supprimée';

    $roomManager->deleteRoom($_GET['supprimerRoom'], $_GET['photo']);

    ?><meta http-equiv="refresh" content="0; url=admin.php?page=UBHB"><?php
}

if (isset($_POST['ajouterChambre'])){

  if($roomManager->chambreExistance($_POST['chambre']) > 0){
    $messageRoomError = 'Ce nom de chambre existe déjà.';
  } else {
    if (!empty($_FILES['photo']['name'])){
        $tailleMax = 2097152;
         if ($_FILES['photo']['size'] <= $tailleMax){
            $dimensions = getimagesize($_FILES['photo']['tmp_name']);
            $longueur = $dimensions[0];
            $largeur = $dimensions[1];
             if ($longueur==300 && $largeur==300) {
               $roomManager->addRoom($_POST['chambre'], $_POST['max'], $_POST['lits'], $_POST['douche'], $_POST['wc'], $_POST['tel'], $_POST['tv'], $_POST['baignoire'], $_POST['wifi'], $_POST['photo'], $_POST['for1'], $_POST['for2'], $_POST['for3'], $_POST['for4'], $_POST['supp']);
               $messageRoom = 'La chambre a bien été ajoutée';

                $roomManager->ajoutPhoto($_FILES['photo']['name'], $_FILES['photo']['tmp_name']);
            } else {
                 $messageRoomError = 'Votre photo doit faire 300*300px<br/>';
             }
         } else {
             $messageRoomError = 'Votre photo ne doit pas dépasser 2Mo<br/>';
         }
    } else {
      $roomManager->addRoom($_POST['chambre'], $_POST['max'], $_POST['lits'], $_POST['douche'], $_POST['wc'], $_POST['tel'], $_POST['tv'], $_POST['baignoire'], $_POST['wifi'], $_POST['photo'], $_POST['for1'], $_POST['for2'], $_POST['for3'], $_POST['for4'], $_POST['supp']);
      $messageRoom = 'La chambre a bien été ajoutée';
    }
  }
}

if (isset($_POST['update'])){

  if (filter_var($_POST['adminmail'], FILTER_VALIDATE_EMAIL)) {
    $configManager->update($_POST['adminmail'], $_POST['persmax'], $_POST['devise']);
  } else {
    $messageRoomError = 'Merci d\'entrer une adresse e-mail valide.';
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
        <h1><center>Configuration : </center></h1>
        <center>
          <?php
          $config = $wpdb->get_results("SELECT * FROM $config_table");
          ?>
        <form method="POST" action="admin.php?page=UBHB">
          <label>Email admin : <input type="text" style="max-width:250px;" name="adminmail" value="<?php echo $config[0]->adminmail ?>"/></label>

          <label>Personnnes Max : <select name="persmax" size="1" class="nbpersonnes">
             <?php
              for ($i=1; $i <= 10; $i++){
                if ($config[0]->personnesmax == $i){
                  echo '<option selected>' . $i . '</option>';
                } else {
                  echo '<option>' . $i . '</option>';
                }

              }
              ?></select></label>

              <label>Devise : <input type="text" style="max-width:30px;" name="devise" value="<?php echo $config[0]->devise ?>"/></label>

          <input type="submit" name="update" value="Enregistrer"/>
        </form>
        </center>
      </div>
<br/>

<br/>
        <div>
        <h1><center>Chambres : </center></h1>
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
          <th>Chambre</th>
          <th>Capacité</th>
          <th>Lits</th>
          <th>douche</th>
          <th>wc</th>
          <th>tel</th>
          <th>tv</th>
          <th>bain</th>
          <th>wifi</th>
          <th>photo 300*300px</th>
          <th>for1</th>
          <th>for2</th>
          <th>for3</th>
          <th>for4</th>
          <th>2Lits</th>
          <th>Actions</th>
          </tr>
        <tr>
           <form method="POST" action="admin.php?page=UBHB" enctype="multipart/form-data">


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
            <td><input type="file" name="photo"/></td>
            <td><input type="text" style="max-width:30px;" name="for1"/></td>
            <td><input type="text" style="max-width:30px;" name="for2"/></td>
            <td><input type="text" style="max-width:30px;" name="for3"/></td>
            <td><input type="text" style="max-width:30px;" name="for4"/></td>
            <td><input type="text" style="max-width:30px;" name="supp"/></td>
            <td><input type="submit" name="ajouterChambre" value="Ajouter"/></td>
            </form>
        </tr>
        <?php
        $room = $wpdb->get_results("SELECT * FROM $rooms_table");
        foreach ($room as $room) {
          echo '<td>', $room->chambre, '</td>';
          echo '<td>', $room->max, '</td>';
          echo '<td>', $room->lits, '</td>';
          echo '<td>', $roomManager->yesOrNo($room->douche), '</td>';
          echo '<td>', $roomManager->yesOrNo($room->wc), '</td>';
          echo '<td>', $roomManager->yesOrNo($room->tel), '</td>';
          echo '<td>', $roomManager->yesOrNo($room->tv), '</td>';
          echo '<td>', $roomManager->yesOrNo($room->baignoire), '</td>';
          echo '<td>', $roomManager->yesOrNo($room->wifi), '</td>';
          echo '<td><center><img src="../wp-content/plugins/ub_hotelbooking/web/img/rooms/', $room->photo,'" style="max-width:60px;"/></center></td>'; //$resAdmin->photo()
          echo '<td>', $room->for1, ' ', $config[0]->devise, '</td>';
          echo '<td>', $room->for2, ' ', $config[0]->devise, '</td>';
          echo '<td>', $room->for3, ' ', $config[0]->devise, '</td>';
          echo '<td>', $room->for4, ' ', $config[0]->devise, '</td>';
          echo '<td>', $room->supp, ' ', $config[0]->devise, '</td>';
          echo '<td><a href="admin.php?page=UBHB&supprimerRoom=', $room->id, '&photo=', $room->photo, '">Supprimer</a></td>';
          echo '</tr>';
        }
        ?>
            </table></center>
        </div>
        <br/><br/><br/><br/><br/><br/><br/>
    </body>
</html>
