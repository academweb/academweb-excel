jQuery(document).ready(function($){

	setTimeout(function(){
		if( $('.alert').length > 0 ){$('.alert').slideUp(2000);}
		},2000);
	
	// agents 
	var agents_list = $('.agents-list');
	agents_list.select2();


	agents_list.on('change', function(){
		if( !$(this).val() ){
			$('#agent_id, #agent_name').val('');
			$('#action_agent').val('edit');
			$('.btn-save').text('Add new');
			return false;	
		} 
		 
		$('#agent_id').val( $(this).val() );
		$('#agent_name').val( $(this)[0].selectedOptions[0].textContent );
		$('.btn-save').text('Update');
	});

	$('.btn-delete').on('click', function(event){
		event.preventDefault();
		if( !$('#agent_id').val() ) {
			alert('Choose Contragent for DELETE!');
			return false;
		}

		if( confirm('Are You shure DELETE this agent?' ) ){
			$('#action_agent').val('delete');
			$('#agents_form').submit();
		}

	});

	// end agents


	// Companies 
	$('#companies_list').on('change', function(){
		if( !$(this).val() ) {
			$('.company-info').hide();
			$('#firm_name').val('');
			$('#firm__full_name').val('');
			$('#edit_firm').text('Add New');
			return false;
		}
		$('#firm_name').val( $(this)[0].selectedOptions[0].textContent );
		$('#firm__full_name').val( $('option:selected', this).attr('full_name') );
		$('.id_firm').val( $(this).val() );
		$('.company-info').show();
		$('#edit_firm').text('Edit');

		var data = {
			action: 'get_company_invoices',
			id_firm: $(this).val()
		};

		jQuery.post( ajaxurl, data, function(response) {
			
			// console.log( response );
			var conv = JSON.parse( response );
			var out = '<option val="">-- Choose an account --</option>';
			for (var i in conv ){
				out+= '<option value="' + conv[i].inv_id + '">' + conv[i].inv_name + '</option>';
			}
			$('#invoices_list').empty().html(out);
		});

		var data = {
			action: 'get_firm_report',
			id_firm: $(this).val()
		};

		jQuery.post( ajaxurl, data, function(response) {
			console.log( response );
			var conv = JSON.parse( response ), out='<table class="table">', daily, start;

			out+='<tr class="row"><th>Invoice name</th><th>Start Ballance</th></tr>';
			for (var i in conv ){

				daily = ( conv[i].daily!= null ) ? conv[i].daily : 0;
				start = ( conv[i].start_ballance ) ? conv[i].start_ballance : '';
				out+= '<tr inv_id="'+ conv[i].inv_id +'" class="row"><td>' + conv[i].inv_name + '</td></td>'+
						'<td><input type="text" data-a-dec="." data-a-sep=" " class="form-control start_ballance" name="start_ballance" placeholder="Insert Start ballance" value="' + start + '" /><button inv_id="' + 
						conv[i].inv_id + '" firm_id="'+ conv[i].firm_id +'" class="btn save_start">Save</button>' +
						'</td></tr>';
			}
			out+='</table>';
		
			$('.company-invoices').empty().html(out);
			$('.start_ballance').autoNumeric('init');
		});




		$(document).on('click', '.save_start', function(event){
			event.preventDefault();
			 
			var data = {
				action: 'save_start',
				inv_id: $(this).attr('inv_id'),
				firm_id: $(this).attr('firm_id'),
				start_bal: $(this).prev().val().replace(' ','')
			};
			jQuery.post( ajaxurl, data, function(response) {
				console.log( response );
			});
		});

	

	});



		$('.invoices-list').on('change', function(){
			console.log( $(this).val() );
			if( !$(this).val() ) {
				$('#invoice_name').val('');
				$('#save_invoice').text('Add New');
				$('#invoice_id').val('');
			return false;
			}else{
				$('#save_invoice').text('Edit an Account');
				$('#invoice_name').val( $(this)[0].selectedOptions[0].textContent );
				$('#invoice_id').val( $(this).val() );
			}
		

		});
	// End Companies

	//  Emails

	$('.emails-list').select2();
	$('#email_edit').change(function(){
		if( !$(this).val() ){
			
			$('#email_id').val('');
			$('#email_name').val('');
			$('#save_email').text('Add new Email');

		}else{

			$('#email_name').val( $(this)[0].selectedOptions[0].textContent );
			$('#email_id').val( $(this).val() );	
			$('#save_email').text('Edit Email');
		}
		
	});


});