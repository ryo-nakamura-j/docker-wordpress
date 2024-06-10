;(function($) {

	var WPPopupsBuilderLite = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			// Document ready
			$( document ).ready(
				function() {
					WPPopupsBuilderLite.ready();
				}
			);

			WPPopupsBuilderLite.bindUIActions();
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function() {
		},

		/**
		 * Element bindings.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function() {

			// WPPopups upgrade panels modal
			$( document ).on(
				'click', '#wppopups-panels-toggle button', function(e) {
					if ($( this ).hasClass( 'upgrade-modal' )) {
						e.preventDefault();
						e.stopImmediatePropagation();
						WPPopupsBuilderLite.upgradeModal( $( this ).text() + ' panel' );
					}
				}
			);

			// WPPopups upgrade field modal
			$( document ).on(
				'click', '.wppopups-add-fields-button', function(e) {
					if ($( this ).hasClass( 'upgrade-modal' )) {
						e.preventDefault();
						e.stopImmediatePropagation();
						WPPopupsBuilderLite.upgradeModal( $( this ).text() + ' field' );
					}
				}
			);

			// WPPopups upgrade template modal
			$( document ).on(
				'click', '.wppopups-template-select', function(e) {
					if ($( this ).closest( '.wppopups-template' ).hasClass( 'upgrade-modal' )) {
						e.preventDefault();
						e.stopImmediatePropagation();
						WPPopupsBuilderLite.upgradeModal( $( this ).data( 'template-name' ) );
					}
				}
			);

			// WPPopups upgrade providers modal
			$( document ).on(
				'click', '.wppopups-panel-sidebar-section', function(e) {
					if ($( this ).hasClass( 'upgrade-modal' )) {
						e.preventDefault();
						e.stopImmediatePropagation();
						WPPopupsBuilderLite.upgradeModal( $( this ).data( 'name' ) );
					}
				}
			);
		},

		/**
		 * Trigger modal for upgrade.
		 *
		 * @since 1.0.0
		 */
		upgradeModal: function(feature) {

			var message = wppopups_builder_lite.upgrade_message.replace( /%name%/g,feature )
			$.alert(
				{
					title: feature + ' ' + wppopups_builder_lite.upgrade_title,
					icon: 'fa fa-lock',
					content: message,
					buttons: {
						confirm: {
							text: wppopups_builder_lite.upgrade_button,
							btnClass: 'btn-confirm',
							keys: ['enter'],
							action: function () {
								window.open( wppopups_builder_lite.upgrade_url,'_blank' );
								$.alert(
									{
										title: false,
										content: wppopups_builder_lite.upgrade_modal,
										icon: 'fa fa-info-circle',
										type: 'blue',
										boxWidth: '565px',
										buttons: {
											confirm: {
												text: wppopups_builder.ok,
												btnClass: 'btn-confirm',
												keys: [ 'enter' ]
											}
										}
									}
								);
							}
						},
						cancel: {
							text: wppopups_builder.ok
						}
					}
				}
			);
		},
	};

	WPPopupsBuilderLite.init();

})( jQuery );
