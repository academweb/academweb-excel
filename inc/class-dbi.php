<?php

class DBI{
	
	function __construct(){

		add_action('wp_ajax_get_row', array($this, 'get_row') );
		// add_action('wp_ajax_nopriv_get_row', array($this,'get_row') );

		add_action('wp_ajax_save_trans_row', array($this, 'save_trans_row') );
		// add_action('wp_ajax_nopriv_save_trans_row', array($this,'save_trans_row') );

		add_action('wp_ajax_update_trans_row', array($this, 'update_trans_row') );
		// add_action('wp_ajax_nopriv_update_trans_row', array($this,'update_trans_row') );
		
		add_action('wp_ajax_delete_row', array($this, 'delete_row') );
		// add_action('wp_ajax_nopriv_delete_row', array($this,'delete_row') );

		add_action('wp_ajax_get_company_invoices', array($this, 'get_filtred_company_invoices') );
		add_action('wp_ajax_get_firm_report', array($this, 'get_firm_report') );
		
		add_action('wp_ajax_save_start', array($this, 'save_start_ballance') );
		
		add_action('wp_ajax_get_filered_daily', array($this, 'get_filered_daily') );
		// add_action('wp_ajax_nopriv_get_filered_daily', array($this,'get_filered_daily') );

	}

	public function get_firms( $id = 0 ){

		global $wpdb;
		if( $id === 0 ) {

			$sql = "SELECT * FROM `firms`";
			return $wpdb->get_results($sql);
		}
		else {

				$sql = "SELECT * FROM `firms` WHERE `id_firm` = %d";
				return	$wpdb->get_results(
				$wpdb->prepare(
					$sql,
					[$id]
					)
				);
			}
		
	}


	public function get_firms_reports( $id = 0, $inv=0 ){

		if( !$id ) return false;
		global $wpdb;

		// $sql = "SELECT invoices.inv_name, SUM(transactions.receivable) as sum_rec, SUM(transactions.payable) as sum_pay FROM `transactions` 
		// 			JOIN invoices ON invoices.inv_id = transactions.inv_id
		// 			WHERE `firm_id` = %d GROUP BY transactions.inv_id";
		// 				return	$wpdb->get_results(
		// 						$wpdb->prepare(
		// 							$sql,
		// 							[$id]
		// 							)
		// 						);
		
		// $sql = "SELECT transactions.firm_id, transactions.inv_id, transactions.daily, invoices.inv_name 
		// 			FROM transactions
		// 			JOIN invoices ON invoices.inv_id = transactions.inv_id
		// 			WHERE id IN(
		// 			 SELECT max(id) FROM transactions
		// 			  WHERE transactions.firm_id=%d GROUP BY transactions.inv_id ORDER BY id DESC
		// 			)";

		$sql = "SELECT daily FROM transactions
					WHERE id IN(
					
                    SELECT id FROM transactions
					  WHERE transactions.firm_id=%d AND inv_id =%d 
                        )  ORDER BY trans_date DESC, id DESC LIMIT 1";

						return	$wpdb->get_results(
								$wpdb->prepare(
									$sql,
									[$id, $inv]
									)
								);

	}


	public function get_firm_summ_report( $firm_id = 0, $inv_id = 0 ){
		
		if( !$firm_id ) return false;
		global $wpdb;

		$sql = "SELECT firms_invoices.start_ballance FROM `firms_invoices` 
						WHERE firms_invoices.firm_id=%d AND firms_invoices.inv_id = %d";
						
						return $wpdb->get_results(
								$wpdb->prepare(
									$sql,
									[$firm_id, $inv_id]
									)
							);


	}


	public function get_firm_report( $id = 0 ){
		$id = (int)$_POST['id_firm'];
		if( !$id ) return false;
		global $wpdb;

		$sql = "SELECT firms_invoices.firm_id, invoices.inv_id, invoices.inv_name, firms_invoices.start_ballance FROM `firms_invoices` 
						LEFT JOIN invoices ON invoices.inv_id = firms_invoices.inv_id
						LEFT JOIN transactions ON transactions.inv_id = firms_invoices.inv_id
						WHERE firms_invoices.firm_id=%d GROUP BY firms_invoices.inv_id";
						
						echo json_encode( $wpdb->get_results(
								$wpdb->prepare(
									$sql,
									[$id]
									)
								)
							);
					wp_die();

	}


