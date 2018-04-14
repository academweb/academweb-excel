<?php
/*Template Name: Firm*/
get_header();

if( isset($_GET['firm_id']) && !empty( (int)$_GET['firm_id'] ) ){
	$firm_id = $_GET['firm_id'];
	$firm_info = $dbi->get_firms($firm_id);
	$firm_bills = $dbi->get_firm_bills($firm_id);
	$contragents = $dbi->get_contragents();
	$transactions = $dbi->get_firm_transactions($firm_id);
	$firms = $dbi->get_firms();
}

?>



<div class="container-fluid">
	<ul class="nav nav-tabs">
		<li ><a href="/#monthly">Balance summary</a></li>
	  	<li><a href="/#daily_report">Daily</a></li>
		<?php foreach($firms as $firm) : ?>
			<?php $active_firm = ( $_GET['firm_id']==$firm->id_firm ) ? 'class="active"' : ''; ?>
				<li <?=$active_firm?>><a  href="/firm?firm_id=<?=$firm->id_firm?>" ><?=$firm->firm_name?></a></li>
	 	<?php endforeach; ?>
	</ul>
	<div class="client-name">
		<p>Company: <b><?=$firm_info[0]->full_name;?></b></p>
	</div>
	<ul class="nav nav-tabs">

		<?php foreach ($firm_bills as $fk=>$firm_bill) : ?>
			<?php $active = ($fk==0) ? ' class="active"' : '';?>
			  <li <?=$active?> ><a data-toggle="tab" inv_id="<?=$firm_bill->inv_id?>" href="#bill_name<?=$firm_bill->inv_id?>"><?=$firm_bill->inv_name?></a></li>
			<?php endforeach; ?>
	</ul>

<div class="tab-content">

	<?php if( $firm_bills ) :?>
	<?php foreach ($firm_bills as $fk=>$firm_bill) : ?>
			<?php $active = ($fk==0) ? ' in active"' : '';?>
  <div id="bill_name<?=$firm_bill->inv_id?>" bill_id="<?=$firm_bill->inv_id?>" class="tab-pane fade <?=$active?>">
	<?php if($firm_bill->start_ballance) : ?>
	<div class="col-xs-12 totals">
			<div class="col-lg-2"></div>
			<div class="col-lg-2"></div>
			<div style="padding-left:0;" class="col-lg-2 receivable<?=$firm_bill->inv_id?>">Receivable <span data-a-dec="." data-a-sep=" " class="span-totals receivable_val_total">0</span></div>
			<div style="padding-left:0;" class="col-lg-2 payable<?=$firm_bill->inv_id?>">Payable <span data-a-dec="." data-a-sep=" " class="span-totals payable_val_total">0</span></div>
			<div class="col-lg-3"></div>
			<div class="col-lg-1"></div>
	</div>


	<div class="col-lg-12 firm-bill-<?=$firm_bill->inv_id?> table-bg">
		<div class="row theader">
			<div class="col-lg-2">Date</div>
			<div class="col-lg-2">Daily balance</div>
			<div class="col-lg-2">Receivable</div>
			<div class="col-lg-2">Payable</div>
			<div class="col-lg-3">Remarks</div>
			<div class="col-lg-1"></div>
		</div>
		
		<div class="row tbody tbody-firm-bill-<?=$firm_bill->inv_id?> clearfix">
			<?php $sum_rec = $sum_pay = $total = $c = 0; ?>
			<?php foreach ($transactions as $transaction) : ?>
				<?php if( $transaction->inv_id!== $firm_bill->inv_id) continue; ?>
				<?php 

					$odd = '';
					if($c%2==0){$odd = 'odd';}
					// $sum_rec+=$transaction->receivable;
					// $sum_pay+=$transaction->payable;
					// $total+=$transaction->receivable + $transaction->payable;
					$tr_remark = ($transaction->remark) ? '( '.$transaction->remark.' )' : '';
					$receivable_val =  ( (int)$transaction->receivable !==0 ) ? number_format(($transaction->receivable),2,'.',' ') : '';
					$payable_val = ((int)$transaction->payable !== 0 ) ? number_format(($transaction->payable),2,'.',' ') : '';
				?>
			<div class="row <?=$odd?> " row_id="<?=$transaction->id?>">
				<div class="col-lg-2"><?=$transaction->formatted_date?></div>
				<div class="col-lg-2"><?=number_format(($transaction->daily),2,'.',' ')?></div>
				<div class="col-lg-2 receivable_val"><?=$receivable_val?></div>
				<div class="col-lg-2 payable_val"><?=$payable_val?></div>
				<div class="col-lg-3"><?=$transaction->name?> <?=$tr_remark?></div>
				<div class="col-lg-1"><span bill_id="<?=$firm_bill->inv_id?>" row_id="<?=$transaction->id?>" class="glyphicon glyphicon-pencil"></span>
					<span firm_id="<?=$transaction->firm_id?>" bill_id="<?=$firm_bill->inv_id?>" row_id="<?=$transaction->id?>"  class="glyphicon glyphicon-trash"></span></div>
			</div>
			<?php $c++; ?>
		<?php endforeach; ?>
		</div>
			<div class="row action-fields">

				<div class="col-lg-2">
					<label>Date:</label>
					<input type="text" name="trans_date" class="form-control trans_date required" value="" />
				</div>
				<div class="col-lg-1"></div>
				<div class="col-lg-2">
					<div class="form-group">
						<label>Receivable: </label>
						<input data-a-dec="." data-a-sep=" " type="text" name="receivable" class="form-control moneyin receivable required" value="" />
					</div>
				</div>
				<div class="col-lg-2">
					<div class="form-group">
						<label>Payable</label>
						<input data-a-dec="." data-a-sep=" " type="text" name="payable" class="form-control payable moneyin required" value="" />
					</div>
				</div>
				<div class="col-lg-4 contragents-wrap">
					<div class="form-group">
						<label>Partner</label>
							<select class="form-control cli_id required contragents_sel" name="cli_id" >
								<option value="" >Choose partner</option>
								<?php
									foreach ($contragents as $contragent) {
										echo '<option value="'.$contragent->id_agent.'" >'.$contragent->name.'</option>';
									}
								?>
							</select>
	
							<label>Remark</label>
							<textarea class="form-control remark" rows="3" name="remark"></textarea>
						</div>						
				</div>
				<div class="col-lg-1">
					<div class="form-group">
						<button firm_id = "<?=$firm_id?>" inv_id = "<?=$firm_bill->inv_id?>" class="btn add">Add</button>
						<button row_id="0" firm_id = "<?=$firm_id?>" bill_id="<?=$firm_bill->inv_id?>" class="btn save hide">Edit</button>
					</div>
				</div>
			</div>
	</div>
	<?php else: ?>
		<div class="alert alert-danger">
			<p>Start ballance of invoice '<?=$firm_bill->inv_name?>' is 0!</p>
		</div>
	<?php endif; ?>
		
  </div>
<?php endforeach; ?>
<?php else: ?>
	<p>Sorry, no accounts!</p>
<?php endif; ?>
 
</div>
<?php wp_footer(); ?>