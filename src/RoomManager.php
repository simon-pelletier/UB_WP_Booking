<?php

class RoomManager{

    public function roomList($dateA, $dateB, $nombreP){

        if ($dateA < $dateB){

            global $wpdb, $table_prefix;
            $resa_table = $table_prefix . 'hb_resa';
            $rooms_table = $table_prefix . 'hb_rooms';

            $dateAup = date("Y-m-d", strtotime($dateA . ' +1 day'));
            $dateBup = date("Y-m-d", strtotime($dateB . ' -1 day'));

            $room = $wpdb->get_results("SELECT C.id, C.chambre, C.max, C.lits, C.douche, C.wc, C.tel, C.tv, C.baignoire, C.wifi, C.clim, C.photo, C.for1, C.for2, C.for3, C.for4, C.supp, C.infosup, R.datedepart
            FROM $rooms_table C
            LEFT JOIN $resa_table R
            ON C.chambre = R.chambre
            AND (
            '$dateAup' BETWEEN R.datearrivee AND R.datedepart
            OR '$dateBup' BETWEEN R.datearrivee AND R.datedepart
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

            $dateAup = date("Y-m-d", strtotime($dateA . ' +1 day'));
            $dateBup = date("Y-m-d", strtotime($dateB . ' -1 day'));


              $room = $wpdb->get_results("SELECT C.id, C.chambre, C.max, C.lits, C.douche, C.wc, C.tel, C.tv, C.baignoire, C.wifi, C.clim, C.photo, C.for1, C.for2, C.for3, C.for4, C.supp, C.infosup
              FROM $rooms_table C
              LEFT JOIN $resa_table R
              ON C.chambre = R.chambre
              AND (
              '$dateAup' BETWEEN R.datearrivee AND R.datedepart
              OR '$dateBup' BETWEEN R.datearrivee AND R.datedepart
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
            return 'Yes';
        } elseif ($answer == 0) {
            return 'No';
        }
    }

    public function imgOrNot($question, $name){
      if($question == 1){
        echo '<td><img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/' . $name . '.svg" class="resaImgList"/></td>';
      } else {
        echo '<td></td>';
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
      $img = ' <img src="' . esc_url( home_url( '/' ) ) . 'wp-content/plugins/ub_hotelbooking/web/img/' . $att . '.svg" class="icon"/>';
      return $img;
    }

    public function chambreExistance($chambre){
      global $wpdb, $table_prefix;
      $rooms_table = $table_prefix . 'hb_rooms';
      $room = $wpdb->get_results("SELECT COUNT(*) as nombre FROM $rooms_table WHERE chambre = $chambre");
      return $room[0]->nombre;
    }

    public function addRoom($chambre, $max, $lits, $douche, $wc, $tel, $tv, $baignoire, $wifi, $clim, $photo, $for1, $for2, $for3, $for4, $supp, $infosup){
      global $wpdb, $table_prefix;
      $photo = 'default.png';
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

      if($clim !== NULL){
          $clim = 1;
      } else {
          $clim = 0;
      }

      if($infosup !== NULL){
          $infosup = $infosup;
      } else {
          $infosup = '';
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
          'clim' => $clim,
          'photo' => $photo,
          'for1' => $for1,
          'for2' => $for2,
          'for3' => $for3,
          'for4' => $for4,
          'supp' => $supp,
          'infosup' => $infosup
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
          '%d',
          '%s',
          '%d',
          '%d',
          '%d',
          '%d',
          '%d',
          '%s'
        )
      );
    }

    public function deleteRoom($id, $photo){
      global $wpdb, $table_prefix;
      $room_table = $table_prefix . 'hb_rooms';

      if ($photo != 'default.png'){

        unlink('../wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $photo);

        $wpdb->delete( 'wp_hb_rooms', array( 'ID' => $id ) );
      } else {
        $wpdb->delete( 'wp_hb_rooms', array( 'ID' => $id ) );
      }


    }

    public function ajoutPhoto($nom, $tmpname){
      global $wpdb, $table_prefix;
      $resa_table = $table_prefix . 'hb_resa';
      $room_table = $table_prefix . 'hb_rooms';
      $config_table = $table_prefix . 'hb_config';

      $last_id = $wpdb->insert_id;
      $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
      $extensionUpload = strtolower(substr(strrchr($nom, '.'), 1));

            if (in_array($extensionUpload, $extensionsValides)){

                $newnom = md5(microtime(TRUE)*100000);

                $chemin = '../wp-content/plugins/ub_hotelbooking/web/img/rooms/' . $newnom . '.' . $extensionUpload;

                $deplacement = move_uploaded_file($tmpname, $chemin);

                if($deplacement){

                  $wpdb->update(
                  	$room_table,
                  	array(
                  		'photo' => $newnom . '.' . $extensionUpload
                  	),
                  	array( 'id' => $last_id ),
                  	array(
                  		'%s'
                  	)
                  );

                } else {
                    echo '<br/><br/><br/><div class="messageResaError">Erreur d\'upload</div>';
                }

            } else {
                echo '<br/><br/><br/><div class="messageResaError">Votre photo doit être un gif un jpeg un jpeg ou un png.</div>';
            }
    }

}
