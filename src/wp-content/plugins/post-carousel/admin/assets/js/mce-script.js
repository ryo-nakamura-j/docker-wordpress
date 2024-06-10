(function() {
	tinymce.PluginManager.add('sp_pc_mce_button', function( editor, url ) {
		editor.addButton('sp_pc_mce_button', {
			text: false,
            icon: false,
            image: url + '/pc-mce-logo.png',
            tooltip: 'Post Carousel',
            onclick: function () {
                editor.windowManager.open({
                    title: 'Insert Shortcode',
					width: 400,
					height: 100,
					body: [
						{
							type: 'listbox',
							name: 'listboxName',
                            label: 'Select Shortcode',
							'values': editor.settings.spPCShortcodeList
						}
					],
					onsubmit: function( e ) {
						editor.insertContent( '[post-carousel id="' + e.data.listboxName + '"]');
					}
				});
			}
		});
	});
})();