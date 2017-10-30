<?php
class Room{
    
    protected $erreurs = [],
    $id,
    $chambre,
    $max,
    $lits,
    $douche,
    $wc,
    $tel,
    $tv,
    $baignoire,
    $wifi,
    $photo,
    $for1,
    $for2,
    $for3,
    $for4,
    $supp;

    const CHAMBRE_INVALIDE = 1;
    const FOR1_INVALIDE = 2;
    const FOR2_INVALIDE = 3;
    const FOR3_INVALIDE = 4;
    const FOR4_INVALIDE = 5;
    
    public function __construct($valeurs = []){
        if (!empty($valeurs)){
            $this->hydrate($valeurs);
        }
    }
    
    public function hydrate($donnees){
        foreach ($donnees as $attribut => $valeur){
            $method = 'set' . ucfirst($attribut);
            
            if (is_callable([$this, $method])){
                $this->$method($valeur);
            }
        }
    }
    
    public function returnImg($att){
        $rep = $this->$att();
        if ($rep == 1 && $att != 'lits' && $att != 'max'){
            $img = ' <img src="web/img/' . $att . '.png"/>';
            return $img;
        } elseif ($att == 'lits' || $att == 'max') {
            $img = ' <img src="web/img/' . $att . '.png"/>';
            return $img;
        } else {
            
        }
  
    }
    
    public function isValid(){
        return !(empty($this->chambre) || is_null($this->for1) || is_null($this->for2) || is_null($this->for3) || is_null($this->for4));
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
    
    //SETTERS//
    
    public function setId($id){
        $this->id = (int) $id;
    }
    public function setChambre($chambre){
        
        $db = Config::getMySqlPDO();
        $roomManager = new RoomManager($db);
            
        if ($roomManager->chambreExistance($chambre) != 0){
            $this->erreurs[] = self::CHAMBRE_INVALIDE;
        } else {
            $this->chambre = $chambre;
        }
    }
    public function setMax($max){
        $this->max = $max;
    }
    public function setLits($lits){
        $this->lits = (int) $lits;
    }
    public function setDouche($douche){
        $this->douche = (int) $douche;
    }
    public function setWc($wc){
        $this->wc = (int) $wc;
    }
    public function setTel($tel){
        $this->tel = (int) $tel;
    }
    public function setTv($tv){
        $this->tv = (int) $tv;
    }
    public function setBaignoire($baignoire){
        $this->baignoire = (int) $baignoire;
    }
    public function setWifi($wifi){
        $this->wifi = (int) $wifi;
    }
    public function setPhoto($photo){
        $this->photo = $photo;
    }
    public function setFor1($for1){
        if ((int) $for1 == 0){
            $this->erreurs[] = self::FOR1_INVALIDE;
        } else {
            $this->for1 = (int) $for1;    
        }
    }
    public function setFor2($for2){
        if ((int) $this->max == 2){
            if ((int)$for2 == 0){
                $this->erreurs[] = self::FOR2_INVALIDE;
            } else {
                $this->for2 = (int) $for2;
            }
        } else {
            $this->for2 = (int) $for2;
        }
    }
    public function setFor3($for3){
        if ((int) $this->max == 3){
            if ((int)$for3 == 0){
                $this->erreurs[] = self::FOR3_INVALIDE;
            } else {
                $this->for3 = (int) $for3;
            }
        } else {
            $this->for3 = (int) $for3;
        }
        
    }
    public function setFor4($for4){
        if ((int) $this->max == 4){
            if ((int) $for4 == 0){
                $this->erreurs[] = self::FOR4_INVALIDE;
            } else {
                $this->for4 = (int) $for4;
            }
        } else {
            $this->for4 = (int) $for4;
        }
        
    }
    public function setSupp($supp){
        $this->supp = (int) $supp;
    }
    
    //GETTERS//

    public function erreurs(){
        return $this->erreurs;
    }
    public function id(){
        return $this->id;
    }
    public function chambre(){
        return $this->chambre;
    }
    public function max(){
        return $this->max;
    }
    public function lits(){
        return $this->lits;
    }
    public function douche(){
        return $this->douche;
    }
    public function wc(){
        return $this->wc;
    }
    public function tel(){
        return $this->tel;
    }
    public function tv(){
        return $this->tv;
    }
    public function baignoire(){
        return $this->baignoire;
    }
    public function wifi(){
        return $this->wifi;
    }
    public function photo(){
        return $this->photo;
    }
    public function for1(){
        return $this->for1;
    }
    public function for2(){
        return $this->for2;
    }
    public function for3(){
        return $this->for3;
    }
    public function for4(){
        return $this->for4;
    }
    public function supp(){
        return $this->supp;
    }
    
}