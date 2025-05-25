! function($, elementor){
	"use strict";
	var modules = elementor.modules;
	var presetSelect = modules.controls.Select.extend({
		isPresetControl: function() {
			return "thegem_elementor_preset" === this.model.get("name") && -1 !== this.getWidgetName().indexOf("thegem-")
		},
		onReady: function() {
			window.thegemWidgets = window.thegemWidgets || {};
			this.loadPresets();
		},
		resetStyles: function() {
			/*console.log(this.model.get('default'));
			this.container.view.allowRender = false;
			var e = elementor.getPanelView().getCurrentPageView().getOption("editedElementView");
			$e.run("document/elements/reset-style", {
				container: e.getContainer()
			});
			this.container.view.allowRender = true;*/
		},
		getElementSettingsModel: function() {
			return this.container.settings
		},
		getWidgetName: function() {
			return this.getElementSettingsModel().get("widgetType")
		},
		isPresetDataLoaded: function() {
			return !_.isUndefined(window.thegemWidgets[this.getWidgetName()])
		},
		loadPresets: function() {
			var item = this;
			this.isPresetControl() && !this.isPresetDataLoaded() && this.getWidgetName() && $.post(thegemElementor.ajaxUrl, {
				action: 'thegem_elementor_get_preset_settings',
				widget: this.getWidgetName(),
				secret: thegemElementor.secret
			}).done(function(response) {
				response.success && item.setPresetData(response.data)
			})
		},
		setPresetData: function(data) {
			window.thegemWidgets[this.getWidgetName()] = data
		},
		getPresetData: function() {
			return _.isUndefined(window.thegemWidgets) ? {} : window.thegemWidgets[this.getWidgetName()] || {}
		},
		onBaseInputChange: function(e) {
			//this.resetStyles();
			if (this.constructor.__super__.onBaseInputChange.apply(this, arguments), this.isPresetControl() && e.currentTarget.value) {
				e.stopPropagation();
				var data = this.getPresetData();
				/*_.isUndefined(data[e.currentTarget.value]) || */this.applyPresetData(data[e.currentTarget.value]);
			}
		},
		applyPresetData: function(data) {
			var widgetControls = this.getElementSettingsModel().controls,
				item = this,
				collection = {};
			_.each(widgetControls, function(control, controlName) {
				if (item.model.get('name') !== controlName && !_.isUndefined(data) && !_.isUndefined(data[controlName])) {
					if (control.is_repeater) {
						var repeater = item.getElementSettingsModel().get(controlName).clone();
						repeater.each(function(subItem, subItemIndex) {
							_.isUndefined(data[controlName][subItemIndex]) || _.each(subItem.controls, function(subControl, subControlName) {
								item.isStyleTransferControl(subControl) && repeater.at(subItemIndex).set(subControlName, data[controlName][subItemIndex][subControlName])
							})
						});
						collection[controlName] = repeater;
					}
					collection[controlName] = data[controlName];
				} else {
					if(item.isStyleTransferControl(control)) {
						collection[controlName] = control.default;
					}
				}
			});
			this.getElementSettingsModel().setExternalChange(collection);
			this.container.view.render();
			this.container.view.renderOnChange();
		},
		isStyleTransferControl: function(control) {
			return !_.isUndefined(control.style_transfer) && control.style_transfer ? control.style_transfer : "content" !== control.tab || control.selectors || control.prefix_class
		}
	});
	elementor.addControlView("select", presetSelect);

	var thegemOldSelect2onBaseInputChange =  modules.controls.Select2.prototype.onBaseInputChange;
	var select2TemplateEditLink = modules.controls.Select2.extend({
		isThegemTemplateSelect: function() {
			return !!this.model.get("thegem_template_link")
		},
		onReady: function() {
			this.thegemTemplateUpdateEditLink();
		},
		onBaseInputChange: function(e) {
			if(this.isThegemTemplateSelect()) {
				if (this.constructor.__super__.onBaseInputChange.apply(this, arguments), this.isThegemTemplateSelect()) {
					e.stopPropagation();
					this.thegemTemplateUpdateEditLink();
				}
			} else {
				thegemOldSelect2onBaseInputChange.apply(this, arguments);
			}
		},
		thegemTemplateUpdateEditLink: function() {
			if(this.isThegemTemplateSelect()) {
				var control = this;
				//var widget = elementor.constructor.getComponent(this._parent.model.id)
				let $inputWrapper = $('.elementor-control-input-wrapper', control.$el);
				let value = control.getCurrentValue() ? control.getCurrentValue() : 0;
				let link = thegemElementor.templateCreateLink;
				let templateType = 'loop-item';
				if(typeof this.model.get('thegem_template_type') !== 'undefined') {
					templateType = this.model.get('thegem_template_type');
				}
				link = link.replace('{type}', templateType);
				let label = thegemElementor.templateCreateLinkText;
				let $thegemTemplateEditLinkWrapper = $('.thegem-template-edit-link-wrapper', control.$el);
				if(!$thegemTemplateEditLinkWrapper.length) {
					$thegemTemplateEditLinkWrapper = $('<div class="thegem-template-edit-link-wrapper"><a class="thegem-template-edit-link elementor-button" href="#" target="_blank">' + label + '</a></div>').insertAfter($inputWrapper);
				}
				let $thegemTemplateEditLink = $('.thegem-template-edit-link', $thegemTemplateEditLinkWrapper);
				if($('.thegem-template-import-link', $thegemTemplateEditLinkWrapper).length) {
					$('.thegem-template-import-link', $thegemTemplateEditLinkWrapper).remove();
				}
				$thegemTemplateEditLink.off('click.thegem-template-edit-open');
				$thegemTemplateEditLink.off('click.thegem-template-create-open');
				if(value) {
					link = thegemElementor.templateEditLinkFormat.replace('{postID}', value);
					label = thegemElementor.templateEditLinkText;
					$thegemTemplateEditLink.on('click.thegem-template-edit-open', function(e) {
						e.preventDefault();
						//var editTemplateWindow = window.open($(this).attr('href'), '_blank');
						const a = document.createElement("a");
						a.setAttribute('href', $(this).attr('href'));
						a.setAttribute('target', '_blank');
						a.click();
						setInterval(function() {
							let lastUpdated = localStorage.getItem('thegemTemplateloopItemUpdated_' + value);
							if(lastUpdated) {
								localStorage.removeItem('thegemTemplateloopItemUpdated_' + value);
								control.container.view.render();
								control.container.view.renderOnChange();
							}
						}, 1000);
					});
				} else {
					let importLink = thegemElementor.templateImportLink;
					importLink = importLink.replace('{type}', templateType);
					let $thegemTemplateImportLink = $('<a class="thegem-template-import-link elementor-button" href="' + importLink + '" target="_blank">' + thegemElementor.templateImportLinkText + '</a>')
					if(templateType !== 'content') {
						$thegemTemplateImportLink.insertAfter($thegemTemplateEditLink);
					}
					$thegemTemplateEditLink.add($thegemTemplateImportLink).on('click.thegem-template-create-open', function(e) {
						e.preventDefault();
						var newTemplateWindow = window.open($(this).attr('href'), '_blank');
						localStorage.setItem('thegemTemplateloopItemCreateWindow', window.location.href);
						var thegemTemplateloopItemNewIDCheckInterval = setInterval(function() {
							var newTemplateID = localStorage.getItem('thegemTemplateloopItemNewID');
							if(newTemplateID) {
								localStorage.removeItem('thegemTemplateloopItemNewID');
								let $select = $('select', control.$el);
								$select.append('<option value="' + newTemplateID + '" selected="selected"> ID = ' + newTemplateID + '</option>');
								$select.trigger('change').select2();
								control.container.view.render();
								control.container.view.renderOnChange();
								clearInterval(thegemTemplateloopItemNewIDCheckInterval);
								setInterval(function() {
									let lastUpdated = localStorage.getItem('thegemTemplateloopItemUpdated_' + newTemplateID);
									if(lastUpdated) {
										localStorage.removeItem('thegemTemplateloopItemUpdated_' + newTemplateID);
										control.container.view.render();
										control.container.view.renderOnChange();
									}
								}, 1000);
							}
						}, 1000);
					});
				}
				$thegemTemplateEditLink.attr('href', link);
				$thegemTemplateEditLink.text(label);
			}
		}
	});
	elementor.addControlView("select2", select2TemplateEditLink);

	elementor.on('document:loaded', function() {
		const queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);
		var thegemTemplateloopItemCreateWindow = localStorage.getItem('thegemTemplateloopItemCreateWindow');
		if(window.opener && window.opener.location.href === thegemTemplateloopItemCreateWindow) {
			localStorage.setItem('thegemTemplateloopItemNewID', elementor.config.initial_document.id);
			localStorage.removeItem('thegemTemplateloopItemCreateWindow');
		}
		elementor.channels.editor.on('TheGemApplyPreview', saveAndReload);
		elementor.saver.on('after:save', function(data) {
			if(parseInt(data.config.document.revisions.current_id) === parseInt(elementor.config.initial_document.id)) {
				const queryString = window.location.search;
				const urlParams = new URLSearchParams(queryString);
				if((urlParams.get('thegemLoopItemCheckUpdate') || (window.opener && window.opener.location.href === thegemTemplateloopItemCreateWindow)) && urlParams.get('post')) {
					localStorage.setItem('thegemTemplateloopItemUpdated_' + urlParams.get('post'), true);
					var closePopup = elementorCommon.dialogsManager.createWidget('confirm', {
						id: 'thegem-template-save-on-close',
						message: thegemElementor.templateClosePopupText,
						position: {
							my: 'center center',
							at: 'center center'
						},
						strings: {
							confirm: thegemElementor.templateClosePopupCloseText,
							cancel: thegemElementor.templateClosePopupCancelText
						},
						onConfirm: function onConfirm() {
							window.close();
							/*if(window.opener) {
								window.opener.postMessage('thegemReturnToPreviousTab', '*');
								window.close();
							}*/
						}
					});
					closePopup.show();
				}
			}
		});
		window.addEventListener('message', function(event) {
			if (event.data === 'thegemReturnToPreviousTab') {
				window.focus();
			}
		}, false);
		if((urlParams.get('thegemLoopItemCheckUpdate') || (window.opener && window.opener.location.href === thegemTemplateloopItemCreateWindow)) && urlParams.get('post')) {
			localStorage.setItem('thegemTemplateloopItemUpdated_' + urlParams.get('post'), true);
			var closePopup = elementorCommon.dialogsManager.createWidget('confirm', {
				id: 'thegem-template-save-on-close',
				message: thegemElementor.templateClosePopupText,
				position: {
					my: 'center center',
					at: 'center center'
				},
				strings: {
					confirm: thegemElementor.templateClosePopupCloseText,
					cancel: thegemElementor.templateClosePopupCancelText
				},
				onConfirm: function onConfirm() {
					window.close();
				}
			});
			closePopup.show();
		}

	});

	function saveAndReload() {
		$e.run('document/save/auto', {
			force: true,
			onSuccess: () => {
				elementor.dynamicTags.cleanCache();
				const isInitialDocument = elementor.config.initial_document.id === elementor.documents.getCurrentId();
				if (isInitialDocument) {
					elementor.reloadPreview();
				} else {
					$e.internal('editor/documents/attach-preview');
				}
			}
		});
	}

}(window.jQuery, window.elementor);