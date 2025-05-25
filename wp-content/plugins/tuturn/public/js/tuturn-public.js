var loader_html = '<div class="tuturn-preloader-section"><div class="tuturn-preloader-holder"><div class="tuturn-loader"></div></div></div>';
var week_days_slots = {};
var unavailable_days_slots = [];
(function($) {
    'use strict';

    function sortableListItems(listId) {
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

    //copy Content
    function copyContent(text) {
        let textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();
    }

    //Copy URL
    jQuery(document).on('click', '#copyurl', function (e) {
        e.preventDefault();
        let _this = $(this);
        let input = $('#urlcopy').html();

        copyContent(input);
        _this.html('<em>'+scripts_vars.copied+'</em>');
        setTimeout(function () {
            _this.text('');
        }, 1000);
    });

    $(function() {
        // dhb sidebar dropdown
        jQuery('.tu-sidebar-dropdown.active > .tu-sidebar-submenu').css('display', 'block');
        jQuery(document).on('change', '#tu-checkstudent-email', function (event) {
			event.preventDefault();
            let _this = jQuery(this)
            let email_val   = _this.val();
            if( tutun_validateEmail(email_val)) {
                let profile_id 	= jQuery('#profile_id').val();
                jQuery('body').append(loader_html);
                jQuery.ajax({
                    type: "POST",
                    url: scripts_vars.ajaxurl,
                    data: {
                        action: 'tuturn_check_student_email',
                        email: email_val,
                        profile_id: profile_id
                    },
                    dataType: "json",
                    success: function (response) {
                        jQuery('body').find('.tuturn-preloader-section').remove();
                        if (response.type === 'success') {
                            _this.parent().addClass('tu-valid');
                            _this.parent().removeClass('tu-invalid');
                            _this.parent().find('.tu-user-info').html(response.user_html);
                            _this.parent().find('.tu-user-info').removeClass('d-none');
                        } else {
                            stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 2000 });
                            _this.parent().addClass('tu-invalid');
                            _this.parent().removeClass('tu-valid');
                            _this.parent().find('.tu-user-info').addClass('d-none');
                            _this.parent().find('.tu-user-info').html('');
                        }
                    }
                }, true);
            } else {
                _this.parent().addClass('tu-invalid');
                _this.parent().removeClass('tu-valid');
                _this.parent().find('.tu-user-info').addClass('d-none');
                _this.parent().find('.tu-user-info').html('');
            }

        });
            //Navigation sidebar
        jQuery(document).on('click', '.tu-sidebar-dropdown > a', function() {
            var _this = jQuery(this)
            _this.closest('li').siblings().children('.tu-sidebar-submenu').slideUp(300)
            _this.next().slideToggle(300)
            _this.closest('li').siblings().removeClass('active')
            _this.closest('li').toggleClass('active')
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
				url: scripts_vars.ajaxurl,
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
						stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
					}
				}
			}, true);
		});

        jQuery(document).on('change', '.tu-get-states', function (event) {
			event.preventDefault();
			let state_val	= jQuery(this).val();
			let id			= jQuery(this).data('id');
			if(state_val){
				jQuery('body').append(loader_html);
				jQuery.ajax({
					type: "POST",
					url: scripts_vars.ajaxurl,
					data: {
						'action'	: 'tuturn_country_array',
						'security'	: scripts_vars.ajax_nonce,
						'country' 	: state_val,
						'type' 		: 'ajax'
					},
					dataType: "json",
					success: function (response) {
						jQuery('body').find('.tuturn-preloader-section').remove();
						if (response.type === 'success') {
							let setle2op	= jQuery('.tu-stat-'+id);
							setle2op.empty().trigger('change');
							$.each(response.countries, function(key,value) {
								var option = new Option(value, key, true, true);
								setle2op.append(option);
							});
							setle2op.trigger('change');							
						} 
					}
				});
			}
		});
        
        jQuery(':radio[name="teaching_type"]').change(function() {
			let teaching_preference = jQuery(this).filter(':checked').val();
            let offline_palce 		= jQuery(':radio[name="offline_type"]').filter(':checked').val();
			if(teaching_preference == 'offline' ){
				jQuery('.tu-custom-offline').removeClass('d-none');
                if(offline_palce == 'tutor' ){
					jQuery('.tu-location-op').removeClass('d-none');
				} else {
					jQuery('.tu-location-op').addClass('d-none');
				}
			} else {
				jQuery('.tu-custom-offline').addClass('d-none');
                jQuery('.tu-location-op').addClass('d-none');
			}
		});

        jQuery(':checkbox[name="teaching_type[]"]').change(function() {
			let teaching_preference = jQuery(this).filter(':checked').val();
            let selected_values = [];
            jQuery('.tu-teaching_type-se:checked').each(function() {
                selected_values.push(jQuery(this).val());
            });
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

        jQuery(':checkbox[name="offline_type[]"]').change(function() {
            let offline_palce = [];
            jQuery('.tu-offline_type-se:checked').each(function() {
                offline_palce.push(jQuery(this).val());
            });
			//let offline_palce = $(this).filter(':checked').val();
            if($.inArray('tutor', offline_palce) != -1 ){
			//if(offline_palce == 'tutor' ){
				jQuery('.tu-location-op').removeClass('d-none');
			} else {
				jQuery('.tu-location-op').addClass('d-none');
			}
		});

		jQuery(':radio[name="offline_type"]').change(function() {
			let offline_palce = jQuery(this).filter(':checked').val();
			if(offline_palce == 'tutor' ){
				jQuery('.tu-location-op').removeClass('d-none');
			} else {
				jQuery('.tu-location-op').addClass('d-none');
			}
		});

        // Toogle passowrd show
        jQuery(".tu-input-group  input").keyup(function(e) {
            let _this = jQuery(this);
            let val = _this.val();
            if (val) {
                _this.parents('.tu-placeholderholder').addClass('show')
            } else {
                _this.parents('.tu-placeholderholder').removeClass('show')
            }
        });
        //toggle mobile menu
        jQuery(".tu-dbmenu").on("click", function() {
            jQuery(".tu-asidewrapper , .tu-asidedetail").toggleClass("tu-opendbmenu");
        });
        //Select 2	
        var config = {
            '.tu-select select': { allowClear: true },
            '#selectv1': { allowClear: true, minimumResultsForSearch: Infinity },
            '#instructor-search-dropdown': { allowClear: true },
            '#wk-selectv5': { allowClear: true },
            '.tu-selectv select': { width: 200, minimumResultsForSearch: Infinity },
        }
        for (var selector in config) {
            jQuery(selector).select2(config[selector]);
        }
        //placeholder
        jQuery('[data-placeholderinput]').each(function(item) {
            var data_placeholder = jQuery('[data-placeholderinput]')[item]
            var tu_id = jQuery(data_placeholder).attr('id')
            var tu_placeholder = jQuery(data_placeholder).attr('data-placeholderinput')
            jQuery('#' + tu_id).on('select2:open', function(e) {
                jQuery('input.select2-search__field').prop('placeholder', tu_placeholder);
            });
        });

        jQuery('.tu-select-category').select2().on('select2:open', function(e) {
            jQuery('.select2-search__field').attr('placeholder', scripts_vars.select_category);
        })

        // Make category drop-down select2 on search instructor
        jQuery('.tu-select-category').select2({
            //minimumResultsForSearch: Infinity,
            placeholder: {
                id: '-1', // the value of the option
                text: scripts_vars.select_category
            },
            allowClear: true
        });



        //load more sub categories
        jQuery(document).on('click', '.tu-show_more', function(e) {
            jQuery(this).text(jQuery(this).text() == "Show Less" ? "Show More" : "Show Less");
            jQuery(this).closest(".tu-asideitem").find(".tu-categorieslist li:nth-child(n+6)").slideToggle();
            jQuery(this).closest(".tu-asideitem").find(".tu-collapseitem .tu-commenteditem:nth-child(n+6)").slideToggle();
            jQuery(this).closest(".tu-aside-content").find(".tu-filterselect li:nth-child(n+6)").slideToggle();
            jQuery(this).closest(".tu-boxlg").find(".tu-commentarea .tu-commentlist:nth-child(n+5)").slideToggle();
        });

        //Copy text
        jQuery(document).on('click', '.copytext', function(e) {
            // var copyText = document.getElementById("urlcopy").innerHTML;
            var copyText = document.getElementById("urlcopy").textContent;
            navigator.clipboard.writeText(copyText);
        });

        /* tippy */
        function tooltipInitialization(selecter) {
            if (typeof tippy === 'function') {
                tippy(selecter, {
                    allowHTML: true,
                    animation: 'scale',
                    content(reference) {
                        const id = reference.getAttribute('data-template');
                        const template = document.getElementById(id);
                        return template.innerHTML;
                    }
                });
            }
        }
        /* initialize tooltip */
        tooltipInitialization('.tu-tooltip-tags');


        /* Login */
        jQuery('form.tu-login-form').on('keydown', 'input', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                jQuery('.tu-user-login').trigger('click');
            }
        });
        jQuery(document).on('click', '.tu-user-login', function(e) {
            e.preventDefault();
            var _serialize = jQuery('form.tu-login-form').serialize();
            jQuery('body').append(loader_html);
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_user_signin',
                    'security': scripts_vars.ajax_nonce,
                    'data': _serialize,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
                        var url = response.redirect;
                        window.location = url.replace('&#038;', '&');
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
                    }
                }
            });
        });

        jQuery('.wpguppy_start_chat').on('click', function (e) {
            e.preventDefault();
            var _post_id    = jQuery(this).data('receiver_id');
            jQuery('body').append(loader_html);
            
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'wp_guppy_start_chat',
                    'security': scripts_vars.ajax_nonce,
                    'post_id': _post_id
                },
                dataType: "json",
                success: function (response) {
                    if (response.type === 'success') {
                        window.setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 2000);
                    } else {
                        StickyAlert(response.message, response.message_desc, {classList: 'danger', autoclose: 5000});
                    }
                }
            });
        });

        /* Registration */
        jQuery('form.tu-signup-form').on('keydown', 'input', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                jQuery('.tu-submit-registration').trigger('click');
            }
        });
        jQuery(document).on('click', '.tu-submit-registration', function(e) {
            e.preventDefault();
            var _serialize = jQuery('form.tu-signup-form').serialize();
            jQuery('body').append(loader_html);
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_user_register',
                    'security': scripts_vars.ajax_nonce,
                    'data': _serialize,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 5000 });
                        window.setTimeout(function(){
                            window.location.replace(response.redirect);
                        }, 5000);
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
                    }
                }
            });
        });

        /* social login after chooose user type */
        jQuery(document).on('change', 'input[name="choose_usertype"]:radio', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let userType = jQuery('input[name="choose_usertype"]:checked').val();
            let userDetail = JSON.parse(jQuery('#social-userdetail').val());
            userDetail["user_type"] = userType;
            jQuery('body').append(loader_html);
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_user_social_login',
                    'security': scripts_vars.ajax_nonce,
                    'data': userDetail,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                        window.location.replace(response.redirect);
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        /* Forgot Password */
        jQuery(document).on('click', '.tu-password-forgot', function(e) {
            e.preventDefault();
            let dataArray = jQuery('.tu-forgot-password').serializeArray();
            let dataObj = {};
            jQuery(dataArray).each(function(i, field) {
                dataObj[field.name] = field.value;
            });

            jQuery('body').append(loader_html);
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_password_reset',
                    'security': scripts_vars.ajax_nonce,
                    'data': dataObj,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        /* password recover after forgot */
        jQuery(document).on('click', '.tu-password-recover', function(e) {
            e.preventDefault();
            let _serialize 	= jQuery('.tu-password-reset-form').serialize();
 			jQuery('body').append(loader_html);
            jQuery.ajax({
                type     : "POST",
                url      : scripts_vars.ajaxurl,
                data        : {
                    'action': 'tuturn_recover_password',
                    'security': scripts_vars.ajax_nonce,
                    'data': _serialize,
                },
                dataType : "json",
                success: function (response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                        window.location.replace(response.redirect);
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        //Newsletter form submit 
        jQuery(document).on('click', '.subscribe_me', function(event) {
            'use strict';
            event.preventDefault();
            var _this = jQuery(this);
            let form_data = _this.parents('form').serialize();
            jQuery('body').append(loader_html);

            jQuery.ajax({
                type: 'POST',
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_subscribe_mailchimp',
                    'security': scripts_vars.ajax_nonce,
                    'data': form_data,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {
                        stickyAlert(response.title, response.message, { classList: 'success', speed: 200, autoclose: 5000 });
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'important', speed: 200, autoclose: 5000 });
                    }
                }
            });
        });

        // Provider by package
        jQuery(document).on('click', '.tu-buy-package', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let post_id = _this.data('pakcage_id');
            let user_id = _this.data('user_id');
            jQuery('body').append(loader_html);
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_package_checkout',
                    'security': scripts_vars.ajax_nonce,
                    'package_id': post_id,
                    'user_id': user_id,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {

                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                        window.setTimeout(function() {
                            window.location.href = response.checkout_url;
                        }, 2000);
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'important', autoclose: 3000 });
                    }
                }
            });
        });

        //Load subcategories with parent category selection
        jQuery(document).on('change', '#instructor-search-dropdown', function(e) {
            e.preventDefault();
            let category = jQuery(this).val();
            jQuery('body').append(loader_html);
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_load_subcategories',
                    'security': scripts_vars.ajax_nonce,
                    'category': category
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {
                        if (typeof response.subcategories != "undefined") {
                            jQuery('#tu-subcategories-instructor').html(response.subcategories);
                            jQuery("#tu-subcategories-instructor li:nth-child(n+6)").hide();
                        }
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            }, true);
        });

        // add to save on profile details
        jQuery(document).on('click', '.tu-linkheart', function(e) {
            let _this = jQuery(this);
            let currentUser = _this.data('user_id');
            let profileId = _this.data('profile_id');
            jQuery('body').append(loader_html);
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_profile_add_to_save',
                    'security': scripts_vars.ajax_nonce,
                    'userId': currentUser,
                    'profileId': profileId,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();

                    if (response.type === 'success') {

                        if (response.instructorStatus == 1) {
                            _this.children("i.icon-heart").addClass("tu-colorred");
                            jQuery('#tu-subcategories-instructor').html(response.subcategories);
                        } else {
                            _this.children("i.icon-heart").removeClass("tu-colorred");
                        }
                        _this.children("span").text(response.statusText);
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                    } else {

                        if (response.login) {
                            window.setTimeout(function() {
                                window.location.href = response.login;
                            }, 100);
                        } else {
                            stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                        }
                    }
                }
            });
        });

        // favourite instrutor add/tremove 
        jQuery(document).on('click', '.tu-favrt-instrutor', function(e) {

            let _this = jQuery(this);
            let currentUser = _this.data('current_user');
            let profileId = _this.data('profile_id');
            let profile_id 	= jQuery('#profile_id').val();
            jQuery('body').append(loader_html);
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_favourite_profile_add_remove',
                    'security': scripts_vars.ajax_nonce,
                    'userId': currentUser,
                    'profile_id': profile_id,
                    'profileId': profileId,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {

                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                        window.location.reload();

                    } else {

                        if (response.login) {
                            window.setTimeout(function() {
                                window.location.href = response.login;
                            }, 100);
                        } else {
                            stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                        }
                    }
                }
            });
        });

        // latest articles ordering asc desc
        jQuery(document).on('change', '.tu-ordering-articles', function(e) {
            jQuery('#article_search_form').submit();
        });

        //instructor search listings sort
        jQuery(document).on('change', '#tu-instructor-search-sortby', function(e) {
            let sort_val = jQuery(this).val();
            if (sort_val) {
                var url = window.location.href;
                var url = removeParam("sort_by", url);
                if (url.indexOf('?') > -1) {
                    url += '&sort_by=' + sort_val
                } else {
                    url += '?sort_by=' + sort_val
                }
                window.location.href = url;
            }
            e.preventDefault();
        });

        //invoice student listings sort
        jQuery(document).on('change', '.invoice-sort-by', function(e) {
            let sort_val = jQuery(this).val();
            if (sort_val) {
                var url = window.location.href;
                var url = removeParam("sort_by", url);
                if (url.indexOf('?') > -1) {
                    url += '&sort_by=' + sort_val
                } else {
                    url += '?sort_by=' + sort_val
                }
                window.location.href = url;
            }
            e.preventDefault();
        });

        //
        jQuery(document).on('change', '.booking-sort-by', function(e) {
            let sort_val = jQuery(this).val();
            if (sort_val) {
                var url = window.location.href;
                var url = removeParam("sort_by", url);
                if (url.indexOf('?') > -1) {
                    url += '&sort_by=' + sort_val
                } else {
                    url += '?sort_by=' + sort_val
                }
                window.location.href = url;
            }
            e.preventDefault();
        });
        // blog sort by
        jQuery(document).on('change', '#blog-sort', function(e) {
            let sort_val = jQuery(this).val();
            if (sort_val) {
                var url = window.location.href;
                var url = removeParam("sort_by", url);
                if (url.indexOf('?') > -1) {
                    url += '&sort_by=' + sort_val
                } else {
                    url += '?sort_by=' + sort_val
                }
                window.location.href = url;
            }
            e.preventDefault();
        });


        // Hourly rate search range input validation
        jQuery(document).on('click', '#tu_search_instructor_filter', function(e) {
            e.preventDefault();
            let min_price = jQuery('input#tu-min-value').val();
            let max_price = jQuery('input#tu-max-value').val();

            if (min_price && max_price) {
                if (parseInt(min_price) > parseInt(max_price)) {
                    StickyAlert(scripts_vars.price_min_max_error_title, scripts_vars.price_min_max_error_desc, { classList: 'danger', autoclose: 5000 });
                    return false;
                }
            }

            jQuery('#tu-instructor-search').submit();
        });

        // search instructor top form
        jQuery(document).on('click', '#tu-instructor-search-keyword', function(e) {
            e.preventDefault();
            jQuery('#tu-instructor-keyword-search-form').submit();
        });

        // index search instructor   form
        jQuery(document).on('click', '#tu-index-instructor-search', function(e) {
            e.preventDefault();
            jQuery('#tu-index-search-form').submit();
        });

        //remove subcategories from chunks
        jQuery(document).on('click', '.remove-subcategory', function(e) {
            e.preventDefault();
            var _this = jQuery(this);
            let position = jQuery(this).data('position');
            let value = jQuery(this).data('value');
            var subcategory = '#sub-category-' + position;
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_remove_subcategories',
                    'security': scripts_vars.ajax_nonce,
                    'sub-category': value
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type === 'success') {
                        jQuery('body').find('#expcheck' + response.subcategories).removeAttr("checked");
                        _this.parents(subcategory).remove();
                        jQuery('#tu-instructor-search').submit();
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            }, true);
        });

        // Add reviews
        jQuery(document).on('click', '.tu-submit-reviews', function(e) {
            e.preventDefault();
            let profile_id = jQuery(this).data('profile_id');
            let booking_rating	= 0;
			if(jQuery("#tu_rating").val() != 0){
				booking_rating	= jQuery("#tu_rating").val();
			}

            let review_content	= jQuery("#tu-reviews-content").val();
            let termsconditions	= jQuery("#termsconditions").val();
            let action_type	    = jQuery("#booking_action_type").val();
            let post_id		= jQuery("#booking_order_id").val();
            let user_id		= jQuery("input[name=user_id]").val();
            jQuery('body').append(loader_html);

            jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: {
					'action': 'tu_booking_appointment',
					'security': scripts_vars.ajax_nonce,
					'profile_id': profile_id,
					'user_id': user_id,
					'postId': post_id,
					'action_type': action_type,
                    'rating': booking_rating,
                    'reviews_content': review_content,
                    'termsconditions': termsconditions,
				},
				dataType: "json",
				success: function (response) {
					jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        jQuery('.tuturn-popup').modal('hide');
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                        window.location.reload();
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
				}
			});
        });


        // Reviews description textarea counter
        jQuery(document).on('keyup', '#tu-reviews-content', function(e) {
            tuMaxLengthCounter(e.target.id)
        });

        //Reviews star rating
        jQuery(document).on('click', '.tu_stars li', function() {
            var _this = jQuery(this);
            var onStar = parseInt(_this.data('value'), 10);
            var stars = _this.parent().children('li.tu-star');

            for (var i = 0; i < stars.length; i++) {
                jQuery(stars[i]).removeClass('active');
            }

            for (var i = 0; i < onStar; i++) {
                jQuery(stars[i]).addClass('active');
            }

            jQuery('#tu_rating').val(onStar);
        });

        /* cancel appointment */
        jQuery(document).on('click', '.tu-cancel-appointment', function(e) {
            let data_time_slots = { time_slots: week_days_slots, week_days: scripts_vars.week_days };
            var load_generated_timeslot_popup = wp.template('tu-available-timeslots');
            load_generated_timeslot_popup = load_generated_timeslot_popup(data_time_slots);
            jQuery('.tu-appointment-content-area').html(load_generated_timeslot_popup);
        });

        /* remove weekdays item */
        jQuery(document).on('click', '.select2-remove-weekdays', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            _this.parents('li').remove();

            let selectedVal = weekDaysList.val();
            if (day) {
                const index = selectedVal.indexOf(day.toLowerCase());
                if (index > -1) {
                    selectedVal.splice(index, 1);
                }
                weekDaysList.val(selectedVal).trigger("change");
            }
        });

       
        /* delete day slot div */
        jQuery(document).on('click', '.tu-dayslots-del', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let day = _this.data('day');
            delete week_days_slots[day];
            _this.parents('.timeslot-parent-container').remove();
        });

        /* delete inner timeslot */
        jQuery(document).on('click', '.tu-timeslot-del', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let day = _this.data('day');
            let slotKey = _this.data('slot_key');
            let slotIndex = week_days_slots[day].findIndex(slot => slot.slot_key == slotKey);
            week_days_slots[day].splice(slotIndex, 1);
            _this.parents('li').remove();
        });

        /**
         * Load saved unavailable records
         */
        jQuery(document).on('click', '#tu-unavailable_dayslot-tab', function(e) {
            e.preventDefault();
            var load_unavailable_popup = wp.template('tu-unavailable_days');
            let data = { days_slots: unavailable_days_slots };
            load_unavailable_popup = load_unavailable_popup(data);
            jQuery('.tu-appointment-content-area').html(load_unavailable_popup);
        });

        /**
         * edit/load unavailable days
         * */
        jQuery(document).on('click', '.tu-unavailable-slots', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let is_unavailable_days = _this.attr("data-is_unavailable_days");
            let profile_id = _this.data("profile_id");
            var load_unavailable_popup = wp.template('tu-unavailable_date');
            load_unavailable_popup = load_unavailable_popup();
            jQuery('.tu-appointment-content-area').html(load_unavailable_popup);
            // call datepicker
            initDatePicker('tu-unavailable-picker', 'MMMM DD YYYY', false);
        });

        /**
         * ADD BOOKING/APPOINTMENT
         *   */

        /* enable/disable filter booking time */
        jQuery(document).on('change', 'select[name=tu_start_time]', function(e) {
            var endTimeSelect = jQuery(this).parents('#tu-booking-slots-filter-form').find('select[name=tu_end_time]');
            var startTimeVal = jQuery(this).val();
            endTimeSelect.find('option').removeAttr('disabled');
            endTimeSelect.find('option').each(function() {
                var current = jQuery(this).val();
                if (current <= startTimeVal) {
                    jQuery(this).attr('disabled', true);
                }
            });
        });

        /* show/hide someoneElse booking form */
        jQuery(document).on('click', '#tu-info-someone-else', function(e) {
            let someOneElse = jQuery('input[name="info_someone_else"]:checked').val();
            if (someOneElse === 'on') {
                jQuery(".tu-some-oneelse-form").show();
                select2placeholder();
            } else {
                jQuery(".tu-some-oneelse-form").hide();
            }
        });

        /**
         * Showing subjects slots
         */
         jQuery(document).on('click', '#tu-book-subject-appointment', function(e) {
            e.preventDefault();
            let _this           = jQuery(this);
            let currentuserID   = _this.data('student_id');
            let profile_id      = _this.data('instructor_profile_id');

            jQuery('body').append(loader_html);
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action'                : 'tuturn_display_subject_slots',
                    'security'              : scripts_vars.ajax_nonce,
                    'userId'                : currentuserID,
                    'instructor_profile_id' : profile_id,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        var url = window.location.pathname;
                        var params = { 'status': 'step0' };
                        var new_url = url + '?' + jQuery.param(params);
                        history.pushState({}, null, new_url);
                        let subjects = { subjects:response.subjects };

                        jQuery(".tu-serviceswizard").css("display", "flex");
                        var load_tu_book_appointment_step0 = wp.template('tu-book-appointment-step0');
                        load_tu_book_appointment_step0 = load_tu_book_appointment_step0(subjects);
                        jQuery('.tu-serviceswizard').html(load_tu_book_appointment_step0);
                        jQuery('.tu-bookingstep1').show();
                        jQuery(".tu-serviceswizard").css("display", "flex");

                    } else {
                        if (response.login) {
                            window.setTimeout(function() {
                                window.location.href = response.login;
                            }, 100);
                        } else {
                            stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                        }
                    }
                }
            });
         });


        /**
         * show book appointment section for students
         *  */
        jQuery(document).on('click', '#tu-book-appointment', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let currentuserID = _this.data('student_id');
            let profile_id = _this.data('instructor_profile_id');
            jQuery('body').append(loader_html);
            executeAjaxRequest({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_add_an_tution',
                    'security': scripts_vars.ajax_nonce,
                    'userId': currentuserID,
                    'profile_id': profile_id,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        /* set qeuryString */
                        var url = window.location.pathname;
                        var params = { 'status': 'step1' };
                        var new_url = url + '?' + jQuery.param(params);
                        history.pushState({}, null, new_url);

                        jQuery(".tu-serviceswizard").css("display", "flex");
                        var load_tu_book_appointment_step2 = wp.template('tu-book-appointment-step2');
                        load_tu_book_appointment_step2 = load_tu_book_appointment_step2();
                        jQuery('.tu-serviceswizard').html(load_tu_book_appointment_step2);
                        jQuery('.tu-bookingstep1').show();
                        jQuery('#tu-start-time-filter').select2();
                        jQuery('#tu-end-time-filter').select2();
                        initDatePicker('tu-startDate-picker');
                        initDatePicker('tu-endDate-picker');
                    } else {

                        if (response.login) {
                            window.setTimeout(function() {
                                window.location.href = response.login;
                            }, 100);
                        } else {
                            stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                        }
                    }
                }
            });
        });
        // show/hide password
        jQuery(document).on('click', '.tu-showpassword', function() {
            let passInput = jQuery("#tu-passwordinput");
            let passInput_icon = jQuery(".tu-showpassword i");
            if (passInput.attr('type') === 'password') {
                passInput.attr('type', 'text');

                jQuery(".tu-showpassword i").addClass("icon-eye");
                jQuery(".tu-showpassword i").removeClass("icon-eye-off");
            } else {
                passInput.attr('type', 'password');
                jQuery(".tu-showpassword i").addClass("icon-eye-off");
                jQuery(".tu-showpassword i").removeClass("icon-eye");
            }
        });

        /**
         * getting slots according to filter
         */
        jQuery(document).on('click', '#tu-filter-days-booking-slots', function(e) {
            e.preventDefault();
            let dataObj = {};
            let _this = jQuery(this);
            let instructor_profile_id = _this.data("instructor_profile_id");
            let student_id = _this.data("student_id");
            var filtered_data = jQuery("#tu-booking-slots-filter-form").serialize();
            dataObj = { 'filtered_data': filtered_data, 'instructor_profile_id': instructor_profile_id, 'student_id': student_id };
            jQuery('body').append(loader_html);
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_get_filtered_days_slots',
                    'security': scripts_vars.ajax_nonce,
                    'data': dataObj,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        let filtered_slots_details = {};
                        filtered_slots_details = { filtered_slots: response.filter_day_slots };
                        var tu_filtered_slots_by_days = wp.template('tu-book-appointment-filter');
                        tu_filtered_slots_by_days = tu_filtered_slots_by_days(filtered_slots_details);
                        jQuery('.tu-bookingstep1').hide();
                        jQuery('.tu-bookingstep2').html(tu_filtered_slots_by_days);
                        jQuery('.tu-bookingstep2').show();

                        initDatePicker('tu-startDate-picker');
                        initDatePicker('tu-endDate-picker');
                        jQuery('#tu-start-time-filter').select2();
                        jQuery('#tu-end-time-filter').select2();
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }

            });

        });

        /**
         * show appointment form for students
         *  */
        jQuery(document).on('click', '#tu-next-addbook-step2', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            var selectedSlots = jQuery("#tu-multicheck-appointment-form").serialize();
            let instructor_profile_id = _this.data("instructor_profile_id");
            let student_id = _this.data("student_id");

            /* add step in url */
            var url = window.location.pathname;
            var params = { 'status': 'step3' };
            var new_url = url + '?' + jQuery.param(params);
            history.pushState({}, null, new_url);
            jQuery('body').append(loader_html);
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_update_book_appointment_step2',
                    'security': scripts_vars.ajax_nonce,
                    'instructor_profile_id': instructor_profile_id,
                    'student_id': student_id,
                    'selectedSlots': selectedSlots,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        let book_student_detail = {};
                        book_student_detail = { student_detail: response.book_student_detail.student_detail, info_relation: response.book_student_detail.info_relation };
                        var tu_book_appointment_step3 = wp.template('tu-book-appointment-step3');
                        tu_book_appointment_step3 = tu_book_appointment_step3(book_student_detail);
                        jQuery('.tu-bookingstep2').hide();
                        jQuery('.tu-bookingstep3').html(tu_book_appointment_step3);
                        jQuery('.tu-bookingstep3').show();

                        jQuery('#tu-info-relations').select2();
                        updatePlaceholder();
                        jQuery('.tu-wizserviceslist').mCustomScrollbar();
                        if (response.book_student_detail.info_someone_else != 'on') {
                            jQuery(".tu-some-oneelse-form").hide();
                        }

                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        /* submit student information form */
        jQuery(document).on('click', '.tu-timeslots', function(e) {
            let timeslot_length = jQuery(this).parents('.tu-tutionslotslist').find('input[type=checkbox]:checked').length;
            jQuery(this).parents('.tu-booking-date-time').find('.selected-slotscount em').text(timeslot_length);
        });

        /* submit student information form */
        jQuery(document).on('click', '#tu-next-addbook-step3', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let dataObj = {};
            let instructor_profile_id = _this.data("instructor_profile_id");
            let student_id = _this.data("student_id");
            let dataArray = jQuery('#tu-book-student-form').serializeArray();
            jQuery(dataArray).each(function(i, field) {
                dataObj[field.name] = field.value;
            });
            dataObj['student_id'] = student_id;
            dataObj['instructor_profile_id'] = instructor_profile_id;

            /* add step in url */
            var url = window.location.pathname;
            var params = { 'status': 'step4' };
            var new_url = url + '?' + jQuery.param(params);
            history.pushState({}, null, new_url);

            if (dataObj.info_someone_else == 'on' && (dataObj.info_email.trim() == '' || !ValidEmail(dataObj.info_email.trim()))) {
                stickyAlert('Validation Error', 'Please add the valid email address', { classList: 'danger', autoclose: 3000 });
                return;
            }
            jQuery('body').append(loader_html);
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_send_booked_form_information',
                    'security': scripts_vars.ajax_nonce,
                    'dataObj': dataObj,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        let booke_student_detail = { booked_information: response.booked_student_detail,booking_option: response.booking_option };

                        var tu_book_appointment_step4 = wp.template('tu-book-appointment-step4');
                        tu_book_appointment_step4 = tu_book_appointment_step4(booke_student_detail);
                        jQuery('.tu-bookingstep3').hide();
                        jQuery('.tu-bookingstep4').html(tu_book_appointment_step4);
                        jQuery('.tu-bookingstep4').show();
                        jQuery('.tu-wizserviceslist').mCustomScrollbar();
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });

        });

        jQuery(document).on('click', '.tu-bookingbackstep', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let current_step = _this.data('current_step');
            let previous_step = _this.data('previous_step');
            jQuery('.tu-bookingstep' + current_step).hide();
            jQuery('.tu-bookingstep' + previous_step).show();
        });


        /**
         * CHECKOUT
         */
        jQuery(document).on('click', '#tu-next-addbook-checkout', function(e) {
            e.preventDefault();
            let this_ = jQuery(this);
            let instructor_id = this_.data("service_author");
            let student_id = this_.data("loggedin_user");
            let data_type = this_.data("type");
            jQuery('body').append(loader_html);
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_service_checkout',
                    'security': scripts_vars.ajax_nonce,
                    'studentId': student_id,
                    'instructor_id': instructor_id,
                    'data_type': data_type,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        window.location.replace(response.checkout_url);
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        /**
         * Complete booking without checkout
         */
        jQuery(document).on('click', '#tu-next-complete-booking', function(e) {
            e.preventDefault();
            let this_ = jQuery(this);
            let profile_id 	    = jQuery('#profile_id').val();
            let instructor_id   = this_.data("service_author");
            let student_id      = this_.data("loggedin_user");
            let data_type       = this_.data("type");

            jQuery('body').append(loader_html);
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_service_complete_booking',
                    'security': scripts_vars.ajax_nonce,
                    'studentId': student_id,
                    'instructor_id': instructor_id,
                    'data_type': data_type,
                    'profile_id': profile_id,		
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        window.location.replace(response.checkout_url);
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });


        /**
         * Load data on timeslot tab click
         */
        jQuery(document).on('click', '#tu-timeslot-tab', function(e) {
            e.preventDefault();
            let data_time_slots = { time_slots: week_days_slots, week_days: scripts_vars.week_days };
            var load_generated_timeslot_popup = wp.template('tu-available-timeslots');
            load_generated_timeslot_popup = load_generated_timeslot_popup(data_time_slots);
            jQuery('.tu-appointment-content-area').html(load_generated_timeslot_popup);
            sortableListItems('time_accordionwrapper');            
        });

        /* disable old time slots for instructor */
        jQuery(document).on('change', 'select[name=tu_appointment_starttime]', function(e) {
            var endTimeSelect = jQuery(this).parents('.tu-timeslotform').find('select[name=tu_appointment_endtime]');
            var startTimeVal = jQuery(this).val();
            endTimeSelect.find('option').removeAttr('disabled');
            endTimeSelect.find('option').each(function() {
                var current = jQuery(this).val();
                if (current <= startTimeVal) {
                    jQuery(this).attr('disabled', true);
                }
            });
        });

        /**
         * Open edit instructor booking main popup 
         */
        jQuery(document).on('click', '.tu-add-appointment', function(e) {
            e.preventDefault();
            let this_ = jQuery(this);
            let is_timeslot = this_.data("is_timeslot");
            let profile_id = this_.data("profile_id");
            let user_id = this_.data("user_id");
            //if data already saved
            week_days_slots = {};
            unavailable_days_slots = [];
            if (is_timeslot == 1) {
                jQuery('body').append(loader_html);
                jQuery.ajax({
                    type: "POST",
                    url: scripts_vars.ajaxurl,
                    data: {
                        'action': 'tuturn_get_instructor_appointment_slots',
                        'security': scripts_vars.ajax_nonce,
                        'profile_id': profile_id,
                        'user_id': user_id,
                    },
                    dataType: "json",
                    success: function(response) {
                        jQuery('body').find('.tuturn-preloader-section').remove();
                        if (response.type === 'success') {
                            if (response.week_days_slots) {
                                let days = Object.keys(response.week_days_slots);
                                days.forEach(day => {
                                    week_days_slots[day] = response.week_days_slots[day];
                                })
                            }
                            let data_time_slots = { time_slots: week_days_slots, week_days: response.week_days};
                            var load_appointment_popup = wp.template('tu-add-appointment');
                            load_appointment_popup = load_appointment_popup();
                            jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_appointment_popup);
                            jQuery('#tuturn-modal-popup').modal('show');

                            jQuery(".tuturn-popup").addClass("tu-appointment-popup");
                            var load_generated_timeslot_popup = wp.template('tu-available-timeslots');
                            load_generated_timeslot_popup = load_generated_timeslot_popup(data_time_slots);
                            jQuery('#timeslot').append(load_generated_timeslot_popup);

                            sortableListItems('time_accordionwrapper');
                        } else {
                            stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                        }
                    }
                });
            } else {
                let data_time_slots = { time_slots: {} };
                var load_appointment_popup = wp.template('tu-add-appointment');
                load_appointment_popup = load_appointment_popup();
                jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_appointment_popup);
                var load_generated_timeslot_popup = wp.template('tu-available-timeslots');
                load_generated_timeslot_popup = load_generated_timeslot_popup(data_time_slots);
                jQuery('.tu-appointment-content-area').html(load_generated_timeslot_popup);
                jQuery('#tuturn-modal-popup').modal('show');
                sortableListItems('time_accordionwrapper');
            }
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_get_tutor_unavailable_days',
                    'security': scripts_vars.ajax_nonce,
                    'profile_id': profile_id,
                    'user_id': user_id,
                },
                dataType: "json",
                success: function(response) {
                    jQuery('body').find('.tuturn-preloader-section').remove();
                    if (response.type == 'success') {
                        let records = response.unavailable_days_slots;
                        if (records.length) {
                            records.forEach(slot => {
                                unavailable_days_slots.push(slot);
                            });
                        }
                    }
                }
            });
        });

        /**
         * button cancel appointment
         *   */
        jQuery(document).on('click', '.tu-cancel-appointment', function(e) {
            e.preventDefault();
            let data_time_slots = { time_slots: week_days_slots, week_days: scripts_vars.week_days };
            var cancel_timeslot_popup = wp.template('tu-available-timeslots');
            cancel_timeslot_popup = cancel_timeslot_popup(data_time_slots);
            jQuery('.tu-appointment-content-area').html(cancel_timeslot_popup);
        });

        /**
         * generate time slots 
         * */
        jQuery(document).on('click', '#tu-generate-timeslots', function(e) {
            var _this = jQuery(this);
            let weekdays = [];
            let dataObj = {};
            let dataArray = jQuery('#tu-add-timeslots').serializeArray();

            jQuery(dataArray).each(function(i, field) {
                dataObj[field.name] = field.value;
            });

            //week days add in array.
            let week_days = jQuery('#tu-add-timeslots .tu_selected_weekdays');
            week_days.each(function(i, field) {
                let id = jQuery(field).data('slug');
                weekdays.push(id);
            });
            let profileId = _this.data('profile_id');
            let userId = _this.data('user_id');
            _this.attr("disabled", true);
            _this.addClass("tu-btnloader");
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_generate_appointment_timeslots',
                    'security': scripts_vars.ajax_nonce,
                    'profileId': profileId,
                    'userId': userId,
                    'weekdays_': weekdays,
                    'week_days_slots': week_days_slots,
                    'data': dataObj
                },
                dataType: "json",
                success: function(response) {
                    _this.attr("disabled", false);
                    _this.removeClass("tu-btnloader");
                    if (response.type === 'success') {
                        var load_appointment_popup = wp.template('tu-add-appointment');
                        load_appointment_popup = load_appointment_popup();
                        jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_appointment_popup);
                        console.log('load_appointment_popup', load_appointment_popup)
                        jQuery('#tuturn-modal-popup').modal('show');

                        if (response.week_days_slots) {
                            let days = Object.keys(response.week_days_slots);
                            days.forEach(day => {
                                week_days_slots[day] = response.week_days_slots[day];
                            })
                        }

                        let data_time_slots = { time_slots: week_days_slots, week_days: response.week_days };
                        var load_generated_timeslot_popup = wp.template('tu-available-timeslots');
                        load_generated_timeslot_popup = load_generated_timeslot_popup(data_time_slots);
                        jQuery('#timeslot').append(load_generated_timeslot_popup);
                        sortableListItems('time_accordionwrapper');
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        /**
         * Open edit instructor booking timeslot popup form
         */
        var weekDaysList = '';
        jQuery(document).on('click', '.tu-add-appointment-timeslot', function(e) {
            e.preventDefault();
            let data = { weekDays: scripts_vars.week_days, selectedDays: Object.keys(week_days_slots) };
            var load_timeslot_popup = wp.template('tu-edit-timeslots');
            load_timeslot_popup = load_timeslot_popup(data);
            jQuery('.tu-appointment-content-area').html(load_timeslot_popup);

            weekDaysList = jQuery('#tu-weekdays').select2({ closeOnSelect: false });
            //dropdownCheckboxUnselectedTabs('weekdays');
            jQuery('#start-time').select2({ dropdownParent: jQuery(".modal-content") });
            jQuery('#end-time').select2({ dropdownParent: jQuery(".modal-content") });
            jQuery('#inter-duration').select2({ dropdownParent: jQuery(".modal-content") });
            jQuery('#apintment-duration').select2({ dropdownParent: jQuery(".modal-content") });
            openSelect2_weekdays('weekdays');
        });

        /**
         * save generated time slot 
         * */
        jQuery(document).on('click', '#tu-save-timeslots-btn', function(e) {
            e.preventDefault();
            let this_ = jQuery(this);
            let profile_id = this_.data("profile_id");
            let user_id = this_.data("user_id");
            let dataSlotTimeArray = jQuery('#tu-save-timeslots').serialize();

            this_.attr("disabled", true);
            this_.addClass("tu-btnloader");
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_save_appointment_timeslots',
                    'security': scripts_vars.ajax_nonce,
                    'profileId': profile_id,
                    'userId': user_id,
                    'data': dataSlotTimeArray,
                },
                dataType: "json",
                success: function(response) {
                    this_.attr("disabled", false);
                    this_.removeClass("tu-btnloader");
                    if (response.type === 'success') {
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                        window.location.reload();
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        /**
         * generate unavailable days slot 
         * */
        jQuery(document).on('click', '#tu-generate-unavailable-days', function(e) {
            e.preventDefault();
            let this_ = jQuery(this);
            let profile_id = this_.data("profile_id");
            let user_id = this_.data("user_id");
            let selectedDate = jQuery('.tu-unavailable-picker').val();
            this_.attr("disabled", true);
            this_.addClass("tu-btnloader");
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_generate_instructor_unavailable_days',
                    'security': scripts_vars.ajax_nonce,
                    'profileId': profile_id,
                    'userId': user_id,
                    'selectedDate': selectedDate,
                },
                dataType: "json",
                success: function(response) {
                    this_.attr("disabled", false);
                    this_.removeClass("tu-btnloader");
                    if (response.type === 'success') {
                        let unavailable_slots = {};
                        if (response.booking_unavailable.length) {
                            unavailable_days_slots.push(response.booking_unavailable[0]);
                        }
                        unavailable_slots = { days_slots: unavailable_days_slots };
                        var load_unavailable_days_popup = wp.template('tu-unavailable_days');
                        load_unavailable_days_popup = load_unavailable_days_popup(unavailable_slots);
                        jQuery('.tu-appointment-content-area').html(load_unavailable_days_popup);
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        //Save Unavailable days
        jQuery(document).on('click', '#tu-save-unavailable-days', function(e) {
            e.preventDefault();
            let this_ = jQuery(this);
            let profile_id = this_.data("profile_id");
            let user_id = this_.data("user_id");
            let dataArray = jQuery('#tu-save-unavailable-days-form').serialize();

            this_.attr("disabled", true);
            this_.addClass("tu-btnloader");
            jQuery.ajax({
                type: "POST",
                url: scripts_vars.ajaxurl,
                data: {
                    'action': 'tuturn_save_instructor_unavailable_days',
                    'security': scripts_vars.ajax_nonce,
                    'profileId': profile_id,
                    'userId': user_id,
                    'data': dataArray,
                },
                dataType: "json",
                success: function(response) {
                    this_.attr("disabled", false);
                    this_.removeClass("tu-btnloader");
                    if (response.type == 'success') {
                        stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                        window.location.reload();
                    } else {
                        stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                    }
                }
            });
        });

        /* delete Unavailable dayslot */
        jQuery(document).on('click', '.tu-unavailalbe-delete-slot', function(e) {
            e.preventDefault();
            let _this = jQuery(this);
            let slotIndex = _this.data('index');
            unavailable_days_slots.splice(slotIndex, 1)
            _this.parents('li').remove();
        });

    });

    /* active class toggle on hover */
    jQuery('.tu-eduplatform').on('mouseover', function() {
        var tu_className = jQuery(this).attr('class');
        var tu_hoverClassName = tu_className.split(' ')[0]
        var tu_ActiveClass = 'tu-activebox';
        jQuery('.' + tu_hoverClassName).removeClass(tu_ActiveClass)
        jQuery(this).addClass(tu_ActiveClass);
    });

    // Service detail sync slider

    jQuery(window).bind("resize", function() {
        var tu_splide = document.getElementById('tu_splide')
        if (tu_splide != null) {
            var secondarySlider = new Splide('#tu_splidev2', {
                direction: scripts_vars.direction,
                height: '100%',
                fixedWidth: 100,
                fixedHeight: 100,
                isNavigation: true,
                gap: 10,
                pagination: false,
                arrows: true,
                focus: 'center',
                updateOnMove: true,
                rewind: false,

            }).mount();
            var primarySlider = new Splide('#tu_splide', {
                type: 'fade',
                direction: scripts_vars.direction,
                pagination: false,
                cover: true,
                arrows: false,
                breakpoints: {
                    1199: {
                        pagination: false,
                        autoplay: true,
                    },
                    767: {
                        pagination: true,
                    },
                },

            })
            primarySlider.sync(secondarySlider).mount();
        }
    }).trigger("resize");

})(jQuery);

/* add selection tabs user select dropdown */
function dropdownCheckboxUnselectedTabs(name) {
    jQuery('#tu-' + name).on('select2:unselect', function(e) {
        var data = e.params.data;
        let verifySelection = jQuery('#tu_wrappersortable').find('#tu-inner-li-' + data.id);
        if (verifySelection.length) {
            verifySelection.remove();
        }
    });
}

/* add weekdays tabs */
function openSelect2_weekdays(name) {
    jQuery('#tu-' + name).on('select2:select', function(e) {
        var data = e.params.data;
        var html = '';
        let verifySelection = jQuery('#tu_wrappersortable').find('#tu-inner-li-' + data.id);
        if (!verifySelection.length) {
            html = '<li id="tu-inner-li-' + data.id + '"><span>' + data.text;
            html += '<a href="javascript:void(0);" data-weekdays="' + data.id + '" class="select2-remove-weekdays"><i class="icon icon-x"></i></a></span>';
            html += '<input type="hidden" name="weekdays[]" class="tu_selected_weekdays" data-slug="' + data.id + '" value="' + data.id + '"></li>';
            jQuery('#tu_wrappersortable').append(html);
        }
    });
}

// Execute ajax resuests 
function executeAjaxRequest(custom_ajax) {
    if (jQuery(custom_ajax['data']).length) {
        let custom_ajax_data = custom_ajax['data'];
        custom_ajax_data.app_type = 'web';
        custom_ajax['data'] = custom_ajax_data;
    }
    jQuery.ajax(custom_ajax);
}
//Number format
function numberFormat(number) {
    return ('0' + number).slice(-2);
}

//Remove param from URL
function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
    }

    return rtn;
}

select2placeholder();

function select2placeholder() {
    jQuery("[data-placeholderinput]").each(function(item) {
        var data_placeholder = jQuery("[data-placeholderinput]")[item];
        var tu_id = jQuery(data_placeholder).attr("id");
        var tu_placeholder = jQuery(data_placeholder).attr("data-placeholderinput");
        jQuery("#" + tu_id + ':not([multiple])').on("select2:open", function(e) {
            jQuery(".select2-search--dropdown input.select2-search__field").prop("placeholder", tu_placeholder);
        });
        jQuery("#" + tu_id + '[multiple]').on("select2:open", function(e) {
            var _this = jQuery(this)
            jQuery(document).on('click', '.select2-results__option', function(e) {
                _this.next().find('.select2-search__field').prop("placeholder", tu_placeholder);
            })
        });
    })
}

/**
 * 
 * Character count
 */
function tuMaxLengthCounter(id = '', currentCharClass = '.tu_current_comment', maxCharClass = '.tu_maximum_comment') {
    if (id) {
        var maxCharCount = jQuery('#' + id).val().length
        var currentChar = jQuery('#' + id).parents('.tu-message-text').find(currentCharClass)
        var maximumChar = jQuery('#' + id).parents('.tu-message-text').find(maxCharClass)
        var maxCharLength = jQuery('#' + id).attr('maxlength');
        if (maxCharLength) {
            var changeColor = 0.75 * maxCharLength;
            currentChar.text(maxCharCount);

            if (maxCharCount > changeColor && maxCharCount < maxCharLength) {
                currentChar.css('color', '#FF4500');
                currentChar.css('fontWeight', 'bold');
            } else if (maxCharCount >= maxCharLength) {
                currentChar.css('color', '#B22222');
                currentChar.css('fontWeight', 'bold');
            } else {
                var char_color = maximumChar.css('color');
                var char_font = maximumChar.css('fontWeight');
                currentChar.css('color', char_color);
                currentChar.css('fontWeight', char_font);
            }
        }
    }
}

/**
 * Update placeholder
 */
function updatePlaceholder() {
    var inputs = jQuery(".tu-form-input[value]");
    inputs.each(function() {
        var value = jQuery(this).attr('value');
        if (value.length > 0) {
            jQuery(this).siblings(".tu-placeholder").hide();
        } else if (value.length == 0) {
            jQuery(this).siblings(".tu-placeholder").show();
        }
    });
}

/**
 * Alert the notification
 */
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
    let close_text  = '';
    if(data.autoclose){
        close_text = 'close|' + data.autoclose
    }
    jQuery.confirm({
        icon: $icon,
        closeIcon: true,
        theme: 'modern',
        animation: 'scale',
        type: $class, //red, green, dark, orange
        title: $title,
        content: $message,
        autoClose: close_text,
        buttons: {
            close: {
                text: scripts_vars.close_text,
                btnClass: 'tu-sticky-alert'
            },
        }
    });
}

//Check valid email
function ValidEmail(email) {
    var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    if (email.match(validRegex)) {
        return true;
    } else {
        return false;
    }
}

/*
 * google login/signup
 *
 */
var google_signin_btn = document.getElementById('google_signin');
if (google_signin_btn != null) {
    var googleUser = {};
    var auth2 = '';
    var userProfile = '';
    let gClientId = scripts_vars.gclient_id;
    //Google connect

    if (gClientId != '') {
        if (navigator.cookieEnabled) {

            // Google sigin response decode
            function decodeJwtResponse(token) {
                var base64Url = token.split('.')[1];
                var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));

                return JSON.parse(jsonPayload);
            }

            //Google sigin callback
            function handleCredentialResponse(response) {
                const responsePayload = decodeJwtResponse(response.credential);
                jQuery('body').append(loader_html);
                let login_type = 'google';
                let picture = responsePayload.picture;
                let email = responsePayload.email;
                let id = responsePayload.sub;
                let name = responsePayload.name;

                let dataObj = {};
                dataObj = { login_type: login_type, picture: picture, email: email, id: id, name: name };
                jQuery.ajax({
                    type: "POST",
                    url: scripts_vars.ajaxurl,
                    data: {
                        'action': 'tuturn_user_social_login',
                        'security': scripts_vars.ajax_nonce,
                        'data': dataObj
                    },
                    dataType: "json",
                    success: function(response) {
                        jQuery('body').find('.tuturn-preloader-section').remove();
                        if (response.type === 'success') {
                            if (response.cooseUserType === 'model') {
                                let userDetail = { userData: JSON.stringify(response.userData) };
                                var load_usertype_popup = wp.template('tu-social-user-type');
                                load_usertype_popup = load_usertype_popup(userDetail);
                                jQuery('#tuturn-modal-popup #tuturn-model-body').html(load_usertype_popup);
                                jQuery('#tuturn-modal-popup').modal('show');
                            } else {
                                stickyAlert(response.title, response.message, { classList: 'success', autoclose: 3000 });
                                window.location.replace(response.redirect);
                            }
                        } else {
                            stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 3000 });
                        }
                    }
                });
            }

            //Google sigin button load
            window.onload = function() {
                google.accounts.id.initialize({
                    client_id: gClientId,
                    ux_mode: 'popup',
                    cancel_on_tap_outside: false,
                    callback: handleCredentialResponse
                });
                google.accounts.id.renderButton(
                    document.getElementById("google_signin"), {
                        type: 'standard',
                        theme: "outline",
                        size: "large",
                        logo_alignment: 'center',
                        width: '400px',
                        text: 'signin_with',
                        shape: 'rectangular'
                    },
                );
                google.accounts.id.prompt();
            };
        }
    }
}

