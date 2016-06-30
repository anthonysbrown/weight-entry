
	jQuery(function($) {
				 $(".sb-selects").select2();
				 
				 
		$(".wt-row-click").on("click", function(){
			
				var clicked = $(this);
				var row_click = 0;
				if(clicked.attr('data-status') == 0 ){
				clicked.removeClass('wt-weight-on');
				clicked.addClass('wt-weight-off');
				clicked.attr('data-status', 1);
				row_click = 1;
				}
				if(row_click == 1){
			jQuery.post(ajaxurl, 
								{'action': 'members_mark_viewed',
								'status': row_click,
								'id': clicked.attr('data-id')								
								},
								 function(response) {
							
				
					console.log(response);
			});
				
				}
		});
				
		$("#send-email-user").on("submit", function(){
			
			var data = {
			'action': 'members_send_email',
			'email_subject': $('.email-subject').val(),
			'email_body': $('.email-body').val(),
			'email_user': $('.email-user').val(),
			'email_coach': $('.email-coach').val(),
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
			$('.email-coach').val($(this).attr('data-coach'));
			console.log($(this).attr('data-id'));
			
		});
				
				
		$(".show-progress-id").on("click",function(){
			
			$('.progress-id-' + $(this).attr('data-id')).slideToggle();		
						
						
			return false;
		});
		
		
		
	});