/* global wp, _, wppopups_admin, jconfirm, wpCookies, Choices, List */

;(function($) {

	'use strict';

	// Global settings access.
	var s;

	// Admin object.
	var WPPopupsAdmin = {

		// Settings.
		settings: {
			iconActivate: '<i class="fa fa-toggle-on fa-flip-horizontal" aria-hidden="true"></i>',
			iconDeactivate: '<i class="fa fa-toggle-on" aria-hidden="true"></i>',
			iconInstall: '<i class="fa fa-cloud-download" aria-hidden="true"></i>',
			iconSpinner: '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>',
			mediaFrame: false
		},

		/**
		 * Start the engine.
		 *
		 * @since 2.0.0
		 */
		init: function() {

			// Settings shortcut.
			s = this.settings;

			// Document ready.
			$( document ).ready( WPPopupsAdmin.ready );

			// Popups Overview.
			WPPopupsAdmin.initPopupOverview();

			// Welcome activation.
			WPPopupsAdmin.initWelcome();

			// Addons List.
			WPPopupsAdmin.initAddons();

			// Settings.
			WPPopupsAdmin.initSettings();

			// Tools.
			WPPopupsAdmin.initTools();
		},

		/**
		 * Document ready.
		 *
		 * @since 2.0.0
		 */
		ready: function() {

			// To prevent jumping (since WP core moves the notices with js),
			// they are hidden initally with CSS, then revealed below with JS,
			// which runs after they have been moved.
			$( '.notice' ).show();

			// If there are screen options we have to move them.
			$( '#screen-meta-links, #screen-meta' ).prependTo( '#wppopups-header-temp' ).show();

			// Init fancy selects via choices.js.
			WPPopupsAdmin.initChoicesJS();

			// Init checkbox multiselects columns.
			WPPopupsAdmin.initCheckboxMultiselectColumns();

			// Init colorpicker
            $( '.wppopups-color-picker').spectrum({
                showAlpha: true,
                allowEmpty: true,
				showInput: true,
            });

			// Init fancy File Uploads.
			$( '.wppopups-file-upload' ).each( function(){
				var $input	 = $( this ).find( 'input[type=file]' ),
					$label	 = $( this ).find( 'label' ),
					labelVal = $label.html();
				$input.on( 'change', function( event ) {
					var fileName = '';
					if ( this.files && this.files.length > 1 ) {
						fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
					} else if( event.target.value ) {
						fileName = event.target.value.split( '\\' ).pop();
					}
					if ( fileName ) {
						$label.find( '.fld' ).html( fileName );
					} else {
						$label.html( labelVal );
					}
				});
				// Firefox bug fix.
				$input.on( 'focus', function(){ $input.addClass( 'has-focus' ); }).on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
			});

			// jquery-confirm defaults.
			jconfirm.defaults = {
				closeIcon: true,
				backgroundDismiss: true,
				escapeKey: true,
				animationBounce: 1,
				useBootstrap: false,
				theme: 'modern',
				boxWidth: '400px',
				animateFromElement: false
			};

			// Upgrade information modal for upgrade links.
			$( document ).on( 'click', '.wppopups-upgrade-modal', function() {

				$.alert({
					title: false,
					content: wppopups_admin.upgrade_modal,
					icon: 'fa fa-info-circle',
					type: 'blue',
					boxWidth: '565px',
					buttons: {
						confirm: {
							text: wppopups_admin.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ]
						}
					}
				});
			});

			// Action available for each binding.
			$( document ).trigger( 'wppopupsReady' );
		},

		/**
		 * Initilize Choices JS elements.
		 *
		 * @since 2.0.0
		 */
		initChoicesJS: function() {

			$( '.choicesjs-select' ).each( function() {
				var $this = $( this ),
					args  = { searchEnabled: false };
				if ( $this.attr( 'multiple' ) ) {
					args.searchEnabled = true;
					args.removeItemButton = true;
				}
				if ( $this.data( 'placeholder' ) ) {
					args.placeholderValue = $this.data( 'placeholder' );
				}
				if ( $this.data( 'sorting' ) === 'off' ) {
					args.shouldSort = false;
				}
				if ( $this.data( 'search' ) ) {
					args.searchEnabled = true;
				}
				new Choices( $this[0], args );
			});
		},

		/**
		 * Initilize checkbox mulit-select columns.
		 *
		 * @since 2.0.0
		 */
		initCheckboxMultiselectColumns: function() {

			$( document ).on( 'change', '.checkbox-multiselect-columns input', function() {

				var $this      = $( this ),
					$parent    = $this.parent(),
					$container = $this.closest( '.checkbox-multiselect-columns' ),
					label      = $parent.text(),
					itemID     = 'check-item-' + $this.val(),
					$item      = $container.find( '#' + itemID );

				if ( $this.prop( 'checked' ) ) {
					$this.parent().addClass( 'checked' );
					if ( ! $item.length ) {
						$container.find('.second-column ul').append( '<li id="'+itemID+'">'+label+'</li>' );
					}
				} else {
					$this.parent().removeClass( 'checked' );
					$container.find( '#' + itemID ).remove();
				}
			});

			$( document ).on( 'click', '.checkbox-multiselect-columns .all', function( event ) {

				event.preventDefault();

				$( this ).closest( '.checkbox-multiselect-columns' ).find( 'input[type=checkbox]' ).prop( 'checked', true ).trigger( 'change' );
				$( this ).remove();
			});
		},

		//--------------------------------------------------------------------//
		// Popups Overview
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for Popup Overview page.
		 *
		 * @since 2.0.0
		 */
		initPopupOverview: function() {

			// Confirm popup entry deletion and duplications.
			$( document ).on( 'click', '#wppopups-overview .wp-list-table .delete a, #wppopups-overview .wp-list-table .duplicate a', function( event ) {

				event.preventDefault();

				var url = $( this ).attr( 'href' ),
					msg = $( this ).parent().hasClass( 'delete' ) ? wppopups_admin.popup_delete_confirm : wppopups_admin.popup_duplicate_confirm;

				// Trigger alert modal to confirm.
				$.confirm({
					title: false,
					content: msg,
					backgroundDismiss: false,
					closeIcon: false,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wppopups_admin.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action: function(){
								window.location = url;
							}
						},
						cancel: {
							text: wppopups_admin.cancel,
							keys: [ 'esc' ]
						}
					}
				});
			});
		},


		//--------------------------------------------------------------------//
		// Welcome Activation.
		//--------------------------------------------------------------------//

		/**
		 * Welcome activation page.
		 * TODO: Popups welcome screen
		 * @since 2.0.0
		 */
		initWelcome: function() {

			// Open modal and play How To video.
			$( document ).on( 'click', '#wppopups-welcome .play-video', function( event ) {

				event.preventDefault();

				var video = '<div class="video-container"><iframe width="1280" height="720" src="https://www.youtube-nocookie.com/embed/_yJ-xHVOQYw?rel=0&amp;showinfo=0&amp;autoplay=1" frameborder="0" allowfullscreen></iframe></div>';

				$.dialog({
					title: false,
					content: video,
					closeIcon: true,
					boxWidth: '70%'
				});
			});
		},

		//--------------------------------------------------------------------//
		// Addons List.
		//--------------------------------------------------------------------//
		/**
		 * Element bindings for Addons List page.
		 *
		 * @since 2.0.0
		 */
		initAddons: function () {

			// Some actions have to be delayed to document.ready.
			$(document).on('wppopupsReady', function () {
				// Only run on the addons page.
				if (!$('#wppopups-admin-addons').length) {
					return;
				}
				// Display all addon boxes as the same height.
				$('.addon-item .details').matchHeight({byrow: false, property: 'height'});

				// Addons searching.
				if ($('#wppopups-admin-addons-list').length) {
					const addonSearch = new List('wppopups-admin-addons-list', {
						valueNames: ['addon-name']
					});
					$('#wppopups-admin-addons-search').on('keyup', function () {
						let searchTerm = $(this).val(),
							$heading = $('#addons-heading');

						if (searchTerm) {
							$heading.text(wppopups_admin.addon_search);
						} else {
							$heading.text($heading.data('text'));
						}
						addonSearch.search(searchTerm);
					});
				}
			});

			// Toggle an addon state.
			$(document).on('click', '#wppopups-admin-addons .addon-item button', function (event) {
				event.preventDefault();
				if ($(this).hasClass('disabled')) {
					return false;
				}
				WPPopupsAdmin.addonToggle($(this));
			});
		},

		/**
		 * Toggle addon state.
		 *
		 * @since 1.3.9
		 */
		addonToggle: function ($btn) {

			const $addon = $btn.closest('.addon-item'),
				plugin = $btn.attr('data-plugin'),
				plugin_type = $btn.attr('data-type');
			let action,
				cssClass,
				statusText,
				buttonText,
				errorText,
				successText;

			if ($btn.hasClass('status-go-to-url')) {
				// Open url in new tab.
				window.open($btn.attr('data-plugin'), '_blank');
				return;
			}

			$btn.prop('disabled', true).addClass('loading');
			$btn.html(s.iconSpinner);

			if ($btn.hasClass('status-active')) {
				// Deactivate.
				action = 'wppopups_deactivate_addon';
				cssClass = 'status-inactive';
				if (plugin_type === 'plugin') {
					cssClass += ' button button-secondary';
				}
				statusText = wppopups_admin.addon_inactive;
				buttonText = wppopups_admin.addon_activate;
				if (plugin_type === 'addon') {
					buttonText = s.iconActivate + buttonText;
				}
				errorText = s.iconDeactivate + wppopups_admin.addon_deactivate;

			} else if ($btn.hasClass('status-inactive')) {
				// Activate.
				action = 'wppopups_activate_addon';
				cssClass = 'status-active';
				if (plugin_type === 'plugin') {
					cssClass += ' button button-secondary disabled';
				}
				statusText = wppopups_admin.addon_active;
				buttonText = wppopups_admin.addon_deactivate;
				if (plugin_type === 'addon') {
					buttonText = s.iconDeactivate + buttonText;
				} else if (plugin_type === 'plugin') {
					buttonText = wppopups_admin.addon_activated;
				}
				errorText = s.iconActivate + wppopups_admin.addon_activate;

			} else if ($btn.hasClass('status-download')) {
				// Install & Activate.
				action = 'wppopups_install_addon';
				cssClass = 'status-active';
				if (plugin_type === 'plugin') {
					cssClass += ' button disabled';
				}
				statusText = wppopups_admin.addon_active;
				buttonText = wppopups_admin.addon_activated;
				if (plugin_type === 'addon') {
					buttonText = s.iconActivate + wppopups_admin.addon_deactivate;
				}
				errorText = s.iconInstall + wppopups_admin.addon_activate;

			} else {
				return;
			}

			const data = {
				action: action,
				nonce: wppopups_admin.nonce,
				plugin: plugin,
				type: plugin_type
			};
			$.post(wppopups_admin.ajax_url, data, function (res) {

				if (res.success) {
					if ('wppopups_install_addon' === action) {
						$btn.attr('data-plugin', res.data.basename);
						successText = res.data.msg;
						if (!res.data.is_activated) {
							cssClass = 'status-inactive';
							if (plugin_type === 'plugin') {
								cssClass = 'button';
							}
							statusText = wppopups_admin.addon_inactive;
							buttonText = s.iconActivate + wppopups_admin.addon_activate;
						}
					} else {
						successText = res.data;
					}
					$addon.find('.actions').append('<div class="msg success">' + successText + '</div>');
					$addon.find('span.status-label')
						.removeClass('status-active status-inactive status-download')
						.addClass(cssClass)
						.removeClass('button button-primary button-secondary disabled')
						.text(statusText);
					$btn
						.removeClass('status-active status-inactive status-download')
						.removeClass('button button-primary button-secondary disabled')
						.addClass(cssClass).html(buttonText);
				} else {
					if ('download_failed' === res.data[0].code) {
						if (plugin_type === 'addon') {
							$addon.find('.actions').append('<div class="msg error">' + wppopups_admin.addon_error + '</div>');
						} else {
							$addon.find('.actions').append('<div class="msg error">' + wppopups_admin.plugin_error + '</div>');
						}
					} else {
						$addon.find('.actions').append('<div class="msg error">' + res.data + '</div>');
					}
					$btn.html(errorText);
				}

				$btn.prop('disabled', false).removeClass('loading');

				// Automatically clear addon messages after 3 seconds.
				setTimeout(function () {
					$('.addon-item .msg').remove();
				}, 3000);

			}).fail(function (xhr) {
				console.log(xhr.responseText);
			});
		},

		//--------------------------------------------------------------------//
		// Settings.
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for Settings page.
		 *
		 * @since 2.0.0
		 */
		initSettings: function() {

			// On ready events.
			$( document ).on( 'wppopupsReady', function() {

				// Only proceed if we're on the settings page.
				if ( ! $( '#wppopups-settings' ).length ) {
					return;
				}

				// Watch for hashes and scroll to if found.
				// Display all addon boxes as the same height.
				var integrationFocus = WPPopupsAdmin.getQueryString( 'wppopups-integration' ),
					jumpTo           = WPPopupsAdmin.getQueryString( 'jump' );

				if ( integrationFocus ) {
					$( 'body' ).animate({
						scrollTop: $( '#wppopups-integration-'+integrationFocus ).offset().top
					}, 1000 );
				} else if ( jumpTo ) {
					$( 'body' ).animate({
						scrollTop: $( '#'+jumpTo ).offset().top
					}, 1000 );
				}
				// Verify license key.
				$(document).on('click', '.wppopups-setting-license-key-verify', function (event) {
					event.preventDefault();
					WPPopupsAdmin.licenseVerify($(this));
				}); // Deactivate license key.

				$(document).on('click', '.wppopups-setting-license-key-deactivate', function (event) {
					event.preventDefault();
					WPPopupsAdmin.licenseDeactivate($(this));
				}); // Refresh license key.

				$(document).on('click', '.wppopups-setting-license-key-refresh', function (event) {
					event.preventDefault();
					WPPopupsAdmin.licenseRefresh($(this));
				});

				
				/**
                 * @todo Refactor providers settings tab. Code below is legacy.
                 **/

                // Integration oauth.
                $(document).on('click', '.wppopups-oauth-btn', function (event) {

                    event.preventDefault();

                    WPPopupsAdmin.oauthConnect($(this));
                });

                // Integration connect.
                $(document).on('click', '.wppopups-settings-provider-connect', function (event) {

                    event.preventDefault();

                    WPPopupsAdmin.integrationConnect($(this));
                });

                // Integration account disconnect.
                $(document).on('click', '.wppopups-settings-provider-accounts-list a', function (event) {

                    event.preventDefault();

                    WPPopupsAdmin.integrationDisconnect($(this));
                });

                // Integration individual display toggling.
                $(document).on('click', '.wppopups-settings-provider-header', function (event) {

                    event.preventDefault();

                    $(this).parent().find('.wppopups-settings-provider-accounts').slideToggle();
                    $(this).parent().find('.wppopups-settings-provider-logo i').toggleClass('fa-chevron-right fa-chevron-down');
                });

                // Integration accounts display toggling.
                $(document).on('click', '.wppopups-settings-provider-accounts-toggle a', function (event) {

                    event.preventDefault();

                    var $connectFields = $(this).parent().next('.wppopups-settings-provider-accounts-connect');
                    $connectFields.find('input[type=text], input[type=password]').val('');
                    $connectFields.slideToggle();
                });
			});

			// Image upload fields.
			$( document ).on( 'click', '.wppopups-setting-row-image button', function( event ) {

				event.preventDefault();

				WPPopupsAdmin.imageUploadModal( $( this ) );
			});

		},

		/**
		 * Connect provider to oauth in new windows and copy result code
		 * @param el button
		 */
		oauthConnect: function (el) {
			// Set variables for the postMessage request.
			const request_url      = 'https://wppopups.com';
			const request_provider = el.data('provider');
			const oauth_window    = window.open( el.attr('href'), '', 'resizable=yes,location=no,width=750,height=600,top=0,left=0' );

			// Listen to the callback and set the fields.
			let request_call = setInterval(function(){
				const message = 'Hello World';
				$.postMessage(message, request_url, oauth_window);
			}, 6000);

			$.receiveMessage(function(e){
				const data = $.getQueryParameters(decodeURIComponent(e.data));
				if( data.event && data.event == 'spu_oauth') {
					clearInterval(request_call);
				}
				if( data.error ) {
					alert(data.error + ' ' + data.error_description);
					return;
				}

				if( data.access_token ) {
					$('#'+ request_provider + '_api' ).val( data.access_token );
					$('#'+ request_provider + '_api' ).next('input').focus();
				}

			}, false);
		},

         /**
         * Connect integration provider account.
         *
         * @since 2.0.0
         */
         integrationConnect: function( el ) {

			var $this       = $( el ),
				buttonWidth = $this.outerWidth(),
				buttonLabel = $this.text(),
				$provider   = $this.closest( '.wppopups-settings-provider' ),
				data        = {
					action  : 'wppopups_settings_provider_add',
					data    : $this.closest( 'form' ).serialize(),
					provider: $this.data( 'provider' ),
					nonce   : wppopups_admin.nonce
				};

			$this.html( 'Connecting...' ).css( 'width', buttonWidth ).prop( 'disabled', true );

			$.post( wppopups_admin.ajax_url, data, function( res ) {

				if ( res.success ){
					$provider.find( '.wppopups-settings-provider-accounts-list ul' ).append( res.data.html );
					$provider.addClass( 'connected' );
					$this.closest( '.wppopups-settings-provider-accounts-connect' ).slideToggle();
				} else {
					var msg = wppopups_admin.provider_auth_error;
					if ( res.data.error_msg ) {
						msg += "\n" + res.data.error_msg; // jshint ignore:line
					}
					$.alert({
						title: false,
						content: msg,
						icon: 'fa fa-exclamation-circle',
						type: 'orange',
						buttons: {
							confirm: {
								text: wppopups_admin.ok,
								btnClass: 'btn-confirm',
								keys: [ 'enter' ]
							}
						}
					});
					console.log(res);
				}

				$this.html( buttonLabel ).css( 'width', 'auto' ).prop( 'disabled', false );

				$( document ).trigger( 'wppopups_integration_connect', [ res, $this ] );

			}).fail( function( xhr ) {
				console.log( xhr.responseText );
			});
		},

         /**
         * Remove integration provider account.
         *
         * @since 2.0.0
         */
         integrationDisconnect: function( el ) {

			var $this = $( el ),
				data = {
					action  : 'wppopups_settings_provider_disconnect',
					provider: $this.data( 'provider' ),
					key     : $this.data( 'key'),
					nonce   : wppopups_admin.nonce
				};

			$.confirm({
				title: wppopups_admin.heads_up,
				content: wppopups_admin.provider_delete_confirm,
				backgroundDismiss: false,
				closeIcon: false,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wppopups_admin.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: function(){
							$.post( wppopups_admin.ajax_url, data, function( res ) {
								if ( res.success ){
									$this.parent().parent().remove();
								} else {
									console.log( res );
								}

								$( document ).trigger( 'wppopups_integration_disconnect', [ res, $this ] );
							}).fail( function( xhr ) {
								console.log( xhr.responseText );
							});
						}
					},
					cancel: {
						text: wppopups_admin.cancel,
						keys: [ 'esc' ]
					}
				}
			});
		},
		
		/**
		 * Verify a license key.
		 *
		 * @since 2.0.0
		 * */
		licenseVerify: function licenseVerify(el) {
			const $this = $(el),
				$row = $this.closest('.wppopups-setting-row'),
				buttonWidth = $this.outerWidth(),
				buttonLabel = $this.text(),
				data = {
					action: 'wppopups_verify_license',
					nonce: wppopups_admin.nonce,
					license: $('#input-' + $this.data('key')).val(),
					option_name: $this.data('key'),
					item_id: $this.data('item-id'),
				};
			$this.html(window.WPPopupsAdmin.settings.iconSpinner).css('width', buttonWidth).prop('disabled', true);
			$.post(wppopups_admin.ajax_url, data, function (res) {
				let icon = 'fa fa-check-circle',
					color = 'green',
					msg;

				if (res.success) {
					msg = res.data.msg;
					$row.find('.type, .desc, .wppopups-setting-license-key-deactivate').show();
					$row.find('.type strong').text(res.data.type);
					$('.wppopups-license-notice').remove();
				} else {
					icon = 'fa fa-exclamation-circle';
					color = 'orange';
					msg = res.data;
					$row.find('.type, .desc, .wppopups-setting-license-key-deactivate').hide();
				}

				$.alert({
					title: false,
					content: msg,
					icon: icon,
					type: color,
					buttons: {
						confirm: {
							text: wppopups_admin.ok,
							btnClass: 'btn-confirm',
							keys: ['enter']
						}
					}
				});
				$this.html(buttonLabel).css('width', 'auto').prop('disabled', false);
			}).fail(function (xhr) {
				console.log(xhr.responseText);
			});
		},

		/**
		 * Verify a license key.
		 *
		 * @since 2.0.0
		 */
		licenseDeactivate: function licenseDeactivate(el) {
			const $this = $(el),
				$row = $this.closest('.wppopups-setting-row'),
				buttonWidth = $this.outerWidth(),
				buttonLabel = $this.text(),
				data = {
					action: 'wppopups_deactivate_license',
					nonce: wppopups_admin.nonce,
					option_name: $this.data('key')
				};
			$this.html(window.WPPopupsAdmin.settings.iconSpinner).css('width', buttonWidth).prop('disabled', true);
			$.post(wppopups_admin.ajax_url, data, function (res) {
				let icon = 'fa fa-info-circle',
					color = 'blue',
					msg = res.data;

				if (res.success) {
					$row.find('.wppopups-setting-license-key').val('');
					$row.find('.type, .desc, .wppopups-setting-license-key-deactivate').hide();
				} else {
					icon = 'fa fa-exclamation-circle';
					color = 'orange';
				}

				$.alert({
					title: false,
					content: msg,
					icon: icon,
					type: color,
					buttons: {
						confirm: {
							text: wppopups_admin.ok,
							btnClass: 'btn-confirm',
							keys: ['enter']
						}
					}
				});
				$this.html(buttonLabel).css('width', 'auto').prop('disabled', false);
			}).fail(function (xhr) {
				console.log(xhr.responseText);
			});
		},

		/**
		 * Refresh a license key.
		 *
		 * @since 2.0.0
		 */
		licenseRefresh: function licenseRefresh(el) {
			const $this = $(el),
				$row = $this.closest('.wppopups-setting-row'),
				data = {
					action: 'wppopups_refresh_license',
					nonce: wppopups_admin.nonce,
					license: $('#input-' + $this.data('key')).val()
				};
			$.post(wppopups_admin.ajax_url, data, function (res) {
				let icon = 'fa fa-check-circle',
					color = 'green',
					msg;

				if (res.success) {
					msg = res.data.msg;
					$row.find('.type strong').text(res.data.type);
				} else {
					icon = 'fa fa-exclamation-circle';
					color = 'orange';
					msg = res.data;
					$row.find('.type, .desc, #wppopups-setting-license-key-deactivate').hide();
				}

				$.alert({
					title: false,
					content: msg,
					icon: icon,
					type: color,
					buttons: {
						confirm: {
							text: wppopups_admin.ok,
							btnClass: 'btn-confirm',
							keys: ['enter']
						}
					}
				});
			}).fail(function (xhr) {
				console.log(xhr.responseText);
			});
		},

		/**
		 * Image upload modal window.
		 *
		 * @since 2.0.0
		 */
		imageUploadModal: function( el ) {

			 if ( s.media_frame ) {
				 s.media_frame.open();
				 return;
			 }

			 var $setting = $( el ).closest( '.wppopups-setting-field' );

			 s.media_frame = wp.media.frames.wppopups_media_frame = wp.media({
				 className: 'media-frame wppopups-media-frame',
				 frame: 'select',
				 multiple: false,
				 title: wppopups_admin.upload_image_title,
				 library: {
					 type: 'image'
				 },
				 button: {
					 text: wppopups_admin.upload_image_button
				 }
			 });

			 s.media_frame.on( 'select', function(){
				 // Grab our attachment selection and construct a JSON representation of the model.
				 var media_attachment = s.media_frame.state().get( 'selection' ).first().toJSON();

				 // Send the attachment URL to our custom input field via jQuery.
				 $setting.find( 'input[type=text]' ).val( media_attachment.url );
				 $setting.find( 'img' ).remove();
				 $setting.prepend( '<img src="'+media_attachment.url+'">' );
			 });

			 // Now that everything has been set, let's open up the frame.
			 s.media_frame.open();
		},

		/**
		 * Verify a license key.
		 *
		 * @since 2.0.0
		 * TODO: move to premium
		licenseVerify: function( el ) {

			var $this       = $( el ),
				$row        = $this.closest( '.wppopups-setting-row' ),
				buttonWidth = $this.outerWidth(),
				buttonLabel = $this.text(),
				data        = {
					action: 'wppopups_verify_license',
					nonce:   wppopups_admin.nonce,
					license: $('#wppopups-setting-license-key').val()
				};

			$this.html( s.iconSpinner ).css( 'width', buttonWidth ).prop( 'disabled', true );

			$.post( wppopups_admin.ajax_url, data, function( res ) {

				var icon  = 'fa fa-check-circle',
					color = 'green',
					msg;

				if ( res.success ){
					msg = res.data.msg;
					$row.find( '.type, .desc, #wppopups-setting-license-key-deactivate' ).show();
					$row.find( '.type strong' ).text( res.data.type );
					$('.wppopups-license-notice').remove();
				} else {
					icon  = 'fa fa-exclamation-circle';
					color = 'orange';
					msg   = res.data;
					$row.find( '.type, .desc, #wppopups-setting-license-key-deactivate' ).hide();
				}

				$.alert({
					title: false,
					content: msg,
					icon: icon,
					type: color,
					buttons: {
						confirm: {
							text: wppopups_admin.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ]
						}
					}
				});

				$this.html( buttonLabel ).css( 'width', 'auto' ).prop( 'disabled', false );

			}).fail( function( xhr ) {
				console.log( xhr.responseText );
			});
		},

		/**
		 * Verify a license key.
		 *
		 * @since 2.0.0
		 *
		licenseDeactivate: function( el ) {

			var $this       = $( el ),
				$row        = $this.closest( '.wppopups-setting-row' ),
				buttonWidth = $this.outerWidth(),
				buttonLabel = $this.text(),
				data        = {
					action: 'wppopups_deactivate_license',
					nonce:   wppopups_admin.nonce
				};

			$this.html( s.iconSpinner ).css( 'width', buttonWidth ).prop( 'disabled', true );

			$.post( wppopups_admin.ajax_url, data, function( res ) {

				var icon  = 'fa fa-info-circle',
					color = 'blue',
					msg   = res.data;

				if ( res.success ){
					$row.find( '#wppopups-setting-license-key' ).val('');
					$row.find( '.type, .desc, #wppopups-setting-license-key-deactivate' ).hide();
				} else {
					icon  = 'fa fa-exclamation-circle';
					color = 'orange';
				}

				$.alert({
					title: false,
					content: msg,
					icon: icon,
					type: color,
					buttons: {
						confirm: {
							text: wppopups_admin.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ]
						}
					}
				});

				$this.html( buttonLabel ).css( 'width', 'auto' ).prop( 'disabled', false );

			}).fail( function( xhr ) {
				console.log( xhr.responseText );
			});
		},

		/**
		 * Refresh a license key.
		 *
		 * @since 2.0.0
		 *
		licenseRefresh: function( el ) {

			var $this       = $( el ),
				$row        = $this.closest( '.wppopups-setting-row' ),
				data        = {
					action: 'wppopups_refresh_license',
					nonce:   wppopups_admin.nonce,
					license: $('#wppopups-setting-license-key').val()
				};

			$.post( wppopups_admin.ajax_url, data, function( res ) {

				var icon  = 'fa fa-check-circle',
					color = 'green',
					msg;

				if ( res.success ){
					msg = res.data.msg;
					$row.find( '.type strong' ).text( res.data.type );
				} else {
					icon  = 'fa fa-exclamation-circle';
					color = 'orange';
					msg   = res.data;
					$row.find( '.type, .desc, #wppopups-setting-license-key-deactivate' ).hide();
				}

				$.alert({
					title: false,
					content: msg,
					icon: icon,
					type: color,
					buttons: {
						confirm: {
							text: wppopups_admin.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ]
						}
					}
				});

			}).fail( function( xhr ) {
				console.log( xhr.responseText );
			});
		},

		/**
		 * Connect integration provider account.
		 *
		 * @since 2.0.0
		 *
		integrationConnect: function( el ) {

			var $this       = $( el ),
				buttonWidth = $this.outerWidth(),
				buttonLabel = $this.text(),
				$provider   = $this.closest( '.wppopups-settings-provider' ),
				data        = {
					action  : 'wppopups_settings_provider_add',
					data    : $this.closest( 'popup' ).serialize(),
					provider: $this.data( 'provider' ),
					nonce   : wppopups_admin.nonce
				};

			$this.html( 'Connecting...' ).css( 'width', buttonWidth ).prop( 'disabled', true );

			$.post( wppopups_admin.ajax_url, data, function( res ) {

				if ( res.success ){
					$provider.find( '.wppopups-settings-provider-accounts-list ul' ).append( res.data.html );
					$provider.addClass( 'connected' );
					$this.closest( '.wppopups-settings-provider-accounts-connect' ).slideToggle();
				} else {
					var msg = wppopups_admin.provider_auth_error;
					if ( res.data.error_msg ) {
						msg += "\n" + res.data.error_msg; // jshint ignore:line
					}
					$.alert({
						title: false,
						content: msg,
						icon: 'fa fa-exclamation-circle',
						type: 'orange',
						buttons: {
							confirm: {
								text: wppopups_admin.ok,
								btnClass: 'btn-confirm',
								keys: [ 'enter' ]
							}
						}
					});
					console.log(res);
				}

				$this.html( buttonLabel ).css( 'width', 'auto' ).prop( 'disabled', false );

			}).fail( function( xhr ) {
				console.log( xhr.responseText );
			});
		},

		/**
		 * Remove integration provider account.
		 *
		 * @since 2.0.0
		 *
		integrationDisconnect: function( el ) {

			var $this = $( el ),
				data = {
					action  : 'wppopups_settings_provider_disconnect',
					provider: $this.data( 'provider' ),
					key     : $this.data( 'key'),
					nonce   : wppopups_admin.nonce
				};

			$.confirm({
				title: wppopups_admin.heads_up,
				content: wppopups_admin.provider_delete_confirm,
				backgroundDismiss: false,
				closeIcon: false,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wppopups_admin.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: function(){
							$.post( wppopups_admin.ajax_url, data, function( res ) {
								if ( res.success ){
									$this.parent().parent().remove();
								} else {
									console.log( res );
								}
							}).fail( function( xhr ) {
								console.log( xhr.responseText );
							});
						}
					},
					cancel: {
						text: wppopups_admin.cancel,
						keys: [ 'esc' ]
					}
				}
			});
		},*/

		//--------------------------------------------------------------------//
		// Tools.
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for Tools page.
		 *
		 * @since 2.0.0
		 */
		initTools: function() {

			// Run import for a specific provider.
			$( document ).on( 'click', '#wppopups-ssl-verify', function( event ) {

				event.preventDefault();

				WPPopupsAdmin.verifySSLConnection();
			});

			// Run import for a specific provider.
			$( document ).on( 'click', '#wppopups-importer-popups-submit', function( event ) {

				event.preventDefault();

				// Check to confirm user as selected a popup.
				if ( $( '#wppopups-importer-popups input:checked' ).length ) {

					var ids = [];
					$( '#wppopups-importer-popups input:checked' ).each( function ( i ) {
						ids[i] = $( this ).val();
					});

					if ( ! wppopups_admin.isPro ) {
						// We need to analyze the popups before starting the
						// actual import.
						WPPopupsAdmin.analyzePopups( ids );
					} else {
						// Begin the import process.
						WPPopupsAdmin.importPopups( ids );
					}

				} else {

					// User didn't actually select a popup so alert them.
					$.alert({
						title: false,
						content: wppopups_admin.importer_popups_required,
						icon: 'fa fa-info-circle',
						type: 'blue',
						buttons: {
							confirm: {
								text: wppopups_admin.ok,
								btnClass: 'btn-confirm',
								keys: [ 'enter' ]
							}
						}
					});
				}
			});

			// Continue import after analyzing.
			$( document ).on( 'click', '#wppopups-importer-continue-submit', function( event ) {

				event.preventDefault();

				WPPopupsAdmin.importPopups( s.popupIDs );
			});
		},

		/**
		 * Perform test connection to verify that the current web host
		 * can successfully make outbound SSL connections.
		 *
		 * @since 2.0.0
		 */
		verifySSLConnection: function() {

			var $btn      = $( '#wppopups-ssl-verify' ),
				btnLabel  = $btn.text(),
				btnWidth  = $btn.outerWidth(),
				$settings = $btn.parent(),
				data      = {
					action: 'wppopups_verify_ssl',
					nonce:   wppopups_admin.nonce
				};

			$btn.css( 'width', btnWidth ).prop( 'disabled', true ).text( wppopups_admin.testing );

			// Trigger AJAX to test connection
			$.post( wppopups_admin.ajax_url, data, function( res ) {

				console.log( res );

				// Remove any previous alerts.
				$settings.find( '.wppopups-alert, .wppopups-ssl-error' ).remove();

				if ( res.success ){
					$btn.before( '<div class="wppopups-alert wppopups-alert-success">' + res.data.msg + '</div>' );
				} else {
					$btn.before( '<div class="wppopups-alert wppopups-alert-danger">' + res.data.msg + '</div>' );
					$btn.before( '<div class="wppopups-ssl-error pre-error">' + res.data.debug + '</div>' );
				}

				$btn.css( 'width', btnWidth ).prop( 'disabled', false ).text( btnLabel );
			});
		},

		/**
		 * Begins the process of importing the popups.
		 *
		 * @since 2.0.0
		 */
		importPopups: function( popups ) {

			var $processSettings = $( '#wppopups-importer-process' );

			// Display total number of popups we have to import.
			$processSettings.find( '.popup-total' ).text( popups.length );
			$processSettings.find( '.popup-current' ).text( '1' );

			// Hide the popup select and popup analyze sections.
			$( '#wppopups-importer-popups, #wppopups-importer-analyze' ).hide();

			// Show processing status.
			$processSettings.show();

			// Create global import queue.
			s.importQueue = popups;
			s.imported    = 0;

			// Import the first popup in the queue.
			WPPopupsAdmin.importPopup();
		},

		/**
		 * Imports a single popup from the import queue.
		 *
		 * @since 2.0.0
		 */
		importPopup: function() {

			var $processSettings = $( '#wppopups-importer-process' ),
				popupID           = _.first( s.importQueue ),
				provider         = WPPopupsAdmin.getQueryString( 'provider' ),
				data             = {
					action:  'wppopups_import_popup_' + provider,
					popup_id: popupID,
					nonce:   wppopups_admin.nonce
				};

			// Trigger AJAX import for this popup.
			$.post( wppopups_admin.ajax_url, data, function( res ) {

				if ( res.success ){
					var statusUpdate;

					if ( res.data.error ) {
						statusUpdate = wp.template( 'wppopups-importer-status-error' );
					} else {
						statusUpdate = wp.template( 'wppopups-importer-status-update' );
					}

					$processSettings.find( '.status' ).prepend( statusUpdate( res.data ) );
					$processSettings.find( '.status' ).show();

					// Remove this popup ID from the queue.
					s.importQueue = _.without( s.importQueue, popupID );
					s.imported++;

					if ( _.isEmpty( s.importQueue ) ) {
						$processSettings.find( '.process-count' ).hide();
						$processSettings.find( '.popups-completed' ).text( s.imported );
						$processSettings.find( '.process-completed' ).show();
					} else {
						// Import next popup in the queue.
						$processSettings.find( '.popup-current' ).text( s.imported+1 );
						WPPopupsAdmin.importPopup();
					}
				}
			});
		},

		//--------------------------------------------------------------------//
		// Helper functions.
		//--------------------------------------------------------------------//

		/**
		 * Get query string in a URL.
		 *
		 * @since 2.0.0
		 */
		getQueryString: function( name ) {

			var match = new RegExp( '[?&]' + name + '=([^&]*)' ).exec( window.location.search );
			return match && decodeURIComponent( match[1].replace(/\+/g, ' ') );
		},

		/**
		 * Debug output helper.
		 *
		 * @since 2.0.0
		 * @param msg
		 */
		debug: function( msg ) {

			if ( WPPopupsAdmin.isDebug() ) {
				if ( typeof msg === 'object' || msg.constructor === Array ) {
					console.log( 'WP Popups Debug:' );
					console.log( msg );
				} else {
					console.log( 'WP Popups Debug: ' + msg );
				}
			}
		},

		/**
		 * Is debug mode.
		 *
		 * @since 2.0.0
		 */
		isDebug: function() {

			return ( window.location.hash && '#wppopupsdebug' === window.location.hash );
		}
	};

	WPPopupsAdmin.init();

	window.WPPopupsAdmin = WPPopupsAdmin;

})( jQuery );
