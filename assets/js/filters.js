jQuery(document).ready(function($){
	var filter_obj = {};
	var filter_daily = function( filter_obj ){

		var data = {
			action:'get_filered_daily',
			filters:JSON.stringify( filter_obj )
		};
		jQuery.post( reports.ajaxurl, data, function(response) {

			var conv = JSON.parse( response );

			var bout = '', rout = '', bsum = 0, rsum = 0;
			for(var i in conv){
				if ( conv[i].receivable*1==0 ) {
					bout+='<tr><td>' + conv[i].trans_date + '</td><td class="aj-pays" data-a-dec="." data-a-sep=" ">' + conv[i].payable + '</td><td>' + conv[i].name + '</td><td>' + conv[i].firm_name + '</td><td>' + conv[i].inv_name + '</td></tr>';
					bsum+= parseFloat(conv[i].payable);
				}
				if ( conv[i].payable*1==0 ){
				 rout+='<tr><td>' + conv[i].trans_date + '</td></td><td class="aj-rec" data-a-dec="." data-a-sep=" ">' + conv[i].receivable + '</td><td>' + conv[i].name + '</td><td>' + conv[i].firm_name + '</td><td>' + conv[i].inv_name + '</td></tr>';
				 rsum+=parseFloat(conv[i].receivable);
				}
			}
		if ( !bout ) bout = '<tr class="empty-alert"><th colspan = "4">No operations for this date!</th></tr>';
		if ( !rout ) rout = '<tr class="empty-alert"><th colspan = "4">No operations for this date!</th></tr>';
			$('.beneficiary tbody').empty().html(bout);
			$('.rec_comp tbody').empty().html(rout);
			$('#payments_total').text(bsum.toFixed(2));
			$('#inwards_toal').text(rsum.toFixed(2));
			$('.aj-pays, .aj-rec').autoNumeric('init');
		});
	};



	var d = new Date();
	var curr_date = d.getDate();
	var curr_month = ( String( d.getMonth() ).length < 2 ) ? ( "0" + (d.getMonth() + 1) ) : ( d.getMonth() + 1);
	var curr_year = d.getFullYear();

	$('#daily_date_report, #daily_date_report_to, .trans_date').datepicker({ dateFormat: "dd-mm-yy" }).val( curr_date + "-" + curr_month + "-" + curr_year );


	function formatDates(date){
		
		if( date==undefined || date=='') return '';
		var explode = date.split('-');
		return explode[2] + '-' + explode[1] + '-' + explode[0];
	}

	$('#daily_date_report, #daily_date_report_to').on('change', function(){

			var date_from = formatDates ( $('#daily_date_report').val() );
			var date_to = formatDates ( $('#daily_date_report_to').val() );
			filter_obj.date_from=date_from;
			filter_obj.date_to=date_to;
			
			filter_daily(  filter_obj  );
	
	});


	$('#filter_contr').on('change', function(){
		filter_obj.filter_contr = $(this).val();
		filter_daily( filter_obj );
	});

	$('.glyphicon-triangle-bottom').on('click', function(){
		$(this).prev().focus();
	});

});
