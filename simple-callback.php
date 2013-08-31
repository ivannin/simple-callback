<?php
/*
Plugin Name: Simple callback
Plugin URI: https://github.com/ivannin/simple-callback
Description: Форма обратного звонка
Version: 0.1
Author: Иван Никитин
Author URI: http://ivannikitin.com
License:
	Copyright 2013  Ivan Nikitin  (email : ivan@nikitin.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
================================================================================
*/

// ------------------------- Инициализация -------------------------
add_action('plugins_loaded', 'simpleCallbackInit');
function simpleCallbackInit() 
{
	// Локализация
	load_plugin_textdomain( 'simple_callback', false, basename(dirname(__FILE__)) . '/lang/' );
}

add_action( 'init', 'githubSimpleCallbackUpdaterInit' );
function githubSimpleCallbackUpdaterInit()
{
	// Обновление с github
	include_once 'updater.php';

	define( 'WP_GITHUB_FORCE_UPDATE', true);

	if ( is_admin() ) 
	{ 
		// note the use of is_admin() to double check that this is happening in the admin
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'simple-callback',
			'api_url' => 'https://api.github.com/repos/ivannin/simple-callback',
			'raw_url' => 'https://raw.github.com/ivannin/simple-callback/master',
			'github_url' => 'https://github.com/ivannin/simple-callback/tree/master',
			'zip_url' => 'https://github.com/ivannin/simple-callback/archive/master.zip',
			'sslverify' => true,
			'requires' => '3.0',
			'tested' => '3.3',
			'readme' => 'README.md',
			'access_token' => '',
		);
		new WP_GitHub_Updater( $config );
	}	
}

// ---------------- Страница параметров плагина ----------------
add_action( 'admin_menu', 'simpleCallBackCreateAdminMenu' );
function simpleCallBackCreateAdminMenu() 
{
	add_options_page(
		__('Simple Callback Options', 'simple_callback'), 
		__('Simple Callback', 'simple_callback'), 
		'manage_options', 'simple-callback', 'simpleCallbackOptionsShow' );
}

function simpleCallbackOptionsShow() 
{
	if (!current_user_can( 'manage_options' ))
		wp_die( __( 'You do not have sufficient permissions to access this page.','simple_callback') );
	
	include(plugin_dir_path(__FILE__).'options.php');
}

// Тип данных 
require(plugin_dir_path(__FILE__) . 'custom-type.php');

?>