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

    public function addResaManuel($nom, $email, $tel, $nombrep, $chambre, $chambreid, $dateA, $dateD, $infos, $tarif, $nuits, $confirmclient, $cleconfirm){
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
           'cleconfirm' => $cleconfirm
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
          '%s'
        )
      );
    }

    public function resaAuto($nom, $email, $tel, $nbp, $chambre, $chambreid, $dateA, $dateB, $infos, $tarif, $nuits, $confirm, $cle){
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
           'cleconfirm' => $cle
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
          '%s'
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



    public function sendMail($mail, $cle, $nom){

        global $wpdb, $table_prefix;
        //$id = $this->db->lastInsertId();

        $resa_table = $table_prefix . 'hb_resa';

        $id = $wpdb->insert_id;

        $url_actuel = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $decoupe = explode('?', $url_actuel);
        $url_serveur = $decoupe[0];

        $sujet = "Confirmez votre réservation";
        $entete = "From: confirmation@votrehotel.com";

        $message = 'Merci d\'avoir réservé une chambre dans notre hotel,
        Pour confirmer votre réservation au nom de ' . $nom . ', veuillez cliquer sur le lien ci dessous ou copier/coller dans votre navigateur internet.

        ' . $url_serveur . '?do=confirm&id='.urlencode($id).'&cle='.urlencode($cle).'



        ---------------
        Pour annuler votre réservation, veuillez cliquer sur le lien ci dessous.
        ' . $url_serveur . '?do=cancel&id='.urlencode($id).'&cle='.urlencode($cle).'

        ---------------
        Ceci est un mail automatique, Merci de ne pas y répondre.';


        //envoi du mail

        wp_mail($mail, $sujet, $message, $entete);

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

    public function calculTarif($nuits, $roomId, $nombrep){
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

        $requete = $wpdb->get_results("SELECT $nb FROM $rooms_table WHERE id = $roomId");

        $prix = $requete[0]->$nb;

        $tarif = (int) $nuits * (int) $prix;

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
