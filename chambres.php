<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$rooms_table = $table_prefix . 'hb_rooms';

$db = Config::getMySqlPDO();

$resaManager = new ResaManager($db);

$roomManager = new RoomManager($db);

if (isset($_GET['supprimerRoom'])){
    $roomManager->deleteRoom((int) $_GET['supprimerRoom'], $_GET['photo']);
    //$message = 'La chambre a bien été supprimée';
    header('Location: admin.php');
}

if (isset($_GET['supprimerResa'])){
    $resaManager->deleteResa((int) $_GET['supprimerResa']);
    //$message = 'La chambre a bien été supprimée';

}

add_action('template_redirect', 'check_for_event_submissions');

function check_for_event_submissions(){
  if(isset($_POST['ajouterChambre'])) // && (get_query_var('pagename') === 'events)
    {
       // process your data here, you'll use wp_insert_post() I assume
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
       wp_redirect($_POST['redirect_url']); // add a hidden input with get_permalink()
       die();
    }

}
/*
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
*/



/*
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

    $room = new Room([
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
    ]);


    if ($room->isValid()){
        if (!empty($_FILES['photo']['name'])){
            $tailleMax = 2097152;
             if ($_FILES['photo']['size'] <= $tailleMax){
                $dimensions = getimagesize($_FILES['photo']['tmp_name']);
                $longueur = $dimensions[0];
                $largeur = $dimensions[1];
                 if ($longueur==300 && $largeur==300) {
                    $roomManager->addRoom($room);
                    $messageRoom = 'La chambre a bien été ajoutée !';

                    $roomManager->ajoutPhoto($_FILES['photo']['name'], $_FILES['photo']['tmp_name']);
                } else {
                     $messageRoomError = 'Votre photo doit faire 300*300px<br/>';
                 }
             } else {
                 $messageRoomError = 'Votre photo ne doit pas dépasser 2Mo<br/>';
             }
        } else {
            $roomManager->addRoom($room);
            $messageRoom = 'La chambre a bien été ajoutée !';
        }
    } else {
        $erreurs = $room->erreurs();
    }
    if (isset($erreurs) && in_array(Room::CHAMBRE_INVALIDE, $erreurs)) {
        $messageRoomError = 'Cette chambre existe déjà<br/>';
    }
    if (isset($erreurs) && in_array(Room::FOR1_INVALIDE, $erreurs)) {
        $messageRoomError = 'Il faut renseigner au moins un prix pour une personne.<br/>';
    }
    if (isset($erreurs) && in_array(Room::FOR2_INVALIDE, $erreurs)) {
        $messageRoomError = 'Il faut renseigner for1 et for2<br/>';
    }
    if (isset($erreurs) && in_array(Room::FOR3_INVALIDE, $erreurs)) {
        $messageRoomError = 'Il faut renseigner for1, for2 et for3<br/>';
    }
    if (isset($erreurs) && in_array(Room::FOR4_INVALIDE, $erreurs)) {
        $messageRoomError = 'Il faut renseigner for1, for2, for3 et for4<br/>';
    }
*/

//}



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












    $resaManuel = new Resa([
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
    ]);

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
           <form method="POST" action="admin.php" enctype="multipart/form-data">
            <td></td>




            <td><input type="text" style="max-width:50px;" name="chambre"/></td>




            <!--<td><input type="text" name="max" value="<?php if (isset($room)) echo $room->max(); ?>" style="max-width:30px;" /><br /></td>-->

            <td><select name="max" size="1" class="nbpersonnes">
               <?php
                for ($i=1; $i <= 4; $i++){
                    echo '<option>' . $i . '</option>';
                }
                ?></select></td>

            <!--<td><input type="text" style="max-width:30px;" name="max"/></td>-->
            <!--<td><input type="text" style="max-width:30px;" name="lits"/></td>-->

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
            <!--<td><input type="button" value="charger" name="photo"/>  </td>-->







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
          echo '<td><a href="?supprimerRoom=', $room->id, '&photo=', $room->photo, '">Supprimer</a></td>';
          echo '</tr>';
        }
        //print_r($post);


/*
        foreach ($roomManager->roomAdminList() as $resAdmin)
          {
            echo '<tr>';

            echo '<td>', $resAdmin->id(), '</td>';
            echo '<td>', $resAdmin->chambre(), '</td>';
            echo '<td>', $resAdmin->max(), '</td>';
            echo '<td>', $resAdmin->lits(), '</td>';
            echo '<td>', $roomManager->yesOrNo($resAdmin->douche()), '</td>';
            echo '<td>', $roomManager->yesOrNo($resAdmin->wc()), '</td>';
            echo '<td>', $roomManager->yesOrNo($resAdmin->tel()), '</td>';
            echo '<td>', $roomManager->yesOrNo($resAdmin->tv()), '</td>';
            echo '<td>', $roomManager->yesOrNo($resAdmin->baignoire()), '</td>';
            echo '<td>', $roomManager->yesOrNo($resAdmin->wifi()), '</td>';
            echo '<td><img src="web/img/rooms/', $resAdmin->photo(),'" style="max-width:60px;"/></td>'; //$resAdmin->photo()
            echo '<td>', $resAdmin->for1(), '</td>';
            echo '<td>', $resAdmin->for2(), '</td>';
            echo '<td>', $resAdmin->for3(), '</td>';
            echo '<td>', $resAdmin->for4(), '</td>';
            echo '<td>', $resAdmin->supp(), '</td>';
            echo '<td><a href="?supprimerRoom=', $resAdmin->id(), '&photo=', $resAdmin->photo(), '">Supprimer</a></td>';
*/
/*
            if($resAdmin->confirmclient() == 1)
            {
                echo '<td class="confirmoui">oui</td>';
            }else{
                echo '<td class="confirmnon">non</td>';
            }

            echo '<td> Editer | Supprimer </td>';
*/
/*
            echo '</tr>';
          }

*/
        ?>

            </table></center>
        </div>
        <br/><br/><br/><br/><br/><br/><br/>
    </body>
</html>
