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

            $room = $wpdb->get_results("SELECT DISTINCT C.id, C.chambre, C.max, C.lits, C.douche, C.wc, C.tel, C.tv, C.baignoire, C.wifi, C.photo, C.for1, C.for2, C.for3, C.for4, C.supp
            FROM $rooms_table C
            LEFT JOIN $resa_table R
            ON C.chambre = R.chambre
            AND (
            $dateA BETWEEN R.datearrivee AND R.datedepart
            OR $dateB BETWEEN R.datearrivee AND R.datedepart
            )
            WHERE
            $nombreP = C.max
            AND R.datearrivee IS NULL
            ORDER BY C.max");

            return $room;

            if (empty($roomList)){
              $room = $wpdb->get_results("SELECT DISTINCT C.id, C.chambre, C.max, C.lits, C.douche, C.wc, C.tel, C.tv, C.baignoire, C.wifi, C.photo, C.for1, C.for2, C.for3, C.for4, C.supp
              FROM $rooms_table C
              LEFT JOIN $resa_table R
              ON C.chambre = R.chambre
              AND (
              $dateA BETWEEN R.datearrivee AND R.datedepart
              OR $dateB BETWEEN R.datearrivee AND R.datedepart
              )
              WHERE
              $nombreP <= C.max
              AND R.datearrivee IS NULL
              ORDER BY C.max");

              return $room;
            }

        } else {
            echo 'ERREUR DE DATE';
            $nothing = array();
            return $nothing;
        }
    }

    public function roomAlone($chambreSelected){
        $reqa = $this->db->prepare('SELECT rooms.id, rooms.chambre, rooms.max, rooms.lits, rooms.douche, rooms.wc, rooms.tel, rooms.tv, rooms.baignoire, rooms.wifi, rooms.photo, rooms.for1, rooms.for2, rooms.for3, rooms.for4, rooms.supp
        FROM rooms
        WHERE :chambre = rooms.chambre'
        );

        $reqa->bindParam(':chambre', $chambreSelected, PDO::PARAM_INT);

        $reqa->execute();
        $reqa->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Room');
        $roomAlone = $reqa->fetchAll();

        $reqa->closeCursor();
        return $roomAlone;
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
      /*
        $rep = $this->$att();
        if ($rep == 1 && $att != 'lits' && $att != 'max'){
            $img = ' <img src="web/img/' . $att . '.png"/>';
            return $img;
        } elseif ($att == 'lits' || $att == 'max') {
            $img = ' <img src="web/img/' . $att . '.png"/>';
            return $img;
        } else {

        }
        */

    }

    public function chambreExistance($chambre){
        $requete = $this->db->prepare('SELECT COUNT(*) FROM rooms WHERE chambre = :chambre');
        $requete->bindValue(':chambre', $chambre);
        $requete->execute();

        $nombre = $requete->fetchColumn();

        return $nombre;
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
