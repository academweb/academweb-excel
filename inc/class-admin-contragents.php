<?php

class Admin_Contragents {


	private $contragents_table = 'clients';


	public function __construct(){
		// add_action('wp_ajax_save_trans_row', array($this, 'save_trans_row') );
		// add_action('wp_ajax_nopriv_save_trans_row', array($this,'save_trans_row') );
		add_action('admin_menu', array($this, 'manage_contragents') );
		if( isset($_POST['action_agent']) && !empty($_POST['action_agent'])) {
				$action_agent = $_POST['action_agent']."_agent";
				$this->$action_agent(); 
				// echo "<pre>";
				// 	print_r( $_POST );
				// echo "</pre>";
				// die();
		}
	}

	// Conragents

	public function manage_contragents(){
		add_menu_page('Manage partners', 'Manage partners', 'manage_options', 'manage_contragents', array(&$this, 'manage_contragents_view'));
	}


	public function manage_contragents_view(){
		global $wpdb;

		// $sql = "SELECT * FROM `clients` ";
		// $agents = $wpdb->get_results($sql);
		$agents = (new DBI)->get_contragents();
		?>
			<h1>Partners</h1>
			<div class="container agents">
				<div class="col-xs-12">
					<form method="post" id="agents_form">
					  <div class="form-group">
					    <label for="agent_name">Partner Name:</label>
					    <input type="text" class="form-control" id="agent_name" name="agent_name">
					    <input type="hidden"  name="agent_id" id="agent_id">
					    <input type="hidden"  name="action_agent" id="action_agent" value="edit">
					  </div>
					  		<button type="submit" name="save_agent" class="btn btn-default btn-save">Add new</button>
					  		<!-- <button type="submit" name="delete_agent" class="btn btn-danger btn-delete">Delete</button> -->
					</form>	
					  <div class="form-group">
					    <label for="agents_list">Choose partner:</label>
					    <select class="form-control agents-list" name="agents_list" id="agents_list">
							<option value="">-- Choose partner --</option>
							<?php foreach ($agents as $agent) {
								echo '<option value="'.$agent->id_agent.'">'.$agent->name.'</option>';
							}
							?>
					    </select>
					  </div>
				
				</div>
			</div>
		<?php
	}


	public function edit_agent(){
		global $wpdb;
		
		// Update agent
		if( isset($_POST['agent_id']) && !empty( (int)$_POST['agent_id'] ) ) {
			$name = sanitize_text_field( $_POST['agent_name'] );

			$wpdb->update( $this->contragents_table,
					array(  'name' => $name ),
					array( 'id_agent' => (int)$_POST['agent_id'] )
			);
		
				 $message = '<div class="alert alert-success">Partner '.$name.' succefully updated!</div>';

		}

		// End Update agent


		// Add new Agent
		if( empty($_POST['agent_id']) && isset( $_POST['agent_name'] ) && !empty( $_POST['agent_name'] ) ){
			$name = sanitize_text_field($_POST['agent_name']);
			$wpdb->insert(
				$this->contragents_table,
					array( 'name' => $name ),
					array( '%s' )
			);
			$message = '<div class="alert alert-success">Partner '.$name.' was added!</div>';

		}
		// End Add New agent

		add_action( 'admin_notices', function() use ($message){
			 echo $message;
		});
	}


	public function delete_agent(){

		if( isset($_POST['agent_id']) && !empty( (int)$_POST['agent_id'] ) ) {

			global $wpdb;	
			$wpdb->delete( $this->contragents_table,
					array( 'id_agent' => (int)$_POST['agent_id'] )
			);
			
			add_action( 'admin_notices', function(){
				 echo '<div class="alert alert-warning">Partner was deleted!</div>';
			});
		}
	}

	// End Contragents

}

new Admin_Contragents;