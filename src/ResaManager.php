<?php

class ResaManager{

    protected $db;

    public function __construct($db){
        $this->db = $db;
    }


    public function resaList(){
        global $wpdb, $table_prefix;

        $resa_table = $table_prefix . 'hb_resa';

        $post = $wpdb->get_results("SELECT * FROM $resa_table");

        return $post;
    }

    public function addResaManuel($nom, $email, $tel, $nombrep, $chambre, $chambreid, $dateA, $dateD, $infos, $tarif, $nuits, $confirmclient, $cleconfirm, $supp){
      global $wpdb, $table_prefix;
      $wpdb->insert(
        'wp_hb_resa',
        array(
          'nom' => $nom,
           'email' => $email,
           'tel' => $tel,
           'nombrep' => $nombrep,
           'chambre' => $chambre,
           'chambreid' => $chambreid,
           'datearrivee' => $dateA,
           'datedepart' => $dateD,
           'infos' => $infos,
           'tarif' => $tarif,
           'nuits' => $nuits,
           'confirmclient' => $confirmclient,
           'cleconfirm' => $cleconfirm,
           'supp' => $supp
        ),
        array(
          '%s',
          '%s',
          '%s',
          '%d',
          '%d',
          '%d',
          '%s',
          '%s',
          '%s',
          '%d',
          '%d',
          '%d',
          '%s',
          '%d'
        )
      );
    }

    public function resaAuto($nom, $email, $tel, $nbp, $chambre, $chambreid, $dateA, $dateB, $infos, $tarif, $nuits, $confirm, $cle, $supp){
      global $wpdb, $table_prefix;
      $wpdb->insert(
        'wp_hb_resa',
        array(
          'nom' => $nom,
           'email' => $email,
           'tel' => $tel,
           'nombrep' => $nbp,
           'chambre' => $chambre,
           'chambreid' => $chambreid,
           'datearrivee' => $dateA,
           'datedepart' => $dateB,
           'infos' => $infos,
           'tarif' => $tarif,
           'nuits' => $nuits,
           'confirmclient' => $confirm,
           'cleconfirm' => $cle,
           'supp' => $supp
        ),
        array(
          '%s',
          '%s',
          '%s',
          '%d',
          '%d',
          '%d',
          '%s',
          '%s',
          '%s',
          '%d',
          '%d',
          '%d',
          '%s',
          '%d'
        )
      );
    }

    public function resaUnique($id){
        $requete = $this->db->query('SELECT id, nom, email, tel, nombrep, chambre, chambreid, datearrivee, datedepart, infos, tarif, nuits, confirmclient FROM reservation WHERE id = :id');
        $requete->bindParam(':id', $id, PDO::PARAM_INT);

        $requete->execute();
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Resa');
        $resaUnique = $requete->fetchAll();

        $requete->closeCursor();

        return $resaUnique;
    }


    public function deleteResa($id){
        $this->db->exec('DELETE FROM reservation WHERE id = ' . (int) $id);
    }