	public function get_firm_bills($id = 0){
		global $wpdb;
		if( !$id ) return false;
		
		$sql = "SELECT  firms_invoices.inv_id, invoices.inv_name, firms_invoices.start_ballance						
						FROM invoices 
						JOIN firms_invoices ON invoices.inv_id = firms_invoices.inv_id
						WHERE firms_invoices.firm_id = %d";
						return	$wpdb->get_results(
								$wpdb->prepare(
									$sql,
									[$id]
									)
								);
	}

	public function save_start_ballance(){
		$inv_id = (int)$_POST['inv_id'];
		$firm_id = (int)$_POST['firm_id'];
		if( !$inv_id ) return false;

		global $wpdb;
		

		// $old = "SELECT `start_ballance` FROM `firms_invoices` WHERE `firm_id`=".$firm_id."  AND `inv_id`=".$inv_id;

		// $old_bal = $wpdb->get_results($old);

		// $diff = $_POST['start_bal'] - $old_bal[0]->start_ballance;

		$up_rows = $wpdb->update( 'firms_invoices',
						array( 'start_ballance' => $_POST['start_bal']),
						array( 'firm_id'=>$firm_id, 'inv_id' =>$inv_id ),
						array( '%f' ),
						array( '%d', '%d' )
					);
		// if( $up_rows ) echo 1;
			 $sql = "SELECT * FROM `transactions` WHERE `firm_id`=".$firm_id." AND `inv_id`=".$inv_id." ORDER BY transactions.trans_date ASC LIMIT 1";
			 $row = $wpdb->get_results($sql);

		// $update_trnsactions = "UPDATE `transactions` SET `daily`=`daily`+(".$diff.") WHERE `firm_id`=".$firm_id." AND `inv_id`=".$inv_id;
		// $up = $wpdb->query($update_trnsactions);
			$this->new_provodka($row, 0, $firm_id, $inv_id);
		wp_die();
	}

	public function get_filtred_company_invoices(){
		global $wpdb;
		$id = (int)$_POST['id_firm'] ? (int)$_POST['id_firm'] : 0;
		if( !$id ) return false;
		$sql = "SELECT * FROM invoices WHERE inv_id NOT IN (SELECT `inv_id`	FROM firms_invoices	WHERE `firm_id`=  %d)";
				echo json_encode( $wpdb->get_results(
								$wpdb->prepare(
									$sql,
									[$id]
									)
								)
			);
		wp_die();
		
	}


	public function get_row(){
		if( isset($_POST['row_id']) && !empty((int)$_POST['row_id'])){
			global $wpdb;
			$sql = "SELECT  DATE_FORMAT(`trans_date`, '%d-%m-%Y') as trans_date, receivable, payable, cli_id, remark FROM `transactions` WHERE id = ".(int)$_POST['row_id'];
			echo json_encode( $wpdb->get_results( $sql ) );
			wp_die();
		}
	}


	public function save_trans_row(){
		
		global $wpdb;

		$fields = ['receivable', 'payable', 'firm_id', 'inv_id', 'cli_id', 'remark', 'trans_date'];
		$insert_fields = [];
		foreach ($fields as $field) {
			$insert_fields[$field] = isset($_POST[$field] ) ? $_POST[$field] : '';
		}

		$old_d = explode("-", $insert_fields['trans_date']);
		$insert_fields['trans_date'] = $old_d[2]."-".$old_d[1]."-".$old_d[0];
		// $sql = "SELECT daily FROM `transactions` WHERE firm_id = %d AND inv_id = %d ORDER BY `trans_date`, `id` DESC LIMIT 1";
			// $sql = "SELECT daily FROM transactions
			// 		WHERE id IN(
			// 		 SELECT max(id) FROM transactions
			// 		  WHERE transactions.firm_id=%d AND inv_id =%d AND trans_date <= '".$insert_fields['trans_date']."' ORDER BY id DESC
			// 		)";

			$sql = "SELECT daily FROM transactions
					WHERE id IN(
					
                    SELECT id FROM transactions
					  WHERE transactions.firm_id=%d AND inv_id =%d AND trans_date <= '".$insert_fields['trans_date']."' 
                        )  ORDER BY trans_date DESC, id DESC LIMIT 1";
			$is_invoice = $wpdb->get_col(
								$wpdb->prepare(
									$sql,
									[(int)$_POST['firm_id'], (int)$_POST['inv_id']]
									)
								);
		
			if(  $is_invoice  ) {
				// $daily_inv = $wpdb->get_results('SELECT daily FROM transactions	WHERE id=');
				$daily = $is_invoice[0];

			}else{
				
				$sql = "SELECT 	start_ballance	FROM `firms_invoices` WHERE firm_id = %d AND inv_id = %d";
				$start_ballance = $wpdb->get_col(
								$wpdb->prepare(
									$sql,
									[(int)$_POST['firm_id'], (int)$_POST['inv_id']]
									)
								);
				$daily = $start_ballance[0];
			}
			
			$receivable = isset( $_POST['receivable'] ) ? $_POST['receivable'] : 0;
			$payable = isset( $_POST['payable'] ) ? $_POST['payable'] : 0;
			if( $receivable ) $diff=$receivable;
			if( $payable ) $diff=-$payable;
			$insert_fields['daily'] = $daily + ($receivable) - ($payable) ;

			$ins_ok = $wpdb->insert(
				'transactions',
				$insert_fields,
				array( '%f', '%f', '%d', '%d', '%d','%s', '%s', '%f' )
			);

			if( $ins_ok ){
			 // echo json_encode( $this->get_firm_transactions( (int)$_POST['firm_id'], (int)$_POST['inv_id'] ) );
				
			// echo json_encode( $this->getLast( $wpdb->insert_id ) );

				echo json_encode($this->provodka($insert_fields['firm_id'], $insert_fields['inv_id'],  $wpdb->insert_id, $insert_fields['trans_date'], $diff));
			}
				else echo 0;
		wp_die();
	}

