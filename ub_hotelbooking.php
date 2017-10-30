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
        add_menu_page( 'UB Hotel Booking', 'Chambres', 'manage_options', 'UBHB', 'chambreView', 'dashicons-building' );
}

function UBHBRESA(){
        add_menu_page( 'UB Hotel Booking', 'Reservations', 'manage_options', 'UBHBRESA', 'resaView', 'dashicons-book-alt' );
}

function chambreView(){
  include('../wp-content/plugins/ub_hotelbooking/chambres.php');
}

function resaView(){
  include('../wp-content/plugins/ub_hotelbooking/resa.php');
}

function bookingView(){
  echo "<h1>Ici Hotel Booking</h1>";
  include('view.php');
}