    public function sendMail($mail, $cle, $nom, $supChambre){

        global $wpdb, $table_prefix;
        //$id = $this->db->lastInsertId();

        $resa_table = $table_prefix . 'hb_resa';
        $config_table = $table_prefix . 'hb_config';

        $id = $wpdb->insert_id;

        $getInfos = $wpdb->get_results("SELECT * FROM $resa_table WHERE id = $id");
        $getConfig = $wpdb->get_results("SELECT * FROM $config_table WHERE id = 1");

        $url_actuel = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $decoupe = explode('?', $url_actuel);
        $url_serveur = $decoupe[0];

        $siteName = get_option( 'blogname' );
        $siteNameAtt=str_replace(' ','',$siteName);

        function wpdocs_set_html_mail_content_type() {
          return 'text/html';
        }
        add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

        $dateA = date("d-m-Y", strtotime($getInfos[0]->datearrivee));
        $dateB = date("d-m-Y", strtotime($getInfos[0]->datedepart));

        $sujet = "Confirmez votre réservation";
        $entete = "From: " . $siteName . " <robotUB@" . $siteNameAtt . ".com>";

        if($getInfos[0]->supp == 1){
          $supplement = 'Option lit séparés '. $supChambre . ' ' . $getConfig[0]->devise;
        } else {
          $supplement = '';
        }

        $message = 'Merci d\'avoir réservé une chambre dans notre hotel ' . $siteName . '.
<br/><br/>
<strong>
Du '. $dateA .'<br/>
Au '. $dateB .'<br/>
('. $getInfos[0]->nuits .' nuit(s))<br/>
Pour '. $getInfos[0]->tarif . ' ' . $getConfig[0]->devise . '<br/>
' . $supplement . '<br/>
</strong>
<br/><br/>
<strong>Pour CONFIRMER votre réservation au nom de ' . $nom . ',<br/>
merci de cliquer sur le lien ci dessous ou copier/coller dans votre navigateur internet :<br/>
' . $url_serveur . '?do=confirm&id='.urlencode($id).'&cle='.urlencode($cle).'</strong>
<br/><br/><br/>

---------------------------------------------<br/>
<i>Pour ANNULER votre réservation, veuillez cliquer sur le lien ci dessous.
' . $url_serveur . '?do=cancel&id='.urlencode($id).'&cle='.urlencode($cle).'</i>
<br/>
---------------------------------------------<br/>
Ceci est un mail automatique, Merci de ne pas y répondre.';
/*
        function mailFromName( $email ){
          return $siteNameAtt; // new email name from sender.
        }
        add_filter( 'wp_mail_from_name', 'mailFromName' );
*/
        //envoi du mail

        wp_mail($mail, $sujet, $message, $entete);


        $mailHotel = $getConfig[0]->adminmail;

        $sujetHotel = 'Nouvelle réservation';

        $messageHotel = 'Vous avez reçu une nouvelle réservation.<br/>
        <strong>
        Nom : ' . $getInfos[0]->nom . '<br/>
        Email : ' . $getInfos[0]->email . '<br/>
        Tel : ' . $getInfos[0]->tel . '<br/><br/>
        Chambre ' . $getInfos[0]->chambre . '<br/>
        Nombre de personnes : ' . $getInfos[0]->nombrep . '<br/><br/>
        Du '. $dateA .'<br/>
        Au '. $dateB .'<br/>
        ('. $getInfos[0]->nuits .' nuit(s))<br/><br/>
        ' . $supplement . '<br/>
        Pour '. $getInfos[0]->tarif . ' ' . $getConfig[0]->devise . '<br/><br/>
        Informations complémentaires : ' . $getInfos[0]->infos . '<br/><br/>
        <a href="' . esc_url( home_url( '/' ) ) . 'wp-admin/" />Se connecter à votre espace Administration Wordpress.</a>
        </strong>';

        wp_mail($mailHotel, $sujetHotel, $messageHotel, $entete);

    }








    public function confirmResa($id, $cle){
      global $wpdb, $table_prefix;
      $resa_table = $table_prefix . 'hb_resa';

      $verif = $wpdb->get_results("SELECT confirmclient, cleconfirm FROM $resa_table WHERE id = $id");

      if ($verif[0]->confirmclient == 1){
        return 'already';
      } else {
        if ($cle == $verif[0]->cleconfirm){
          $wpdb->update(
          	$resa_table,
          	array(
          		'confirmclient' => 1
          	),
          	array( 'id' => $id ),
          	array(
          		'%s'
          	)
          );
          return 'valid';
        } else {
          return 'notvalid';
        }
      }
    }

    public function annulResa($id, $cle){
      global $wpdb, $table_prefix;
      $resa_table = $table_prefix . 'hb_resa';

      $verif = $wpdb->get_results("SELECT id, cleconfirm FROM $resa_table WHERE id = $id");

      if (!empty($verif[0]->id)){
        if ($cle == $verif[0]->cleconfirm){
          $wpdb->delete( $resa_table, array( 'id' => $id ) );
          return 'valid';
        } else {
          return 'notvalid';
        }
      } else {
        return 'dontexist';
      }
    }










    public function quelIdDeChambre($chambre){
        global $wpdb, $table_prefix;
        $rooms_table = $table_prefix . 'hb_rooms';

        $id = $wpdb->get_results("SELECT id FROM $rooms_table WHERE chambre = $chambre");

        return $id[0]->id;
    }

    public function calculTarif($nuits, $roomId, $nombrep, $supp){
        global $wpdb, $table_prefix;
        $rooms_table = $table_prefix . 'hb_rooms';

        if ($nombrep == 1){
            $nb = 'for1';
        } elseif ($nombrep == 2){
            $nb = 'for2';
        } elseif ($nombrep == 3){
            $nb = 'for3';
        } elseif ($nombrep == 4){
            $nb = 'for4';
        }

        $requete = $wpdb->get_results("SELECT $nb, supp FROM $rooms_table WHERE id = $roomId");

        $prix = $requete[0]->$nb;

        if ((int)$supp == 1){
          $suppAdd = $requete[0]->supp;
          $tarif = (int) $nuits * ((int) $prix + (int) $suppAdd);
        } else {
          $tarif = (int) $nuits * (int) $prix;
        }


        return $tarif;
    }

    public function nombreNuits($arrivee, $depart){
        $dateA = strtotime($arrivee);
        $dateB = strtotime($depart);

        $nuitsTimestamp = $dateB - $dateA;

        $nuits = intval($nuitsTimestamp / 86400); //60*60*24

        return $nuits;
    }


}
