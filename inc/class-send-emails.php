<?php

class Send_Emails{

	private $emails_list = '';
	private $dir = __DIR__.'/';
	function __construct(){
		
		$emails_id = get_option('emails_for_send');
		$this->get_list_emails($emails_id);
		
	}

	private function get_list_emails($emails_id=null){
		if( !$emails_id ) return;
		
		global $wpdb;

		$emails_id_list = implode(',', $emails_id);
		
		$sql = "SELECT * FROM `emails` WHERE id IN( ".$emails_id_list.")";
		$res = $wpdb->get_results( $sql );
		$send_mass = [];
		foreach ($res as $email) {
			$send_mass[] = $email->email;
		}

		$this->emails_list =  $send_mass;
		
		if( $_GET['send_report']=='summ' ) $this->generate_html_pdf_summary();
		if( $_GET['send_report']=='day' ) $this->generate_html_pdf_daily();
	}

	private function generate_html_pdf_summary(){
		require_once __DIR__ . '/vendor/autoload.php';

		$mpdf = new \Mpdf\Mpdf();
		
		$dbi = new DBI;
		$firms = $dbi->get_firms();
		$out='';
		$out.='<div class="tab-content">
	  	<div id="monthly" class="tab-pane fade in active">
			<table class="table table-striped table-bordered">
			<thead>
			<tr>
				<th>Balance summary</th><th></th>
			</tr>
			</thead>
			<tbody>';
				$grand_total = 0;
				foreach ($firms as $firm ) {
					$total_firm = 0;
					$out.='<tr><th colspan="2">'.$firm->firm_name.'</th></tr>';
					foreach( $dbi->get_firm_bills($firm->id_firm) as $bill ){
				
						$out.='<tr><td>'.$bill->inv_name.'</td>';
						// echo '<tr><td>'.$bill->inv_name.'</td><td>'.number_format( ( $bill->daily ) ,0,'.',' ').'</td></tr>';
						// $total_firm+=$bill->daily;
						$daily_firm_ballance = $dbi->get_firms_reports($firm->id_firm, $bill->inv_id);
						$db = isset($daily_firm_ballance[0]->daily) ? $daily_firm_ballance[0]->daily : 0;
						$out.='<td>'.number_format( $db ,0,'.',' ').'</td></tr>';
						$total_firm+=$db;
						
					}
					$out.='<tr><th></th><th>'.number_format( $total_firm ,0,'.',' ').'</th></tr>';
					$grand_total+=$total_firm;
				}
				$out.='<tr class="grand-total"><th>Grand Total</th><th>'.number_format( $grand_total ,0,'.',' ').'</th></tr>';
			$out.='</tbody></table>';
		
		$mpdf->WriteHTML($out);
		$filename = 'summary';
		$mpdf->Output($this->dir.$filename.'.pdf','F');
		$mpdf=null;
		$this->send_report($filename, 'Summary Report');
	}


	private function generate_html_pdf_daily(){
		require_once __DIR__ . '/vendor/autoload.php';
		$mpdf = new \Mpdf\Mpdf();
		$dbi = new DBI;
		$daily_report = $dbi->get_daily();
		$contragents = $dbi->get_contragents();
		
		$out='';

		$out.='<div class="col-xs-12">

		  	<div class="col-lg-6">
		  		<h4>Payments</h4>
		  		
			   <table class="table table-striped table-bordered beneficiary">
					<thead>
					<tr>
						<th style="width: 20%;">Date</th><th>Amount</th><th>Beneficiary</th><th>Company</th>
					</tr>
					</thead>
					<tbody>';
						 
						$pay_sum = 0;
							foreach ($daily_report as $dr) {
								if( $dr->payable == 0 ) continue;
								$out.='<tr><td>'.$dr->trans_date.'</td><td>'.$dr->payable.'</td><td>'.$dr->name.'</td><td>'.$dr->firm_name.'</td></tr>';
								$pay_sum+=$dr->payable;
							}					
						
					$out.='</tbody>
					
				</table>
				<h5>Total: <span id="payments_total">'.$pay_sum.'</span></h5>
			</div>
			<div class="col-lg-6">
				<h4>Inwards</h4>
				
			   <table class="table table-striped table-bordered rec_comp">
					<thead>
					<tr>
						<th style="width: 20%;">Date</th><th>Amount</th><th>Paid from</th><th>Company</th>
					</tr>
					</thead>
					<tbody>';
						
						$rec_sum = 0;
							foreach ($daily_report as $dr) {
								if( $dr->receivable == 0 ) continue;
								$out.='<tr><td>'.$dr->trans_date.'</td><td>'.$dr->receivable.'</td><td>'.$dr->name.'</td><td>'.$dr->firm_name.'</td></tr>';
								$rec_sum+=$dr->receivable;
							}
						
					$out.='</tbody></table><h5>Total: <span id="inwards_toal">'.$rec_sum.'</span></h5></div></div>';
// echo $out;

	  	$mpdf->WriteHTML($out);
	  	$filename = 'daily';
		$mpdf->Output($this->dir.$filename.'.pdf','F');
		$mpdf=null;
		$this->send_report($filename, 'Daily Report');

	}


	private function send_report($filename = '', $theme=''){
		if(!$filename) return;
		$file = $this->dir.$filename.'.pdf';

		$headers = 'From: Reports <devwp@ukr.net>' . "\r\n";

		wp_mail($this->emails_list, $theme, 'Content', $headers, $file);

		die();
	}	

}