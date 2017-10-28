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

$hotelbooking = new Hotelbooking();
$hotelbooking->execute();