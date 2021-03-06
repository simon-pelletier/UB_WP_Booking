<?php
/*
Plugin Name: UB Hotel Booking
Description: Hotel booking room system
Version: 0.1
Author: UnicornBuster
Author URI: http://simonpelletier.net
*/

add_shortcode('search', 'searchView');

add_shortcode('searchen', 'searchViewen');

add_action('admin_menu', 'UBHBADMIN');

add_action('admin_menu', 'UBHBRESA');

function UBHBADMIN(){
  add_menu_page( 'Rooms', 'Rooms', 'manage_options', 'UBHBADMIN', 'chambreView', 'dashicons-building' );
}

function UBHBRESA(){
  add_menu_page( 'Booking', 'Booking', 'manage_options', 'UBHBRESA', 'resaView', 'dashicons-book-alt' );
}

function chambreView(){
  include('room.php');
}

function resaView(){
  include('resa.php');
}

function searchViewen(){
  include('viewen.php');
}

function searchView(){
  include('view.php');
}

function create_plugin_database_table()
{
  global $table_prefix, $wpdb;

  $tbconfig = 'hb_config';
  $tbroom = 'hb_rooms';
  $tbresa = 'hb_resa';
  $wp_table_config = $table_prefix . "$tbconfig";
  $wp_table_room = $table_prefix . "$tbroom";
  $wp_table_resa = $table_prefix . "$tbresa";

  if($wpdb->get_var( "show tables like '$wp_table_config'" ) != $wp_table_config)
  {
      $sql = "CREATE TABLE `". $wp_table_config . "` ( ";
      $sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
      $sql .= "  `adminmail`  varchar(255), ";
      $sql .= "  `personnesmax`  int(10), ";
      $sql .= "  `devise`  text, ";
      $sql .= "  `fumeur`  int(11), ";
      $sql .= "  `animaux`  int(11), ";
      $sql .= "  `parking`  int(11), ";
      $sql .= "  `cb`  int(11), ";
      $sql .= "  `cvac`  int(11), ";
      $sql .= "  `infoscompen`  text, ";
      $sql .= "  `infoscompfr`  text, ";
      $sql .= "  `supplitsstatus`  int(11), ";
      $sql .= "  `supplits`  int(11), ";
      $sql .= "  `suppsaisonstatus`  int(11), ";
      $sql .= "  `suppsaison`  int(11), ";
      $sql .= "  `supptidejstatus`  int(11), ";
      $sql .= "  `supptidej`  int(11), ";
      $sql .= "  `tidejcompris`  int(11), ";
      $sql .= "  `suppdiversstatus`  int(11), ";
      $sql .= "  `suppdiverstexten`  text, ";
      $sql .= "  `suppdiverstextfr`  text, ";
      $sql .= "  `suppdivers`  int(11), ";
      $sql .= "  PRIMARY KEY `order_id` (`id`) ";
      $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
      require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
      dbDelta($sql);

      //Ajout des informations de base en configuration
      $admin_email = get_option( 'admin_email' );
      $wpdb->insert($wp_table_config, array(
        'adminmail' => $admin_email,
        'personnesmax' => '4',
        'devise' => '???',
        'fumeur' => NULL,
        'animaux' => '1',
        'parking' => '1',
        'cb' => '1',
        'cvac' => '1',
        'infoscompen' => 'A restaurant is at your disposal on the ground floor',
        'infoscompfr' => 'Un restaurant est ?? votre disposition au RDC',
        'supplitsstatus' => '1',
        'supplits' => '5',
        'suppsaison' => '13',
        'supptidejstatus' => '1',
        'supptidej' => '5',
        'tidejcompris' => NULL,
        'suppdiversstatus' => '1',
        'suppdiverstexten' => 'Dry cleaning',
        'suppdiverstextfr' => 'Pressing',
        'suppdivers' => '8'
      ));
  }

  if($wpdb->get_var( "show tables like '$wp_table_room'" ) != $wp_table_room)
  {
      $sqlr = "CREATE TABLE `". $wp_table_room . "` ( ";
      $sqlr .= "  `id`  int(11)   NOT NULL auto_increment, ";
      $sqlr .= "  `chambre`  varchar(255), ";
      $sqlr .= "  `max`  int(11), ";
      $sqlr .= "  `lits`  int(11), ";
      $sqlr .= "  `douche`  int(11) DEFAULT 0, ";
      $sqlr .= "  `wc`  int(11) DEFAULT 0, ";
      $sqlr .= "  `tel`  int(11) DEFAULT 0, ";
      $sqlr .= "  `tv`  int(11) DEFAULT 0, ";
      $sqlr .= "  `baignoire`  int(11) DEFAULT 0, ";
      $sqlr .= "  `wifi`  int(11) DEFAULT 0, ";
      $sqlr .= "  `clim`  int(11) DEFAULT 0, ";
      $sqlr .= "  `photo`  varchar(255) DEFAULT 'default.png', ";
      $sqlr .= "  `for1`  int(11), ";
      $sqlr .= "  `for2`  int(11), ";
      $sqlr .= "  `for3`  int(11), ";
      $sqlr .= "  `for4`  int(11), ";
      $sqlr .= "  `infosupen`  text, ";
      $sqlr .= "  `infosupfr`  text, ";
      $sqlr .= "  PRIMARY KEY `order_id` (`id`) ";
      $sqlr .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
      require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
      dbDelta($sqlr);
  }

  if($wpdb->get_var( "show tables like '$wp_table_resa'" ) != $wp_table_resa)
  {
      $sqla = "CREATE TABLE `". $wp_table_resa . "` ( ";
      $sqla .= "  `id`  int(11)   NOT NULL auto_increment, ";
      $sqla .= "  `nom`  varchar(255), ";
      $sqla .= "  `email`  varchar(255), ";
      $sqla .= "  `tel`  varchar(255), ";
      $sqla .= "  `nombrep`  int(11), ";
      $sqla .= "  `chambre`  int(11), ";
      $sqla .= "  `chambreid`  int(11), ";
      $sqla .= "  `datearrivee`  date, ";
      $sqla .= "  `datedepart`  date, ";
      $sqla .= "  `infos`  text, ";
      $sqla .= "  `tarif`  int(11), ";
      $sqla .= "  `nuits`  int(11) DEFAULT 1, ";
      $sqla .= "  `confirmclient` int(11) DEFAULT 0, ";
      $sqla .= "  `cleconfirm`  varchar(255), ";
      $sqla .= "  `litsupp`  int(11) DEFAULT 0, ";
      $sqla .= "  PRIMARY KEY `order_id` (`id`) ";
      $sqla .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
      require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
      dbDelta($sqla);
    }
}

function tableRemover(){
  global $table_prefix, $wpdb;

  $tbconfig = 'hb_config';
  $config = $wpdb->prefix . "$tbconfig";
  $sql = "DROP TABLE IF EXISTS $config";
  $wpdb->query($sql);

  $tbroom = 'hb_rooms';
  $room = $wpdb->prefix . "$tbroom";
  $sqlr = "DROP TABLE IF EXISTS $room";
  $wpdb->query($sqlr);

  $tbresa = 'hb_resa';
  $resa = $wpdb->prefix . "$tbresa";
  $sqla = "DROP TABLE IF EXISTS $resa";
  $wpdb->query($sqla);

}

register_activation_hook( __FILE__, 'create_plugin_database_table' );

register_uninstall_hook( __FILE__, 'tableRemover' );
