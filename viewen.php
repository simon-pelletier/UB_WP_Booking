<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';
$rooms_table = $table_prefix . 'hb_rooms';
$config_table = $table_prefix . 'hb_config';

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
      $chambreid = (int)$_GET['chambreid'];

      if(isset($_POST['litsep'])){
          $supp = 1;

          global $wpdb, $table_prefix;
          $room_table = $table_prefix . 'hb_rooms';
          $room = $wpdb->get_results("SELECT * FROM $rooms_table WHERE id = $chambreid");

          $tarif = (int)$_GET['tarif'] + ((int)$_GET['nuits'] * (int)$room[0]->supp);
      } else {
          $supp = 0;
          $tarif = $_GET['tarif'];
      }

      $resaManager->resaAuto(
        $_POST['nom'],
        $_POST['email'],
        $_POST['tel'],
        $_GET['nbp'],
        $_GET['chambre'],
        $chambreid,
        $_GET['dateA'],
        $_GET['dateB'],
        $_POST['infos'],
        $tarif,
        $_GET['nuits'],
        0,
        $cle,
        $supp
      );

      $resaManager->sendMail($email, $cle, $_POST['nom'], $room[0]->supp);

      $messageResa = 'You will receive an e-mail to confirm your reservation<br/>
      You will be redirected in 3 seconds ...
      <br/>';
      ?><meta http-equiv="refresh" content="3; url=."><?php

    } else {
      $messageResaError = 'Please enter a valid email address.';
    }

  }else{
    $messageResaError = 'Please fill in your name.';
  }

  }

/*
  if (isset($message)){
  echo $message . '<br/>';
*/

if (isset($_GET['chambre']) && isset($_GET['chambreid']) ){
  global $wpdb, $table_prefix;
  $config_table = $table_prefix . 'hb_config';
  $getConfig = $wpdb->get_results("SELECT * FROM $config_table WHERE id = 1");
  ?>
  <!DOCTYPE HTML>
  <html>
    <head>
       <title>Hotel Booking Page</title>
       <meta charset="utf-8"/>
       <link rel="stylesheet" type="text/css" href="../wp-content/plugins/ub_hotelbooking/web/css/style.css">
     </head>
     <body>
        <div><center><a href="." class="retour">Return</a></center></div>
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
          echo '<div class="titre">Room ' . $room->chambre . '</div>';
          echo '<div class="infos">';
          echo $room->max . $manager->returnImg('max');
          echo $room->lits . $manager->returnImg('lits');
          echo '</div>';
          echo '<div class="tarif">' . $_GET['tarif'] . ' ' . $getConfig[0]->devise . ' - ' . $_GET['nuits'] . ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/nuit.png" class="icon" /></div>';

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
          <label>Your Name* : <br/><input type="text" name="nom" class="champs"/></label>
          <br/>
          <label>Your e-mail* : <br/><input type="text" name="email" class="champs"/></label>
          <br/>
          <label>Your Phone Number : <br/><input type="text" name="tel" class="champs"/></label>
          <br/>
          <label>Informations, additional requests : <br/><input type="text" name="infos" class="champs"/></label>
          <br/><br/>

          Date of your arrival : <strong><?php $dateA = $_GET['dateA']; echo date("d-m-Y", strtotime($dateA));?></strong>
          <br/>
          Date of your departure : <strong><?php $dateB = $_GET['dateB']; echo date("d-m-Y", strtotime($dateB));?></strong>
          <br/>
          Number of persons : <strong><?php echo $_GET['nbp']; ?></strong>
          <br/>
          Total : <strong><?php echo $_GET['tarif'] . ' ' . $getConfig[0]->devise  ?> for <?php echo $_GET['nuits']; ?> night(s).</strong>
          <br/><br/>

          <?php
          if ($_GET['nbp'] == 2){
              ?>
              <label>Separate bed option : ( + <?php echo $room->supp . ' ' . $getConfig[0]->devise ?> ) <input type="checkbox" value="1" name="litsep" /> </label>
              <?php
          }
          ?>

          <br/><br/>
          <input type="submit" name="reserver" value="Book" class="btn"/>
          </form>
          </center>
        </body>
      </html>













    <?php
  } else if(isset($_GET['do']) && isset($_GET['id']) && isset($_GET['cle'])){
    if($_GET['do'] == 'confirm'){
      $check = $resaManager->confirmResa($_GET['id'], $_GET['cle']);
      if ($check == 'valid'){
        $messageResa = 'Confirmed reservation !';
      } else if ($check == 'notvalid'){
        $messageResaError = 'The key does not match !';
      } else if ($check == 'already'){
        $messageResaError = 'This reservation has already been confirmed !';
      }

    } else if($_GET['do'] == 'cancel'){
      $check = $resaManager->annulResa($_GET['id'], $_GET['cle']);

      if ($check == 'valid'){
        $messageResa = 'Reservation canceled !';
      } else if ($check == 'notvalid'){
        $messageResaError = 'The key does not match !';
      } else if ($check == 'dontexist'){
        $messageResaError = 'This reservation has already been confirmed !';
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
         <div class="messageResa">You will be redirected in 3 seconds ...</div><br/>
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
          <label>Arrival date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="date" name="arrivee" value="<?php echo $selectedA; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></label>
          <label>Date of departure &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="date" name="depart" value="<?php echo $selectedB; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></label>

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
                $getConfig = $wpdb->get_results("SELECT * FROM $config_table WHERE id = 1");
                $countRoom = 0;
                  foreach ($manager->roomList($_POST['arrivee'], $_POST['depart'], $_POST['nombrepersonnes']) as $room){
                      $countRoom += 1;
                      echo '<div class="rchambre">';
                      echo '<center>';
                      $pagename = basename(get_permalink());
                      $nbreNuits = $resaManager->nombreNuits($_POST['arrivee'], $_POST['depart']);
                      $tarif = $resaManager->calculTarif($nbreNuits, $room->id, $_POST['nombrepersonnes'], 0);
                      echo '<a href="../' . $pagename . '/?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $_POST['depart'] . '&nuits=' . $nbreNuits . '&tarif=' . $tarif . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '" >';
                      echo '<img src="../wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';
                      echo '<br/>';
                      echo '<div class="titre">Room ' . $room->chambre . '</div>';
                      echo '<div class="infos">';
                      $maxImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/max.png"/>';
                      $litsImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/lits.png"/>';
                      echo $room->max . $maxImg . $room->lits . $litsImg;
                      echo '</div>';


                      echo '<div class="tarif">' . $tarif . ' ' . $getConfig[0]->devise . ' - ' . $nbreNuits . ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/nuit.png" class="icon" /></div>';
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
                        $tarif = $resaManager->calculTarif($nbreNuits, $room->id, $_POST['nombrepersonnes'], 0);
                        echo '<a href="../' . $pagename . '/?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $_POST['depart'] . '&nuits=' . $nbreNuits . '&tarif=' . $tarif . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '" >';
                        echo '<img src="../wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';
                        echo '<br/>';
                        echo '<div class="titre">Room ' . $room->chambre . '</div>';
                        echo '<div class="infos">';
                        $maxImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/max.png"/>';
                        $litsImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/lits.png"/>';
                        echo $room->max . $maxImg . $room->lits . $litsImg;
                        echo '</div>';

                        echo '<div class="tarif">' . $tarif . ' ' . $getConfig[0]->devise . ' - ' . $nbreNuits . ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/nuit.png" class="icon" /></div>';

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
