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

function UBHBADMIN(){
        add_menu_page( 'UB Hotel Booking', 'Hotel Booking', 'manage_options', 'UBHB', 'adminView' );
}

function adminView(){
    echo "<h1>Ici l'admin</h1>";
}

function bookingView(){
  echo "<h1>Ici Hotel Booking</h1>";
}
