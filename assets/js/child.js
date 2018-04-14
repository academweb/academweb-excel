(function ($, root, undefined) {
	
	$(function () {
		
		'use strict';
		

		$('.contragents_sel').select2();
		// $('.trans_date').datepicker({
		// 	dateFormat: "yy-mm-dd",
		// 	// minDate: new Date(),
		// 	onSelect: function(date,obj) {
  //           	// console.log( date );
  //           	// console.log( obj);
  //       	},
		// });

		$('.moneyin').autoNumeric('init');

		$('.nav-tabs a').each(function(i, obj){
			 if( $(obj)[0].hash == window.location.hash) $(obj).trigger('click');
		});


		function check_totals(){
		
			$('.tab-pane').each(function(i, tab){
			 	var rec_sum = 0, pay_sum = 0;
			 	var bill_id = $(tab).attr('bill_id');
				 $(tab).find('.receivable_val').each( function(r, rec_obj){
				 	rec_sum+= ( parseFloat( $(rec_obj).text().replace(' ', '') )*1 ) ? parseFloat( $(rec_obj).text().replace(' ', '') ) : 0;
				 });
				 $(tab).find('.payable_val').each( function(r, pay_obj){
				 	pay_sum+= parseFloat( $(pay_obj).text().replace(' ', '')*1 ) ? parseFloat( $(pay_obj).text().replace(' ', '') ) : 0;
				 });


				 // $(tab).find('.total'+bill_id + ' span').html(rec_sum*1 + pay_sum*1 );
				 $(tab).find('.receivable'+bill_id + ' span').html( parseFloat( rec_sum ).toFixed(2) );
				 $(tab).find('.payable'+bill_id + ' span').html( parseFloat( pay_sum ).toFixed(2) );
				 $('.span-totals').autoNumeric('init');
			});

		}

		

		check_totals();

		//  DELETE 
			$(document).on('click', '.glyphicon-trash', function(){
				var row = $(this).parent().parent();
				var row_id = $(this).attr('row_id');
				var inv_id = $(this).attr('bill_id');
				var firm_id = $(this).attr('firm_id');
				
				var data = {
					action: 'delete_row',
					row_id: row_id, 
					inv_id: inv_id,
					firm_id: firm_id
				};
				
				if(confirm('Are you shure, delete this row?')){
					jQuery.post( reports.ajaxurl, data, function(response) {

					console.log( response );
						var conv = JSON.parse( response );
					if(conv){
							var out = '', remark='';
							for (var i in conv ){
								remark = conv[i].remark ? conv[i].remark : '';
								out+='<div class="row" row_id="'+ conv[i].id +'">' +
										'<div class="col-lg-2">' + conv[i].formatted_date + '</div>' +
										'<div class="col-lg-2">' + conv[i].daily + '</div>' + 
										'<div class="col-lg-2 receivable_val">' +  conv[i].receivable + '</div>' +
										'<div class="col-lg-2 payable_val">' +  conv[i].payable + '</div>' + 
										'<div class="col-lg-3">' + conv[i].name + '<br/>' + remark + '</div>' + 
										'<div class="col-lg-1"><span bill_id="'+conv[i].inv_id+'" row_id="'+conv[i].id+'"  class="glyphicon glyphicon-pencil"></span>'+
										'<span firm_id="'+ conv[i].firm_id +'" bill_id="'+ conv[i].inv_id +'" row_id="'+ conv[i].id +'"  class="glyphicon glyphicon-trash"></span></div>' + 
									'</div>'
							}
							// console.log( '.tbody-firm-bill-' + conv[0].inv_id );
							$( '.tbody-firm-bill-' + conv[0].inv_id  ).empty().html( out );
							check_totals();
						}else{
							$( '.tbody-firm-bill-' + conv[0].inv_id  ).empty();
						}
						
					});
				}
			});
		//  END DELETE


			// INSERT TRANSACTION
		var aj_report = function( data = {} ){
	
			jQuery.post( reports.ajaxurl, data, function(response) {
			console.log( response );
				var conv = JSON.parse( response );
			
				var out = '', remark='';
				for (var i in conv ){
					remark = conv[i].remark ? conv[i].remark : '';
					out+='<div class="row" row_id="'+ conv[i].id +'">' +
							'<div class="col-lg-2">' + conv[i].formatted_date + '</div>' +
							'<div class="col-lg-2">' + conv[i].daily + '</div>' + 
							'<div class="col-lg-2 receivable_val">' +  conv[i].receivable + '</div>' +
							'<div class="col-lg-2 payable_val">' +  conv[i].payable + '</div>' + 
							'<div class="col-lg-3">' + conv[i].name + '<br/>' + remark + '</div>' + 
							'<div class="col-lg-1"><span bill_id="'+conv[i].inv_id+'" row_id="'+conv[i].id+'"  class="glyphicon glyphicon-pencil"></span>'+
							'<span firm_id="'+ conv[i].firm_id +'" bill_id="'+ conv[i].inv_id +'" row_id="'+ conv[i].id +'"  class="glyphicon glyphicon-trash"></span></div>' + 
						'</div>'
				}
				// console.log( '.tbody-firm-bill-' + conv[0].inv_id );
				$( '.tbody-firm-bill-' + conv[0].inv_id  ).empty().html( out );
				check_totals();
				$('.required').val('').change();
					var d = new Date();
				var curr_date = d.getDate();
				var curr_month = ( String( d.getMonth() ).length < 2 ) ? ( "0" + (d.getMonth() + 1) ) : ( d.getMonth() + 1);
				var curr_year = d.getFullYear();
				$('.action-fields .trans_date').val( curr_date + "-" + curr_month + "-" + curr_year );
				setTimeout(function(){$('.last-row').removeClass('last-row');},3000);
			});
		}

		
		$('.add').on('click', function(event){
			var data = {}, error = 0, repl = '', rec_pay = 0;;
			$('.required').removeClass('alert-field');
			event.preventDefault();
			var firm_id =  $(this).attr('firm_id');
			var inv_id =  $(this).attr('inv_id');			

			var parent = $(this).parent().parent().parent();

			if( $(parent).find('.trans_date').val().trim()=='' ){
				error = 1;
				$('.trans_date').addClass('alert-field');
				alert('Date is required!');
				return false;
			}

			if( $(parent).find('.payable').val() ) rec_pay++;
			if( $(parent).find('.receivable').val() ) rec_pay++;
			

			if( rec_pay>1 ) {
				error = 1;
				alert( 'Must be only \'Receivable\' OR  \'Payable\'! ');
				$('.payable, .receivable').addClass('alert-field');
				return false;
			}

			if( $(parent).find('.payable').val().trim()=='' && $(parent).find('.receivable').val().trim()=='' ) {
				error=1;
				$(parent).find('.payable, .receivable').addClass('alert-field');
				alert( '\'Receivable\' OR  \'Payable\' must be!' );
				return false;
			}

			if( $(parent).find('.cli_id').val().trim()=='' ){
				error = 1;
				$(parent).find('.cli_id').addClass('alert-field');
				alert('Contragent is required!');
				return false;
			}

			data['trans_date'] = $(parent).find('.trans_date').val();
			data['receivable'] = String($(parent).find('.receivable').val()).replace(/[\s]+/g,'');
			data['payable'] = $(parent).find('.payable').val().replace(/[\s]+/g,'');
			data['cli_id'] = $(parent).find('.cli_id').val();
			data['action'] = 'save_trans_row';
			data['firm_id'] = firm_id;
			data['inv_id'] = inv_id;
			data['remark'] = $(parent).find('.remark').val();

			if( error==0 )	aj_report( data );
		});

		$('.required').on('blur', function(){
			if( $(this).val().trim()!=='' ) $(this).removeClass('alert-field');
		});

		// END INSERT TRANSACTION
		


		// Edit row
		$(document).on('click', '.glyphicon-pencil', function(){
			
			var row_id = $(this).attr('row_id');
			var bill_id = $(this).attr('bill_id');
			var error = 0;
			var data = {
				action: 'get_row',
				row_id: row_id
			};


			jQuery.post( reports.ajaxurl, data, function(response) {
				console.log( response );
				// console.log( $('#bill_id'+bill_id ) );
				var conv = JSON.parse(response);
				$('#bill_name'+bill_id + ' .trans_date').val( conv[0].trans_date );
				$('#bill_name'+bill_id + ' .receivable').val( conv[0].receivable*1 );
				$('#bill_name'+bill_id + ' .payable').val( conv[0].payable*1 );
				$('#bill_name'+bill_id + ' .cli_id').val( conv[0].cli_id ).change();
				$('#bill_name'+bill_id + ' .remark').val( conv[0].remark );
				$('.add').addClass('hide');
				$('.save').removeClass('hide').attr('row_id', row_id);
			});

		});

		$('.save').on('click', function(){
			var row_id = $(this).attr('row_id');
			var inv_id = $(this).attr('bill_id');
			var firm_id = $(this).attr('firm_id');
			var parent = $(this).parent().parent().parent();
			var rec_pay=0, error=0;
			if( $(parent).find('.trans_date').val().trim()=='' ){
				error = 1;
				$('.trans_date').addClass('alert-field');
				alert('Date is required!');
				return false;
			}

			if( $(parent).find('.payable').val()>0 ) rec_pay++;
			if( $(parent).find('.receivable').val()>0 ) rec_pay++;
			

			if( rec_pay>1 ) {
				error = 1;
				alert( 'Must be only \'Receivable\' OR  \'Payable\'! ');
				$('.payable, .receivable').addClass('alert-field');
				return false;
			}

			if( $(parent).find('.payable').val().trim()=='' && $(parent).find('.receivable').val().trim()=='' ) {
				error=1;
				$(parent).find('.payable, .receivable').addClass('alert-field');
				alert( '\'Receivable\' OR  \'Payable\' must be!' );
				return false;
			}

			if( $(parent).find('.cli_id').val().trim()=='' ){
				error = 1;
				$(parent).find('.cli_id').addClass('alert-field');
				alert('Contragent is required!');
				return false;
			}

			if(error) return false;
			var data = {
				action: 'update_trans_row',
				row_id: row_id,
				firm_id: firm_id,
				inv_id: inv_id,
				trans_date: $('#bill_name'+inv_id).find('input.trans_date').val(),
				receivable: $('#bill_name'+inv_id).find('input.receivable').val().replace(/[\s]+/g,''),
				payable: $('#bill_name'+inv_id).find('input.payable').val().replace(/[\s]+/g,''),
				cli_id: $('#bill_name'+inv_id).find('select.cli_id').val(),
				remark: $('#bill_name'+inv_id).find('textarea.remark').val()
			};

			jQuery.post( reports.ajaxurl, data, function(response) {
		console.log( response );
				$('.action-fields input, .action-fields select, .action-fields textarea').val('').trigger('change');

				$('.save').addClass('hide').attr('row_id', 0);
				$('.add').removeClass('hide');
				var conv = JSON.parse( response );
			
				var out = '', remark='', c=1, odd='';

				for (var i in conv ){
					odd='';
					if(c%2==0) odd='odd';
					remark = conv[i].remark ? '( ' + conv[i].remark + ' )': '';
					out+='<div class="row '+odd+'" row_id="'+ conv[i].id +'">' +
							'<div class="col-lg-2">' + conv[i].formatted_date + '</div>' +
							'<div class="col-lg-2">' + conv[i].daily + '</div>' + 
							'<div class="col-lg-2 receivable_val">' +  conv[i].receivable + '</div>' +
							'<div class="col-lg-2 payable_val">' +  conv[i].payable + '</div>' + 
							'<div class="col-lg-3">' + conv[i].name + remark + '</div>' + 
							'<div class="col-lg-1"><span bill_id="'+conv[i].inv_id+'" row_id="'+conv[i].id+'"  class="glyphicon glyphicon-pencil"></span>'+
							'<span firm_id="'+ conv[i].firm_id +'" bill_id="'+ conv[i].inv_id +'" row_id="'+ conv[i].id +'"  class="glyphicon glyphicon-trash"></span></div>' + 
						'</div>';
						c++;
				}

				$( '.tbody-firm-bill-' + conv[0].inv_id  ).empty().html( out );
				check_totals();
				$('.required').val('').change();

			});


		});
		// End edit row


	});
	
})(jQuery, this);

