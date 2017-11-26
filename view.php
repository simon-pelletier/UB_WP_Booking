<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';
$rooms_table = $table_prefix . 'hb_rooms';
$config_table = $table_prefix . 'hb_config';

$getConfig = $wpdb->get_results("SELECT * FROM $config_table WHERE id = 1");

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

      if ($_GET['lits'] == 2){
        $litsupp = 2;
        $tarif = $resaManager->calculTarif((int) $_GET['nuits'], (int) $chambreid,  (int) $_GET['nbp'], 2);
      } else {
        $litsupp = 1;
        $tarif = $resaManager->calculTarif((int)$_GET['nuits'], (int) $chambreid,  (int) $_GET['nbp'], 1);
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

      $messageResa = 'Vous allez recevoir un e-mail pour confirmer votre réservation.<br/>
      Vous allez être redirigé dans 3 secondes...
      <br/>';
      ?><meta http-equiv="refresh" content="3; url=."><?php

    } else {
      $messageResaError = 'Merci de renseigner une adresse e-mail valide.';
    }

  }else{
    $messageResaError = 'Merci de renseigner votre nom.';
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
       <meta charset="utf-8"/>
       <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css">
     </head>
     <body>
        <div><center><a href="." class="ub-retour">Retour</a></center></div>
        <br/>
        <?php
        if (isset($messageResa)){
        echo '<div class="ub-messageResa">' . $messageResa . '</div><br/>';
        }
        if (isset($messageResaError)){
        echo '<div class="ub-messageResaError">' . $messageResaError . '</div><br/>';
        }
        ?>
        <br/>

        <div class="ub-resaArea">
        <?php

        foreach($manager->roomAlone($_GET['chambre']) as $room){
          echo '<div class="ub-chambreResa">';
          echo '<br/>';
          echo '<img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="ub-photo" />';
          echo '<br/>';
          echo '<div class="ub-titre">Chambre ' . $room->chambre . '</div>';
          echo '<div class="ub-infos">';
          echo $room->max . $manager->returnImg('max') . ' - ';
          echo $room->lits . $manager->returnImg('lits');
          echo '</div>';
          echo '<div class="ub-tarif">' . $_GET['tarif'] . ' ' . $getConfig[0]->devise . ' - ' . $_GET['nuits'] . ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/nuit.svg" class="ub-icon" /></div>';
          echo '<div class="ub-optionResa">';
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
          if (!empty($room->infosupfr)){
            echo '<div class="ub-infosupResa">';
            echo $room->infosupfr;
            echo '</div>';
          }
          echo '<div class="ub-infosupGeneral">';
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
          if($getConfig[0]->infoscompfr !== NULL){
            echo '<br/><br/>';
            echo '<span class="ub-infoscomp">' . $getConfig[0]->infoscompfr . '</span>';
          } else {

          }

          if($getConfig[0]->tidejcompris !== NULL){
            echo '<br/>';
            echo '<span class="ub-infoscomp">Petit déjeuner inclus.</span>';

          } else {

          }
          if ($getConfig[0]->suppdiversstatus !== NULL){
              ?>
              <br/>
              <?php echo '<span class="ub-infoscomp">' . $getConfig[0]->suppdiverstextfr ?> : + <?php echo $getConfig[0]->suppdivers . ' ' . $getConfig[0]->devise ?>
              <?php
          }
          if($getConfig[0]->tidejcompris == NULL){
            if ($getConfig[0]->supptidejstatus !== NULL){
                ?>
                <br/>
                Petit déjeuner : + <?php echo '<span class="ub-infoscomp">' . $getConfig[0]->supptidej . ' ' . $getConfig[0]->devise ?>
                <?php
            }
          }
          echo '</div>';
          echo '</div>';
        }
        ?>

        <div class="ub-formResa">
          Arrivée le <strong><?php $dateA = $_GET['dateA']; echo date("d-m-Y", strtotime($dateA));?></strong>
          <br/>
          Départ le <strong><?php $dateB = $_GET['dateB']; echo date("d-m-Y", strtotime($dateB));?></strong>
          <br/>
          Nombre de personnes : <strong><?php echo $_GET['nbp']; ?></strong>
          <br/>
          Total : <strong><?php echo $_GET['tarif'] . ' ' . $getConfig[0]->devise  ?> pour <?php echo $_GET['nuits']; ?> nuit(s).</strong>
          <br/>
          <?php if ($_GET['lits'] == 2){
            echo '<strong>Lits séparés</strong>';
          } else {

          }
          ?>
          <br/><br/>
          <form method="POST" action="">
            <label>Votre nom* : <br/><center><input type="text" name="nom" class="ub-champs"/></center></label>
            <br/>
            <label>Votre e-mail* : <br/><center><input type="text" name="email" class="ub-champs"/></center></label>
            <br/>
            <label>Téléphone : <br/><center><input type="text" name="tel" class="ub-champs"/></center></label>
            <br/>
            <label>Informations complémentaires : <br/><center><input type="text" name="infos" class="ub-champs"/></center></label>
            <br/><br/>
            <br/>
            <center><input type="submit" name="reserver" value="Réserver" class="ub-btnreserve"/></center>
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
        $messageResa = 'Réservation confirmée !';
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
           <meta charset="utf-8"/>
           <meta http-equiv="refresh" content="3; url=.">
           <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css">
       </head>
       <body>

         <?php
         if (isset($messageResa)){
         echo '<div class="ub-messageResa">' . $messageResa . '</div><br/>';
         }
         if (isset($messageResaError)){
         echo '<div class="ub-messageResaError">' . $messageResaError . '</div><br/>';
         }
         ?>
         <div class="ub-messageResa">Vous allez être redirigé dans 3 secondes...</div><br/>
       </body>
       </html>

       <?php

















    } else {

          ?>
          <!DOCTYPE HTML>
          <html>
             <head>
                 <meta charset="utf-8"/>
                 <link rel="stylesheet" type="text/css" href="<?php echo esc_url( home_url( '/' ) ) ?>wp-content/plugins/ub_hotelbooking/web/css/style.css">
             </head>
             <body>

          <div class="ub-recherche" id="search">
          <form method="POST" action=".">
          <center>
          <br/>
          <label>Date d'arrivée <input type="date" name="arrivee" value="<?php echo $selectedA; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="ub-date"/></label>
          <?php
          if (isset($_POST['nbnuits'])){
            $nval = $_POST['nbnuits'];
          } else {
            $nval = 1;
          }
          ?>
          <br/>
          <label>Nombre de nuits :
          <select name="nbnuits" size="1" class="ub-nbnuits">
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

          <div class="ub-choixnbpersonnes">
          <p><label>Nombre de personnes :
          <select name="nombrepersonnes" size="1" class="ub-nbpersonnes" id="nombredepersonnes">
             <?php
              for ($i = 1; $i <= $personnesMax; $i++){
                  if ($nombreDePersonnes == $i){
                      echo '<option value="' . $i . '" selected>' . $i . '</option>';
                  } else {
                      echo '<option value="' . $i . '">' . $i . '</option>';
                  }
              }
             ?>
          </select>
          </label>

              </p>
      </div>
      <div class="litsep">

      </div>
      <br/>
          <input type="submit" name="selecteddate" class="ub-btnrecherche" value="Rechercher"/>
          </center>
          </form>
          </div>
             <div class="ub-liste">
              <?php

              if (isset($_POST['arrivee']) && isset($_POST['nbnuits'])){
                if (isset($_POST['litsupp'])){
                  $checkLit = 2;
                } else {
                  $checkLit = 1;
                }
                $countRoom = 0;
                $getConfig = $wpdb->get_results("SELECT * FROM $config_table WHERE id = 1");
                $departure = date("Y-m-d", strtotime($_POST['arrivee'] . ' +' . $_POST['nbnuits'] . ' day'));
                  foreach ($manager->roomList($_POST['arrivee'], $departure, $_POST['nombrepersonnes'], $checkLit) as $room){
                      $countRoom += 1;
                      echo '<div class="ub-rchambre">';
                      echo '<center>';
                      $pagename = basename(get_permalink());
                      $nbreNuits = $_POST['nbnuits'];

                      $tarif = $resaManager->calculTarif($nbreNuits, $room->id, $_POST['nombrepersonnes'], $checkLit);

                      echo '<a href="../' . $pagename . '/?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $departure . '&nuits=' . $nbreNuits . '&tarif=' . $tarif . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '&lits=' . $checkLit . '" >';
                      echo '<img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="ub-photo" />';
                      echo '<br/>';
                      echo '<div class="ub-titre">Chambre ' . $room->chambre . '</div>';
                      echo '<div class="ub-infos">';
                      $maxImg = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="ub-icon"/>';
                      $litsImg = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/lits.svg" class="ub-icon"/>';
                      echo $room->max . $maxImg . ' - ' . $room->lits . $litsImg;
                      echo '</div>';

                      echo '<div class="ub-tarif">' . $tarif . ' ' . $getConfig[0]->devise . ' - ' . $nbreNuits . ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/nuit.svg" class="ub-icon" /></div>';

                      echo '<div class="ub-option">';
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

                      if (!empty($room->infosupfr)){
                        echo '<div class="ub-infosup">';
                        echo $room->infosupfr;
                        echo '</div>';
                      }

                      echo '</a>';
                      echo '</center>';
                      echo '</div>';
                  }
                  if ($countRoom == 0){
                    foreach ($manager->roomListBis($_POST['arrivee'], $departure, $_POST['nombrepersonnes'], $checkLit) as $room){
                        $countRoom += 1;
                        echo '<div class="ub-rchambre">';
                        echo '<center>';
                        $pagename = basename(get_permalink());
                        $nbreNuits = $_POST['nbnuits'];

                        $tarif = $resaManager->calculTarif($nbreNuits, $room->id, $_POST['nombrepersonnes'], $checkLit);

                        echo '<a href="../' . $pagename . '/?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $departure . '&nuits=' . $nbreNuits . '&tarif=' . $tarif . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '&lits=' . $checkLit . '" >';
                        echo '<img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="ub-photo" />';
                        echo '<br/>';
                        echo '<div class="ub-titre">Chambre ' . $room->chambre . '</div>';
                        echo '<div class="ub-infos">';
                        $maxImg = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/max.svg" class="ub-icon"/>';
                        $litsImg = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/lits.svg" class="ub-icon"/>';
                        echo $room->max . $maxImg . ' - ' . $room->lits . $litsImg;
                        echo '</div>';

                        echo '<div class="ub-tarif">' . $tarif . ' ' . $getConfig[0]->devise . ' - ' . $nbreNuits . ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/nuit.svg" class="ub-icon" /></div>';

                        echo '<div class="ub-option">';
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

                        if (!empty($room->infosupfr)){
                          echo '<div class="ub-infosup">';
                          echo $room->infosupfr;
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

     <script
    src="https://code.jquery.com/jquery-3.2.1.min.js"
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
    crossorigin="anonymous"></script>

     <script type="text/javascript">

     $('.ub-nbpersonnes').on('change', function() {
       myFunc();
     });



     function myFunc() {
          var sel = $('.ub-nbpersonnes').find(":selected").text();
          <?php
          if ($getConfig[0]->supplitsstatus == 1){
           ?>
          if (sel == 2){
            <?php
            if (isset($_POST['litsupp'])){
              ?>var optionAjout = $('<label>Lits séparés ( +<?php echo $getConfig[0]->supplits . ' ' . $getConfig[0]->devise ?>) <input type="checkbox" value="1" name="litsupp" id="ub-checkbox" checked="checked"/></label>');<?php
            } else {
              ?>var optionAjout = $('<label>Lits séparés ( +<?php echo $getConfig[0]->supplits . ' ' . $getConfig[0]->devise ?>) <input type="checkbox" value="1" name="litsupp" id="ub-checkbox"/></label>');<?php
            }

            ?>

            optionAjout.appendTo('.litsep');

          } else {
            $('.litsep').html('');
          }
          <?php
        } else {

        }
           ?>
      }
      myFunc();
    </script>
     </body>
     </html>
<?php
   }
?>
