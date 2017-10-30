<?php

class RoomManager{
    
    protected $db;
    
    public function __construct(PDO $db){
        $this->db = $db;
    }
    
    public function roomList($dateA, $dateB, $nombreP){

        if ($dateA < $dateB){

            $requete = $this->db->prepare('SELECT DISTINCT C.id, C.chambre, C.max, C.lits, C.douche, C.wc, C.tel, C.tv, C.baignoire, C.wifi, C.photo, C.for1, C.for2, C.for3, C.for4, C.supp 
            FROM rooms C 
            LEFT JOIN reservation R 
            ON C.chambre = R.chambre
            AND (
            :SdateA BETWEEN R.datearrivee AND R.datedepart
            OR :SdateB BETWEEN R.datearrivee AND R.datedepart
            )
            WHERE
            :nombredepersonnes = C.max
            AND R.datearrivee IS NULL
            ORDER BY C.max');
            $requete->bindParam(':SdateA', $dateA, PDO::PARAM_INT);
            $requete->bindParam(':SdateB', $dateB, PDO::PARAM_INT);
            $requete->bindParam(':nombredepersonnes', $nombreP, PDO::PARAM_INT);
    
            $requete->execute();
            
            $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Room');

            $roomList = $requete->fetchAll();

            if (empty($roomList)){
                $requete = $this->db->prepare('SELECT DISTINCT C.id, C.chambre, C.max, C.lits, C.douche, C.wc, C.tel, C.tv, C.baignoire, C.wifi, C.photo, C.for1, C.for2, C.for3, C.for4, C.supp 
                FROM rooms C 
                LEFT JOIN reservation R 
                ON C.chambre = R.chambre
                AND (
                :SdateA BETWEEN R.datearrivee AND R.datedepart
                OR :SdateB BETWEEN R.datearrivee AND R.datedepart
                )
                WHERE
                :nombredepersonnes <= C.max
                AND R.datearrivee IS NULL
                ORDER BY C.max');


                $requete->bindParam(':SdateA', $dateA, PDO::PARAM_INT);
                $requete->bindParam(':SdateB', $dateB, PDO::PARAM_INT);
                $requete->bindParam(':nombredepersonnes', $nombreP, PDO::PARAM_INT);

                $requete->execute();

                $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Room');

                $roomList = $requete->fetchAll();
                $requete->closeCursor();
                echo '<p><center>Il n\'y a plus de chambres avec les critères entrés, mais voici une liste étendue :</center></p>';
                return $roomList;
            } else {
                $requete->closeCursor();
                return $roomList;
            }
  
        } else {
            echo 'ERREUR DE DATE';
            $nothing = array();
            return $nothing; 
        }
    }
    
    public function addRoom(Room $room){
        $requete = $this->db->prepare('INSERT INTO rooms (chambre, max, lits, douche, wc, tel, tv, baignoire, wifi, photo, for1, for2, for3, for4, supp) VALUES (:chambre, :max, :lits, :douche, :wc, :tel, :tv, :baignoire, :wifi, :photo, :for1, :for2, :for3, :for4, :supp)');
        $requete->bindValue(':chambre', $room->chambre());
        $requete->bindValue(':max', $room->max());
        $requete->bindValue(':lits', $room->lits());
        $requete->bindValue(':douche', $room->douche());
        $requete->bindValue(':wc', $room->wc());
        $requete->bindValue(':tel', $room->tel());
        $requete->bindValue(':tv', $room->tv());
        $requete->bindValue(':baignoire', $room->baignoire());
        $requete->bindValue(':wifi', $room->wifi());
        $requete->bindValue(':photo', $room->photo());
        $requete->bindValue(':for1', $room->for1());
        $requete->bindValue(':for2', $room->for2());
        $requete->bindValue(':for3', $room->for3());
        $requete->bindValue(':for4', $room->for4());
        $requete->bindValue(':supp', $room->supp());
        
        //$last_id = $this->db->lastInsertId();
        
        $requete->execute();
        $requete->closeCursor();
 
        //return $last_id;
    }
    
    public function deleteRoom($id, $photo){
        $this->db->exec('DELETE FROM rooms WHERE id = ' . (int) $id);
        if ($photo != 'default'){
            unlink('web/img/rooms/' . $photo); 
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
            $requete = $this->db->prepare('SELECT rooms.id, rooms.chambre, rooms.max, rooms.lits, rooms.douche, rooms.wc, rooms.tel, rooms.tv, rooms.baignoire, rooms.wifi, rooms.photo, rooms.for1, rooms.for2, rooms.for3, rooms.for4, rooms.supp 
            FROM rooms 
            ORDER BY rooms.max');
    
            $requete->execute();
            
            $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Room');

            $roomAdminList = $requete->fetchAll();
        
        return $roomAdminList;
    }
    
    public function yesOrNo($answer){
        if ($answer == 1){
            return 'oui';
        } elseif ($answer == 0) {
            return 'non';
        }
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





