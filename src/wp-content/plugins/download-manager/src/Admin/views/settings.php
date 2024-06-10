<?php

use WPDM\Admin\Menu\Settings;

if ( ! defined( "ABSPATH" ) ) {
	die( "Shit happens!" );
}
?>
<link rel="stylesheet" href="<?= WPDM_CSS_URL ?>settings-ui.css"/>
<style>
    #wpfooter {
        display: none;
    }
</style>
<div class="wrap w3eden <?php echo (int)get_option('__wpdm_left_aligned', 0) === 0 ? 'wpdms-body-centered': '' ?>">
    <form method="post" id="wdm_settings_form">
		<?php

		$actions = [
			[
				'type' => "submit",
				"class" => "primary btn-full-height",
				"name" => '<i class="sinc far fa-hdd"></i> ' . __( "Save Settings", "download-manager" )
			]
		];

		//WPDM()->admin->pageHeader(esc_attr__('Settings', WPDM_TEXT_DOMAIN), 'cog sinc color-purple', [], $actions, ['class' => 'pr-0']);

		?>



		<?php
		wp_nonce_field( WPDMSET_NONCE_KEY, '__wpdms_nonce' );
		?>
        <div class="panel panel-default" id="wpdm-wrapper-panel">

            <div id="wpdm-admin-page-container">
                <div id="wpdm-admin-page-sidebar" data-simplebar>
                    <div id="sidebarlogo" class="panel panel-default">
                        <a href="https://www.wpdownloadmanager.com/" target="_blank">
                        <svg style="width: 160px;" xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 327.3 327.3">
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="download-manager-logo">
                                    <g id="wpdm_logo_v3" data-name="WordPress Download Manage Pro">
                                        <path id="arrow" fill="#3c80e4"
                                              d="M149.87,180.29l-91.41-91A20,20,0,0,1,58.39,61L64,55.36a20,20,0,0,1,28.29-.07l71.52,71.18,71-71.7a20,20,0,0,1,28.29-.13l5.68,5.62a20,20,0,0,1,.14,28.29l-90.75,91.64A20,20,0,0,1,149.87,180.29Z"></path>
                                        <path id="circle"
                                              d="M186.66,202.9a32,32,0,0,1-45.29.16L97.58,159.48a78,78,0,1,0,132.49-.41Z"
                                              style="fill: #094168;"></path>
                                    </g>
                                </g>
                            </g>
                        </svg>
                        </a>
                    </div>
                    <div>
                        <ul id="tabs" class="nav nav-pills nav-stacked settings-tabs">
							<?php Settings::renderMenu( $tab = wpdm_query_var( 'tab', [
								'validate' => 'txt',
								'default'  => 'basic'
							] ) ); ?>
                        </ul>
                    </div>
                    <div class="panel panel-default sidebar-button">
                        <div class="panel-body">
                            <button type="submit" style="min-width:200px" class="btn btn-admin btn-block btn-lg">
                                <i class="sinc far fa-hdd"></i>
                                &nbsp;<?php _e( "Save Settings", "download-manager" ); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="wpdm-admin-page-body">
                    <div class="tab-content">
                        <div class="panel panel-default" id="setting-head">
                            <div class="panel-body" style="padding: 20px 0 !important;">
                                <div class="media">
                                    <div class="pull-right">
                                        <div id="wpdm_notify"
                                             style="position: absolute; cursor: pointer;right: 220px"></div>
                                        <button type="submit" style="min-width:200px"
                                                class="btn btn-admin btn-block btn-lg">
                                            <i class="sinc far fa-hdd"></i>
                                            &nbsp;<?php _e( "Save Settings", "download-manager" ); ?>
                                        </button>
                                    </div>
                                    <div class="media-body">
                                        <h2 class="m-0" id="stitle" style="font-size: 14pt;line-height: 42px">
                                            <div class="pull-left text-primary" style="width: 28px">
                                                <svg id="leftal" style="width: 24px;margin-top: 11px;margin-right: 6px;"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     viewBox="0 0 327.3 327.3">
                                                    <title>wpdm logo v3</title>
                                                    <g id="Layer_2" data-name="Layer 2">
                                                        <g id="download-manager-logo">
                                                            <g id="wpdm_logo_v3" data-name="wpdm logo v3">
                                                                <path id="arrow"  fill="#3c80e4"
                                                                      d="M149.87,180.29l-91.41-91A20,20,0,0,1,58.39,61L64,55.36a20,20,0,0,1,28.29-.07l71.52,71.18,71-71.7a20,20,0,0,1,28.29-.13l5.68,5.62a20,20,0,0,1,.14,28.29l-90.75,91.64A20,20,0,0,1,149.87,180.29Z"/>
                                                                <path id="circle"
                                                                      d="M186.66,202.9a32,32,0,0,1-45.29.16L97.58,159.48a78,78,0,1,0,132.49-.41Z"
                                                                      style="fill: #094168;"/>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                                <i :class="icon" id="centeral"></i>
                                            </div>
											<?= __( 'Settings', WPDM_TEXT_DOMAIN ) ?> <i
                                                    class="fa fa-angle-double-right"></i> <span>{{stitle}}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="height: 55px"></div>
                        <div class="alert alert-success"
                             style="max-width: 300px !important;display: none;position: fixed; right: 15px;top: 80px;background: #ffffff !important;cursor: pointer"
                             id="wpdm_message"></div>

                        <input type="hidden" name="task" id="task" value="wdm_save_settings"/>
                        <input type="hidden" name="action" id="action" value="wpdm_settings"/>
                        <input type="hidden" name="section" id="section" value="<?php echo $tab; ?>"/>
                        <div id="fm_settings">
							<?php
							global $stabs;
							if ( isset( $stabs[ $tab ], $stabs[ $tab ]['callback'] ) ) {
								call_user_func( $stabs[ $tab ]['callback'] );
							} else {
								echo "<div class='panel panel-danger'><div class='panel-body color-red'><i class='fa fa-exclamation-triangle'></i> " . __( "Something is wrong!", "download-manager" ) . "</div></div>";
							}
							?>
                        </div>


                    </div>
                </div>

            </div>


        </div>

    </form>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL ?>assets/simplebar/simplebar.css"/>
    <script src="<?php echo WPDM_BASE_URL ?>assets/simplebar/simplebar.min.js"></script>
    <script type="text/javascript">

        var stitle = new Vue({
            el: '#stitle',
            data: {
                stitle: '<?= __( 'General', WPDM_TEXT_DOMAIN ); ?>',
                icon: 'fa-solid fa-sliders'
            }
        });

        jQuery(function ($) {
            var $body = $('body'), section;
            $body.on('click', '#wpdm_message.alert-success', function () {
                $(this).fadeOut();
            });

            stitle.stitle = $('#tabs li.active a').text();
            stitle.icon = $('#tabs li.active a').data('icon');

            $('select:not(.system-ui)').select2({minimumResultsForSearch: 6});
            $("ul#tabs li").click(function () {

            });
            $('#wpdm_message').removeClass('hide').hide();
            $("ul#tabs li a").click(function () {
                stitle.stitle = $(this).text();
                stitle.icon = $(this).data('icon');
                ///jQuert("ul#tabs li").removeClass('active')
                $("ul#tabs li").removeClass("active");
                $(this).parent('li').addClass('active');
                WPDM.blockUI('#fm_settings');
                section = this.id;
                $.post(ajaxurl, {action: 'wpdm_settings', section: this.id}, function (res) {
                    $('#fm_settings').html(res);
                    $('#section').val(section)
                    $('select:not(.system-ui)').select2({minimumResultsForSearch: 6});
                    window.history.pushState({
                        "html": res,
                        "pageTitle": "response.pageTitle"
                    }, "", "edit.php?post_type=wpdmpro&page=settings&tab=" + section);
                    $('#wpdm-lsp').fadeOut(function () {
                        $(this).remove();
                    });
                    WPDM.unblockUI('#fm_settings');
                    $('#ttip').tooltip({placement: 'bottom'});
                });
                return false;
            });

            window.onpopstate = function (e) {
                if (e.state) {
                    $("#fm_settings").html(e.state.html);
                    //document.title = e.state.pageTitle;
                }
            };


            $('#wdm_settings_form').submit(function () {

                $('.sinc').removeClass('far fa-hdd').addClass('fas fa-sun fa-spin');

                $(this).ajaxSubmit({
                    url: ajaxurl,
                    beforeSubmit: function (formData, jqForm, options) {
                        $('.wpdm-ssb').addClass('wpdm-spin');
                        $('#wdms_loading').addClass('wpdm-spin');
                    },
                    success: function (response) {
                        var section = $('input#section').val();
                        if (typeof response === 'string')
                            WPDM.notify("<div style='margin-bottom: 5px;text-transform: uppercase'><strong>" + $('#' + section).html() + "</strong></div>" + response, 'success', '#wpdm_notify', 10000);
                        else {
                            if (response.success === true)
                                WPDM.notify("<div style='margin-bottom: 5px;text-transform: uppercase'><strong>" + $('#' + section).html() + ":</strong></div>" + response.msg, 'success', '#wpdm_notify', 10000);
                            if (response.success === false)
                                WPDM.notify("<div style='margin-bottom: 5px;text-transform: uppercase'><strong>" + $('#' + section).html() + ":</strong></div>" + response.msg, 'danger', '#wpdm_notify', 10000);
                            if (response.reload === true) {
                                $('#' + section).trigger('click');
                            }
                        }

                        WPDM.doAction("wpdm_save_settings", $('#section').val(), response)

                        $('.wpdm-ssb').removeClass('wpdm-spin');
                        $('.sinc').removeClass('fas fa-sun fa-spin').addClass('far fa-hdd');
                        $('#wdms_loading').removeClass('wpdm-spin');
                    }
                });

                return false;
            });

            $('body').on("click", '.nav-tabs a', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            function adjustSidebarHeight() {
                var abh = $('#wpadminbar').height();
                $('#wpdm-admin-page-sidebar').css('height', (window.innerHeight - abh) + 'px');
            }

            adjustSidebarHeight();
            $(window).on('resize', function () {
                adjustSidebarHeight();
            });


        });

    </script>

