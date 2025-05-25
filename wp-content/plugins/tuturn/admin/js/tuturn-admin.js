(function( $ ) {
	var loader_html	= '<div class="tuturn-preloader-section"><div class="tuturn-preloader-holder"><div class="tuturn-loader"></div></div></div>';
	'use strict';
	$(function() {
		// Woocommerce General tab show price fields
		if(jQuery('#general_product_data .pricing').length>0){
			jQuery('#general_product_data .pricing').addClass('show_if_packages');
			jQuery('#general_product_data .pricing').addClass('show_if_service');
		}
		$(document).on('click', '.tu_download_zip_file', function (e) {
			e.preventDefault();
			let _this = $(this);
			let post_id	= _this.data('post_id');
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: admin_scripts_vars.ajaxurl,
				data: {
					'action': 'tu_download_zip_file',
					'security': admin_scripts_vars.ajax_nonce,
					'post_id': post_id,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						window.location = response.attachment;
					} else {
						stickyAdminAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			});
		});

		function sortTable(f,n){
			var rows = jQuery('.wp-list-table tbody  tr').get();
		
			rows.sort(function(a, b) {		
				var A = getVal(a);
				var B = getVal(b);
		
				if(A < B) {
					return -1*f;
				}
				if(A > B) {
					return 1*f;
				}
				return 0;
			});
		
			function getVal(elm){
				var v = jQuery(elm).children('td').eq(n).text().toUpperCase();
				if(jQuery.isNumeric(v)){
					v = parseInt(v,10);
				}
				return v;
			}
		
			jQuery.each(rows, function(index, row) {
				jQuery('.wp-list-table').children('tbody').append(row);
			});
		}
		var f_sl = 1;
		var f_nm = 1;
		var f_dh = 1;
		jQuery(document).on('click','#declined_hours', function (e) {
			f_dh *= -1;
			var n = jQuery(this).prevAll().length;
			sortTable(f_dh,n);
		});
		jQuery(document).on('click','#total_hours', function (e) {
			f_sl *= -1;
			var n = jQuery(this).prevAll().length;
			sortTable(f_sl,n);
		});
		jQuery(document).on('click','#approved_hours', function (e) {
			f_nm *= -1;
			var n = jQuery(this).prevAll().length;
			sortTable(f_nm,n);
		});
		
		
		//Woocommerce Package Switcher type
		jQuery(document).on('change','.select-package-type', function (e) {
			var _this	= jQuery(this);
			var _current	= _this.val();

			if( _current !== null &&  _current === 'tutor' ){
				jQuery('.tu-tutor-package').parents('.form-field').show();
			} else if( _current !== null &&  _current === 'student' ){
				jQuery('.tu-tutor-package').parents('.form-field').hide();
			}
		});

		if( jQuery( 'body' ).find( '.woocommerce_options_panel' ) ){
			var select_pack	= jQuery('.select-package-type').val();
			if( select_pack !== null &&  select_pack === 'tutor' ){
				jQuery('.tu-tutor-package').parents('.form-field').show();
			} else if( select_pack !== null &&  select_pack === 'student' ){
				jQuery('.tu-tutor-package').parents('.form-field').hide();
			}
		}

		// Appearance menu
		if(jQuery('#menu-management').length>0){
			if( wp.template == null ) return;
			var html =  wp.template('tu-menus-settings');
			if(html){
				jQuery('#post-body-content').append( html );
			}
		}

		//View documents
		jQuery(document).on('click', '.do_download_identity', function() {
			var _this 		= jQuery(this);
			var user_id		= _this.data('user'); 
			
			jQuery.confirm({
				title: admin_scripts_vars.account_verification,
				content: '',
				boxWidth: '500px',
				useBootstrap: false,
				typeAnimated: true,
				closeIcon: function(){
					return false; 
				},
				closeIcon: 'aRandomButton',
				onOpenBefore: function(data, status, xhr){
					var jc	= this; 
					jc.showLoading();
				},
				onContentReady: function () {
					var jc		= this; 
					var html	= '';
					var dataString = 'security='+admin_scripts_vars.ajax_nonce+'&user_id='+user_id+'&action=tuturn_view_identity_detail';
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						dataType:"json",
						data: dataString,
						success: function(response) {
 							if( response.type === 'success' ){
								html = response.html;
								jc.hideLoading();
								jc.setContent(html);
								
							} else{
								jc.hideLoading();
								jc.setContent(response.message);
							}
						}
					});
				},
				buttons: {
					close: {
						text: admin_scripts_vars.close
					}
				},
			});
		});

		// identity profile view
		jQuery(document).on('click', '.do_verify_identity', function() {
			var _this 		= jQuery(this);
			var _type		= _this.data('type'); 
			
			if( _type === 'inprogress' ){
				var localize_title = admin_scripts_vars.approve_identity;
				var localize_vars_message = admin_scripts_vars.approve_identity_message;
			}else{
				var localize_title = admin_scripts_vars.reject_identity;
				var localize_vars_message = admin_scripts_vars.reject_identity_message;
			}
			
			var _user_id	= _this.data('id'); 
			var _post_id	= _this.data('post_id');
			jQuery.confirm({
				title: localize_title,
				content: localize_vars_message,
				boxWidth: '500px',
				useBootstrap: false,
				typeAnimated: true,
				closeIcon: function(){
					return false; 
				},
				closeIcon: 'aRandomButton',
				onAction: function (btnName) {
					var jc	= this; 
					if(btnName === 'reject'){
						jc.showLoading();
						var formdata =	'<form class="reject-identity-form">' +
											'<div class="form-group jconfirm-buttons">' +
												'<p>'+admin_scripts_vars.reason+'</p>' +
												'<textarea class="form-control reason-content" required /></textarea>' +
												'<button type="submit" class="btn btn-red reject-identity">'+admin_scripts_vars.reject+'</button>' +
											'</div>' +
										'</form>';
						this.setContent(formdata);
						this.buttons.accept.hide();
						this.buttons.reject.hide();
						jc.hideLoading();
						
						jQuery(document).on('click', '.reject-identity', function(e) {
							e.preventDefault();
							jc.showLoading();
							var reason	= jQuery('.reason-content').val();
							var dataString  = 'security='+admin_scripts_vars.ajax_nonce+'&reason='+reason+'&type=reject&post_id='+_post_id+'&user_id='+_user_id+'&action=tuturn_identity_verification';
							jQuery.ajax({
								type: "POST",
								url: ajaxurl,
								dataType:"json",
								data: dataString,
								success: function(response) {
									jc.hideLoading();
									jc.$content.html(response.message);
									jc.buttons.accept.hide();
									jc.buttons.reject.hide();
									window.location.reload();
								}
							});

							return false;
						});
					}
				},

				buttons: {
					accept: {
						text: admin_scripts_vars.accept,
						action: function () {
							var jc	= this; 
							var dataString  = 'security='+admin_scripts_vars.ajax_nonce+'&type=approve&post_id='+_post_id+'&user_id='+_user_id+'&action=tuturn_identity_verification';
							jc.showLoading();
							jQuery.ajax({
								type: "POST",
								url: ajaxurl,
								dataType:"json",
								data: dataString,
								success: function(response) {
									jc.hideLoading();
									jc.$content.html(response.message);
									jc.buttons.accept.hide();
									jc.buttons.reject.hide();
									window.location.reload();
								}
							});
							return false;
						}
					},
					reject: {
						text: admin_scripts_vars.reject,
						action: function () {
							return false;
						}
					},
				},
			});
		});

		jQuery(document).on('click', '.wt-download-file-attachment', function (e) {
            var _this = jQuery(this);
            var attachment_id = _this.data('attachment_id');
			var _post_id = _this.data('post_id');
            downloadAttachment(attachment_id,_post_id);
        
        });

		jQuery(document).on('click', '.wt-download-attachments', function (e) {
			e.preventDefault();
			var _this = jQuery(this);
			var _post_id = _this.data('post_id');
	
			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'tuturn_download_attachments',
					post_id: _post_id,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						window.location = atob(reverse(response.attachment));
					} else {
						stickyAdminAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			}, true);
		});
		function downloadAttachment(attachment_id,_post_id) {

			jQuery('body').append(loader_html);
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'tuturn_download_single_attachment',
					attachment_id: attachment_id,
					post_id	: _post_id
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						var filehref = atob(reverse(response.attachment));
						var link = document.createElement('a');
						var filename = filehref.split('/').pop();
						link.href = filehref;
						link.download = filename;
						link.click();
						link.remove();
					} else {
						if (response.url) {
							location.href = response.url;
						} else {
							stickyAdminAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
						}
					}
				}
			}, true);
		}
		function reverse(s) {
			if (s.length < 2)
				return s;
			var halfIndex = Math.ceil(s.length / 2);
			return reverse(s.substr(halfIndex)) +
				reverse(s.substr(0, halfIndex));
		}
		//veryfy profiles
		jQuery(document).on('click', '.do_verify_user_confirm', function(e) {
			var _this 		= jQuery(this);
			var _type		= _this.data('type');
			var _user_id	= _this.data('user_id');
			var _id			= _this.data('id');
			var _user_id	= _this.data('user_id');
			var dataString  = 'security='+admin_scripts_vars.ajax_nonce+'&type='+_type+'&id='+_id+'&user_id='+_user_id+'&action=tuturn_approve_profile';
			let approve_text_title	= admin_scripts_vars.approve_account;
			let approve_text    	= admin_scripts_vars.approve_account;

			if(_type == 'approve'){
				approve_text_title      = admin_scripts_vars.approve_account;
				approve_text            = admin_scripts_vars.approve_account_message;
			} else {
				approve_text_title      = admin_scripts_vars.reject_account;
				approve_text            = admin_scripts_vars.reject_account_message;
			}
			jQuery.confirm({
				title: approve_text_title,
				content: approve_text,
				boxWidth: '500px',
				useBootstrap: false,
				typeAnimated: true,
				closeIcon: function(){
					return false; 
				},
				closeIcon: 'aRandomButton',
				buttons: {
				yes: {
					text: admin_scripts_vars.yes,
					action: function () {
						if(loader_html){jQuery('body').append(loader_html);}
						jQuery.ajax({
							type     : "POST",
							url      : admin_scripts_vars.ajaxurl,
							data     : dataString,
							dataType : "json",
							success: function (response) {
								jQuery('.tuturn-preloader-section').remove();
								if (response.type === 'success'){      
									stickyAdminAlert(response.message, response.message_desc, {classList: 'success', autoclose: 2000});
									setTimeout(function(){
										window.location.reload();
									}, 2000);            
								}else{
									jQuery('body').find('.sticky-queue').remove();
									stickyAdminAlert(response.message, response.message_desc, {classList: 'danger', autoclose: 5000});
									
								}
							}
						});
						
					}
				},
				no: {
					close: {
					text: admin_scripts_vars.close
					}
				}
				}
			});
			e.preventDefault();
		});

		// Order status update
		jQuery(document).on('click', '#tu-order-status-complete', function(e) {
			e.preventDefault();
 			let order_id		= $(this).data('order_id');
			if(loader_html){jQuery('body').append(loader_html);}

			executeConfirmAjaxRequest({
				type: "POST",
				url: admin_scripts_vars.ajaxurl,
				data: {
					security:	admin_scripts_vars.ajax_nonce,
					action:		'tuturn_admin_order_status_update',
					order_id:	order_id,
				},
				dataType: "json",
				success: function (response) {
					jQuery('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						stickyAdminAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
						setTimeout(function(){ 
							window.location.reload();
						}, 2000);
					} else {						
						stickyAdminAlert(response.title, response.message, {classList: 'important', autoclose: 3000});
					}
				}
			}, admin_scripts_vars.order_status_title, admin_scripts_vars.order_status_message);
		});

		// Create package
		jQuery(document).on('click', '#tu-assign-package', function(e) {
			e.preventDefault();
			let package_id	= $('#package_id').val();
			let user_id		= $(this).data('user_id');
			let profile_id	= $(this).data('profile_id');
			if(loader_html){jQuery('body').append(loader_html);}
			jQuery.ajax({
				type: "POST",
				url: admin_scripts_vars.ajaxurl,
				data: {
					security:	admin_scripts_vars.ajax_nonce,
					action:		'tuturn_instructor_assign_package',
					package_id:	package_id,
					user_id:	user_id,
					profile_id:	profile_id,
				},
				dataType: "json",
				success: function (response) {
					jQuery('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						stickyAdminAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
						setTimeout(function(){ 
							window.location.reload();
						}, 2000);
					} else {						
						_this.removeAttr("disabled");
						stickyAdminAlert(response.title, response.message, {classList: 'important', autoclose: 3000});
					}
				}
			});
		});

		jQuery(document).on('click', '#tu-assign-package-student', function(e) {
			e.preventDefault();
			let package_id	= $('#package_id').val();
			let user_id		= $(this).data('user_id');
			let profile_id	= $(this).data('profile_id');
			if(loader_html){jQuery('body').append(loader_html);}
			
			jQuery.ajax({
				type: "POST",
				url: admin_scripts_vars.ajaxurl,
				data: {
					security:	admin_scripts_vars.ajax_nonce,
					action:		'tuturn_student_assign_package',
					package_id:	package_id,
					user_id:	user_id,
					profile_id:	profile_id,
				},
				dataType: "json",
				success: function (response) {
					jQuery('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						stickyAdminAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
						setTimeout(function(){ 
							window.location.reload();
						}, 2000);
					} else {						
						_this.removeAttr("disabled");
						stickyAdminAlert(response.title, response.message, {classList: 'important', autoclose: 3000});
					}
				}
			});
		});

		/* mailchimp update list */
		jQuery(document).on('click', '.tu-latest-mailchimp-list', function(e) {
			e.preventDefault();
			if(loader_html){jQuery('body').append(loader_html);}
			jQuery.ajax({
				type: "POST",
				url: admin_scripts_vars.ajaxurl,
				data: {
					security:	admin_scripts_vars.ajax_nonce,
					action:	'tuturn_mailchimp_array',
				},
				dataType: "json",
				success: function (response) {
					jQuery('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						stickyAdminAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
						setTimeout(function(){ 
							window.location.reload();
						}, 2000);
					} else {						
						_this.removeAttr("disabled");
						stickyAdminAlert(response.title, response.message, {classList: 'important', autoclose: 3000});
					}
				}
			});
		});

		// Verify item purchase
		jQuery(document).on('click', '#tuturn_verify_btn', function(e){
			e.preventDefault();
			let _this	= jQuery(this);
			let epv_purchase_code = jQuery('#tuturn_purchase_code').val();

			if(epv_purchase_code == '' || epv_purchase_code == null){
				let epv_purchase_code_title = jQuery('#tuturn_purchase_code').attr('title');
				stickyAdminAlert('', epv_purchase_code_title, {classList: 'important', autoclose: 3000});
				return false;
			} else {
				_this.attr('disabled', 'disabled');
			}
			if(loader_html){jQuery('body').append(loader_html);}
			jQuery.ajax({
				type: "POST",
				url: admin_scripts_vars.ajaxurl,
				data: {
					purchase_code:	epv_purchase_code,
					security:	admin_scripts_vars.ajax_nonce,
					action:	'tuturn_verifypurchase',
				},
				dataType: "json",
				success: function (response) {
					jQuery('.tuturn-preloader-section').remove();
					if (response.type === 'success') {	
						stickyAdminAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
						setTimeout(function(){ 
							window.location.reload();
						}, 2000);
					} else {
						
						_this.removeAttr("disabled");
						stickyAdminAlert(response.title, response.message, {classList: 'important', autoclose: 3000});
					}
				}
			});
		});

		//Remove license
		jQuery(document).on('click', '#tuturn_remove_license_btn', function(e){
			e.preventDefault();
			let _this	= jQuery(this);
			let epv_purchase_code = jQuery('#tuturn_purchase_code').val();

			if(epv_purchase_code == '' || epv_purchase_code == null){
				let epv_purchase_code_title = jQuery('#tuturn_purchase_code').attr('title');
				stickyAdminAlert('', epv_purchase_code_title, {classList: 'important', autoclose: 3000});
				return false;
			} else {
				_this.attr('disabled', 'disabled');
			}

			if(loader_html){jQuery('body').append(loader_html);}
			jQuery.ajax({
				type: "POST",
				url: admin_scripts_vars.ajaxurl,
				data: {
					purchase_code:	epv_purchase_code,
					security:	admin_scripts_vars.ajax_nonce,
					action:	'tuturn_remove_license',
				},
				dataType: "json",
				success: function (response) {
					jQuery('.tuturn-preloader-section').remove();
					if (response.type === 'success') {
						stickyAdminAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
						setTimeout(function(){ 
							window.location.reload();
						}, 2000);
					} else {						
						_this.removeAttr("disabled");
						stickyAdminAlert(response.title, response.message, {classList: 'important', autoclose: 3000});
					}
				}
			});
		});
		
		//import dummy users
		jQuery(document).on('click', '.doc-import-users', function() { 
			jQuery.confirm({
				title: admin_scripts_vars.import,
				content: admin_scripts_vars.import_message,
				boxWidth: '500px',
				useBootstrap: false,
				typeAnimated: true,
				closeIcon: function(){
					return false; 
				},
				closeIcon: 'aRandomButton',
				buttons: {
					yes: {
						text: admin_scripts_vars.yes,
						action: function () {
							var jc	= this; 
							jc.showLoading();
							var dataString = 'security='+admin_scripts_vars.ajax_nonce+'&action=tuturn_import_users';
							var $this = jQuery(this);
							jQuery.ajax({
								type: "POST",
								url: ajaxurl,
								dataType:"json",
								data: dataString,
								success: function(response) {
									jQuery('#import-users').find('.inportusers').remove();
									stickyAdminAlert(response.title, response.message, {classList: 'success', autoclose: 3000});
									setTimeout(function(){ 
										window.location.reload();
									}, 2000);
								}
							});                    
							return false;
						}
					},
					no: {
						close: {
						text: admin_scripts_vars.close
						}
					}
				}
			});
		});

		/* media upload for amentities */
		jQuery(document).on('click',".system_media_upload_button", function() {
			var _this 			= jQuery(this);
			var inputfield 		= _this.parent().prev('.input-sec').find('input').attr('id');
			var screenshot 		= _this.parent().parent().find('.screenshot');
			var selector		= _this.parents('.section-upload');
			
			var custom_uploader = wp.media({
				title: 'Select File',
				button: {
					text: 'Add File'
				},
				multiple: false
			}).on('select', function() {
					var attachment  = custom_uploader.state().get('selection').first().toJSON();
					var itemurl			= attachment.url;
					var itemid			= attachment.id;
					var btnContent 		= '<a href="'+itemurl+'"><img class="system-upload-image" src="'+itemurl+'"/></a>';
					selector.find('.remove-item-image').css( 'display', 'inline-block' );
					$('#' + inputfield).val(itemurl);
					$('#' + inputfield).next('input').val(itemid);
					screenshot.fadeIn().html(btnContent);
				}).open();

		});	
		
		/* remove media upload for amentities */
		jQuery(document).on('click',".remove-item-image", function() {
			var _this 		= jQuery(this);
			var selector	= _this.parents('.section-upload')
			selector.find('.remove-item-image').hide().addClass('hide');
			selector.find('.upload').val('');
			selector.find('.screenshot').slideUp();
		});
		
		// Widget upload image
		jQuery(document).on('click', ".upload_button_wgt", function () {
            var _this = jQuery(this);
            var inputfield = _this.parent().find('input').attr('id');
            var custom_uploader = wp.media({
                title: 'Select File',
                button: {
                    text: 'Add File'
                },
                multiple: false
            })
                .on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    var itemurl = attachment.url;
                    jQuery('#' + inputfield).val(itemurl);
                }).open();
        });

		// Approve withdraw request
		jQuery(document).on('click', '.tu-update-earning', function (e) {
			e.preventDefault();
			var this_ 	= jQuery(this);
			let status 	= this_.data('status');
			let id 		= this_.data('id');
			if (id) {
				if(loader_html){jQuery('body').append(loader_html);}
				executeConfirmAjaxRequest({
					type: "POST",
					url: admin_scripts_vars.ajaxurl,
					data: {
						id:			id,
						status:		status,
						security:	admin_scripts_vars.ajax_nonce,
						action:		'tuturn_update_earning_withdraw',
					},
					dataType: "json",
					success: function (response) {
						jQuery('.tuturn-preloader-section').remove();
						if (response.type === 'success') {
							stickyAdminAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
							window.location.reload();
						} else {
							stickyAdminAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
						}
					}
				}, admin_scripts_vars.withdraw_title, admin_scripts_vars.withdraw_desc);
			}

		});
	});

})( jQuery );

