<?php
require 'src/autoload.php';

global $wpdb, $table_prefix;
$resa_table = $table_prefix . 'hb_resa';
$rooms_table = $table_prefix . 'hb_rooms';

$manager = new RoomManager($wpdb);

$resa = new Resa();
?>


<?php



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

?>


<!DOCTYPE HTML>
<html>
   <head>
       <title>Hotel Booking Page</title>
       <meta charset="utf-8"/>
       <link rel="stylesheet" type="text/css" href="../wp-content/plugins/ub_hotelbooking/web/css/style.css">
   </head>
    <body>

      <?php
        if (isset($message)){
        echo $message . '<br/>';
        }
        ?>

       <div class="menu"><a href="admin.php"><img src="web/img/gear.png"/></a></div>



    <div class="recherche">
    <form method="POST" action=".">
    <center>
    <label>Date d'arrivée &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="date" name="arrivee" value="<?php echo $selectedA; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></label>
     &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
    <label>Date de départ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="date" name="depart" value="<?php echo $selectedB; ?>" min="<?php echo $mindaya; ?>" max="<?php echo $maxday; ?>" class="date"/></label>


    <div class="choixnbpersonnes">
    <p><label>Nombre de personnes :
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




    <input type="submit" name="selecteddate" class="btn" value="Rechercher"/>
    </center>
    </form>

    </div>


       <div class="liste">

        <?php




        if (isset($_POST['arrivee']) && isset($_POST['depart'])){

            foreach ($manager->roomList($_POST['arrivee'], $_POST['depart'], $_POST['nombrepersonnes']) as $room){

                echo '<div class="rchambre">';
                echo '<center>';



                echo '<a href="reservation.php?chambre=' . $room->chambre . '&dateA=' . $_POST['arrivee'] . '&dateB=' . $_POST['depart'] . '&nuits=' . $resa->nombreNuits($_POST['arrivee'], $_POST['depart']) . '&tarif=' . $room->prix * $resa->nombreNuits($_POST['arrivee'], $_POST['depart']) . '&nbp=' . $_POST['nombrepersonnes'] . '&chambreid=' . $room->id . '" >';



                echo '<img src="../wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $room->photo . '" class="photo" />';

                echo '<br/>';

                echo '<div class="titre">Chambre ' . $room->chambre . '</div>';

                echo '<div class="infos">';

                $maxImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/max.png"/>';
                $litsImg = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/lits.png"/>';
                echo $room->max . $maxImg . $room->lits . $litsImg;

                echo '</div>';

                echo '<div class="tarif">' . $manager->prix($_POST['nombrepersonnes']) * $resa->nombreNuits($_POST['arrivee'], $_POST['depart']) . ' € - ' . $resa->nombreNuits($_POST['arrivee'], $_POST['depart']) . ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/nuit.png" class="icon" /></div>';


                echo '<div class="option">';
                echo $manager->returnImg('douche');
                echo $manager->returnImg('wc');
                echo $manager->returnImg('tel');
                echo $manager->returnImg('tv');
                echo $manager->returnImg('baignoire');
                echo $manager->returnImg('wifi');

                echo '</div>';
                echo '</a>';
                echo '</center>';
                echo '</div>';
            }

        }








        ?>
        </div>
    </body>
</html>
