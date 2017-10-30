<?php
require 'src/autoload.php';

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
       <link rel="stylesheet" type="text/css" href="web/css/style.css"/>
   </head>

    <body>
       <div>
        <h1><center>Reservations : </center></h1>
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
             <form method="POST" action="admin.php">
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
                foreach ($roomManager->roomAdminList() as $aroom){
                    echo '<option>' . $aroom->chambre() . '</option>';
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
                <td><input type="date" name="datedepart" value="<?php echo $selectedB; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></td>

                <td><input type="text" style="max-width:100px;" name="infos"/></td>
                <td></td>
                <td></td>
                <td><input type="checkbox" value="1" name="confirmclient"/></td>
                <td><input type="submit" name="ajouterResa" value="Ajouter"/></td>

              </form></tr>




        <?php

        foreach ($resaManager->resaList() as $resa)
          {
            echo '<tr>';

            echo '<td>', $resa->id(), '</td>';
            echo '<td>', $resa->nom(), '</td>';
            echo '<td>', $resa->email(), '</td>';
            echo '<td>', $resa->tel(), '</td>';
            echo '<td>', $resa->nombrep(), '</td>';
            echo '<td>', $resa->chambre(), '</td>';
            echo '<td>', $resa->datearrivee()->format('d/m/Y'), '</td>';
            echo '<td>', $resa->datedepart()->format('d/m/Y'), '</td>';
            echo '<td>', $resa->infos(), '</td>';
            echo '<td>', $resa->tarif(), ' €</td>';
            echo '<td>', $resa->nuits(), '</td>';
            if($resa->confirmclient() == 1)
            {
                echo '<td class="confirmoui">oui</td>';
            }else{
                echo '<td class="confirmnon">non</td>';
            }

            echo '<td><a href="?supprimerResa=', $resa->id(), '">Supprimer</a></td>';


            echo '</tr>';
          }

        ?>

        </table></center></div>
    </body>
</html>
