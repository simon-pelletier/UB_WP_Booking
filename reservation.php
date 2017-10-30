<?php
require 'src/autoload.php';

$db = Config::getMySqlPDO();

$manager = new RoomManager($db);

$resaManager = new ResaManager($db);

if(isset($_POST['reserver'])){

    $cle = md5(microtime(TRUE)*100000);

    $resa = new Resa([
       'nom' => $_POST['nom'],
        'email' => $_POST['email'],
        'tel' => $_POST['tel'],
        'nombrep' => $_GET['nbp'],
        'chambre' => $_GET['chambre'],
        'chambreid' => $_GET['chambreid'],
        'datearrivee' => $_GET['dateA'],
        'datedepart' => $_GET['dateB'],
        'infos' => $_POST['infos'],
        'tarif' => $_GET['tarif'],
        'nuits' => $_GET['nuits'],
        'confirmclient' => 0,
        'cleconfirm' => $cle
    ]);




    if ($resa->isValid()){
        $messageResa = 'La réservation a bien été ajoutée !<br/>Vous allez recevoir un e-mail de confirmation. Merci de cliquer sur le lien pour valider la réservation.';

        $resaManager->addResa($resa);

        $resaManager->sendMail($_POST['email'], $cle);
        //header('Location: index.php');
    } else {
        $erreurs = $resa->erreurs();
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
       <title>Hotel Booking Reservation</title>
       <meta charset="utf-8"/>
       <link rel="stylesheet" type="text/css" href="web/css/style.css"/>
   </head>
    <body>
        <div><center><a href="index.php" class="retour">Retour</a></center></div>
     <br/><br/>
     <center>
     <form method="POST" action="">
      <?php

        foreach($manager->roomAlone($_GET['chambre']) as $room){
            echo '<div>';
                echo '<center>';

                echo '<img src="web/img/rooms/' . $room->photo() . '" class="photo" />';

                echo '<br/>';

                echo '<div class="titre">Chambre ' . $room->chambre() . '</div>';

                echo '<div class="infos">';

                echo $room->max() . $room->returnImg('max');


                echo $room->lits() . $room->returnImg('lits');

                echo '</div>';

                echo '<div class="tarif">' . $_GET['tarif'] . ' € - ' . $_GET['nuits'] . ' <img src="web/img/nuit.png" class="icon" /></div>';

                echo $room->returnImg('douche');
                echo $room->returnImg('wc');
                echo $room->returnImg('tel');
                echo $room->returnImg('tv');
                echo $room->returnImg('baignoire');
                echo $room->returnImg('wifi');

                echo '</center>';
                echo '</div>';
        }

        ?>

    <br/><br/>

    Arrivée le <?php $dateA = $_GET['dateA']; echo date("d-m-Y", strtotime($dateA));?>
    <br/><br/>
    Départ le <?php $dateB = $_GET['dateB']; echo date("d-m-Y", strtotime($dateB));?>
    <br/><br/>
    Nombre de personnes : <?php echo $_GET['nbp']; ?>
    <br/><br/>
    Total : <?php echo $_GET['tarif']; ?> euros pour <?php echo $_GET['nuits']; ?> nuits.
    <br/><br/>

    <label>Votre nom : <input type="text" name="nom" /></label>
    <br/><br/>
    <label>Votre e-mail : <input type="text" name="email" /></label>
    <br/><br/>
    <label>Téléphone : <input type="text" name="tel" /></label>
    <br/><br/>



    <label>Informations complémentaires : <input type="text" name="infos" /></label>
    <br/><br/>
    <?php
        if (isset($messageResa)){
        echo '<div class="messageResa">' . $messageResa . '</div><br/>';
        }
        if (isset($messageResaError)){
        echo '<div class="messageResaError">' . $messageResaError . '</div><br/>';
        }

        ?>
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
    <br/><br/>




    </body>
</html>