	public function update_trans_row(){
		global $wpdb;
		$id = (int)$_POST['row_id'];
		$firm_id = (int)$_POST['firm_id'];
		$inv_id = (int)$_POST['inv_id'];
		$fields = [ 'trans_date', 'receivable', 'payable', 'cli_id', 'remark'];
		$update_fields = [];
		foreach ($fields as $field) {
			$update_fields[$field] = isset($_POST[$field] ) ? $_POST[$field] : '';
		}
		$old_d = explode("-", $update_fields['trans_date']);
		$update_fields['trans_date'] = $old_d[2]."-".$old_d[1]."-".$old_d[0];
		///////////////////////////////////////////////////////////////////
		$prev_sql = "SELECT * FROM `transactions` WHERE `id`=".$id;
		$curr_row = $wpdb->get_results($prev_sql);
		$prev_ballance = $curr_row[0]->daily - $curr_row[0]->receivable + $curr_row[0]->payable;
			
		$new_ballance = $prev_ballance + $update_fields['receivable'] - $update_fields['payable'];
		///////////////////////////////////////////////////////////////////
		$update_fields['daily'] = $new_ballance;
		
		$diff = $new_ballance - $curr_row[0]->daily;

		// echo "<pre>";
		// 	print_r( $res );
		// echo "</pre>";
		$up_rows = $wpdb->update( 'transactions',
						$update_fields,
						array( 'id'=>$id),
						array( '%s', '%f', '%f', '%d', '%s', '%f' ),
						array( '%d' )
					);
		if( $up_rows ) {

			echo json_encode( $this->provodka($firm_id, $inv_id, $id, $update_fields['trans_date'], $diff) );
			
		}
		wp_die();
	}


	public function new_provodka($row= null, $prev_bal=null, $firm_id=0, $inv_id=0){
		global $wpdb;
		$daily = 0;
		if(!$row) return;

		if(!$prev_bal) {
			$start_ballance_sql = "SELECT * FROM `firms_invoices` WHERE `firm_id`=".$firm_id."  AND `inv_id`=".$inv_id;
			$start_ballance = $wpdb->get_results($start_ballance_sql);
			
			$prev_ballance = $start_ballance[0]->start_ballance;
		
		}else{
		
			$prev_bal_d =  $prev_bal[0]->daily;
			$prev_ballance = $prev_bal_d;
		}

		// UPDATE 

	$daily = floatval($prev_ballance) + floatval($row[0]->receivable) - floatval($row[0]->payable);

		// $update_sql = "UPDATE `transactions` SET `daily` = ".$daily." WHERE `id`=".$row[0]->id;
		// $up_res = $wpdb->query($update_sql);
		

		

	$up_rows = $wpdb->update( 'transactions',
						['daily'=>$daily],
						array( 'id'=>$row[0]->id),
						array( '%f' ),
						array( '%d' )
					);

	if($row[0]->id==200){
				$res = $wpdb->get_results("SELECT * FROM `transactions` WHERE id=".$row[0]->id);
				echo "<pre>";
					print_r( $res  );
				echo "</pre>";
		 echo $daily;
	}

		if( $row[0]->id == 200 ){

		// 	echo " PREV: ".$prev_ballance;
		// 	echo " Rec: ". $row[0]->receivable;
		// 	echo " Pay: ".$row[0]->payable;
			// echo " Daily: ".$daily;
		}
		// NEXT
		$res = $wpdb->get_results("SELECT * FROM `transactions` WHERE id=".$row[0]->id);

		$next_sql_row = "SELECT * FROM `transactions` WHERE `firm_id`=".$firm_id." AND `inv_id`=".$inv_id." AND ( ( transactions.trans_date='".$row[0]->trans_date."' AND id>".$row[0]->id.") OR transactions.trans_date>'".$row[0]->trans_date."' )  ORDER BY transactions.trans_date ASC, transactions.id ASC LIMIT 1";
		// $next_sql_row = "SELECT * FROM `transactions` WHERE `firm_id`=3 AND `inv_id`=7 AND ( ( transactions.trans_date='2017-12-01' AND id>393) OR transactions.trans_date>'2017-12-01') ORDER BY transactions.trans_date ASC LIMIT 1";

		$next = $wpdb->get_results($next_sql_row);


		$this->new_provodka($next, $res, $firm_id, $inv_id);
		
	}






