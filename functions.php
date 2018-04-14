<?php

require_once __DIR__.'/inc/class-dbi.php';
require_once __DIR__.'/inc/class-send-emails.php';
$dbi = new DBI;

if( isset($_GET['send_report']) ){
    new Send_Emails;
}

if( is_admin() ){
    require_once __DIR__.'/inc/class-admin.php';
    $admin = new Admin_Theme;
    // add_menu_page( 'Дополнительные настройки сайта', 'Пульт', 'manage_options', 'site-options', 'add_my_setting', '', 1 ); 
}

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

function my_theme_enqueue_styles() {
	wp_dequeue_script( 'jquery' );
	wp_deregister_script( 'jquery' );
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js', [], '2.2.4', true);

	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker');
   // wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
 
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css'
        // array( 'parent_style' ),
        // wp_get_theme()->get('Version')
    );
    wp_enqueue_style( 'boot-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',[], '3.3.7', 'all' );
    wp_enqueue_style( 'select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css',[], '4.0.5', 'all' );
    wp_enqueue_style( 'jquery-ui-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',[], '1.12', 'all' );
    
    wp_enqueue_script('boot-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array(), '3.3.7', true);
    wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', array(), '4.0.5', true);
    
    wp_enqueue_script('autonum-js', get_stylesheet_directory_uri().'/assets/js/autoNumeric.js', array('jquery'), '1.9', true);
    wp_enqueue_script('filters-js', get_stylesheet_directory_uri().'/assets/js/filters.js', array('jquery'), '1.0', true);
    wp_enqueue_script('child-js', get_stylesheet_directory_uri().'/assets/js/child.js', array('jquery'), '1.0', true);
    wp_localize_script( 'child-js', 'reports', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

