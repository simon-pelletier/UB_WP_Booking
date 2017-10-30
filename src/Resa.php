<?php

class Resa{
    
    protected $erreurs = [],
    $id,
    $nom,
    $email,
    $tel,
    $nombrep,
    $chambre,
    $chambreid,
    $datearrivee,
    $datedepart,
    $infos,
    $tarif,
    $nuits,
    $confirmclient,
    $cleconfirm;
    
    const NOM_INVALIDE = 1;
    const EMAIL_INVALIDE = 2;
    const TEL_INVALIDE = 3;

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
    
    public function isValid(){
        return !(empty($this->nom) || empty($this->email) || empty($this->tel));
    }
    
    public function nombreNuits($arrivee, $depart){
        $dateA = strtotime($arrivee);
        $dateB = strtotime($depart);
        
        $nuitsTimestamp = $dateB - $dateA;
        
        $nuits = intval($nuitsTimestamp / 86400); //60*60*24

        return $nuits;
    }
    
    
    
    
    //SETTERS
    
    public function setId($id){
        $this->id = $id;
    }
    public function setNom($nom){
        $this->nom = $nom;
    }
    public function setEmail($email){
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            $this->erreurs[] = self::EMAIL_INVALIDE;
        }
 
    }
    public function setTel($tel){
        // En attente, prendre en compte ou pas les numéros étrangers
        $this->tel = $tel;
    }
    public function setNombrep($nombrep){
        $this->nombrep = $nombrep;
    }
    public function setChambre($chambre){
        $this->chambre = $chambre;
    }
    public function setChambreid($chambreid){
        $this->chambreid = $chambreid;
    }
    public function setDatearrivee($datearrivee){
        $this->datearrivee = $datearrivee;
    }
    public function setDatedepart($datedepart){
        $this->datedepart = $datedepart;
    }
    public function setInfos($infos){
        $this->infos = $infos;
    }
    public function setTarif($tarif){
        $this->tarif = $tarif;
    }
    public function setNuits($nuits){
        $this->nuits = $nuits;
    }
    public function setConfirmclient($confirmclient){
        $this->confirmclient = $confirmclient;
    }
    public function setCleconfirm($cleconfirm){
        $this->cleconfirm = $cleconfirm;
    }
    
    //GETTERS//
    public function erreurs(){
        return $this->erreurs;
    }
    public function id(){
        return $this->id;
    }
    public function nom(){
        return $this->nom;
    }
    public function email(){
        return $this->email;
    }
    public function tel(){
        return $this->tel;
    }
    public function nombrep(){
        return $this->nombrep;
    }
    public function chambre(){
        return $this->chambre;
    }
    public function chambreid(){
        return $this->chambreid;
    }
    public function datearrivee(){
        return $this->datearrivee;
    }
    public function datedepart(){
        return $this->datedepart;
    }
    public function infos(){
        return $this->infos;
    }
    public function tarif(){
        return $this->tarif;
    }
    public function nuits(){
        return $this->nuits;
    }
    public function confirmclient(){
        return $this->confirmclient;
    }
    public function cleconfirm(){
        return $this->cleconfirm;
    }
    
    
}