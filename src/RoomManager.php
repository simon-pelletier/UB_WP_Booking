<?php

class RoomManager{

    protected $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function roomList($dateA, $dateB, $nombreP){

        if ($dateA < $dateB){

            global $wpdb, $table_prefix;
            $resa_table = $table_prefix . 'hb_resa';
            $rooms_table = $table_prefix . 'hb_rooms';



            $room = $wpdb->get_results("SELECT C.id, C.chambre, C.max, C.lits, C.douche, C.wc, C.tel, C.tv, C.baignoire, C.wifi, C.photo, C.for1, C.for2, C.for3, C.for4, C.supp, R.datedepart
            FROM $rooms_table C
            LEFT JOIN $resa_table R
            ON C.chambre = R.chambre
            AND (
            '$dateA' BETWEEN R.datearrivee AND R.datedepart
            OR '$dateB' BETWEEN R.datearrivee AND R.datedepart
            )
            WHERE
            $nombreP = C.max
            AND R.datearrivee IS NULL
            ORDER BY C.max");

            return $room;

        } else {
            $nothing = array();
            return $nothing;
        }
    }

    public function roomListBis($dateA, $dateB, $nombreP){

        if ($dateA < $dateB){

            global $wpdb, $table_prefix;
            $resa_table = $table_prefix . 'hb_resa';
            $rooms_table = $table_prefix . 'hb_rooms';

              $room = $wpdb->get_results("SELECT C.id, C.chambre, C.max, C.lits, C.douche, C.wc, C.tel, C.tv, C.baignoire, C.wifi, C.photo, C.for1, C.for2, C.for3, C.for4, C.supp
              FROM $rooms_table C
              LEFT JOIN $resa_table R
              ON C.chambre = R.chambre
              AND (
              '$dateA' BETWEEN R.datearrivee AND R.datedepart
              OR '$dateB' BETWEEN R.datearrivee AND R.datedepart
              )
              WHERE
              $nombreP <= C.max
              AND R.datearrivee IS NULL
              ORDER BY C.max");

              return $room;


        } else {
            echo 'ERREUR DE DATE';
            $nothing = array();
            return $nothing;
        }
    }

    public function roomAlone($chambreSelected){

      global $wpdb, $table_prefix;

      $rooms_table = $table_prefix . 'hb_rooms';

      $room = $wpdb->get_results("SELECT * FROM $rooms_table WHERE chambre = $chambreSelected");

      return $room;

    }

    public function roomAdminList(){
        global $wpdb, $table_prefix;

        $rooms_table = $table_prefix . 'hb_rooms';

        $room = $wpdb->get_results("SELECT * FROM $rooms_table");

        return $room;
    }

    public function yesOrNo($answer){
        if ($answer == 1){
            return 'oui';
        } elseif ($answer == 0) {
            return 'non';
        }
    }

    public function prix($nbreP){
        if ($nbreP == 1){
            return $tarif = $this->for1;
        } else if ($nbreP == 2){
            return $tarif = $this->for2;
        } else if ($nbreP == 3){
            return $tarif = $this->for3;
        } else if ($nbreP == 4){
            return $tarif = $this->for4;
        } else {
            return $tarif = 0;
        }
    }

    public function returnImg($att){
      $img = ' <img src="../wp-content/plugins/ub_hotelbooking/web/img/' . $att . '.png"/>';
      return $img;
    }

    public function chambreExistance($chambre){
        $requete = $this->db->prepare('SELECT COUNT(*) FROM rooms WHERE chambre = :chambre');
        $requete->bindValue(':chambre', $chambre);
        $requete->execute();

        $nombre = $requete->fetchColumn();

        return $nombre;
    }

    public function addRoom($chambre, $max, $lits, $douche, $wc, $tel, $tv, $baignoire, $wifi, $photo, $for1, $for2, $for3, $for4, $supp){

      $photo = 'default';

      if($douche !== NULL){
          $douche = 1;
      } else {
          $douche = 0;
      }
      if($wc !== NULL){
          $wc = 1;
      } else {
          $wc = 0;
          }
      if($tel !== NULL){
          $tel = 1;
      } else {
          $tel = 0;
      }
      if($tv !== NULL){
          $tv = 1;
      } else {
          $tv = 0;
      }
      if($baignoire !== NULL){
          $baignoire = 1;
      } else {
          $baignoire = 0;
      }
      if($wifi !== NULL){
          $wifi = 1;
      } else {
          $wifi = 0;
      }


      $wpdb->insert(
        'wp_hb_rooms',
        array(
          'chambre' => $chambre,
          'max' => $max,
          'lits' => $lits,
          'douche' => $douche,
          'wc' => $wc,
          'tel' => $tel,
          'tv' => $tv,
          'baignoire' => $baignoire,
          'wifi' => $wifi,
          'photo' => $photo,
          'for1' => $for1,
          'for2' => $for2,
          'for3' => $for3,
          'for4' => $for4,
          'supp' => $supp
        ),
        array(
          '%s',
          '%d',
          '%d',
          '%d',
          '%d',
          '%d',
          '%d',
          '%d',
          '%d',
          '%s',
          '%d',
          '%d',
          '%d',
          '%d',
          '%d'
        )
      );
    }

    public function ajoutPhoto($nom, $tmpname){
        $last_id = $this->db->lastInsertId();
        //$tailleMax = 2097152; //2mo ?!
        $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');

        //if ($size <= $tailleMax){

            //if ($longueur==300 && $largeur==300) {
                $extensionUpload = strtolower(substr(strrchr($nom, '.'), 1));


            if (in_array($extensionUpload, $extensionsValides)){

                $newnom = md5(microtime(TRUE)*100000);

                $chemin = 'web/img/rooms/' . $newnom . '.' . $extensionUpload;

                $deplacement = move_uploaded_file($tmpname, $chemin);

                if($deplacement){

                    $updateavatar = $this->db->prepare('UPDATE rooms SET photo = :photo WHERE id = :id');
                    $updateavatar->execute(array(
                        'photo' => $newnom . '.' . $extensionUpload,
                        'id' => $last_id
                        ));
                    //header('Location: index.php?page=profil');

                } else {
                    echo '<br/><br/><br/><div class="messageResaError">Erreur d\'upload</div>';
                }

            } else {
                echo '<br/><br/><br/><div class="messageResaError">Votre photo doit être un gif un jpeg un jpeg ou un png.</div>';
            }

           //} else {
                //echo '<br/><br/><div class="messageResaError">Votre photo doit être de 300*300 pixels en 72dpi</div>';
            //}



        //} else {
            //echo '<br/><br/><br/><div class="messageResaError">Votre photo de profil ne doit pas dépasser 2Mo :)</div>';
        //}

    }













}
