<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$rooms_table = $table_prefix . 'hb_rooms';

$roomManager = new RoomManager($wpdb);

if (isset($_GET['supprimerRoom'])){
    $message = 'La chambre a bien été supprimée';
    $wpdb->delete( 'wp_hb_rooms', array( 'ID' => $_GET['supprimerRoom'] ) );
}

if (isset($_POST['ajouterChambre'])){

  if(isset($_POST['douche'])){
      $douche = 1;
  } else {
      $douche = 0;
  }
  if(isset($_POST['wc'])){
      $wc = 1;
  } else {
      $wc = 0;
      }
  if(isset($_POST['tel'])){
      $tel = 1;
  } else {
      $tel = 0;
  }
  if(isset($_POST['tv'])){
      $tv = 1;
  } else {
      $tv = 0;
  }
  if(isset($_POST['baignoire'])){
      $baignoire = 1;
  } else {
      $baignoire = 0;
  }
  if(isset($_POST['wifi'])){
      $wifi = 1;
  } else {
      $wifi = 0;
  }

  $wpdb->insert(
    'wp_hb_rooms',
    array(
      'chambre' => $_POST['chambre'],
      'max' => $_POST['max'],
      'lits' => $_POST['lits'],
      'douche' => $douche,
      'wc' => $wc,
      'tel' => $tel,
      'tv' => $tv,
      'baignoire' => $baignoire,
      'wifi' => $wifi,
      'photo' => 'default',
      'for1' => $_POST['for1'],
      'for2' => $_POST['for2'],
      'for3' => $_POST['for3'],
      'for4' => $_POST['for4'],
      'supp' => $_POST['supp']
    ),
    array(
      '%s',
      '%d',
      '%d',
      '%d',
      '%d',
      '%d',
      '%d',
      '%d',
      '%d',
      '%s',
      '%d',
      '%d',
      '%d',
      '%d',
      '%d'
    )
  );
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
          <th>ID</th>
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
          <th>supp</th>
          <th>Actions</th>

          </tr>

        <tr>
           <form method="POST" action="admin.php?page=UBHB" enctype="multipart/form-data">
            <td></td>

            <td><input type="text" style="max-width:50px;" name="chambre"/></td>

            <td><select name="max" size="1" class="nbpersonnes">
               <?php
                for ($i=1; $i <= 4; $i++){
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
          echo '<td>', $room->id, '</td>';
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
          echo '<td>', $room->for1, '</td>';
          echo '<td>', $room->for2, '</td>';
          echo '<td>', $room->for3, '</td>';
          echo '<td>', $room->for4, '</td>';
          echo '<td>', $room->supp, '</td>';
          echo '<td><a href="admin.php?page=UBHB&supprimerRoom=', $room->id, '&photo=', $room->photo, '">Supprimer</a></td>';
          echo '</tr>';
        }

        ?>
            </table></center>
        </div>
        <br/><br/><br/><br/><br/><br/><br/>
    </body>
</html>
