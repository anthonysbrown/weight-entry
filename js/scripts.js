
	jQuery(function($) {
				 $(".sb-selects").select2();
				
		$("#send-email-user").on("submit", function(){
			
			var data = {
			'action': 'members_send_email',
			'email_subject': $('.email-subject').val(),
			'email_body': $('.email-body').val(),
			'email_user': $('.email-user').val(),
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			alert(response);
			console.log(response);
			$('.tb-close-icon').trigger( "click" );
		});
			
			
			
			return false;
		});
		$(".email-user-id").on("click", function(){
			$('.email-subject').val('');
			$('.email-body').val('');
			$('.email-user').val($(this).attr('data-id'));
			console.log($(this).attr('data-id'));
			
		});
				
				
		$(".show-progress-id").on("click",function(){
			
			$('.progress-id-' + $(this).attr('data-id')).slideToggle();		
						
						
			return false;
		});
		
		
		
	});