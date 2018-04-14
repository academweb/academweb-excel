<?php

class Admin_Companies {
	// Firms

	private $firms_table = 'firms';
	private $dbi = '';

	public function __construct(){
		add_action('admin_menu', array($this, 'manage_firms') );
		$this->dbi = new DBI;
		if( isset($_POST['add_firm_invoice']) && !empty( $_POST['invoices_list'] ) ){
			$this->add_invoice( (int)$_POST['id_firm'],  $_POST['invoices_list'] );
		}
	}

	public function manage_firms(){
		add_menu_page('Manage companies', 'Manage Companies', 'manage_options', 'manage_firms', array(&$this, 'manage_firms_view'));
	}


	public function manage_firms_view(){
		$firms = $this->dbi->get_firms();
		?>
			<h1>Companies</h1>
			<div class="col-lg-12">
				<div class="col-lg-3">
					<div class="row">
						<form method="post" id="manage_firms_form" >
							<label for="firm_name">Company: </label>
							<input type="hidden" name="id_firm" class="form-control id_firm">
							<input type="text" name="firm_name" id="firm_name" class="form-control">
							<button id="edit_firm" class="btn" name="edit_firm" >Add New</button>
						</form>
					</div>
					<div class="row">
						<label for="list_firms">Companies List</label>
						<select name="companies_list" id="companies_list" class="companies_list" >
							<option value="" >-- Choose Company --</option>
							<?php
								foreach ($firms as $firm) {
									echo '<option value="'.$firm->id_firm.'" >'.$firm->firm_name.'</option>';
								}
							?>
						</select>
					</div>
				</div>


				<div class="col-lg-9 company-info">
					<div class="row">
						<form method="post" id="manage_firms_form" >
							<label for="firm_name">Account: </label>
							<input type="hidden" name="id_firm" class="form-control id_firm">
							<select name="invoices_list" id="invoices_list" class="invoices_list" ></select>
							<button class="btn" name="add_firm_invoice" >Add To Firm</button>
						</form>
					</div>

					<div class="row">
						<div class="col-lg-12 company-invoices"></div>
					</div>
				</div>

			</div>
		<?php
	}


	public function add_invoice($firm_id=0, $inv_id=0){
		global $wpdb;
		$ins_ok = $wpdb->insert(
				'firms_invoices',
				['firm_id'=>$firm_id, 'inv_id'=>$inv_id],
				array( '%d', '%d' )
			);

			if( $ins_ok ) {
					$message = '<div class="alert alert-success">Account was added!</div>';

			}else $message = '<div class="alert alert-warning">Something wrong!</div>';

			add_action( 'admin_notices', function() use ($message){
				 echo $message;
			});

	}

	// End Firms 

}

new Admin_Companies;