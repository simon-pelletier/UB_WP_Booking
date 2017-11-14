<?php

class ResaManager{

    protected $db;

    public function __construct($db){
        $this->db = $db;
    }


    public function resaList(){
        $requete = $this->db->query('SELECT id, nom, email, tel, nombrep, chambre, chambreid, datearrivee, datedepart, infos, tarif, nuits, confirmclient FROM reservation ORDER BY id DESC');

        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Resa');

        $listeResa = $requete->fetchAll();

        foreach ($listeResa as $resa){
            $resa->setDatearrivee(new DateTime($resa->datearrivee()));
            $resa->setDatedepart(new DateTime($resa->datedepart()));
        }

        $requete->closeCursor();

        return $listeResa;
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

    public function addResa(Resa $resa){

        $requete = $this->db->prepare('INSERT INTO reservation (nom, email, tel, nombrep, chambre, chambreid, datearrivee, datedepart, infos, tarif, nuits, confirmclient, cleconfirm) VALUES (:nom, :email, :tel, :nombrep, :chambre, :chambreid, :datearrivee, :datedepart, :infos, :tarif, :nuits, :confirmclient, :cleconfirm)');
        $requete->bindValue(':nom', $resa->nom());
        $requete->bindValue(':email', $resa->email());
        $requete->bindValue(':tel', $resa->tel());
        $requete->bindValue(':nombrep', $resa->nombrep());
        $requete->bindValue(':chambre', $resa->chambre());
        $requete->bindValue(':chambreid', $resa->chambreid());
        $requete->bindValue(':datearrivee', $resa->datearrivee());
        $requete->bindValue(':datedepart', $resa->datedepart());
        $requete->bindValue(':infos', $resa->infos());
        $requete->bindValue(':tarif', $resa->tarif(), PDO::PARAM_INT);
        $requete->bindValue(':nuits', $resa->nuits(), PDO::PARAM_INT);
        $requete->bindValue(':confirmclient', $resa->confirmclient());
        $requete->bindValue(':cleconfirm', $resa->cleconfirm());
        $requete->execute();



        $requete->closeCursor();

        //return $last_id;
    }

    public function deleteResa($id){
        $this->db->exec('DELETE FROM reservation WHERE id = ' . (int) $id);
    }


    public function sendMail($mail, $cle){
        $id = $this->db->lastInsertId();
        $url_actuel = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $decoupe = explode('reservation', $url_actuel);
        $url_serveur = $decoupe[0];

        $sujet = "Confirmez votre réservation";
        $entete = "From: confirmation@votrehotel.com";

        // Le lien d'activation est composé du mail et de la clé(cle)
        // Le lien d'annulation est composé du mail et de la clé(cle)
        $message = 'Merci d\'avoir réservé une chambre dans notre hotel,
Pour confirmer votre réservation, veuillez cliquer sur le lien ci dessous ou copier/coller dans votre navigateur internet.

' . $url_serveur . 'confirmation.php?id='.urlencode($id).'&cle='.urlencode($cle).'



---------------
Pour annuler votre réservation, veuillez cliquer sur le lien ci dessous.
' . $url_serveur . 'annulation.php?id='.urlencode($id).'&cle='.urlencode($cle).'

---------------
Ceci est un mail automatique, Merci de ne pas y répondre.';


        //envoi du mail
        mail($mail, $sujet, $message, $entete);

    }

    public function confirmResa($id, $cle){
        $requete = $this->db->prepare('UPDATE reservation SET confirmclient = 1 WHERE id= :id AND cleconfirm = :cle');
        $requete->bindValue(':id', $id);
        $requete->bindValue(':cle', $cle);
        $requete->execute();
    }

    public function annulResa($id, $cle){
        $requete = $this->db->prepare('DELETE FROM reservation WHERE id = :id AND cleconfirm = :cle');
        $requete->bindValue(':id', $id);
        $requete->bindValue(':cle', $cle);
        $requete->execute();
        $requete->closeCursor();
    }

    public function quelIdDeChambre($chambre){
        $requete = $this->db->prepare('SELECT id FROM rooms WHERE chambre = :chambre');
        $requete->bindValue(':chambre', $chambre);
        $requete->execute();
        $id = $requete->fetchColumn();
        return $id;
    }

    public function calculTarif($nuits, $roomId, $nombrep){
        $requete = $this->db->prepare('SELECT for1, for2, for3, for4 FROM rooms WHERE id = :roomId');
        $requete->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        $requete->execute();

        $tableau = $requete->fetchAll();

        if ($nombrep == 1){
            $tarifChambre = $tableau[0][0];
        } elseif ($nombrep == 2){
            $tarifChambre = $tableau[0][1];
        } elseif ($nombrep == 3){
            $tarifChambre = $tableau[0][2];
        } elseif ($nombrep == 4){
            $tarifChambre = $tableau[0][3];
        }

        $tarif = (int) $nuits * (int) $tarifChambre;

        return $tarif;
    }


}