// Confirm before submit
function executeConfirmAjaxRequest(ajax, title = 'Confirm', message = '', loader) {
	let yes = admin_scripts_vars.yes;
	let no = admin_scripts_vars.no;
	jQuery.confirm({
		title: title,
		content: message,
		class: 'blue',
		theme: 'modern',
		animation: 'scale',
		closeIcon: true, // hides the close icon.
		'buttons': {
			yes: {
				'btnClass': 'btn-dark tu-yesbtn',
				'action': function () {
					if (loader) {
						jQuery('body').append(loader_html);
					}
					jQuery.ajax(ajax);
				}
			},
			no: {
				'btnClass': 'btn-default tu-nobtn',
				'action': function () {
					jQuery('.tuturn-preloader-section').remove();
					return true;
				}
			},
		}
	});
}

function stickyAdminAlert($title = '', $message = '', data) {
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
			close: {btnClass: 'tu-sticky-alert'}
		}
	});
}

/**
 * show/hide page layout sidebar
 */
jQuery(document).ready(function() {
	jQuery(document).on('click', "input[name$='page_side_layout']", function (e) {
        var pageLayoutVal = jQuery(this).val();
		if(pageLayoutVal ===  'none'){
			jQuery("div.tu-page-sidebar").hide();
		} else {
			jQuery("div.tu-page-sidebar").show();
		}
    });
});