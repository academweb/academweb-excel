<?php


class Admin_Invoices{


	private $table = 'invoices';
	function __construct(){
		add_action('admin_menu', array($this, 'manage_bills') );
			if( isset($_POST['action_invoice']) && !empty($_POST['action_invoice'])) {
				$action_agent = $_POST['action_invoice']."_invoice";
				$this->$action_agent(); 
				
		}
	}

	// bills

	public function manage_bills(){
		add_menu_page('Accounts', 'Manage Accounts', 'manage_options', 'manage_bills', array(&$this, 'manage_bills_view'));
	}


	public function manage_bills_view(){
		global $wpdb;

		$sql = "SELECT * FROM `".$this->table."`";
		$invoices = $wpdb->get_results($sql);

		?>
			<h1>Accounts</h1>
			<div class="container invoices">
				<div class="col-xs-12">
					<form method="post" id="invoices_form">
					  <div class="form-group">
					    <label for="invoice_name">Account Name:</label>
					    <input type="text" class="form-control" id="invoice_name" name="invoice_name">
					    <input type="hidden"  name="invoice_id" id="invoice_id">
					    <input type="hidden"  name="action_invoice" id="action_invoice" value="edit">
					  </div>
					  		<button type="submit" name="save_invoice" id="save_invoice" class="btn btn-default btn-save">Add new</button>
					  		<!-- <button type="submit" name="delete_invoice" class="btn btn-danger btn-delete">Delete</button> -->
					</form>	
					  <div class="form-group">
					    <label for="invoices_list">Choose an account:</label>
					    <select class="form-control invoices-list" name="invoices_list" id="invoices_list">
							<option value="">-- Choose an account --</option>
							<?php foreach ($invoices as $invoice) {
								echo '<option value="'.$invoice->inv_id.'">'.$invoice->inv_name.'</option>';
							}
							?>
					    </select>
					  </div>
				
				</div>
			</div>
		<?php
	}


	public function edit_invoice(){
		global $wpdb;
		
		// Update invoice
		if( isset($_POST['invoice_id']) && !empty( (int)$_POST['invoice_id'] ) ) {
			$name = sanitize_text_field( $_POST['invoice_name'] );

			$wpdb->update( $this->table,
					array(  'inv_name' => $name ),
					array( 'inv_id' => (int)$_POST['invoice_id'] )
			);
		
				 $message = '<div class="alert alert-success">Invoice '.$name.' succefully updated!</div>';

		}

		// End Update invoice


		// Add new Invoice
		if( empty($_POST['invoice_id']) && isset( $_POST['invoice_name'] ) && !empty( $_POST['invoice_name'] ) ){
			$name = sanitize_text_field($_POST['invoice_name']);
			$wpdb->insert(
				$this->table,
					array( 'inv_name' => $name ),
					array( '%s' )
			);
			$message = '<div class="alert alert-success">Account '.$name.' was added!</div>';

		}
		// End Add New invoice

		add_action( 'admin_notices', function() use ($message){
			 echo $message;
		});
	}

}

new Admin_Invoices;