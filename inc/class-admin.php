<?php

class Admin_Theme {




	private $bills_table = 'invoices';


	public function __construct(){
		// add_action('wp_ajax_save_trans_row', array($this, 'save_trans_row') );
		// add_action('wp_ajax_nopriv_save_trans_row', array($this,'save_trans_row') );

    	require_once __DIR__.'/class-dbi.php';
		require_once __DIR__.'/class-admin-contragents.php';
		require_once __DIR__.'/class-admin-companies.php';
		require_once __DIR__.'/class-admin-invoices.php';
		require_once __DIR__.'/class-admin-emails.php';



    	add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}



	public function scripts(){
	
		$screens = [ 'toplevel_page_manage_contragents', 'toplevel_page_manage_firms', 'toplevel_page_manage_bills', 'toplevel_page_manage_emails' ];

		if ( in_array(get_current_screen()->base , $screens) ){
			wp_enqueue_style( 'boot-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',[], '3.3.7', 'all' );
			wp_enqueue_style( 'select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css',[], '4.0.5', 'all' );
			wp_enqueue_style( 'admin-style', get_stylesheet_directory_uri().'/assets/css/admin.css',[], '1.0', 'all' );
			wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', array(), '4.0.5', true);
			wp_enqueue_script('admin-js', get_stylesheet_directory_uri().'/assets/js/admin.js', array('jquery'), '1.0', true);
			wp_enqueue_script('admin-numeric', get_stylesheet_directory_uri().'/assets/js/autoNumeric.js', array('jquery'), '1.0', true);				
		}

	}



}