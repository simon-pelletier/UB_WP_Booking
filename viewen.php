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
if (isset($_POST['nbnuits'])){
  $nombreDeNuits = $_POST['nbnuits'];
} else {
  $nombreDeNuits = 1;
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
      $chambreid = (int) $_GET['chambreid'];


      if(!empty($_POST['litsep'])){
          $litsupp = 1;
          $tarif = $resaManager->calculTarif((int) $_GET['nuits'], (int) $chambreid,  (int) $_GET['nbp'], 1);
      } else {
          $litsupp = 0;
          $tarif = $resaManager->calculTarif((int)$_GET['nuits'], (int) $chambreid,  (int) $_GET['nbp'], 0);
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
        $litsupp
      );

      $resaManager->sendMail($email, $cle, $_POST['nom']);

      $messageResa = 'You will receive an e-mail to confirm your reservation. <br/>
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
       <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css">
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

        <div class="resaArea">
        <?php

        foreach($manager->roomAlone($_GET['chambre']) as $room){
          echo '<div class="chambreResa">';
          echo '<br/>';
          echo '<img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';
          echo '<br/>';
          echo '<div class="titre">Bedroom ' . $room->chambre . '</div>';
          echo '<div class="infos">';
          echo $room->max . $manager->returnImg('max') . ' - ';
          echo $room->lits . $manager->returnImg('lits');
          echo '</div>';
          echo '<div class="tarif">' . $_GET['tarif'] . ' ' . $getConfig[0]->devise . ' - ' . $_GET['nuits'] . ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/nuit.svg" class="icon" /></div>';
          echo '<div class="optionResa">';
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
          if ($room->clim == 1){
            echo $manager->returnImg('clim');
          }
          echo '</div>';
          if (!empty($room->infosupen)){
            echo '<div class="infosupResa">';
            echo $room->infosupen;
            echo '</div>';
          }
          echo '<div class="infosupGeneral">';
          if($getConfig[0]->fumeur == 1){
            echo $manager->returnImg('fumeur');
          } else {
            echo $manager->returnImg('nofumeur');
          }
          if($getConfig[0]->animaux == 1){
            echo $manager->returnImg('animaux');
          } else {
            echo $manager->returnImg('noanimaux');
          }
          if($getConfig[0]->parking == 1){
            echo $manager->returnImg('parking');
          } else {

          }
          if($getConfig[0]->cb == 1){
            echo $manager->returnImg('cb');
          } else {
            echo $manager->returnImg('nocb');
          }
          if($getConfig[0]->cvac == 1){
            echo $manager->returnImg('cvac');
          } else {
            echo $manager->returnImg('nocvac');
          }
          if($getConfig[0]->infoscompen !== NULL){
            echo '<br/>';
            echo '<span class="infoscomp">' . $getConfig[0]->infoscompen . '</span>';
          } else {

          }

          if($getConfig[0]->tidejcompris !== NULL){
            echo '<br/>';
            echo '<span class="infoscomp">Breakfast included.</span>';

          } else {

          }
          if ($getConfig[0]->suppdiversstatus !== NULL){
              ?>
              <br/>
              <?php echo '<span class="infoscomp">' . $getConfig[0]->suppdiverstexten ?> : + <?php echo $getConfig[0]->suppdivers . ' ' . $getConfig[0]->devise ?>
              <?php
          }
          if($getConfig[0]->tidejcompris == NULL){
            if ($getConfig[0]->supptidejstatus !== NULL){
                ?>
                <br/>
                Breakfast : + <?php echo '<span class="infoscomp">' . $getConfig[0]->supptidej . ' ' . $getConfig[0]->devise ?>
                <?php
            }
          }
          echo '</div>';
          echo '</div>';
        }
        ?>

        <div class="formResa">
          Arrival on <strong><?php $dateA = $_GET['dateA']; echo date("d-m-Y", strtotime($dateA));?></strong>
          <br/>
          Departs the <strong><?php $dateB = $_GET['dateB']; echo date("d-m-Y", strtotime($dateB));?></strong>
          <br/>
          Number of persons : <strong><?php echo $_GET['nbp']; ?></strong>
          <br/>
          Total : <strong><?php echo $_GET['tarif'] . ' ' . $getConfig[0]->devise  ?> for <?php echo $_GET['nuits']; ?> night(s).</strong>
          <br/><br/>
          <form method="POST" action="">
            <label>Your name* : <br/><input type="text" name="nom" class="champs"/></label>
            <br/>
            <label>Your email* : <br/><input type="text" name="email" class="champs"/></label>
            <br/>
            <label>Phone number : <br/><input type="text" name="tel" class="champs"/></label>
            <br/>
            <label>Further information : <br/><input type="text" name="infos" class="champs"/></label>
            <br/><br/>
            <?php
            if ($_GET['nbp'] == 2){
              if ($getConfig[0]->supplitsstatus !== NULL){
                ?>
                <label>Separate beds :<br/><input type="checkbox" value="1" name="litsep" /> ( + <?php echo $getConfig[0]->supplits . ' ' . $getConfig[0]->devise ?> )</label>
                <?php
              }
            }


            ?>
            <br/><br/><br/>
            <center><input type="submit" name="reserver" value="Book" class="btn"/></center>
            </div>

          </form>

      </div>
    </body>
  </html>



















    <?php
  } else if(isset($_GET['do']) && isset($_GET['id']) && isset($_GET['cle'])){
    if($_GET['do'] == 'confirm'){
      $check = $resaManager->confirmResa($_GET['id'], $_GET['cle']);
      if ($check == 'valid'){
        $messageResa = 'Confirmed reservation !';
      } else if ($check == 'notvalid'){
        $messageResaError = 'The key does not match!';
      } else if ($check == 'already'){
        $messageResaError = 'This reservation has already been confirmed!';
      }

    } else if($_GET['do'] == 'cancel'){
      $check = $resaManager->annulResa($_GET['id'], $_GET['cle']);

      if ($check == 'valid'){
        $messageResa = 'Reservation canceled!';
      } else if ($check == 'notvalid'){
        $messageResaError = 'The key does not match!';
      } else if ($check == 'dontexist'){
        $messageResaError = 'This reservation has already been canceled!';
      }
    }
    ?>
    <!DOCTYPE HTML>
    <html>
       <head>
           <title>Hotel Booking Page</title>
           <meta charset="utf-8"/>
           <meta http-equiv="refresh" content="3; url=.">
           <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css">
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
                 <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css">
             </head>
             <body>

          <div class="recherche">
          <form method="POST" action=".">
          <center>
          <label>Arrival date <input type="date" name="arrivee" value="<?php echo $selectedA; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></label>
          <?php
          if (isset($_POST['nbnuits'])){
            $nval = $_POST['nbnuits'];
          } else {
            $nval = 1;
          }
          ?>
          <label>Number of nights :
          <select name="nbnuits" size="1" class="nbpersonnes">
          <?php
           for ($j = 1; $j <= 30; $j++){
               if ($nombreDeNuits == $j){
                   echo '<option selected>' . $j . '</option>';
               } else {
                   echo '<option>' . $j . '</option>';
               }
           }
          ?>
        </select>
        </label>

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

              if (isset($_POST['arrivee']) && isset($_POST['nbnuits'])){
                $countRoom = 0;
                $getConfig = $wpdb->get_results("SELECT * FROM $config_table WHERE id = 1");
                $departure = date("Y-m-d", strtotime($_POST['arrivee'] . ' +' . $_POST['nbnuits'] . ' day'));
                  foreach ($manager->roomList($_POST['arrivee'], $departure, $_POST['nombrepersonnes']) as $room){
                      $countRoom += 1;
                      echo '<div class="rchambre">';
                      echo '<center>';
                      $pagename = basename(get_permalink());
                      $nbreNuits = $_POST['nbnuits'];

                      $tarif = $resaManager->calculTarif($nbreNuits, $room->id, $_POST['nombrepersonnes'], 0, 0, 0);

                      echo '<a href="../' . $pagename . '/?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $departure . '&nuits=' . $nbreNuits . '&tarif=' . $tarif . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '" >';
                      echo '<img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';
                      echo '<br/>';
                      echo '<div class="titre">Bedroom ' . $room->chambre . '</div>';
                      echo '<div class="infos">';
                      $maxImg = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="icon"/>';
                      $litsImg = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/lits.svg" class="icon"/>';
                      echo $room->max . $maxImg . ' - ' . $room->lits . $litsImg;
                      echo '</div>';

                      echo '<div class="tarif">' . $tarif . ' ' . $getConfig[0]->devise . ' - ' . $nbreNuits . ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/nuit.svg" class="icon" /></div>';

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
                      if ($room->clim == 1){
                        echo $manager->returnImg('clim');
                      }
                      echo '</div>';

                      if (!empty($room->infosupen)){
                        echo '<div class="infosup">';
                        echo $room->infosupen;
                        echo '</div>';
                      }

                      echo '</a>';
                      echo '</center>';
                      echo '</div>';
                  }
                  if ($countRoom == 0){
                    foreach ($manager->roomListBis($_POST['arrivee'], $departure, $_POST['nombrepersonnes']) as $room){
                        $countRoom += 1;
                        echo '<div class="rchambre">';
                        echo '<center>';
                        $pagename = basename(get_permalink());
                        $nbreNuits = $_POST['nbnuits'];

                        $tarif = $resaManager->calculTarif($nbreNuits, $room->id, $_POST['nombrepersonnes'], 0, 0, 0);

                        echo '<a href="../' . $pagename . '/?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $departure . '&nuits=' . $nbreNuits . '&tarif=' . $tarif . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '" >';
                        echo '<img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';
                        echo '<br/>';
                        echo '<div class="titre">Bedroom ' . $room->chambre . '</div>';
                        echo '<div class="infos">';
                        $maxImg = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="icon"/>';
                        $litsImg = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/lits.svg" class="icon"/>';
                        echo $room->max . $maxImg . ' - ' . $room->lits . $litsImg;
                        echo '</div>';

                        echo '<div class="tarif">' . $tarif . ' ' . $getConfig[0]->devise . ' - ' . $nbreNuits . ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/nuit.svg" class="icon" /></div>';

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
                        if ($room->clim == 1){
                          echo $manager->returnImg('clim');
                        }
                        echo '</div>';

                        if (!empty($room->infosupen)){
                          echo '<div class="infosup">';
                          echo $room->infosupen;
                          echo '</div>';
                        }

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
