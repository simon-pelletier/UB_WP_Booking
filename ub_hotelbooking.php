<?php
/*
Plugin Name: UB Hotel Booking
Description: Hotel booking room system
Version: 0.1
Author: UnicornBuster
Author URI: http://simonpelletier.net
*/

add_shortcode('booking', 'bookingView');

add_action('admin_menu', 'UBHBADMIN');

add_action('admin_menu', 'UBHBRESA');

function UBHBADMIN(){
  add_menu_page( 'UB Hotel Booking', 'Rooms', 'manage_options', 'UBHB', 'chambreView', 'dashicons-building' );
}

function UBHBRESA(){
  add_menu_page( 'UB Hotel Booking', 'Booking', 'manage_options', 'UBHBRESA', 'resaView', 'dashicons-book-alt' );
}

function chambreView(){
  include('../wp-content/plugins/ub_hotelbooking/chambres.php');
}

function resaView(){
  include('../wp-content/plugins/ub_hotelbooking/resa.php');
}

function bookingView(){
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
        $sql .= "  `confirmadminmail`  text, ";
        $sql .= "  `confirmclientmail`  text, ";
        $sql .= "  PRIMARY KEY `order_id` (`id`) ";
        $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        dbDelta($sql);

        //Ajout des informations de base en configuration
        $admin_email = get_option( 'admin_email' );
        $wpdb->insert($wp_table_config, array(
          'adminmail' => $admin_email,
          'personnesmax' => '10',
          'confirmadminmail' => 'BLABLABLABLABLA',
          'confirmclientmail' => 'BLUBLUBLUBLUBLUBLUBLUBLUBLUBLU'
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
        $sqlr .= "  `photo`  varchar(255) DEFAULT 'default.png', ";
        $sqlr .= "  `for1`  int(11), ";
        $sqlr .= "  `for2`  int(11), ";
        $sqlr .= "  `for3`  int(11), ";
        $sqlr .= "  `for4`  int(11), ";
        $sqlr .= "  `supp`  int(11), ";
        $sqlr .= "  PRIMARY KEY `order_id` (`id`) ";
        $sqlr .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        dbDelta($sqlr);

        //Ajout de Rooms de base
        $wpdb->insert($wp_table_room, array(
          'chambre' => "1",
          'max' => '2',
          'lits' => '2',
          'douche' => '1',
          'wc' => '1',
          'tel' => '1',
          'tv' => '0',
          'baignoire' => '0',
          'wifi' => '1',
          'for1' => '50',
          'for2' => '55',
          'for3' => '0',
          'for4' => '0',
          'supp' => '5'
        ));
        $wpdb->insert($wp_table_room, array(
          'chambre' => "4",
          'max' => '4',
          'lits' => '2',
          'douche' => '0',
          'wc' => '1',
          'tel' => '1',
          'tv' => '1',
          'baignoire' => '1',
          'wifi' => '0',
          'for1' => '55',
          'for2' => '55',
          'for3' => '75',
          'for4' => '85',
          'supp' => '5'
        ));
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
        $sqla .= "  PRIMARY KEY `order_id` (`id`) ";
        $sqla .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        dbDelta($sqla);

        //Ajout d'une reservation de base
        $wpdb->insert($wp_table_resa, array(
          'nom' => "nom client",
          'email' => '10',
          'tel' => '0622556633',
          'nombrep' => '2',
          'chambre' => '10',
          'chambreid' => '1',
          'datearrivee' => '2017-11-15',
          'datedepart' => '2017-11-16',
          'infos' => 'Nous arriverons vers 19h',
          'tarif' => '55',
          'nuits' => '1',
          'confirmclient' => '1',
          'cleconfirm' => '00000000',
        ));
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

register_deactivation_hook( __FILE__, 'tableRemover' );
