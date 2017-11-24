<?php

class ConfigManager{

    public function update($mail, $persmax, $devise, $fumeur, $animaux, $parking, $cb, $cvac, $infoscompen, $infoscompfr, $supplitsstatus, $supplits, $suppsaisonstatus, $suppsaison, $supptidejstatus, $supptidej, $tidejcompris, $suppdiversstatus, $suppdiverstexten, $suppdiverstextfr, $suppdivers){
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
          'infoscompen' => $infoscompen,
          'infoscompfr' => $infoscompfr,
          'supplitsstatus' => $supplitsstatus,
          'supplits' => $supplits,
          'suppsaisonstatus' => $suppsaisonstatus,
          'suppsaison' => $suppsaison,
          'supptidejstatus' => $supptidejstatus,
          'supptidej' => $supptidej,
          'tidejcompris' => $tidejcompris,
          'suppdiversstatus' => $suppdiversstatus,
          'suppdiverstexten' => $suppdiverstexten,
          'suppdiverstextfr' => $suppdiverstextfr,
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
          '%s',
          '%d'
      	)
      );

    }

    public function checkedOrNot($question){
      if ($question == 1){
        return 'checked';
      } else {
        return '';
      }
    }

  }
