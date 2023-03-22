<?php
/**
 * Plugin Name:       Order management
 * Plugin URI:        https://github.com/juanjcgo
 * Description:       Order management for consultants
 * Version:           1.0
 * Requires at least: 6.0.1
 * Requires PHP:      7.3
 * Author:            Juan Carlos
 * Author URI:        https://github.com/juanjcgo
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ord_order_management
 */


define('ORD_PATH', plugin_dir_path(__FILE__));

/* 
 *Controllers
*/
require_once ORD_PATH . "controllers/controller_api.php";
require_once ORD_PATH . "controllers/controller_shortcode.php";
require_once ORD_PATH . "controllers/controller_scripts_register.php";



