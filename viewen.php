<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';
$rooms_table = $table_prefix . 'hb_rooms';

$manager = new RoomManager($wpdb);
$resaManager = new ResaManager($wpdb);

$timezone = "Europe/Paris";
date_default_timezone_set($timezone);
$mindaya = date("Y-m-d");
$mindayd = date("Y-m-d", strtotime($mindaya . ' +1 day'));
$maxday = date("Y-m-d", strtotime($mindaya . ' +1 YEAR'));

$selectedA = $mindaya;
$selectedB = $mindayd;

$personnesMax = 4;

if (isset($_POST['nombrepersonnes'])){
  $nombreDePersonnes = $_POST['nombrepersonnes'];
} else {
  $nombreDePersonnes = 1;
}
if(isset($_POST['selecteddate'])){
  $selectedA = $_POST['arrivee'];
  $selectedB = $_POST['depart'];
}



if(isset($_POST['reserver'])){
  if(!empty($_POST['nom'])){
    $email = $_POST['email'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $cle = md5(microtime(TRUE)*100000);

      $resaManager->resaAuto(
        $_POST['nom'],
        $_POST['email'],
        $_POST['tel'],
        $_GET['nbp'],
        $_GET['chambre'],
        $_GET['chambreid'],
        $_GET['dateA'],
        $_GET['dateB'],
        $_POST['infos'],
        $_GET['tarif'],
        $_GET['nuits'],
        0,
        $cle
      );

      $resaManager->sendMail($email, $cle, $_POST['nom']);


    } else {
      $messageResaError = 'Merci de renseigner une adresse e-mail valide.';
    }

  }else{
    $messageResaError = 'Merci de renseigner votre nom.';
  }

  }

/*
  if (isset($message)){
  echo $message . '<br/>';
*/