		// $sql = "SELECT * FROM `transactions` WHERE `firm_id`=".$firm_id." AND `inv_id`=".$inv_id." ORDER BY transactions.trans_date ASC LIMIT 1";
		// $min_row = $wpdb->get_results($sql);

		// $prev_ballance = 0;
		// if( !$min_row ){
			
			
		// 	$date = '0000-00-00';
		// }else{
		// 	$prev_ballance = $min_row[0]->daily;
		// 	$date = $min_row[0]->trans_date;
		// }

		// $next_sql_row = "SELECT * FROM `transactions` WHERE `firm_id`=".$firm_id." AND `inv_id`=".$inv_id." AND id>".$min_row[0]->id." AND transactions.trans_date='".$date."' OR transactions.trans_date>'".$date."' ORDER BY transactions.trans_date ASC LIMIT 1";

	


	public function provodka($firm_id = 0, $inv_id = 0, $row_id = 0, $date='', $daily_up = 0){
		global $wpdb;

 		$sql = "SELECT id FROM transactions WHERE firm_id = ".$firm_id." AND inv_id=".$inv_id." AND trans_date>'".$date."' OR (trans_date='".$date."' AND id>".$row_id.")";
 		$array_id = $wpdb->get_results($sql, ARRAY_A);
 		$up_array = [];
 		foreach ($array_id as $key => $value) {
 			  $up_array[] = $array_id[$key]['id'];
 		}
 		$up_array = implode(',', $up_array);
 		if( $up_array ) {
 			$up_sql = "UPDATE `transactions` SET `daily`=`daily`+(".$daily_up.") WHERE `id` IN (".$up_array.")";
 		 	$wpdb->query($up_sql);
 		}
 		
 		 return $this->get_firm_transactions( $firm_id, $inv_id );


	}

	public function get_row_daily($row_id = 0 ){
		global $wpdb;
		$sql = "SELECT `daily` FROM `transactions` WHERE `id`=".$row_id;
		$daily = $wpdb->get_results($sql);
		return $daily[0]->daily;
	}

	public function delete_row(){
		$row_id = (int)$_POST['row_id'];
		$firm_id = (int)$_POST['firm_id'];
		$inv_id = (int)$_POST['inv_id'];

		if(!$row_id) return false;
			
			global $wpdb;
			$sql = "SELECT * FROM `transactions` WHERE `id`=".$row_id;

			$daily = $wpdb->get_results($sql);
			$del_rows = $wpdb->delete( 'transactions', array( 'id' => $row_id ) );
			$sql = "SELECT * FROM `transactions` WHERE `firm_id`=".$firm_id." AND `inv_id`=".$inv_id." ORDER BY transactions.trans_date ASC LIMIT 1";
			 $row = $wpdb->get_results($sql);
			// echo json_encode( $this->provodka( $firm_id, $inv_id, $row_id, $daily[0]->trans_date, -$daily[0]->daily ) );
		$this->new_provodka($row,0, $firm_id, $inv_id );
			echo json_encode( $this->get_firm_transactions( $firm_id, $inv_id ) );
			
		wp_die();
	}

