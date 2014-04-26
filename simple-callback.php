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

// Тип данных и таксономия
define('SIMPLE_CALLBACK_TYPE', 'simple_callback');
define('SIMPLE_CALLBACK_TAXONOMY', 'callback_status');
// Типы оповещений
define('SIMPLE_CALLBACK_EMAIL_NOTIFICATION_NONE', 0);
define('SIMPLE_CALLBACK_EMAIL_NOTIFICATION_ADMINS', 1);
define('SIMPLE_CALLBACK_EMAIL_NOTIFICATION_EMAIL_LIST', 2);
// POST параметры
define('SIMPLE_CALLBACK_POST_NAME', 'simpleCallbackName');
define('SIMPLE_CALLBACK_POST_PHONE', 'simpleCallbackPhone');


// ------------------------- Инициализация -------------------------
add_action('plugins_loaded', 'simpleCallbackInit');
function simpleCallbackInit() 
{
	// Локализация
	load_plugin_textdomain( 'simple_callback', false, basename(dirname(__FILE__)) . '/lang/' );
}

// Подключение скриптов
add_action('wp_enqueue_scripts', 'simpleCallbackScripts' );
function simpleCallbackScripts() 
{
	// Подключаем jQuery UI dialog, если указан телефон в параметрах 
	$phoneNumber = trim(get_option('simple_callback_phone_number'));
	if (! empty($phoneNumber))
	{
		wp_enqueue_script('simpleCallback', plugins_url('simple-callback.js' , __FILE__  ), 
			array('jquery-ui-dialog'), '0.0.2', true);
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

// Обработка формы
require(plugin_dir_path(__FILE__) . 'callback-form.php');

?>