// Loade More
let classes = [
    // '.tu-categoriesfilter',
    '.tu-subcategoriesfilter',
    '.tu-commenteditem',
    '.tu-filterselect li',
    '.tu-commentarea'
];
for (let i = 0; i < classes.length; ++i) {
    if (classes[i].length <= 5) {
        jQuery(".tu-show_more").hide();
    } else if (classes[i].length >= 5) {

        //jQuery(".tu-categoriesfilter li:nth-child(n+6)").hide();
        jQuery(".tu-categorieslist li:nth-child(n+6)").hide();
        jQuery(".tu-collapseitem .tu-commenteditem:nth-child(n+6)").hide();
        jQuery(".tu-filterselect li:nth-child(n+6)").hide();
    }
}

/* date picker */
function initDatePicker(selector, dateformat = "DD-MM-YYYY", single_mode = true) {

    let date_fields = jQuery('.' + selector);
    date_fields.each(function(i, field) {
        let today = moment();
        today.subtract(1, 'days').format(dateformat);
        const disallowedDates = [
            ['01-01-2022', today]
        ]
        if (field !== null) {
            new Litepicker({
                element: field,
                singleMode: single_mode,
                format: dateformat,
                autoRefres: true,
                selectForward: false,
                mobileFriendly: true,
                autoRefresh: true,
                allowRepick: true,
                lockDays: disallowedDates,
                // parentEl: '.tu-calendar',
                setup: (picker) => {
                    picker.on('hide', (el) => {});
                    picker.on('show', (el) => {
                        if (el.value == '') {
                            picker.clearSelection();
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



function downloadAttachment(attachment_id,post_id) {

    jQuery('body').append(loader_html);
    jQuery.ajax({
        type: "POST",
        url: scripts_vars.ajaxurl,
        data: {
            action: 'tuturn_download_single_attachment',
            attachment_id: attachment_id,
            post_id : post_id
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
                    stickyAlert(response.title, response.message, { classList: 'danger', autoclose: 5000 });
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

function tutun_validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test( $email );
}

