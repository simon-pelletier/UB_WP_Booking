<?php

class ResaManager{

    public function resaList(){
        global $wpdb, $table_prefix;
        $resa_table = $table_prefix . 'hb_resa';
        $post = $wpdb->get_results("SELECT * FROM $resa_table ORDER BY datearrivee DESC");
        return $post;
    }

    public function addResaManuel($nom, $email, $tel, $nombrep, $chambre, $chambreid, $dateA, $dateD, $infos, $tarif, $nuits, $confirmclient, $cleconfirm, $litsupp){
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
           'litsupp' => $litsupp
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
           'litsupp' => $supp
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

    public function sendMail($mail, $cle, $nom){

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

        $sujet = "Confirmez votre r??servation/Confirm your reservation";
        $entete = "From: " . $siteName . " <robotUB@" . $siteNameAtt . ".com>";

        if($getInfos[0]->litsupp == 2){
          $supplement = 'Option lit s??par??s '. $getConfig[0]->supplits . ' ' . $getConfig[0]->devise . '<br/>Separate bed option '. $getConfig[0]->supplits . ' ' . $getConfig[0]->devise;
        } else {
          $supplement = '';
        }

        if ($getInfos[0]->nuits > 1){
          $nuitAccord = 'nuits';
          $nuitAccorden = 'nights';
        } else {
          $nuitAccord = 'nuit';
          $nuitAccorden = 'night';
        }

        $message = 'Merci d\'avoir r??serv?? une chambre dans notre hotel ' . $siteName . '.
        <br/>Thank you for booking a room in our hotel ' . $siteName . '.
<br/><br/>
<strong>
Du/From <br/>'. $dateA .'<br/>
Au/To <br/>'. $dateB .'<br/>
'. $getInfos[0]->nuits . ' ' . $nuitAccord . '/
'. $getInfos[0]->nuits . ' ' . $nuitAccorden . '<br/>
' . $supplement . '<br/>
Total : '. $getInfos[0]->tarif . ' ' . $getConfig[0]->devise . '
</strong>
<br/><br/><br/><br/>
<strong>Pour CONFIRMER votre r??servation au nom de ' . $nom . ',<br/>
merci de cliquer sur le lien ci dessous ou copier/coller dans votre navigateur internet :<br/>
To CONFIRM your reservation on behalf of '. $nom . '<br/>
please click on the link below or copy/paste in your internet browser:<br/>
<a href="' . $url_serveur . '?do=confirm&id='.urlencode($id).'&cle='.urlencode($cle).'"/>' . $url_serveur . '?do=confirm&id='.urlencode($id).'&cle='.urlencode($cle).'</a></strong>
<br/><br/><br/>

---------------------------------------------<br/>
<i>Pour ANNULER votre r??servation, veuillez cliquer sur le lien ci dessous.<br/>
To CANCEL your reservation, please click on the link below.<br/>
<a href="' . $url_serveur . '?do=cancel&id='.urlencode($id).'&cle='.urlencode($cle).'/>' . $url_serveur . '?do=cancel&id='.urlencode($id).'&cle='.urlencode($cle).'</a></i>
<br/>
---------------------------------------------<br/>
Ceci est un mail automatique, Merci de ne pas y r??pondre.<br/>
This is an automatic email, please do not answer it.';

        wp_mail($mail, $sujet, $message, $entete);

        $mailHotel = $getConfig[0]->adminmail;

        $sujetHotel = 'Nouvelle r??servation';

        $messageHotel = 'Vous avez re??u une nouvelle r??servation.<br/>
        <strong>
        Nom : ' . $getInfos[0]->nom . '<br/>
        Email : ' . $getInfos[0]->email . '<br/>
        Tel : ' . $getInfos[0]->tel . '<br/><br/>
        Chambre ' . $getInfos[0]->chambre . '<br/>
        Nombre de personnes : ' . $getInfos[0]->nombrep . '<br/><br/>
        Du '. $dateA .'<br/>
        Au '. $dateB .'<br/>
        '. $getInfos[0]->nuits . ' ' . $nuitAccord . '<br/><br/>
        ' . $supplement . '<br/>
        Total : '. $getInfos[0]->tarif . ' ' . $getConfig[0]->devise . '<br/><br/>
        Informations compl??mentaires : ' . $getInfos[0]->infos . '<br/><br/>
        <a href="' . esc_url( home_url( '/' ) ) . 'wp-admin/" />Se connecter ?? votre espace Administration Wordpress.</a>
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

    public function calculTarif($nuits, $roomId, $nombrep, $supplits){

        global $wpdb, $table_prefix;
        $rooms_table = $table_prefix . 'hb_rooms';
        $config_table = $table_prefix . 'hb_config';

        $getConfig = $wpdb->get_results("SELECT * FROM $config_table WHERE id = 1");



        if ($getConfig[0]->suppsaisonstatus == 1){
          $suppSaison = $getConfig[0]->suppsaison;
        } else {
          $suppSaison = 0;
        }



        if($supplits == 2){
          $add = $getConfig[0]->supplits;
        } else {
          $add = 0;
        }

        if ($nombrep == 1){
            $nb = 'for1';
        } elseif ($nombrep == 2){
            $nb = 'for2';
        } elseif ($nombrep == 3){
            $nb = 'for3';
        } elseif ($nombrep == 4){
            $nb = 'for4';
        }

        $requete = $wpdb->get_results("SELECT $nb FROM $rooms_table WHERE id = $roomId");

        $prix = $requete[0]->$nb;

        $tarif = (int) $nuits * ((int) $prix + (int) $suppSaison + (int) $add);



        return $tarif;
    }

    public function nombreNuits($arrivee, $depart){
        $dateA = strtotime($arrivee);
        $dateB = strtotime($depart);

        $nuitsTimestamp = $dateB - $dateA;

        $nuits = intval($nuitsTimestamp / 86400); //60*60*24

        return $nuits;
    }

    public function deletePast(){
      global $wpdb, $table_prefix;
      $resa_table = $table_prefix . 'hb_resa';
      $today = date("Y-m-d");

      $count = $wpdb->query($wpdb->prepare("DELETE FROM $resa_table WHERE $resa_table.datedepart < %s", $today));

      return $count;
    }
}
