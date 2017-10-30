<?php
/*
Plugin Name: UB Hotel Booking
Description: Hotel booking room system
Version: 0.1
Author: UnicornBuster
Author URI: http://simonpelletier.net
*/

require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

use Hotelbooking\Hotelbooking;

function bookingCreation(){
  echo 'Hello I\'m Hotel Booking !';
  //$hotelbooking = new Hotelbooking();
  //$hotelbooking->execute();
}

add_shortcode('booking', 'bookingCreation');

add_action('admin_menu', 'UBHBADMIN');

function UBHBADMIN(){
        add_menu_page( 'UB Hotel Booking', 'Hotel Booking', 'manage_options', 'UBHB', 'adminView' );
}

function adminView(){
        echo "<h1>Hello World!</h1>";
}