	public function get_firm_transactions( $firm_id=0, $inv_id = 0, $filters = null ){

		global $wpdb;

		if( !$firm_id ) return;

		if( !$inv_id ) {
			// $sql = "SELECT *, DATE_FORMAT(`trans_date`, '%d-%m-%Y') as formatted_date FROM `transactions` WHERE MONTH(`trans_date`) = MONTH(CURRENT_DATE()) AND `firm_id` = ".$firm_id;
			// $sql = "SELECT *, DATE_FORMAT(`trans_date`, '%d-%m-%Y') as formatted_date FROM `transactions` JOIN clients ON clients.id_agent = transactions.cli_id WHERE MONTH(`trans_date`) = MONTH(CURRENT_DATE()) AND `firm_id` = ".$firm_id." ORDER BY `trans_date`, `id` DESC";

						$sql = "SELECT *, DATE_FORMAT(`trans_date`, '%d-%m-%Y') as formatted_date FROM `transactions` JOIN clients ON clients.id_agent = transactions.cli_id WHERE `firm_id` = ".$firm_id." ORDER BY  transactions.trans_date DESC, transactions.id DESC";

		return	$wpdb->get_results( $sql );
		}
		else{
			$sql = "SELECT *, DATE_FORMAT(`trans_date`, '%d-%m-%Y') as formatted_date FROM `transactions` JOIN clients ON clients.id_agent = transactions.cli_id WHERE `firm_id` = ".$firm_id." AND `inv_id` = ".$inv_id." ORDER BY  transactions.trans_date DESC,transactions.id DESC";

		return	$wpdb->get_results($sql);
		}
	}


	public function getLast( $last_id = 0 ){
		if( $last_id ) {
			global $wpdb;
			$sql = "SELECT *, DATE_FORMAT(`trans_date`, '%d-%m-%Y') as formatted_date FROM `transactions` JOIN clients ON clients.id_agent = transactions.cli_id WHERE `id` = ".$last_id;

			return	$wpdb->get_results( $sql );
		}
	}


	public function get_contragents(){
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM `clients` ORDER BY `name` ASC");
	}


	public function get_filered_daily(){
		global $wpdb;
		$fields = json_decode( stripcslashes( $_POST['filters'] ) );
		$and_contr = '';
		if( $fields->filter_contr ) $and_contr=" AND `cli_id`=".$fields->filter_contr;

			if($fields->date_from && !$fields->date_to) {
				$sql = "SELECT DATE_FORMAT (transactions.trans_date, '%d-%m-%Y') as trans_date, transactions.receivable, transactions.payable, firms.firm_name, clients.name, invoices.inv_name FROM transactions,firms, clients, invoices WHERE invoices.inv_id=transactions.inv_id AND firms.id_firm = transactions.firm_id AND clients.id_agent = transactions.cli_id AND transactions.trans_date >= '".$fields->date_from."'".$and_contr."  ORDER BY trans_date DESC";
				// echo "1";
			}

			if(!$fields->date_from && $fields->date_to ) {
				$sql = "SELECT DATE_FORMAT (transactions.trans_date, '%d-%m-%Y') as trans_date, transactions.receivable, transactions.payable, firms.firm_name, clients.name, invoices.inv_name FROM transactions,firms, clients, invoices WHERE invoices.inv_id=transactions.inv_id AND firms.id_firm = transactions.firm_id AND clients.id_agent = transactions.cli_id AND transactions.trans_date =< '".$fields->date_to."'".$and_contr."  ORDER BY trans_date DESC";
				// echo "2";
			}

			if( $fields->date_from && $fields->date_to ){
				$sql = "SELECT DATE_FORMAT (transactions.trans_date, '%d-%m-%Y') as trans_date, transactions.receivable, transactions.payable, firms.firm_name, clients.name, invoices.inv_name FROM transactions,firms, clients, invoices WHERE invoices.inv_id=transactions.inv_id AND firms.id_firm = transactions.firm_id AND clients.id_agent = transactions.cli_id AND transactions.trans_date BETWEEN '".$fields->date_from."' AND '".$fields->date_to."'".$and_contr."  ORDER BY trans_date DESC";
				// echo "3";
			}
			echo json_encode($wpdb->get_results($sql));
		wp_die();
	}

	public function get_daily(){
		
		global $wpdb;

			// $sql = "SELECT DATE_FORMAT (transactions.trans_date, '%d-%m-%Y') as trans_date, transactions.receivable, transactions.payable, firms.firm_name, clients.name FROM transactions,firms, clients WHERE firms.id_firm = transactions.firm_id AND clients.id_agent = transactions.cli_id AND transactions.trans_date = CURDATE()";
			$sql = "SELECT DATE_FORMAT (transactions.trans_date, '%d-%m-%Y') as trans_date, transactions.receivable, transactions.payable, firms.firm_name, clients.name, invoices.inv_name FROM transactions,firms, clients, invoices WHERE invoices.inv_id=transactions.inv_id AND firms.id_firm = transactions.firm_id AND clients.id_agent = transactions.cli_id AND transactions.trans_date = CURDATE()";
		
		return $wpdb->get_results($sql);
	}

}



/*
set global sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
set session sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

*/