(function( $ ) {
	var loader_html	= '<div class="tuturn-preloader-section"><div class="tuturn-preloader-holder"><div class="tuturn-loader"></div></div></div>';
	'use strict';
	$(function() {
		// Load payout setup
		jQuery(document).on('click','.tu-payout-modal', function(e){
			let payout_id	= jQuery(this).data('id');
			let payout_form = wp.template(payout_id);		
			payout_form = payout_form();
			jQuery('.tuturn-popup').find('#tuturn-model-body').html(payout_form);		
			jQuery('.tuturn-popup').modal('show'); 
			e.preventDefault();
		});

		/* Toggle class when elementor header use */
		jQuery('.tu-elementor-dash-menu .sub-menu-holder').on('click', function() {
			jQuery(this).toggleClass('tu-open-usermenu');
			jQuery('.sub-menu-holder > .sub-menu').slideToggle(300);
		});

		// Default payout checked
		jQuery(document).on('change', 'input[type=radio][name=payout_settings_type]', function (event) {
			event.preventDefault();
			if(jQuery(this).val()){
				let _this = jQuery(this);
				let payout_type	= jQuery(this).val();
 				jQuery('body').append(loader_html);
				jQuery.ajax({
					type: "POST",
					url: scripts_vars.ajaxurl,
					data: {
						'action': 'tu_default_payout',
						'security': scripts_vars.ajax_nonce,
 						'data' :{ type: payout_type}
					},
					dataType: "json",
					success: function (response) {
						jQuery('body').find('.tuturn-preloader-section').remove();
						if (response.type === 'success') {
							stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
							 setTimeout(function () {
								window.location.reload();
							}, 3000);
						} else {
							stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
						}
					}
				});

			}	
		});

		/**
		 * Delete/Cancel verification
		 */
		jQuery(document).on('click', '#tu-cancel-identity-verifi', function (e) {
			e.preventDefault();
			var _this = jQuery(this);
			var _post_id = _this.data('post_id');
			jQuery.confirm({
				title: scripts_vars.cancel_verification,
                content: scripts_vars.cancel_reupload,
                closeIcon: true,
                boxWidth: '600px',
                theme: 'modern',
                draggable: false,
                useBootstrap: false,
                typeAnimated: true,
				buttons: {
					yes: {
						text: scripts_vars.cancel_verification,
                        btnClass: 'tb-btn',
						action: function () {
							jQuery('body').append(loader_html);
							jQuery.ajax({
								type: 'POST',
								url: scripts_vars.ajaxurl,
								data: {
								  action: 'tuturn_cancel_resend_verification',
								  post_id: _post_id,
								},
								dataType: 'json',
								success: function (response) {
								  jQuery('body').find('.tuturn-preloader-section').remove();
								  if (response.type === 'success') {
									stickyAlert(response.title, response.message, {
									  classList: 'success',
									  autoclose: 3000,
									});
									window.location.replace(response.redirect);
								  } else {
									stickyAlert(response.title, response.message, {
									  classList: 'danger',
									  autoclose: 5000,
									});
								  }
								},
							  });                    
							return false;
						}
					}
				},
				no: {
					text: scripts_vars.btntext_cancelled,
					btnClass: 'tb-btnvthree',
				}
			});
		  });

		//Tuturn dwonloaded log
		jQuery(document).on('click', '.download-csv-log', function(e){
			jQuery('#tu-logdata-form').submit();
		});

		//subject delete
		jQuery(document).on('click', '.btn-hours-approve', function (e) {
			let post_id = jQuery(this).data('post_id');
			let profile_id 	= jQuery('#profile_id').val();
			jQuery.confirm({
               // icon: 'icon icon-trash tu-deleteclr',
                title: scripts_vars.approve_hours,
                content: scripts_vars.approve_hours_message,
                closeIcon: true,
                boxWidth: '600px',
                theme: 'modern',
                draggable: false,
                useBootstrap: false,
                typeAnimated: true,
                buttons: {
                    yes: {
                        text: scripts_vars.approve_hours,
                        btnClass: 'tb-btn',
                        action: function () {
							var jc	= this; 
							//jc.showLoading();
							jQuery('body').append(loader_html);
 							jQuery.ajax({
								type: "POST",
								url: scripts_vars.ajaxurl,
								dataType:"json",
								data: {
									'action': 'tu_approve_hours',
									'security': scripts_vars.ajax_nonce,
									'post_id':  post_id,
									'profile_id': profile_id
								},
								success: function(response) {
									
									jQuery('body').find('.tuturn-preloader-section').remove();
									if (response.type === 'success') {
										jQuery('.tuturn-popup').modal('show'); 
										stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
										setTimeout(function () {
											window.location.reload();
										}, 3000);
									} else {
										stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
									}
								}
							});                    
							return false;
						}
                    },
                    no: {
                        text: scripts_vars.btntext_cancelled,
                        btnClass: 'tb-btnvthree',
                    }
                }
            });
		});
		// Remove hour log

		jQuery(document).on('click', '.tu_remove_hours', function (e) {
			e.preventDefault();
			let _this = jQuery(this);
			var post_id 		= _this.data('post_id');
			var user_id 		= _this.data('user-id');
			let profile_id 		= jQuery('#profile_id').val();

			jQuery.confirm({
                icon: 'icon icon-trash tu-deleteclr',
                title: scripts_vars.remove_hour_log_title,
                content: scripts_vars.remove_hour_log_desc,
                closeIcon: true,
                boxWidth: '600px',
                theme: 'modern',
                draggable: false,
                useBootstrap: false,
                typeAnimated: true,
                buttons: {
                    yes: {
                        text: scripts_vars.yes_btntext,
                        btnClass: 'tb-btn',
                        action: function () {
							var jc	= this; 
							jc.showLoading();
 							jQuery.ajax({
								type: "POST",
								url: scripts_vars.ajaxurl,
								dataType:"json",
								data: {
									'action': 'tu_remove_log',
									'security': scripts_vars.ajax_nonce,
									'post_id':  post_id,
									'user_id':  user_id,
									'profile_id': profile_id
								},
								success: function(response) {
									jQuery('body').find('.tuturn-preloader-section').remove();
									if (response.type === 'success') {
										jQuery('.tuturn-popup').modal('show'); 
										stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
										setTimeout(function () {
											window.location.reload();
										}, 3000);
									} else {
										stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
									}
								}
							});                    
							return false;
							}
                    },
					no: {
						text: scripts_vars.btntext_cancelled,
						btnClass: 'tb-btnvthree',
					}
                }
            }); 
			
		});

		jQuery(document).on('click', '.tu-update-hours', function (e) {
			e.preventDefault();
			let _this = jQuery(this);
			let _serialize 	= jQuery('#tu-hourly-form').serialize();
			let profile_id 	= jQuery('#profile_id').val();
			let input_files = jQuery('#tu-hourly-form .hour_file_name');
			let files = [];
 			input_files.each(function (i, field) {
				files.push( jQuery(field).data("file_url"));
 			 
			});	
		 
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_update_hours',
					'security': scripts_vars.ajax_nonce,
					'data': _serialize,
					'profile_id': profile_id,
					'attachments': files,

				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('show'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});
		jQuery(':checkbox[name="teaching_preference[]"]').change(function() {
			let teaching_preference = jQuery(this).filter(':checked').val();
            let selected_values = [];
            jQuery('.tu-teaching_type-se:checked').each(function() {
                selected_values.push(jQuery(this).val());
            });
			console.log(selected_values);
            //let offline_palce 		= jQuery(':radio[name="offline_type"]').filter(':checked').val();
            let offline_palce = [];
            jQuery('.tu-offline_type-se:checked').each(function() {
                offline_palce.push(jQuery(this).val());
            });
			if($.inArray('offline', selected_values) != -1 ){
				jQuery('.tu-custom-offline').removeClass('d-none');
                if($.inArray('tutor', offline_palce) != -1 ){
					jQuery('.tu-location-op').removeClass('d-none');
				} else {
					jQuery('.tu-location-op').addClass('d-none');
				}
			} else {
				jQuery('.tu-custom-offline').addClass('d-none');
                jQuery('.tu-location-op').addClass('d-none');
			}
		});
		
		jQuery(':checkbox[name="offline_place[]"]').change(function() {
            let offline_palce = [];
            jQuery('.tu-offline_type-se:checked').each(function() {
                offline_palce.push(jQuery(this).val());
            });
			console.log(offline_palce);
			//let offline_palce = $(this).filter(':checked').val();
            if(jQuery.inArray('tutor', offline_palce) != -1 ){
			//if(offline_palce == 'tutor' ){
				jQuery('.tu-location-op').removeClass('d-none');
			} else {
				jQuery('.tu-location-op').addClass('d-none');
			}
		});
		// jQuery(':radio[name="teaching_preference[]"]').change(function() {
		// 	let teaching_preference = $(this).filter(':checked').val();
		// 	let offline_palce 		= jQuery(':radio[name="offline_place"]').filter(':checked').val();
		// 	if(teaching_preference == 'offline' ){
		// 		jQuery('.tu-custom-offline').removeClass('d-none');
		// 		if(offline_palce == 'tutor' ){
		// 			jQuery('.tu-location-op').removeClass('d-none');
		// 		} else {
		// 			jQuery('.tu-location-op').addClass('d-none');
		// 		}
		// 	} else {
		// 		jQuery('.tu-custom-offline').addClass('d-none');
		// 		jQuery('.tu-location-op').addClass('d-none');
		// 	}
		// });

		// jQuery(':radio[name="offline_place"]').change(function() {
		// 	let offline_palce = $(this).filter(':checked').val();
		// 	if(offline_palce == 'tutor' ){
		// 		jQuery('.tu-location-op').removeClass('d-none');
		// 	} else {
		// 		jQuery('.tu-location-op').addClass('d-none');
		// 	}
		// });
		jQuery(document).on('click', '.tu-reminder_btn', function (e) {
			e.preventDefault();
			let _this 		= jQuery(this);
			let _post_id	= _this.data('id');
			jQuery('#tu-postreminder-id').val(_post_id);
			jQuery('#tu_hour_reminder').trigger("reset");
			jQuery('#tu-send-reminder').modal('show');

		});

		jQuery(document).on('click', '.btn-hours-decline', function (e) {
			e.preventDefault();
			let _this 		= jQuery(this);
			let _post_id	= _this.data('post_id');
			jQuery('#tu-declinepost-id').val(_post_id);
			jQuery('#tu_hour_decline').trigger("reset");
			jQuery('#tu-send-decline').modal('show');

		});

		jQuery(document).on('click', '.tu-postdecline-btn', function (e) {
			e.preventDefault();
			let _serialize 	= jQuery('#tu_hour_decline').serialize();
			let profile_id 	= jQuery('#profile_id').val();
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_decline_hours',
					'security': scripts_vars.ajax_nonce,
					'profile_id': profile_id,
					'data': _serialize,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('#tu-send-reminder').modal('hide');
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		jQuery(document).on('click', '.tu-postreminder-btn', function (e) {
			e.preventDefault();
			let _serialize 	= jQuery('#tu_hour_reminder').serialize();
			let profile_id 	= jQuery('#profile_id').val();
			jQuery('body').append(loader_html);

			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_send_reminder',
					'security': scripts_vars.ajax_nonce,
					'profile_id': profile_id,
					'data': _serialize,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					
					if (response.type === 'success') {
						jQuery('#tu-send-reminder').modal('hide');
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}  
 			});
		});

		jQuery(document).on('click', '.tb-addhourly-form', function (e) {
			let hourly_form_data 	= wp.template('updatehours-temp');
			let data				= {
				date : '',
				email : "",
				documents : "",
				user_html : "",
				user_class : ""
			};
			let hours_data			= {
				content : '',
				title	: '',
				data	: data
			};		
			hourly_form_data 		= hourly_form_data(hours_data);
			jQuery('#tu-add-hour-log').find('#temp-hours-body').html(hourly_form_data);		
			initDatePicker('tu-datepicker', 'YYYY-MM-DD');
			jQuery('#tu-start-time-filter').select2();
			jQuery('#tu-end-time-filter').select2();
			jQuery('#tu-add-hour-log').find('#hour_post_id').val("");
			let plupUploadParamshr = {
				btnID : 'tu-hr-verification-droparea' , 
				containerID :'tu-hr-upload-verification', 
				dropareaID : 'tuturn-hr-attachment-btn', 
				type : 'file_name',
				previewID  : 'tu-hr-tuturn-fileprocessing', 
				templateID : 'usefull-load-hr', 
				multiSelection : true, 
				filetype :'', 
				maxFileCount : 15, 
				fileSize : '1024mb', 
				fileSizes : [],
				dragDrop : true,
				uploadedFileFun : 'appendAttachmentshr'
			}
			plupUpload(plupUploadParamshr);	
			jQuery('#tu-add-hour-log').modal('show'); 
		});

		// edit hours
		jQuery(document).on('click', '.tu_edit_hours', function (e) {
			e.preventDefault();
			var _this 	= jQuery(this);
			var id 		= _this.data('post_id');
			if (typeof hourly_data[id] !== 'undefined' && hourly_data[id] !== '') {
				let hours_data			= hourly_data[id];
				let hourly_form_data 	= wp.template('updatehours-temp');		
				hourly_form_data 		= hourly_form_data(hours_data);
				jQuery('#tu-add-hour-log').find('#temp-hours-body').html(hourly_form_data);		
				jQuery('#tu-add-hour-log').find('#hour_post_id').val(id);
				jQuery('#tu-add-hour-log').find('.tu-user-info').removeClass('d-none');
				jQuery('#tu-add-hour-log').find('.tu-user-info').html(hours_data.user_html);
				initDatePicker('tu-datepicker', 'YYYY-MM-DD');
				jQuery('#tu-start-time-filter option[value='+hours_data.data.start_time+']').prop('selected', 'selected').change();
				jQuery('#tu-end-time-filter option[value='+hours_data.data.end_time+']').prop('selected', 'selected').change();
				jQuery('#tu-start-time-filter').select2();
				jQuery('#tu-end-time-filter').select2();
				let plupUploadParamshr = {
					btnID : 'tu-hr-verification-droparea' , 
					containerID :'tu-hr-upload-verification', 
					dropareaID : 'tuturn-hr-attachment-btn', 
					type : 'file_name',
					previewID  : 'tu-hr-tuturn-fileprocessing',
					templateID : 'usefull-load-hr', 
					multiSelection : true, 
					filetype :'', 
					maxFileCount : 15, 
					fileSize : '1024mb', 
					fileSizes : [],
					dragDrop : true,
					uploadedFileFun : 'appendAttachmentshr'
				}	
				plupUpload(plupUploadParamshr);
				jQuery('#tu-add-hour-log').modal('show'); 
			}
		});
		/**
		 * Download zip file
		 * Settings
		 */
		 jQuery(document).on('click', '.tu_download_zip_file', function (e) {
			e.preventDefault();
			let _this = jQuery(this);
			let post_id	= _this.data('post_id');
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_download_zip_file',
					'security': scripts_vars.ajax_nonce,
					'post_id': post_id,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						window.location = response.attachment;
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		/**
		 * Payout Methods
		 * Settings
		 */
		
		jQuery(document).on('click', '.tu-payrols-settings', function (e) {
			e.preventDefault();
			let _this = jQuery(this);
			let profileId	= _this.data('profile_id');
			let user_id	= _this.data('user_id');
			let _serialize 	= jQuery('.tu-payout-user-form').serialize();
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_payout_settings',
					'security': scripts_vars.ajax_nonce,
					'data': _serialize,
					'profileId': profileId,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('show'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		// Load withdraw money popup
		jQuery(document).on('click','.tu-withdraw-payment-modal', function(e){
			let payout_form = wp.template('withdraw-saved-payment');		
			payout_form = payout_form();
			jQuery('.tuturn-popup').find('#tuturn-model-body').html(payout_form);		
			jQuery('.tuturn-popup').modal('show'); 
			e.preventDefault();
		});
		
		//Payout Request for withdraw
		jQuery(document).on('click', '.tu-withdraw-money', function (e) {
			e.preventDefault();
			var consent_selected = jQuery('input[name="withdraw_consent"]:checked').length > 0;
			if (!consent_selected){
				return false;
			}		
			var _this		= jQuery(this);
			let profileId	= _this.data('profile_id');
			let _serialize 	= jQuery('.tu-withdrawform').serialize()+'&profileId=' + profileId;
			jQuery('body').append(loader_html);

			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_withdraw_money',
					'security': scripts_vars.ajax_nonce,
					'data': _serialize,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('hide'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});	
		
		// Booking details
		jQuery(document).on('click', '.tu-bookingdetails', function(e){
			e.preventDefault();
			let _this		= jQuery(this);
			let booking_id	= _this.data('booking_id');
			let user_id	= _this.data('user_id');
 			jQuery('body').append(loader_html);

			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_booking_details',
					'security': scripts_vars.ajax_nonce,
					'booking_id': booking_id,
					'user_id': user_id,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('#tuturn-modal-popup #tuturn-model-body').html(response.booking_details);
						jQuery('.mCustomScrollbar').mCustomScrollbar();
						jQuery('#tuturn-modal-popup').modal('show');
					
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		//Delete account
		jQuery(document).on('click','.delete-my-account', function(e){
			e.preventDefault();
			let _this		= jQuery(this);
			let _serialize 	= jQuery('#tu-delete-account').serialize();
 			jQuery('body').append(loader_html);

			var dataString 		= 'security='+scripts_vars.ajax_nonce+'&'+_serialize +'&action=tuturn_delete_account';

			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
						window.location = response.redirect;
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		// Cancel/Decline booking
		jQuery(document).on('click','.tu-decline-submit', function(e){
			e.preventDefault();
			let _this		= jQuery(this);
			let _serialize 	= jQuery('#tu-booking-decline-form').serialize();
			let profile_id 	= jQuery('#profile_id').val();
 			jQuery('body').append(loader_html);

			var dataString 		= 'security='+scripts_vars.ajax_nonce+'&profile_id='+profile_id+'&'+_serialize +'&action=tu_decline_appointment';

			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('hide'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
						setTimeout(function () {
							window.location.reload();
						}, 3000);
 						
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
						if (response.refund_error == 1) {
							setTimeout(function () {
								window.location.reload();
							}, 3000);
						}
						
					}
				}
			});
		});
		//Booking detail
		jQuery(".tu_show_it").on("click", function ($) {
			var wrapper = jQuery(this).closest(".tu-bookingwrapper").find(".tu-deniedarea");		
			var h = wrapper.height();	
			wrapper.css("height", "0%");
			wrapper.animate(
				{ h: "100%" },
				1000,
				function () { }
			);
			jQuery(this).parent(".tu-showdetails").css("display", "none");
			jQuery(this).parents(".tu-decnoti").addClass("tu-decnoti_show");
		});
		
		// Booking request modal
		jQuery(document).on('click change','.tu-booking-modal', function(e){
			let order_id	= jQuery(this).data('order_id');
			let action_type	= jQuery(this).data('action_type');
			if(action_type){
				let booking_form = wp.template('booking-'+action_type);		
				booking_form = booking_form();
				jQuery('.tuturn-popup').find('#tuturn-model-body').html(booking_form);		
				jQuery('.tuturn-popup').find('#booking_order_id').val(order_id);		
				jQuery('.tuturn-popup').find('#booking_action_type').val(action_type);	
				updatePlaceholder();
				jQuery('.tuturn-popup').modal('show'); 
				e.preventDefault();
			}
		});

		// meeting detail modal
		jQuery(document).on('click change','.tu-meeting-modal', function(e){
			let order_id		= jQuery(this).data('order_id');
			let action_type		= jQuery(this).data('action_type');
			let meeting			= jQuery(this).data('meeting');
			var data = {
				degree_title: '',
				meeting_url: '',
				meeting_desc: '',
			};
			if(action_type){
				
				var data = {
					//degree_title: meeting.degree_title,
					meeting_url: meeting.meeting_url,
					meeting_desc: meeting.meeting_desc,
					meeting_type: meeting.meeting_type,
				};

				let booking_form = wp.template('booking-'+action_type);		
				booking_form = booking_form(data);
				jQuery('.tuturn-popup').find('#tuturn-model-body').html(booking_form);		
				jQuery('.tuturn-popup').find('#booking_order_id').val(order_id);		
				jQuery('.tuturn-popup').find('#booking_action_type').val(action_type);	
				updatePlaceholder();
				jQuery('.tuturn-popup').modal('show'); 
				e.preventDefault();
			}
		});

	 // Approve/Complete booking
		jQuery(document).on('click','.tu-approve-submit', function(e){
			e.preventDefault();
			let _this		= jQuery(this);
			let post_id		= jQuery("#booking_order_id").val();
			let action_type	= jQuery("#booking_action_type").val();
			let profile_id 	= jQuery('#profile_id').val();
			let booking_rating	= 0;

			if(jQuery(".tuturn-popup #tu_booking_rating").length>0){
				booking_rating	= jQuery(".tuturn-popup #tu_booking_rating").val();
			}

			jQuery('body').append(loader_html);

			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_booking_appointment',
					'security': scripts_vars.ajax_nonce,
					'profile_id': profile_id,
					'postId': post_id,
					'action_type': action_type,
					'rating': booking_rating,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('hide'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		// Booking meeting detail
		jQuery(document).on('click','.tu-save-meeting-detail', function(e){
			e.preventDefault();
			let _this		= jQuery(this);
			let profile_id 	= jQuery('#profile_id').val();
			let post_id		= jQuery("#booking_order_id").val();
			let action_type	= jQuery("#booking_action_type").val();
			let _serialize 	= jQuery('#tu-meetingform-inst-form').serialize() +"&postId="+post_id +"&action_type="+action_type ;
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_meeting_detail',
					'security': scripts_vars.ajax_nonce,
					'data': _serialize,
					'profile_id': profile_id
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('hide'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		//Select2 remove item
		jQuery(document).on('click', '.select2-remove-item', function (e) {
			e.preventDefault();
			let _this 	= jQuery(this);
			let lang 	= _this.data('slug');
			_this.parents('#'+lang).remove();
		});		

		// profile avatar upload
		function tuturn_upload_profile_image( parems ) {
			let {
					btnID, 
					containerID, 
					dropareaID,
					type, 
					previewID, 
					templateID, 
					_type,
					defaultTemplateID, 
					filetype, 
					isCropped,
					onlyForImages,
					minWidth,
					maxWidth,
					maxHeight,
					minHeight,
				} = parems; // extends variables

			if ( typeof plupload === 'object' ) {
				var sys_upload_nonce = scripts_vars.sys_upload_nonce;
				var ProjectUploaderArguments = {
						browse_button: btnID, // this can be an id of a DOM element or the DOM element itself
						file_data_name: type,
						container: containerID,
						drop_element: dropareaID,
						multipart_params: {
							"type": type,
						},
						multi_selection: _type,
						url: scripts_vars.ajaxurl + "?action=tuturn_temp_file_uploader&ajax_nonce=" + scripts_vars.ajax_nonce,
						filters: {
							mime_types: [
								{ title: 'file', extensions: filetype }
							],
							max_file_size: scripts_vars.upload_size,
							max_file_count: 1,
							prevent_duplicates: false,
							min_width: minWidth,
							max_width: maxWidth,
							max_height: maxHeight,
							min_height: minHeight,
						}
					};
				var ProjectUploader = new plupload.Uploader(ProjectUploaderArguments);
				ProjectUploader.init();

				/* Image width and height validation */
				if(onlyForImages){
					plupload.addFileFilter('min_width', function(minwidth, file, cb) {
						var self = this, img = new o.Image();				
						function finalize(result) {
							// cleanup
							img.destroy();
							img = null;
						// if rule has been violated in one way or another, trigger an error
							if (!result) {
								stickyAlert(scripts_vars.error_title, scripts_vars.prof_min_image_width_msg, {classList: 'danger', autoclose: 3000});
						}
							cb(result);
						}
						img.onload = function() {
							// check if resolution cap is not exceeded
							finalize(img.width >= minwidth);
						};
						img.onerror = function() {
							finalize(false);
						};
						img.load(file.getSource());
					});

					plupload.addFileFilter('min_height', function(minheight, file, cb) {
						var self = this, img = new o.Image();
						function finalize(result) {
							// cleanup
							img.destroy();
							img = null;
					
						// if rule has been violated in one way or another, trigger an error
							if (!result) {
								stickyAlert(scripts_vars.error_title, scripts_vars.prof_min_image_height_msg, {classList: 'danger', autoclose: 3000});
						}
							cb(result);
						}
						img.onload = function() {
							// check if resolution cap is not exceeded
							finalize(img.height >= minheight);
						};
						img.onerror = function() {
							finalize(false);
						};
						img.load(file.getSource());
					});

					plupload.addFileFilter('max_width', function(maxwidth, file, cb) {
						var self = this, img = new o.Image();
					
						function finalize(result) {
							// cleanup
							img.destroy();
							img = null;
					
						// if rule has been violated in one way or another, trigger an error
							if (!result) {
								stickyAlert(scripts_vars.error_title, scripts_vars.prof_mx_image_width_msg, {classList: 'danger', autoclose: 3000});
						}
							cb(result);
						}
						img.onload = function() {
							// check if resolution cap is not exceeded
							finalize(img.width <= maxwidth);
						};
						img.onerror = function() {
							finalize(false);
						};
						img.load(file.getSource());
					});
	
					plupload.addFileFilter('max_height', function(maxheight, file, cb) {
						var self = this, img = new o.Image();
					
						function finalize(result) {
							// cleanup
							img.destroy();
							img = null;
					
						// if rule has been violated in one way or another, trigger an error
							if (!result) {
								stickyAlert(scripts_vars.error_title, scripts_vars.prof_mx_image_height_msg, {classList: 'danger', autoclose: 3000});
						}
							cb(result);
						}
						img.onload = function() {
							// check if resolution cap is not exceeded
							finalize(img.height <= maxheight);
						};
						img.onerror = function() {
							finalize(false);
						};
						img.load(file.getSource());
					});
				}
				/* End image width and height validation */

				//bind
				ProjectUploader.bind('FilesAdded', function (up, files) {
					var _Thumb = "";
					plupload.each(files, function (file) {
						var load_thumb = wp.template(defaultTemplateID);
						var _size = bytesToSize(file.size);
						var data = { id: file.id, size: _size, name: file.name, percentage: file.percent };
						load_thumb = load_thumb(data);
						_Thumb += load_thumb;
					});

					if ( _type == false ) {
						jQuery('#' + previewID).html(_Thumb);
					} else {
						jQuery('#' + previewID).append(_Thumb);
					}

					jQuery('#' + previewID).removeClass('tuturn-empty-uploader');
					jQuery('#' + previewID).addClass('tuturn-infouploading');
					jQuery('#' + previewID + " .progress").addClass('tuturn-infouploading');
					
					up.refresh();
					ProjectUploader.start();
				});
				//bind
				ProjectUploader.bind('UploadProgress', function (up, file) {
					jQuery('body').append(loader_html);
					var _html = ' <span class="progress-bar uploadprogressbar" style="height:6px; width:' + file.percent + '%"></span>';
					jQuery('#thumb-' + file.id + ' .progress .uploadprogressbar').replaceWith(_html);
				});

				//Error
				ProjectUploader.bind('Error', function (up, err) {
					var errorMessage = err.message
					if (err.code == '-600') {
						errorMessage = scripts_vars.file_size_error
					}
					let extra_params = {};
					extra_params['note_desc'] = errorMessage;

				});
				//display data
				ProjectUploader.bind('FileUploaded', function (up, file, ajax_response) {
					jQuery('.tuturn-preloader-section').remove();
					try {
						var response = jQuery.parseJSON(ajax_response.response);
						if ( response.type === 'success' ) {
							var load_thumb = wp.template(templateID);
							var _size = bytesToSize(file.size);
							var data = { id: file.id, size: _size, name: file.name, percentage: file.percent, url: response.thumbnail };

							if( isCropped ) {
								cropImagePopup(data);
							} else {
								var load_thumb = load_thumb(data);
								jQuery("#thumb-" + file.id).html(load_thumb);

								if (btnID == 'tb-profile-attachment-btn') {
									jQuery('.tb-remove-profile-img').css('display', '');
								}
							}

							jQuery("#thumb-" + file.id + " .progress").removeClass('tuturn-infouploading');

						} else {
							StickyAlert('', response.message, {classList: 'danger', autoclose: 5000});
						}
					} catch (err) {
						stickyAlert('', scripts_vars.invalid_image, {classList: 'danger', autoclose: 3000});
					}
					
				});
			}
		} // Image upload fun end	


		// init profile avatar object parems
		let parems = {
			btnID       : 'profile-avatar',
			containerID : 'tu-asideprostatusv2',
			dropareaID  : 'tuturn-droparea',
			type        : 'file_name',
			previewID   : 'tu-profile-upload-attachment-preview',
			templateID  : 'load-profile-avatar',
			_type       : 'true',
			filetype    : scripts_vars.default_image_extensions ? scripts_vars.default_image_extensions :'jpg,jpeg,gif,png',
			isCropped   : true,
			defaultTemplateID : 'load-default-image',
			onlyForImages 	: true,
			minWidth		: scripts_vars.prof_min_image_width,
			minHeight		: scripts_vars.prof_min_image_height,
			maxWidth		: scripts_vars.prof_mx_image_width,
			maxHeight		: scripts_vars.prof_mx_image_height,
		}

		// init profile avatar object
		tuturn_upload_profile_image( parems );

		// init profile avatar object parems
		parems = {
			btnID       : 'profile-avatar-icon',
			containerID : 'tu-asideprostatusv2',
			dropareaID  : 'tuturn-droparea',
			type        : 'file_name',
			previewID   : 'tu-profile-upload-attachment-preview',
			templateID  : 'load-profile-avatar',
			_type       : 'true',
			filetype    : scripts_vars.default_image_extensions ? scripts_vars.default_image_extensions :'jpg,jpeg,gif,png',
			isCropped   : true,
			defaultTemplateID : 'load-default-image',
			onlyForImages 	: true,
			minWidth		: scripts_vars.prof_min_image_width,
			minHeight		: scripts_vars.prof_min_image_height,
			maxWidth		: scripts_vars.prof_mx_image_width,
			maxHeight		: scripts_vars.prof_mx_image_height,
		}

		// init profile avatar object
		tuturn_upload_profile_image( parems );

		// cropped image popup modal
		function cropImagePopup(data){
			let load_profile_avatar = wp.template('load-profile-avatar');
			jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_profile_avatar);
			jQuery('#tuturn-modal-popup').modal('show');

			setTimeout(function() {
				image_crop = jQuery('#crop_img_area').croppie({
					enableExif: true,
					viewport: {
						width: 200,
						height: 200,
						type: 'square'
					},
					boundary: {
						width: 300,
						height: 300
					},
					url: data.url,
				});
			}, 500);

		}

		//  Cropped Image
		jQuery(document).on('click', '#save-profile-img', function (e) {
			jQuery('body').append(loader_html);
			image_crop.croppie('result', {type: 'base64',quality: 1, format: 'png',size: "original"}).then(function(base64) {
				
				jQuery.ajax({
					type: "POST",
					url: scripts_vars.ajaxurl,
					data: {
						action: 'tuturn_update_avatar',
						image_url : base64
					},
					dataType: "json",
					success: function (response) {
						jQuery('.tuturn-preloader-section').remove();
						if (response.type === 'success') {
							jQuery('#profile-avatar-menue-icon img').attr('src', response.avatar_50_x_50);
							jQuery('#user_profile_avatar').attr('src', response.avatar_150_x_150);
							jQuery('#tuturn-modal-popup').modal('hide');
						} else {
							StickyAlert(response.message, response.message_desc, {classList: 'danger', autoclose: 5000});
						}
					}
				}, true);
			});
		});

		// Save profile settings
		function tu_tmce_getContent(editor_id, textarea_id) {
			if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
			if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
			
			if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
			  return tinyMCE.get(editor_id).getContent();
			}else{
			  return jQuery('#'+textarea_id).val();
			}
		}

		// Save profile settings
		jQuery(document).on('click', '.tu-save-settings', function (e) {
			e.preventDefault();
			var _this = jQuery(this);
			_this.attr("disabled", false);
			jQuery('body').append(loader_html);
			let brief_introduction	= tu_tmce_getContent('profile_introduction', 'brief_introduction');
 			executeAjaxRequest({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_save_profile_settings',
					'security': scripts_vars.ajax_nonce,
					'data': '&' + jQuery('#tu-profile-settings').serialize()+'&profile_introduction=' + brief_introduction,
					'introduction' : brief_introduction,
				},
				dataType: "json",
				success: function (response) {
					_this.attr("disabled", false);
					jQuery('body').find('.tuturn-preloader-section').remove();	
 					if (response.type == 'success') {
						stickyAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
						window.location.reload();
					} else {
						stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
					}
				}
			});
		});
	 
		

		//Add/edit education from modal
		jQuery(document).on('click', '.tu_edit_education', function (e) {
			let education_key = Date.now()+Math.floor(Math.random() * 100);
			var data = {
				id: education_key,
				degree_title: '',
				institute_title: '',
				institute_location: '',
				education_start_date: '',
				education_end_date:'',
				currently_studying: '',
				education_description: '',
			};			
			var load_education = wp.template('load-education-popup');
			let operation	= jQuery(this).data("operation");

			if(operation == 'edit'){
				let education_key = jQuery(this).data('education_key');
				let education = jQuery(this).data('education');

				var data = {
					id: education_key,
					degree_title: education.degree_title,
					institute_title: education.institute_title,
					institute_location: education.institute_location,
					education_start_date: education.education_start_date,
					education_end_date: education.education_end_date,
					currently_studying: education.currently_studying,
					education_description: education.education_description,
					education_degree_desc: education.education_degree_desc,
				};
			}
			
			load_education = load_education(data);
			jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_education);
			jQuery('#tuturn-modal-popup').modal('show');
			initDatePicker('tu-datepicker', 'YYYY-MM-DD');
			e.preventDefault();
		});

		// show/hide end_date field based on currently studying field
		jQuery(document).on('click','.tu_currently_studying', function(){		
			let _this = jQuery(this);
			let is_currently_studying = _this.is(":checked");

			if(is_currently_studying){
				jQuery("#tu_education_end_date").val("");
				jQuery("#tu_education_end_date").addClass("d-none");
				jQuery("#tu_education_end_date").next().removeClass("d-none");
				jQuery("#tu_education_end_date").next().addClass("d-block");
			}else{
				jQuery("#tu_education_end_date").removeClass("d-none");
			}
		}); 

		//submit education from modal
		jQuery(document).on('click', '#tu-submit-education', function (e) {
			jQuery('body').append(loader_html);
			let uniqueid  	= jQuery('#education_key').val();
			let education_degree_title  		= jQuery('#education_degree_title').val();
			let education_institute_title  		= jQuery('#education_institute_title').val();
			let	education_institute_location 	= jQuery('#education_institute_location').val();
			let	tu_education_start_date 		= jQuery('#tu_education_start_date').val();
			let	tu_education_end_date 			= jQuery('#tu_education_end_date').val();
			let	tu_currently_studying 			= jQuery('#tu_currently_studying:checked').val();
			let	tu_education_degree_desc 		= jQuery('#education_degree_desc').val();
			let	tu_edu_deg_dis_able 			= jQuery('#edu_deg_dis_able').val();

			if(tu_currently_studying == null){
				tu_currently_studying	= 'off';
			}
			let	tu_education_description 	= jQuery('#tu-education-description').val();
			
			if( education_degree_title	==  '' || education_institute_title == '' || tu_education_description == '' || education_institute_location == ''){
				jQuery('.tuturn-preloader-section').remove();
				stickyAlert('', scripts_vars.required_fields, {classList: 'danger', autoclose: 3000});
				return;
			}
			if(tu_edu_deg_dis_able	== ''){
				if(   tu_education_start_date == '') {
					jQuery('.tuturn-preloader-section').remove();
					stickyAlert('', scripts_vars.required_fields, {classList: 'danger', autoclose: 3000});
					return;
				}				 
			}

			var load_education = wp.template('load-education');
			var data = {
				id: uniqueid,
				degree_title: education_degree_title,
				institute_title: education_institute_title,
				institute_location: education_institute_location,
				education_start_date: tu_education_start_date,
				education_end_date: tu_education_end_date,
				currently_studying: tu_currently_studying,
				education_description: tu_education_description,
				education_degree_desc: tu_education_degree_desc,
				edu_deg_dis_able: tu_edu_deg_dis_able,
			};

			load_education = load_education(data);
			
			if(jQuery('.tu-accordion-item.tu-education-item-'+uniqueid).length>0){				
				jQuery('.tu-accordion-item.tu-education-item-'+uniqueid).html(load_education);
			} else {
				load_education	= '<div class="tu-accordion-item tu-education-item-'+uniqueid+'">'+load_education+'</div>';  
				jQuery('#tu-edusortable').append(load_education);
			}

			jQuery('.tuturn-preloader-section').remove();
			jQuery('#tuturn-modal-popup').modal('hide');
			e.preventDefault();
		});

		//Education description text characters count
		jQuery(document).on('keyup', '#tu-education-description, #subcategory_desc', function (e) {
 			tuMaxLengthCounter(e.target.id);
		});

		//education delete
		jQuery(document).on('click', '.tu-education-delete', function (e) {
			let education_key = jQuery(this).data('education_key');
			jQuery.confirm({
                icon: 'icon icon-trash tu-deleteclr',
                title: scripts_vars.remove_education,
                content: scripts_vars.remove_education_message,
                closeIcon: true,
                boxWidth: '600px',
                theme: 'modern',
                draggable: false,
                useBootstrap: false,
                typeAnimated: true,
                buttons: {
                    yes: {
                        text: scripts_vars.remove_education,
                        btnClass: 'tb-btn',
                        action: function () {
							jQuery('body').append(loader_html);
							jQuery('.tu-education-item-'+education_key).remove();
							jQuery('.tuturn-preloader-section').remove();
							e.preventDefault();
						}
                    },
                    no: {
                        text: scripts_vars.btntext_cancelled,
                        btnClass: 'tb-btnvthree',
                    }
                }
            });
		});

		//add subjects from modal
		jQuery(document).on('click', '.tu-add-subjects', function (e) {
			let subject_key = Date.now()+Math.floor(Math.random() * 100);
			let operation	= jQuery(this).data("operation");
			let selected_category	= '';
			let selected_subcategories	= '';
			if(operation == 'edit'){
				subject_key = jQuery(this).data('subject_key');
				let subject = jQuery(this).data('subject');
				selected_category	= subject.parent_category.slug;
				selected_subcategories	= subject.subcategories;
			}

			jQuery('body').append(loader_html);
			var data = {
				id			: subject_key,
				operation	: operation,
			};	
			executeAjaxRequest({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tuturn_load_categories',
					'security': scripts_vars.ajax_nonce,
					'category': selected_category,
					'subcategories': selected_subcategories,
					'operation': operation,
				},
				dataType: "json",
				success: function (response) {
					jQuery('.tuturn-preloader-section').remove();	
					if (response.type === 'success') {										
						categories 				= response.categories;
						sub_categories 			= response.sub_categories;
						selected_sub_categories	= response.selected_categories;
						var load_subject = wp.template('load-skills-popup');
						load_subject = load_subject(data);
						jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_subject);			
	
						if(categories){
							jQuery('#tu-profile-categories').html(categories);
							jQuery("#tu-profile-categories-drop-down").select2({allowClear: true, minimumResultsForSearch: Infinity});
						}
	
						if(sub_categories){
							jQuery('#tu-profile-sub-categories').addClass('tu-select');							
							jQuery('#tu-profile-sub-categories').html(sub_categories);
							jQuery('#tu-profile-sub-categories-drop-down').select2({allowClear: true, minimumResultsForSearch: Infinity});
						}
	
						if(selected_sub_categories){
							jQuery('#tu-profile-selected-categories').html(selected_sub_categories);
						}
	
						if(selected_category){
						   jQuery('#tu-profile-categories-drop-down').attr('disabled', 'disabled');
						   jQuery('#selcted_cat_id').val(selected_category);
						}
						jQuery('#tuturn-modal-popup').modal('show');
					} else {
						stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
					}
				}
			});
			e.preventDefault();
		});

		//subject delete
		jQuery(document).on('click', '.tu-subject-delete', function (e) {
			let subject_key = jQuery(this).data('subject_key');
			jQuery.confirm({
                icon: 'icon icon-trash tu-deleteclr',
                title: scripts_vars.remove_subject,
                content: scripts_vars.remove_subject_message,
                closeIcon: true,
                boxWidth: '600px',
                theme: 'modern',
                draggable: false,
                useBootstrap: false,
                typeAnimated: true,
                buttons: {
                    yes: {
                        text: scripts_vars.remove_subject,
                        btnClass: 'tb-btn',
                        action: function () {
							jQuery('body').append(loader_html);
							jQuery('.tu-subject-item-'+subject_key).remove();
							jQuery('.tuturn-preloader-section').remove();
							e.preventDefault();
						}
                    },
                    no: {
                        text: scripts_vars.btntext_cancelled,
                        btnClass: 'tb-btnvthree',
                    }
                }
            });
		});

		// Load instructor service sub-categories
		jQuery(document).on('change','#tu-profile-categories-drop-down', function(){
			let _this = jQuery(this);
			let sub_categories = '';
			var category_id = _this.find(":selected").val();
			jQuery('body').append(loader_html);	
			executeAjaxRequest({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tuturn_load_subcategories_dropdown',
					'security': scripts_vars.ajax_nonce,
					'category_slug': category_id,
				},
				dataType: "json",
				success: function (response) {	
					jQuery('body').find('.tuturn-preloader-section').remove();			
					if (response.type === 'success') {
						sub_categories = response.subcategories;
						jQuery('#tu-profile-sub-categories').addClass('tu-select');
						jQuery('#tu-profile-sub-categories').siblings().removeClass('d-none');
						jQuery('#tu-profile-sub-categories').html(sub_categories);
						jQuery('#tu-profile-sub-categories-drop-down').select2({allowClear: true, minimumResultsForSearch: Infinity});
					} else {
						stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
					}
				}
			});
		});

		// on change service sub category call methods
		jQuery(document).on('change','#tu-profile-sub-categories-drop-down', function(){
			openSelect2('profile-sub-categories-drop-down', 'subject_sub_categories');
		});

		//=====================

		/**
		 * delete parent category from 
		 * sub-categories.
		 */
		 jQuery(document).on('click', '.tu_subcat_subject_delete', function (e) {
			e.preventDefault();
			let _this = jQuery(this);
			jQuery.confirm({
                icon: 'icon icon-trash tu-deleteclr',
                title: scripts_vars.remove_subject,
                content: scripts_vars.remove_subject_message,
                closeIcon: true,
                boxWidth: '600px',
                theme: 'modern',
                draggable: false,
                useBootstrap: false,
                typeAnimated: true,
                buttons: {
                    yes: {
                        text: scripts_vars.remove_subject,
                        btnClass: 'tb-btn',
                        action: function () {
						let subject_id = jQuery(_this).data('subject_key');
						jQuery('body').append(loader_html);
						executeAjaxRequest({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: {
								action		: 'tuturn_delete_subcategory_subject',
								security	: scripts_vars.ajax_nonce,
								subject_id 	: subject_id,
							},
							dataType: "json",
							success: function (response) {	
								jQuery('body').find('.tuturn-preloader-section').remove();			
								if (response.type === 'success') {
									jQuery('#tuturn-modal-popup').modal('hide');
									stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
									setTimeout(function () {
										window.location = response.redirect;
									}, 900);
								} else {
									stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
								}
							}
						});
					}
					},
					no: {
						text: scripts_vars.btntext_cancelled,
						btnClass: 'tb-btnvthree',
					}
				}
			});
		});

		/**
		 * add subjects category price from modal
		 *  */ 
		jQuery(document).on('click', '.tu-add-subcat-subjects', function (e) {
			e.preventDefault();
			let _this			= jQuery(this);
			let parent_subject_id		= _this.data("tu_parent_subject_id");
			let child_subject_id		= _this.data("tu_child_subject_id");
			let child_subject_obj		= _this.data("subcat_subject");
			let parent_subject_obj		= _this.data("parent_subject");
			if (Object.keys(child_subject_obj).length>0) {
				let data = {
					child_subject_id	: child_subject_id,
					parent_subject_id 	: parent_subject_id,
					parent_subject_obj 	: parent_subject_obj,
					child_subject_obj 	: child_subject_obj,
				}
				jQuery('body').append(loader_html);

				var load_subcat_detail_popup = wp.template('load-subcat-detail-popup');
				load_subcat_detail_popup = load_subcat_detail_popup(data);
				jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_subcat_detail_popup);

				jQuery('.tuturn-preloader-section').remove();
				jQuery('#tuturn-modal-popup').modal('show');
			}
			
		});

		/**
		 * Save subcategory subject form data 
		 */
		 jQuery(document).on('click', '#tu-submit-child-subject-form', function (e) {
			e.preventDefault();
			let _this					= jQuery(this);
			let profile_id				= jQuery('#profile_id').val();
			let parent_term_id  		= _this.data("tu_parent_term_id");
			let child_term_id  			= _this.data("tu_child_term_id");
			let price					= jQuery('#subcategory_price').val();
			let desc					= jQuery('#subcategory_desc').val();

			jQuery('body').append(loader_html);
			executeAjaxRequest({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					action		: 'tuturn_save_subcat_subject_data',
					security	: scripts_vars.ajax_nonce,
					parent_term_id : parent_term_id,
					child_term_id : child_term_id,
					price : price,
					desc : desc,
					profile_id: profile_id,	
				},
				dataType: "json",
				success: function (response) {	
					jQuery('body').find('.tuturn-preloader-section').remove();			
					if (response.type === 'success') {
						jQuery('#tuturn-modal-popup').modal('hide');
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location = response.redirect;
						}, 900);

					} else {
						stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
					}
				}
			});

		 });

		 /**
		 * Save subcategory subject form data 
		 * after delete single subcategory
		 */
		 jQuery(document).on('click', '.tu-sub-subject-delete', function (e) {
			e.preventDefault();
			let _this	= jQuery(this);
			jQuery.confirm({
                icon: 'icon icon-trash tu-deleteclr',
                title: scripts_vars.remove_subcat_subject,
                content: scripts_vars.remove_subcat_subject_message,
                closeIcon: true,
                boxWidth: '600px',
                theme: 'modern',
                draggable: false,
                useBootstrap: false,
                typeAnimated: true,
                buttons: {
                    yes: {
                        text: scripts_vars.remove_subcategory,
                        btnClass: 'tb-btn',
                        action: function () {
						let parent_term_id  		= _this.data("tu_parent_subject_id");
						let child_term_id  			= _this.data("tu_child_subject_id");
						let data = {
							parent_term_id 	: parent_term_id,
							child_term_id 	: child_term_id
						}
						jQuery('body').append(loader_html);

						executeAjaxRequest({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: {
								action		: 'tuturn_remove_subcat_subject_data',
								security	: scripts_vars.ajax_nonce,
								data		: data,
							},
							dataType: "json",
							success: function (response) {	
								jQuery('body').find('.tuturn-preloader-section').remove();			
								if (response.type === 'success') {
									jQuery('#tuturn-modal-popup').modal('hide');
									stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
									setTimeout(function () {
										window.location = response.redirect;
									}, 900);

								} else {
									stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
								}
							}
						});
					}
				},
				no: {
					text: scripts_vars.btntext_cancelled,
					btnClass: 'tb-btnvthree',
				}
			}
			});

		 });


		/**
		 * 
		 * Submit parent subject form from sub-categories template
		 * 
		 */
		 jQuery(document).on('click', '#tu-subcat-submit-subjects', function (e) {
			 let uniqueid  		= jQuery('#subject_key').val();
			 let category  		= jQuery('#tu-profile-categories-drop-down').val();
			 let operation		= jQuery(this).data("operation");
			 let subcategories 	= jQuery('input[name="subject_sub_categories[]"]').map(function () {
				 return this.value;
				}).get();
				
			jQuery('body').append(loader_html);
			executeAjaxRequest({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action'		: 'tuturn_update_categories_form',
					'security'		: scripts_vars.ajax_nonce,
					category		: category,
					subcategories	: subcategories,
					is_edit			: operation,
				},
				dataType: "json",
				success: function (response) {				
					jQuery('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('#tuturn-modal-popup').modal('hide');
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
					} else {
						stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
					}
					setTimeout(function () {
						window.location = response.redirect;
					}, 3000);
				}
			});
			e.preventDefault();
		 });

		//submit education from modal
		jQuery(document).on('click', '#tu-submit-subjects', function (e) {
			jQuery('body').append(loader_html);
			let uniqueid  	= jQuery('#subject_key').val();
			let category  	= jQuery('#tu-profile-categories-drop-down').val();
			var selected_cat = jQuery("#tu-profile-categories-drop-down option:selected");
			var category_text = selected_cat.text();
			let profile_id 	= jQuery('#profile_id').val();
			
			let subcategories = jQuery('input[name="subject_sub_categories[]"]').map(function () {
				return this.value;
			}).get();
			let subcategories_array = jQuery('input[name="subject_sub_categories_array[]"]').map(function () {
				return this.value;
			}).get();

			executeAjaxRequest({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tuturn_submit_categories_form',
					'security': scripts_vars.ajax_nonce,
					category: category,
					profile_id: profile_id,
					subcategories: subcategories,
				},
				dataType: "json",
				success: function (response) {				
					jQuery('.tuturn-preloader-section').remove();
					
					if (response.type === 'success') {
						var load_subjects = wp.template('load-skills-list');
						var data = {
							id: uniqueid,
							categories: response.categories,
						};
						load_subjects = load_subjects(data);
						if(jQuery('#tu-subsortable li.tu-subject-item-'+uniqueid).length>0){				
							jQuery('li.tu-subject-item-'+uniqueid).html(load_subjects);
						} else {
							load_subjects	= '<li class="tu-accordion-item tu-subject-item-'+uniqueid+'">'+load_subjects+'</li>';  
							jQuery('#tu-subsortable').append(load_subjects);
						}
						
						jQuery('#tuturn-modal-popup').modal('hide');
					} else {
						stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
					}
				}
			});
			e.preventDefault();
		});

		//Sortable 
		function sortableList(listId) {
			var el  = document.getElementById(listId);
			if( el ){
				Sortable.create(el, {
					group: "sorting",
					handle: '.tu-sort-handle',
					animation: 150,
					sort: true,
				  }); 
			}           
		}

		// Open edit media gallery popup
		jQuery(document).on('click', '#tu_edit_media_gallery', function (e) {
			e.preventDefault();
			jQuery('body').append(loader_html);
			let _this 							= jQuery(this);
			var load_media_gallery_popup 		= wp.template('media-gallery');
				load_media_gallery_popup 		= load_media_gallery_popup();
			let item_limit 						= scripts_vars.media_gallery_items_limit ? scripts_vars.media_gallery_items_limit : 15;
			let upload_max_file_size 			= scripts_vars.upload_max_file_size ? scripts_vars.upload_max_file_size : '5MB';

			jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_media_gallery_popup);
			
			let params = {
				btnID : 'tu_upload_images_btn' , 
				containerID :'tu_upload_images', 
				dropareaID : 'tu_gallery_droparea', 
				type : 'file_name',
				previewID  : 'tu_gallery_fileprocessing', 
				templateID : 'load-gallary-images', 
				multiSelection : true, 
				filetype :'jpg,jpeg,gif,png,mp4,mp3', 
				maxFileCount : item_limit, 
				fileSize : upload_max_file_size.toString(), 
				fileSizes : ['tu_user_profile'],
				dragDrop : true,
				uploadedFileFun : 'appendMediaGalleryAttachments',
				minWidth		: 500,
				minHeight		: 500,
				maxWidth		: scripts_vars.gallery_max_image_width,
				maxHeight		: scripts_vars.gallery_max_image_height,
			}
			plupUpload(params);
			sortableList('tu_gallery_fileprocessing');
			jQuery('.tuturn-preloader-section').remove();
			jQuery('#tuturn-modal-popup').modal('show');
		});

		let plupUploadParams = {
			btnID : 'tu-verification-droparea' , 
			containerID :'tu-upload-verification', 
			dropareaID : 'tuturn-attachment-btn', 
			type : 'file_name',
			previewID  : 'tu-tuturn-fileprocessing', 
			templateID : 'usefull-load-attachment', 
			multiSelection : true, 
			filetype :'', 
			maxFileCount : 15, 
			fileSize : '1024mb', 
			fileSizes : [],
			dragDrop : true,
			uploadedFileFun : 'appendAttachments'
		}
		let plupUploadParamspp = {
			btnID : 'tu-pp-verification-droparea' , 
			containerID :'tu-pp-upload-verification', 
			dropareaID : 'tuturn-pp-attachment-btn', 
			type : 'file_name',
			previewID  : 'tu-pp-tuturn-fileprocessing', 
			templateID : 'usefull-load-pp', 
			multiSelection : false, 
			filetype :'jpg,jpeg,gif,png', 
			maxFileCount : 15, 
			fileSize : '1024mb', 
			fileSizes : [],
			dragDrop : true,
			uploadedFileFun : 'appendAttachmentspp'
		}

		
		plupUpload(plupUploadParams);
		plupUpload(plupUploadParamspp);
		
		
		// remove uploaded media gallery
		jQuery(document).on('click', '.tu_delete_item', function (e) {
			e.preventDefault();
			let _this = jQuery(this);
			_this.parents('li').remove();
		});

		// remove uploaded attachments
		jQuery(document).on('click', '#tu_delete_attachment', function (e) {
			e.preventDefault();
			let _this = jQuery(this);
			_this.parents('li').remove();
			let record = jQuery('#tu-attachments-fileprocessing li');
 			if ( jQuery('#appent-attachment ul li').size() == 0 ) {
 				jQuery('#appent-attachment').addClass('d-none');
			}
			if(record.length == 0){
				jQuery('#tu-attachments-fileprocessing').addClass('d-none');
			}
		});

			// save verification form
		jQuery(document).on('click','.tu-identity-verification-btn',function(){
			var _this 		= jQuery(this);
			let profile_id 	= jQuery('#profile_id').val();
			let dataArray 	= jQuery('#tu-verification-required').serialize();
			let input_files = jQuery('#tu-verification-required .file_name');
			let files = [];
			input_files.each(function (i, field) {
				files.push( jQuery(field).data("file_url"));
 			 
			});		
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_update_identity',
					'security': scripts_vars.ajax_nonce,
					'data': dataArray,
					'profile_id': profile_id,
					'attachments': files,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('hide'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {

						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		jQuery(document).on('click','.tu-user-verification-btn',function(){
			var _this 		= jQuery(this);
			let profile_id 	= jQuery('#profile_id').val();
			let dataArray 	= jQuery('#tu-verification-required').serialize();
			let input_files = jQuery('#tu-verification-required .file_name');
			let profile_photo = jQuery('#tu-verification-required .profile_file_name');
 			var user_type	= _this.data('user_type');
			let files = [];
			let profile_photos = [];
			input_files.each(function (i, field) {
				files.push( jQuery(field).data("file_url"));
 			 
			});	
			profile_photo.each(function (i, field) {
				profile_photos.push( jQuery(field).data("file_url"));
 			 
			});		
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_user_identity',
					'security': scripts_vars.ajax_nonce,
					'data': dataArray,
					'attachments': files,
					'profile_photo': profile_photos,
					'profile_id': profile_id,
					'user_type': user_type,
				},
				dataType: "json",
				success: function (response) {
					
					 
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('hide'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location = response.redirect;
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		//cancel upload document
		jQuery(document).on('click','.tu-document-cancelled .tu-pb-lg',function(){
			var _this 		= jQuery(this);
			var user_id		= _this.data('user_id');
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'cancelledIdentity',
					'security': scripts_vars.ajax_nonce,
					'data': user_id,
 				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						jQuery('.tuturn-popup').modal('hide'); 
						stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
 						setTimeout(function () {
							window.location.reload();
						}, 3000);
					} else {
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		// Video URL
		jQuery(document).on('input', '#tu_video_url', function (e) {
			let _this = jQuery(this);
			let url = _this.val();
				url = url ? url.trim(): '';
			if( !url ){
				jQuery('#tu_add_video_url').attr('disabled', true);
			} else {
				jQuery('#tu_add_video_url').removeAttr('disabled');
			}
		});

		// remove uploaded media gallery
		jQuery(document).on('click', '#tu_add_video_url', function (e) {
			e.preventDefault();
			let _this = jQuery(this);
			let url = jQuery('#tu_video_url').val();
			let isValidate = validURL(url);
			let totalRec = jQuery('ul#tu_gallery_fileprocessing li');
			let item_limit = scripts_vars.media_gallery_items_limit ? scripts_vars.media_gallery_items_limit : 15;

			if( totalRec.length < Number(item_limit)){

				if(isValidate){
					let itemId = Math.floor(Math.random()*1000);
					var load_gallery_video = wp.template('load-gallary-vidoe-url');
					var data =  { id : itemId, url };
					load_gallery_video = load_gallery_video(data);
					jQuery('ul#tu_gallery_fileprocessing').append(load_gallery_video);
					jQuery('#tu_video_url').val("");
					jQuery('#tu_add_video_url').attr('disabled', true);
				} else {
					stickyAlert('Validation Error', 'Fill add the valid url', {classList: 'danger', autoclose: 3000});
					return;
				}
			} else {
				stickyAlert('Validation Error', 'You can olny add '+item_limit+ ' media items', {classList: 'danger', autoclose: 3000});
				return;
			}
			
		});

		//updateUsefull downloads 
		jQuery(document).on('click','#tu_update_media_gallery', function(e){
			e.preventDefault();		
			let _this = jQuery(this);
			let profile_id	= jQuery('#profile_id').val();
			
			let input_files = jQuery('#tu_media_gallary_frm .file_name');
			let attachments = [];		
			input_files.each(function (i, field) {
				let attachmentType = jQuery(field).data('attachment_type');
				let dataSrc = jQuery(field).data('attachment_src');
				let isSave = jQuery(field).data('is_save');
				if(attachmentType == 'url'){
					let url = dataSrc;
					dataSrc = { url, attachmentType }
					attachments.push( dataSrc );
				}else if(attachmentType == 'video'){
					let file = dataSrc;
					dataSrc = { file, attachmentType, isSaveVideo : isSave }
					attachments.push( dataSrc );
				} else {
					dataSrc['isSaveImage'] = isSave;
					dataSrc['attachmentType'] = 'image';
					attachments.push( dataSrc );
				}
			});
 			_this.attr("disabled", true);
			_this.addClass("tu-btnloader");
			jQuery('body').append(loader_html);
			executeAjaxRequest({
				type: "POST",			
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tuturn_update_media_gallery',
					'security': scripts_vars.ajax_nonce,
					'attachments': attachments,
					'profile_id': profile_id,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					_this.attr("disabled", false);
					_this.removeClass("tu-btnloader")
					if (response.type === 'success') {
						jQuery('.tuturn-modal-popup').modal('hide');
						stickyAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
						window.location.reload();
					} else {
						stickyAlert(response.title, response.message, {classList: 'danger', autoclose: 3000});
					}
				}
			});
		});

		//Delete attachment Image
		jQuery(document).on('click', '.tu-remove-attachment', function (e) {
			e.preventDefault();
			var _this = jQuery(this);
			_this.parents('li').remove();
		});

	});
	
	// btnID, containerID, dropareaID, type, previewID, templateID
	function plupUpload( params ) {
		let {
			btnID, 
			containerID, 
			dropareaID, 
			previewID, 
			templateID, 
			multiSelection, 
			filetype, 
			maxFileCount, 
			fileSize, 
			fileSizes,
			dragDrop,
			uploadedFileFun,
			minWidth,
			maxWidth,
			maxHeight,
			minHeight,
			onlyForImages,
		} = params;
		if( !filetype ){
			filetype = "pdf,doc,docx,xls,xlsx,ppt,pptx,zip,csv,jpg,jpeg,gif,png,mp4,mp3,3gp,flv,ogg,wmv,avi,heic,jfif";
		}
		
		var UploaderArguments = {
			browse_button: btnID, // this can be an id of a DOM element or the DOM element itself
			dragdrop: dragDrop, // boolean value
			multi_selection:multiSelection,
			container: containerID,
			drop_element: dropareaID,
			multipart_params: {
				fileSizes
			},
			// multi_selection: _type,
			url: scripts_vars.ajaxurl + "?action=tuturn_temp_multiple_files_uploader&ajax_nonce=" + scripts_vars.ajax_nonce,
			headers:{
				Authorization: `Bearer `,
			},
			filters: {
				mime_types: [
					{ extensions: filetype }
				],
				max_file_size: fileSize,
				max_file_count: maxFileCount,
				prevent_duplicates: false,
				min_width: minWidth,
				max_width: maxWidth,
				max_height: maxHeight,
				min_height: minHeight,
			}
		};
		var plupUploader = new plupload.Uploader(UploaderArguments);
		plupUploader.init();
		if(onlyForImages){
			plupload.addFileFilter('min_width', function(minwidth, file, cb) {
				var self = this, img = new o.Image();				
				function finalize(result) {
					// cleanup
					img.destroy();
					img = null;
			
				// if rule has been violated in one way or another, trigger an error
					if (!result) {
						stickyAlert(scripts_vars.error_title, scripts_vars.prof_min_image_width_msg, {classList: 'danger', autoclose: 3000});
				}
					cb(result);
				}
				img.onload = function() {
					// check if resolution cap is not exceeded
					finalize(img.width >= minwidth);
				};
				img.onerror = function() {
					finalize(false);
				};
				img.load(file.getSource());
			});

			plupload.addFileFilter('min_height', function(minheight, file, cb) {

				var self = this, img = new o.Image();
			
				function finalize(result) {
					// cleanup
					img.destroy();
					img = null;
			
				// if rule has been violated in one way or another, trigger an error
					if (!result) {
						stickyAlert(scripts_vars.error_title, scripts_vars.prof_min_image_height_msg, {classList: 'danger', autoclose: 3000});
				}
					cb(result);
				}
				img.onload = function() {
					// check if resolution cap is not exceeded
					finalize(img.height >= minheight);
				};
				img.onerror = function() {
					finalize(false);
				};
				img.load(file.getSource());
			});

			plupload.addFileFilter('max_width', function(maxwidth, file, cb) {
				var self = this, img = new o.Image();
			
				function finalize(result) {
					// cleanup
					img.destroy();
					img = null;
			
				// if rule has been violated in one way or another, trigger an error
					if (!result) {
						stickyAlert(scripts_vars.error_title, scripts_vars.prof_mx_image_width_msg, {classList: 'danger', autoclose: 3000});
				}
					cb(result);
				}
				img.onload = function() {
					// check if resolution cap is not exceeded
					finalize(img.width <= maxwidth);
				};
				img.onerror = function() {
					finalize(false);
				};
				img.load(file.getSource());
			});

			plupload.addFileFilter('max_height', function(maxheight, file, cb) {
				var self = this, img = new o.Image();
			
				function finalize(result) {
					// cleanup
					img.destroy();
					img = null;
			
				// if rule has been violated in one way or another, trigger an error
					if (!result) {
						stickyAlert(scripts_vars.error_title, scripts_vars.prof_mx_image_height_msg, {classList: 'danger', autoclose: 3000});
				}
					cb(result);
				}
				img.onload = function() {
					// check if resolution cap is not exceeded
					finalize(img.height <= maxheight);
				};
				img.onerror = function() {
					finalize(false);
				};
				img.load(file.getSource());
			});
		}
		//bind
		plupUploader.bind('FilesAdded', function (up, files) {
			var _Thumb = "";
			var i = files.length,
			maxCountError = false;
			plupload.each(files, function (file) {

				if(plupUploader.settings.filters.max_file_count && i > plupUploader.settings.filters.max_file_count){
					maxCountError = true;
					setTimeout(function(){ up.removeFile(file); }, 50);
				}
				i++;
			});

			if(maxCountError){
				plupUploader.refresh();
				stickyAlert('Error', 'Too many files uploaded', {classList: 'danger', autoclose: 3000});
			}

			if( templateID == 'usefull-load-attachment'){
				jQuery('#tu-attachments-fileprocessing').removeClass('d-none');
			}

			if(maxFileCount == 1){ // for single file
				plupload.each(files, function (file) {
					var load_thumb = wp.template(templateID);
					var _size 	= bytesToSize(file.size);
					var data 	= { id: file.id, size: _size, name: file.name, percentage: file.percent, fileType : file.type };
					load_thumb 	= load_thumb(data);
					_Thumb 		= load_thumb;
				});
			} else if( maxFileCount > 1 ){
				let counter = 0;
				plupload.each(files, function (file) {
					let prevous_files	 = jQuery('#'+previewID+' li').length;
					let file_count  	= maxFileCount - prevous_files;
					if (maxFileCount < 1 ||  counter < file_count) {               
						var load_thumb = wp.template(templateID);
						var _size 		= bytesToSize(file.size);
						var data 		= { id: file.id, size: _size, name: file.name,percentage: file.percent, fileType: file.type.split('/')[0] };
						load_thumb 		= load_thumb(data);
						_Thumb += load_thumb;
					}
					if (maxFileCount > 1){
						counter++;
					}
				});
			}


			if (multiSelection) {
				jQuery('#' + previewID).append(_Thumb);
			} else {
				jQuery('#' + previewID).addClass('tu-hasimgloader');
				jQuery('#' + previewID).html(_Thumb);
			}

			jQuery('#' + previewID).addClass('tu-infouploading');
			if(maxCountError){
				jQuery('.tu-profile-img').removeClass('tu-hasimgloader');
				maxCountError = false;
			} else {
				jQuery('.tu-update-record').attr("disabled",true);
				jQuery('.tu-update-record').addClass("tu-btnloader");
			}

			jQuery('#' + previewID).addClass('tuturn-infouploading');
			jQuery('#' + previewID + " .progress").addClass('tuturn-infouploading');
			up.refresh();
			plupUploader.start();
		});

		// file removed
		plupUploader.bind('FilesRemoved', function(up, files) {
			if (maxFileCount > 1 ) {
				let prevous_files = jQuery('#'+previewID+' li').length;
				if (up.files.length >= maxFileCount) {
					jQuery('#'+containerID).show('slow');
				}
			}
		});

		//upload progress
		plupUploader.bind('UploadProgress', function (up, file) {
			var _html = '<span class="progress-bar uploadprogressbar" style="height:6px; width:' + file.percent + '%"></span>';
			jQuery('#tu_file_' + file.id + ' .progress .progress-bar').replaceWith(_html);
		});

		//Error
		plupUploader.bind('Error', function (up, err) {
			var errorMessage = err.message
			if (err.code == '-600') {
				errorMessage = 'You can only upload '+ scripts_vars.upload_max_file_size +' maximum file size';
			}
			stickyAlert('Error', errorMessage, {classList: 'danger', autoclose: 3000});
		});

		// uploaded File
		plupUploader.bind('FileUploaded', function (up, file, ajax_response) {
			try {
				var response = jQuery.parseJSON(ajax_response.response);
				if (response.type === 'success') {
					window[uploadedFileFun](up, file, response );
				} else {
					stickyAlert('', response.message, {classList: 'danger', autoclose: 3000});
				}
			} catch (err) {
				// Do something about the exception here
				jQuery('li#tu_file_' + file.id).remove();
				stickyAlert('', scripts_vars.invalid_image, {classList: 'danger', autoclose: 3000});
			}
			
			jQuery('li#tu_file_' + file.id + " .progress").removeClass('tuturn-infouploading');
		});
	}

	// Execute ajax resuests 
	function executeAjaxRequest(custom_ajax) {
		if(jQuery(custom_ajax['data']).length){
			custom_ajax_data	= custom_ajax['data'];
			custom_ajax_data.app_type	= 'web';
			custom_ajax['data']	= custom_ajax_data;
		}
		jQuery.ajax(custom_ajax);
	}

	// Alert the notification
	function stickyAlert($title = '', $message = '', data) {
		var $icon = 'ti-face-sad';
		var $class = 'dark';

		if (data.classList === 'success') {
			$icon = 'icon icon-check';
			$class = 'green';
		} else if (data.classList === 'danger') {
			$icon = 'icon icon-x';
			$class = 'red';
		}

		jQuery.confirm({
			icon: $icon,
			closeIcon: true,
			theme: 'modern',
			animation: 'scale',
			type: $class, //red, green, dark, orange
			title: $title,
			content: $message,
			autoClose: 'close|' + data.autoclose,
			buttons: {
				close: {
					text: scripts_vars.close_text,
					btnClass: 'tu-sticky-alert'
				},
			}
		});
	}

	
	

})( jQuery );

// date picker
function initDatePicker(selector, dateformat="MM/DD/YYYY", single_mode=true, callback='') {
	let date_fields = jQuery('.'+selector);
	date_fields.each(function (i, field) {
		var _this = jQuery(this)
		if(field !== null ){
			new Litepicker({
				element: field,
				singleMode: single_mode,
				format: dateformat,
				autoRefres:true,
				selectForward: false,
				mobileFriendly:true,
				autoRefresh: true,
				allowRepick: true,
				// parentEl: '.tu-calendar',
				setup: (picker) => {
					picker.on('hide', (el) => {});
					picker.on('show', (el) => {
						if(el.value == ''){
							picker.clearSelection();
						}
					});
					picker.on('selected', (date1, date2) => {
						_this.next().addClass('d-none');
						if(callback){
							callback();
						}
					});					
				}, 
				dropdowns: {
					minYear: 1980,
					maxYear: 2022,
					months: true,
					years: "asc"
				},
			});
		}
	});
}

// Multiple select data
function openSelect2(name, field_name='languages') { 
	jQuery('#tu-' + name).on('select2:select', function (e) {
		var data = e.params.data;
		var html = '';
		let verifySelection = jQuery('.tu_wrappersortable').find('#'+data.id);
		if( !verifySelection.length ) {
			html  = '<li id='+data.id+'><span>'+data.text;
			html +='<a href="javascript:void(0);" data-slug="'+data.id+'" class="select2-remove-item"><i class="icon icon-x"></i></a></span>';
			html += '<input type="hidden" data-slug="'+data.id+'" value="'+data.id+'" name="'+field_name+'[]"></li>';
			html += '<input type="hidden" data-slug="'+data.id+'" value="'+data.text+'" name="'+field_name+'_array[]"></li>';
			jQuery('.tu_wrappersortable').prepend(html);
		}
	});
}

//convert bytes to KB< MB,GB,TB
function bytesToSize(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 Byte';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};

//Ater upload file
function uploadedFileFun( up, file, response ) {
	jQuery('#thumb-' + file.id).removeClass('tuturn-uploading');
	jQuery('#thumb-' + file.id).removeClass('tu-uploading');
	jQuery('#thumb-' + file.id).addClass('tuturn-file-uploaded');
	jQuery('#thumb-' + file.id + ' .attachment_url').val(response.thumbnail);
}

//Media gallery attachments
function appendMediaGalleryAttachments( up, file, response ) {
	if( response.attachments.attachmentType == 'images'){
		if( Object.keys(response.attachments.sizes).length ){
			let data = {
				file : response.attachments.file,
				fileName : response.attachments.fileName,
				thumbnail : response.attachments.sizes.shop_thumbnail,
			}
			
			jQuery('#tu_file_' + file.id+ ' img.tu_image_'+file.id).attr('src',response.attachments.sizes.tu_user_profile);
			jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-attachment_src', JSON.stringify(data));
		}
	} else {
		jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-attachment_src',response.attachments.file)
	}
	jQuery('#tu_file_' + file.id+ ' input.file_name').val(response.attachments.fileName);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-attachment_type', response.attachments.attachmentType);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-file_size', response.attachments.fileSize);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-file_size', response.attachments.fileSize);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-upload_file', 1);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-file_url', response.attachments.file);
}

//append attachments
function appendAttachments( up, file, response ) {
	jQuery('#appent-attachment').removeClass('d-none');
	if( response.attachments.attachmentType == 'images'){
		if( Object.keys(response.attachments.sizes).length ){
			let data = {
				file : response.attachments.fileName,
				thumbnail : response.attachments.sizes.shop_thumbnail,
			}
 
			jQuery('#tu_file_' + file.id+ ' img.sv_image_'+file.id).attr('src',response.attachments.sizes.shop_thumbnail);
			jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-attachment_src', JSON.stringify(data));
		}
	} else {
		jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-attachment_src',response.attachments.fileName)
	}
	jQuery('#tu_file_' + file.id+ ' input.file_name').val(response.attachments.fileName);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-attachment_type', response.attachments.attachmentType);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-file_size', response.attachments.fileSize);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-file_size', response.attachments.fileSize);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-upload_file', 1);
	jQuery('#tu_file_' + file.id+ ' input.file_name').attr('data-file_url', response.attachments.file);
	if(up.files[up.files.length-1]['id'] == file.id){
		jQuery('.tu-update-record').attr("disabled",false);
		jQuery('.tu-update-record').removeClass("tu-btnloader");
	}
}
//append profile image

function appendAttachmentspp( up, file, response ) {
	jQuery('#appent-attachment-pp').removeClass('d-none');
	if( response.attachments.attachmentType == 'images'){

		if( Object.keys(response.attachments.sizes).length ){
			let data = {
				file : response.attachments.fileName,
				thumbnail : response.attachments.sizes.shop_thumbnail,
			}
 
			jQuery('#tu_file_' + file.id+ ' img.sv_image_'+file.id).attr('src',response.attachments.sizes.shop_thumbnail);
			jQuery('#tu_file_' + file.id+ ' input.profile_file_name').attr('data-attachment_src', JSON.stringify(data));
		}
	} else {
		jQuery('#tu_file_' + file.id+ ' input.profile_file_name').attr('data-attachment_src',response.attachments.fileName)
	}
	jQuery('#tu_file_' + file.id+ ' input.profile_file_name').val(response.attachments.fileName);
	jQuery('#tu_file_' + file.id+ ' input.profile_file_name').attr('data-attachment_type', response.attachments.attachmentType);
	jQuery('#tu_file_' + file.id+ ' input.profile_file_name').attr('data-file_size', response.attachments.fileSize);
	jQuery('#tu_file_' + file.id+ ' input.profile_file_name').attr('data-file_size', response.attachments.fileSize);
	jQuery('#tu_file_' + file.id+ ' input.profile_file_name').attr('data-upload_file', 1);
	jQuery('#tu_file_' + file.id+ ' input.profile_file_name').attr('data-file_url', response.attachments.file);
	jQuery('#tu_file_' + file.id+ ' img.file_url').attr('src', response.attachments.file);
	if(up.files[up.files.length-1]['id'] == file.id){
		jQuery('.tu-update-record').attr("disabled",false);
		jQuery('.tu-update-record').removeClass("tu-btnloader");
	}
} 

//append hours log image

function appendAttachmentshr( up, file, response ) {
	jQuery('#appent-attachment-hr').removeClass('d-none');
	if( response.attachments.attachmentType == 'images'){
 		if( Object.keys(response.attachments.sizes).length ){
			let data = {
				file : response.attachments.fileName,
				thumbnail : response.attachments.sizes.shop_thumbnail,
			}
 
			jQuery('#tu_file_' + file.id+ ' img.sv_image_'+file.id).attr('src',response.attachments.sizes.shop_thumbnail);
			jQuery('#tu_file_' + file.id+ ' input.hour_file_name').attr('data-attachment_src', JSON.stringify(data));
		}
	} else {
		jQuery('#tu_file_' + file.id+ ' input.hour_file_name').attr('data-attachment_src',response.attachments.fileName)
	}
	jQuery('#tu_file_' + file.id+ ' input.hour_file_name').val(response.attachments.file);
	jQuery('#tu_file_' + file.id+ ' input.hour_file_name').attr('data-attachment_type', response.attachments.attachmentType);
	jQuery('#tu_file_' + file.id+ ' input.hour_file_name').attr('data-file_size', response.attachments.fileSize);
	jQuery('#tu_file_' + file.id+ ' input.hour_file_namee').attr('data-file_size', response.attachments.fileSize);
	jQuery('#tu_file_' + file.id+ ' input.hour_file_name').attr('data-upload_file', 1);
	jQuery('#tu_file_' + file.id+ ' input.hour_file_name').attr('data-file_url', response.attachments.file);
	jQuery('#tu_file_' + file.id+ ' img.file_url').attr('src', response.attachments.file);
	if(up.files[up.files.length-1]['id'] == file.id){
		jQuery('.tu-update-record').attr("disabled",false);
		jQuery('.tu-update-record').removeClass("tu-btnloader");
	}
} 
//Check valid URL
function validURL(str) {
	var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
	  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
	  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
	  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
	  '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
	  '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
	return !!pattern.test(str);
}