if (isset($_GET['chambre']) && isset($_GET['chambreid']) ){

  ?>
  <!DOCTYPE HTML>
  <html>
    <head>
       <title>Hotel Booking Page</title>
       <meta charset="utf-8"/>
       <link rel="stylesheet" type="text/css" href="../wp-content/plugins/ub_hotelbooking/web/css/style.css">
     </head>
     <body>
        <div><center><a href="." class="retour">Retour</a></center></div>
        <br/>
        <?php
        if (isset($messageResa)){
        echo '<div class="messageResa">' . $messageResa . '</div><br/>';
        }
        if (isset($messageResaError)){
        echo '<div class="messageResaError">' . $messageResaError . '</div><br/>';
        }
        ?>
        <br/>
        <center>

        <?php

        foreach($manager->roomAlone($_GET['chambre']) as $room){
          echo '<div>';
          echo '<center>';
          echo '<img src="../wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';
          echo '<br/>';
          echo '<div class="titre">Chambre ' . $room->chambre . '</div>';
          echo '<div class="infos">';
          echo $room->max . $manager->returnImg('max');
          echo $room->lits . $manager->returnImg('lits');
          echo '</div>';
          echo '<div class="tarif">' . $_GET['tarif'] . ' € - ' . $_GET['nuits'] . ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/nuit.png" class="icon" /></div>';

          if ($room->douche == 1){
            echo $manager->returnImg('douche');
          }
          if ($room->wc == 1){
            echo $manager->returnImg('wc');
          }
          if ($room->tel == 1){
            echo $manager->returnImg('tel');
          }
          if ($room->tv == 1){
            echo $manager->returnImg('tv');
          }
          if ($room->baignoire == 1){
            echo $manager->returnImg('baignoire');
          }
          if ($room->wifi == 1){
            echo $manager->returnImg('wifi');
          }

          echo '</center>';
          echo '</div>';
        }

        ?>

        <br/><br/>

        <form method="POST" action="">



          <label>Votre nom* : <br/><input type="text" name="nom" class="champs"/></label>
          <br/>
          <label>Votre e-mail* : <br/><input type="text" name="email" class="champs"/></label>
          <br/>
          <label>Téléphone : <br/><input type="text" name="tel" class="champs"/></label>
          <br/>
          <label>Informations complémentaires : <br/><input type="text" name="infos" class="champs"/></label>
          <br/><br/>

          Arrivée le <strong><?php $dateA = $_GET['dateA']; echo date("d-m-Y", strtotime($dateA));?></strong>
          <br/>
          Départ le <strong><?php $dateB = $_GET['dateB']; echo date("d-m-Y", strtotime($dateB));?></strong>
          <br/>
          Nombre de personnes : <strong><?php echo $_GET['nbp']; ?></strong>
          <br/>
          Total : <strong><?php echo $_GET['tarif']; ?> euros pour <?php echo $_GET['nuits']; ?> nuits.</strong>
          <br/>


          <?php


          if ($_GET['nbp'] == 2){
              ?>
              <label>Option lit séparé : ( + 5€ ) <input type="checkbox" value="lit" name="litsep" /> </label>
              <?php
          }

          ?>

          <br/><br/>

          <input type="submit" name="reserver" value="Réserver" class="btn"/>

          </form>
          </center>

        </body>
      </html>


















    <?php
  } else if(isset($_GET['do']) && isset($_GET['id']) && isset($_GET['cle'])){
    if($_GET['do'] == 'confirm'){
      $check = $resaManager->confirmResa($_GET['id'], $_GET['cle']);
      if ($check == 'valid'){
        $messageResa = 'Réservation confirmée !';
        header("refresh:5;url=index.php");
      } else if ($check == 'notvalid'){
        $messageResaError = 'La clé ne correspond pas !';
      } else if ($check == 'already'){
        $messageResaError = 'Cette réservation a déjà été confirmée !';
      }

    } else if($_GET['do'] == 'cancel'){
      $check = $resaManager->annulResa($_GET['id'], $_GET['cle']);

      if ($check == 'valid'){
        $messageResa = 'Réservation annulée !';
      } else if ($check == 'notvalid'){
        $messageResaError = 'La clé ne correspond pas !';
      } else if ($check == 'dontexist'){
        $messageResaError = 'Cette réservation a déjà été annulée !';
      }
    }
    ?>
    <!DOCTYPE HTML>
    <html>
       <head>
           <title>Hotel Booking Page</title>
           <meta charset="utf-8"/>
           <meta http-equiv="refresh" content="3; url=.">
           <link rel="stylesheet" type="text/css" href="../wp-content/plugins/ub_hotelbooking/web/css/style.css">
       </head>
       <body>

         <?php
         if (isset($messageResa)){
         echo '<div class="messageResa">' . $messageResa . '</div><br/>';
         }
         if (isset($messageResaError)){
         echo '<div class="messageResaError">' . $messageResaError . '</div><br/>';
         }
         ?>
         <div class="messageResa">Vous allez être redirigé dans 3 secondes...</div><br/>
       </body>
       </html>




























       <?php

    } else {
          ?>
          <!DOCTYPE HTML>
          <html>
             <head>
                 <title>Hotel Booking Page</title>
                 <meta charset="utf-8"/>
                 <link rel="stylesheet" type="text/css" href="../wp-content/plugins/ub_hotelbooking/web/css/style.css">
             </head>
             <body>

          <div class="recherche">
          <form method="POST" action=".">
          <center>
          <label>Arrival &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="date" name="arrivee" value="<?php echo $selectedA; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></label>
          <label>Start &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="date" name="depart" value="<?php echo $selectedB; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></label>

          <div class="choixnbpersonnes">
          <p><label>Number of persons :
          <select name="nombrepersonnes" size="1" class="nbpersonnes">
             <?php
              for ($i = 1; $i <= $personnesMax; $i++){
                  if ($nombreDePersonnes == $i){
                      echo '<option selected>' . $i . '</option>';
                  } else {
                      echo '<option>' . $i . '</option>';
                  }
              }
             ?>
          </select>
          </label>
              </p>
      </div>
          <input type="submit" name="selecteddate" class="btn" value="Search"/>
          </center>
          </form>
          </div>
             <div class="liste">
              <?php
              if (isset($_POST['arrivee']) && isset($_POST['depart'])){
                $countRoom = 0;
                  foreach ($manager->roomList($_POST['arrivee'], $_POST['depart'], $_POST['nombrepersonnes']) as $room){
                      $countRoom += 1;
                      echo '<div class="rchambre">';
                      echo '<center>';
                      $pagename = basename(get_permalink());
                      $nbreNuits = $resaManager->nombreNuits($_POST['arrivee'], $_POST['depart']);
                      $tarif = $resaManager->calculTarif($nbreNuits, $room->id, $_POST['nombrepersonnes']);
                      echo '<a href="../' . $pagename . '/?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $_POST['depart'] . '&nuits=' . $nbreNuits . '&tarif=' . $tarif . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '" >';
                      echo '<img src="../wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';
                      echo '<br/>';
                      echo '<div class="titre">Chambre ' . $room->chambre . '</div>';
                      echo '<div class="infos">';
                      $maxImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/max.png"/>';
                      $litsImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/lits.png"/>';
                      echo $room->max . $maxImg . $room->lits . $litsImg;
                      echo '</div>';

                      echo '<div class="tarif">' . $tarif . ' € - ' . $nbreNuits . ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/nuit.png" class="icon" /></div>';

                      echo '<div class="option">';
                      if ($room->douche == 1){
                        echo $manager->returnImg('douche');
                      }
                      if ($room->wc == 1){
                        echo $manager->returnImg('wc');
                      }
                      if ($room->tel == 1){
                        echo $manager->returnImg('tel');
                      }
                      if ($room->tv == 1){
                        echo $manager->returnImg('tv');
                      }
                      if ($room->baignoire == 1){
                        echo $manager->returnImg('baignoire');
                      }
                      if ($room->wifi == 1){
                        echo $manager->returnImg('wifi');
                      }
                      echo '</div>';
                      echo '</a>';
                      echo '</center>';
                      echo '</div>';
                  }
                  if ($countRoom == 0){
                    foreach ($manager->roomListBis($_POST['arrivee'], $_POST['depart'], $_POST['nombrepersonnes']) as $room){
                        $countRoom += 1;
                        echo '<div class="rchambre">';
                        echo '<center>';
                        $pagename = basename(get_permalink());
                        $nbreNuits = $resaManager->nombreNuits($_POST['arrivee'], $_POST['depart']);
                        $tarif = $resaManager->calculTarif($nbreNuits, $room->id, $_POST['nombrepersonnes']);
                        echo '<a href="../' . $pagename . '/?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $_POST['depart'] . '&nuits=' . $nbreNuits . '&tarif=' . $tarif . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '" >';
                        echo '<img src="../wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';
                        echo '<br/>';
                        echo '<div class="titre">Chambre ' . $room->chambre . '</div>';
                        echo '<div class="infos">';
                        $maxImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/max.png"/>';
                        $litsImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/lits.png"/>';
                        echo $room->max . $maxImg . $room->lits . $litsImg;
                        echo '</div>';

                        echo '<div class="tarif">' . $tarif . ' € - ' . $nbreNuits . ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/nuit.png" class="icon" /></div>';

                        echo '<div class="option">';
                        if ($room->douche == 1){
                          echo $manager->returnImg('douche');
                        }
                        if ($room->wc == 1){
                          echo $manager->returnImg('wc');
                        }
                        if ($room->tel == 1){
                          echo $manager->returnImg('tel');
                        }
                        if ($room->tv == 1){
                          echo $manager->returnImg('tv');
                        }
                        if ($room->baignoire == 1){
                          echo $manager->returnImg('baignoire');
                        }
                        if ($room->wifi == 1){
                          echo $manager->returnImg('wifi');
                        }
                        echo '</div>';
                        echo '</a>';
                        echo '</center>';
                        echo '</div>';
                      }
                  }


         }
       ?>
     </div>
     </body>
     </html>
<?php
   }
?>