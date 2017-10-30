<?php
class Config{
    public static function getMySqlPDO(){
        $db = new PDO('mysql:host=localhost;dbname=hotelbooking', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $db;
    }
}