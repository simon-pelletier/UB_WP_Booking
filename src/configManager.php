<?php

class ConfigManager{

    public function update($mail, $persmax, $devise, $fumeur, $animaux, $parking, $cb, $cvac, $infoscomp, $suppsaisonstatus, $suppsaison, $supptidejstatus, $supptidej, $tidejcompris, $suppdiversstatus, $suppdiverstext, $suppdivers){
      global $wpdb, $table_prefix;
      $config_table = $table_prefix . 'hb_config';
      $wpdb->update(
      	$config_table,
      	array(
      		'adminmail' => $mail,
      		'personnesmax' => $persmax,
      		'devise' => $devise,
          'fumeur' => $fumeur,
          'animaux' => $animaux,
          'parking' => $parking,
          'cb' => $cb,
          'cvac' => $cvac,
          'infoscomp' => $infoscomp,
          'suppsaisonstatus' => $suppsaisonstatus,
          'suppsaison' => $suppsaison,
          'supptidejstatus' => $supptidejstatus,
          'supptidej' => $supptidej,
          'tidejcompris' => $tidejcompris,
          'suppdiversstatus' => $suppdiversstatus,
          'suppdiverstext' => $suppdiverstext,
          'suppdivers' => $suppdivers
      	),
      	array( 'ID' => 1 ),
      	array(
      		'%s',
      		'%d',
      		'%s',
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
          '%d',
          '%s',
          '%d',
      	)
      );

    }

  }
