<?php

class ConfigManager{

    protected $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function update($mail, $persmax, $devise){
      global $wpdb, $table_prefix;
      $config_table = $table_prefix . 'hb_config';
      $wpdb->update(
      	$config_table,
      	array(
      		'adminmail' => $mail,
      		'personnesmax' => $persmax,
      		'devise' => $devise
      	),
      	array( 'ID' => 1 ),
      	array(
      		'%s',
      		'%d',
      		'%s'
      	)
      );

    }

  }
