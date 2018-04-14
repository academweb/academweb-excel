<?php

class Admin_Emails{

	public function __construct(){
		add_action('admin_menu', array($this, 'manage_emails') );
		if( isset($_POST['save_email']) ) $this->edit_email(); 
		if( isset($_POST['save_emails_for_send']) ) $this->save_emails_for_send(); 
	}

	public function manage_emails(){
		add_menu_page('Manage Emails', 'Manage Emails', 'manage_options', 'manage_emails', array(&$this, 'manage_emails_view'));
	}

	public function manage_emails_view(){
		$emails = $this->get_emails();
	
		?>
		<h1>Emails</h1>
		<div class="container invoices">
				<div class="col-xs-12">
					<fieldset class="fieldset-emails">
						<legend>Manage Emails:</legend>
					<form method="post" id="emails_form">
					  <div class="form-group">
					    <label for="email_name">Email:</label>
					    <input type="email" class="form-control" required="required" id="email_name" name="email_name">			    
					    <input type="hidden" name="email_id" id="email_id" >
					  </div>
					  <label>Choose an Email For Edit:</label> <select class="form-control emails-list" id="email_edit">
							<option value="">-- Choose an emails --</option>
							<?php 

							if($emails){
								
								foreach ($emails as $email) {
									echo '<option '.$selected.' value="'.$email->id.'">'.$email->email.'</option>';
								}
							}
							?>
					    </select>
					  		<button type="submit" name="save_email" id="save_email" class="btn btn-default btn-save">Add new Email</button>
					  		<!-- <button type="submit" name="delete_invoice" class="btn btn-danger btn-delete">Delete</button> -->
					</form>	
				</fieldset>
					<form method="post" >
					  <div class="form-group">
					    <label for="emails_list">Choose an Emails for send:</label>
					    <select class="form-control emails-list" name="emails_list[]" id="emails_list" multiple="multiple">
							<option value="">-- Choose an emails --</option>
							<?php 

							if($emails){
								$emails_for_send = get_option('emails_for_send');
								foreach ($emails as $email) {
									$selected =  in_array($email->id, $emails_for_send) ? 'selected="selected"' : '';
									echo '<option '.$selected.' value="'.$email->id.'">'.$email->email.'</option>';
								}
							}
							?>
					    </select>
					  </div>
					  	<button type="submit" name="save_emails_for_send" class="btn btn-default btn-save">Save Emails for Send</button>
					</form>
				</div>
			</div>
		<?php
	}


	public function get_emails(){
		global $wpdb;
		$sql = "SELECT * FROM `emails`";
		return $wpdb->get_results($sql);
	}


	public function edit_email(){
		global $wpdb;
				
		
		if( isset($_POST['email_id']) && !empty($_POST['email_id']) ){
			$wpdb->update( 'emails',
						array( 'email' =>  $_POST['email_name']),
						array( 'id'=>$_POST['email_id'] ),
						array( '%s' ),
						array( '%d' )
					);
			$message = '<div class="alert alert-success">Email was changed!</div>';
		}else{
			$message = '<div class="alert alert-success">Email was added!</div>';
			$ins_ok = $wpdb->insert(
				'emails',
				['email'=>$_POST['email_name']],
				array( '%s' )
			);
		}	

		add_action( 'admin_notices', function() use ($message){
   				 echo $message;
   		});
     	
		
	}


	public function save_emails_for_send(){
		if( isset($_POST['emails_list']) && !empty($_POST['emails_list']) ){
			update_option('emails_for_send', $_POST['emails_list']);
		}
	}

}

new Admin_Emails;