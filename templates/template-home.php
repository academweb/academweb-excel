<?php
/*Template Name: Home*/
get_header();


$firms = $dbi->get_firms();
$daily_report = $dbi->get_daily();
$contragents = $dbi->get_contragents();

?>

<div class="container-fluid">
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#monthly">Balance summary</a></li>
	  <li><a data-toggle="tab" href="#daily_report">Daily</a></li>
	<!--   <li><a data-toggle="tab" href="#firms">Firms</a></li> -->
	<?php foreach($firms as $firm) : ?>
				<li><a  href="/firm?firm_id=<?=$firm->id_firm?>" ><?=$firm->firm_name?></a></li>
	 <?php endforeach; ?>
	</ul>

	<div class="tab-content">
	  <div id="monthly" class="tab-pane fade in active">
	   

		<table class="table table-striped table-bordered all-reports">
			<thead>
			<tr>
				<th style="width:30%;">Balance summary</th><th style="width:70%;"></th>
			</tr>
			</thead>
			<tbody>
				<?php 
				$grand_total = 0;
				foreach ($firms as $firm ) {
					$total_firm = 0;
					echo '<tr><th colspan="2" style="color: #a77e1a;">'.$firm->firm_name.' - "'.$firm->full_name.'"</th></tr>';
					foreach( $dbi->get_firm_bills($firm->id_firm) as $bill ){
				
						echo '<tr><td>'.$bill->inv_name.'</td>';
						// echo '<tr><td>'.$bill->inv_name.'</td><td>'.number_format( ( $bill->daily ) ,0,'.',' ').'</td></tr>';
						// $total_firm+=$bill->daily;
						$daily_firm_ballance = $dbi->get_firms_reports($firm->id_firm, $bill->inv_id);
						$stb= $dbi->get_firm_summ_report($firm->id_firm, $bill->inv_id);
						$start_bal = $stb ? $stb[0]->start_ballance : 0;
						$db = isset($daily_firm_ballance[0]->daily) ? $daily_firm_ballance[0]->daily : $start_bal;
						echo '<td>'.number_format( $db ,0,'.',' ').'</td></tr>';
						$total_firm+=$db;
						
					}

					echo '<tr><th></th><th>'.number_format( $total_firm ,0,'.',' ').'</th></tr>';
					$grand_total+=$total_firm;
				}
				echo '<tr class="grand-total"><th>Grand Total</th><th>'.number_format( $grand_total ,0,'.',' ').'</th></tr>';
				?>
			
			

			</tbody>
			
		</table>
	  </div>
	  <div id="daily_report" class="tab-pane fade">
	  	<div class="col-xs-2"><label>From Date: </label><input type="text" id="daily_date_report" class="form-control"><span class="glyphicon glyphicon-triangle-bottom"></span></div>
	  	<div class="col-xs-2"><label>To Date: </label><input type="text" id="daily_date_report_to" class="form-control"><span class="glyphicon glyphicon-triangle-bottom"></div>
	  	<div class="col-xs-4"><label>Partner: </label><select id="filter_contr" class="form-control contragents_sel">
	  			<option value="" >Choose partner</option>
						<?php
							foreach ($contragents as $contragent) {
								echo '<option value="'.$contragent->id_agent.'" >'.$contragent->name.'</option>';
							}
						?>
	  	</select></div>
	  	<div class="col-xs-12 daily-tables">

		  	<div class="col-lg-6">
		  		<h4>Payments</h4>
		  		
			   <table class="table table-striped table-bordered beneficiary">
					<thead>
					<tr>
						<th style="width:12%;">Date</th><th>Amount</th><th>Beneficiary</th><th>Company</th><th>Account</th>
					</tr>
					</thead>
					<tbody>
						<?php 
						$pay_sum = 0;
							foreach ($daily_report as $dr) {
					
								if( $dr->payable == 0 ) continue;
								echo '<tr><td>'.$dr->trans_date.'</td><td>'.number_format( $dr->payable ,0,'.',' ').'</td><td>'.$dr->name.'</td><td>'.$dr->firm_name.'</td><td>'.$dr->inv_name.'</td></tr>';
								$pay_sum+=$dr->payable;
							}
						?>
						
					</tbody>
					
				</table>
				<h4 class="daily-totals">Total: <span id="payments_total"><?=number_format( $pay_sum ,0,'.',' ')?></span></h4>
			</div>
			<div class="col-lg-6">
				<h4>Inwards</h4>
				
			   <table class="table table-striped table-bordered rec_comp">
					<thead>
					<tr>
						<th>Date</th><th>Amount</th><th>Paid from</th><th>Company</th><th>Account</th>
					</tr>
					</thead>
					<tbody>
						<?php 
						$rec_sum = 0;
							foreach ($daily_report as $dr) {
								if( $dr->receivable == 0 ) continue;
								echo '<tr><td>'.$dr->trans_date.'</td><td>'.number_format( $dr->receivable ,0,'.',' ').'</td><td>'.$dr->name.'</td><td>'.$dr->firm_name.'</td><td>'.$dr->inv_name.'</td></tr>';
								$rec_sum+=$dr->receivable;
							}
						?>
					</tbody>
					
				</table>
				<h4  class="daily-totals">Total: <span id="inwards_toal"><?=number_format( $rec_sum ,0,'.',' ')?></span></h4>
			</div>
	  </div>

	</div>
	
</div>
<?php wp_footer(); ?>