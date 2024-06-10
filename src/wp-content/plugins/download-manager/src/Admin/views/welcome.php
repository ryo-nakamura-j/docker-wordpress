<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/download-manager/assets/adminui/css/base.css');?>" />
<link rel="stylesheet" href="<?php echo plugins_url('/download-manager/assets/css/front3.css'); ?>" />
<link href='//fonts.googleapis.com/css?family=Overpass:300,400,700' rel='stylesheet' type='text/css'>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<style>
    .w3eden .lead,
    .w3eden p{
        font-family: 'Overpass', sans-serif;
        font-weight: 100;
    }

    .w3eden h3, .w3eden h2, .w3eden h1{
        font-family: 'Overpass', sans-serif;
        font-weight: 700;
        margin-top: 20px;
        margin-bottom: 5px;
    }
    .w3eden a{
        font-family: 'Overpass', sans-serif;
        font-weight: 700;
    }

    .r{
        font-family: 'Overpass', sans-serif;
        font-weight: 300;
        font-size: 11pt;
    }
    b{
        font-size: 14pt;
        margin-bottom: 10px;
    }
    .r b{
        display: block;
        clear: both;
        margin-bottom: 5px;
    }

    input{
        padding: 7px;
    }
    #wphead{
        border-bottom:0px;
    }
    #screen-meta-links{
        display: none;
    }
    .wrap{
        margin: 0px;
        padding: 0px;
    }
    #wpbody{
        margin-left: -19px;
    }
    select{
        min-width: 150px;
    }
    .media-body b {
        font-size: 13pt;
        display: block;
        margin-bottom: 10px;
    }
    .media-body p{
        font-size: 11pt;
    }
    .media .btn-success{
        margin-top: 3px;
    }

    .w3eden .label.label-info{
        font-size: 11px;background: rgba(132, 111, 168, 0.38) !important; padding: 5px 10px !important;;border-radius: 2px !important;font-weight: 300;font-family: Overpass;letter-spacing: 1px;
    }
    .w3eden .alert.alert-info:before{ line-height: 30px; }
    .wpdm-loading {
        background: url('<?php  echo plugins_url('download-manager/assets/images/wpdm-settings.png'); ?>') center center no-repeat;
        width: 16px;
        height: 16px;
        /*border-bottom: 2px solid #2a2dcb;*/
        /*border-left: 2px solid #ffffff;*/
        /*border-right: 2px solid #c30;*/
        /*border-top: 2px solid #3dd269;*/
        /*border-radius: 100%;*/

    }

    .w3eden .btn{
        border-radius: 0.2em !important;
    }
    .well{ box-shadow: none !important; background: #FFFFFF !important; } .btn{ border: 0 !important; }

    .w3eden .nav-pills a{
        background: #f5f5f5;
    }

    #addonmodal{ background: rgba(0,0,0,0.7); z-index: 9999; }

    #addonmodal .modal-dialog{
        margin-top: 100px;

    }

    .w3eden .form-control,
    .w3eden .nav-pills a{
        border-radius: 0.2em !important;
        box-shadow: none !important;
        font-size: 9pt !important;
    }

    .wpdm-spin{
        -webkit-animation: spin 2s infinite linear;
        -moz-animation: spin 2s infinite linear;
        -ms-animation: spin 2s infinite linear;
        -o-animation: spin 2s infinite linear;
        animation: spin 2s infinite linear;
    }

    @keyframes "spin" {
        from {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -webkit-transform: rotate(359deg);
            -moz-transform: rotate(359deg);
            -o-transform: rotate(359deg);
            -ms-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-moz-keyframes spin {
        from {
            -moz-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -moz-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-webkit-keyframes "spin" {
        from {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -webkit-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-ms-keyframes "spin" {
        from {
            -ms-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -ms-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-o-keyframes "spin" {
        from {
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -o-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    .panel-heading h3.h{
        font-size: 11pt;
        font-weight: 700;
        margin: 0;
        padding: 5px 10px;
        font-family: 'Open Sans';
    }

    .panel-heading .btn.btn-primary{
        margin-top: -4px;
        margin-right: -6px;
        border-radius: 3px;
        border:1px solid rgba(255,255,255,0.8);
        -webkit-transition: all 400ms ease-in-out;
        -moz-transition: all 400ms ease-in-out;
        -o-transition: all 400ms ease-in-out;
        transition: all 400ms ease-in-out;
    }

    .panel-heading .btn.btn-primary:hover{
        margin-top: -4px;
        margin-right: -6px;
        border-radius: 3px;
        border:1px solid rgba(255,255,255,1);

    }

    .alert-info {
        background-color: #DFECF7 !important;
        border-color: #B0D1EC !important;
    }

    ul.nav li a:active,
    ul.nav li a:focus,
    ul.nav li a{
        outline: none !important;
    }

    #modalcontents .wrap h2{ display: none; }
    .cpgpanel .panel-heading{
        font-family: Overpass, sans-serif;
        font-size: 12pt !important;
    }
    .cpgpanel td{
        font-family: Overpass, sans-serif;
        font-size: 11pt;
        line-height: 32px !important;
    }
</style>

<div class="wrap w3eden">

<div class="wcheader" style="background: #fff;border-bottom: 1px solid #ddd">
    <div class="container-fluid" style="margin-top: 0;max-width: 1200px">
    <div class="row">
        <div class="col-md-12">
            <img style="max-width: 100%" src="<?php echo WPDM_BASE_URL; ?>assets/images/wpdm-welcome.png"/>
        </div>
    </div>
    </div>

</div>
    <div class="container-fluid" style="margin-top: 10px;max-width: 1200px">
        <div class="row">
            <?php //if(!function_exists('wpdm_tinymce')){ ?>
            <div class="col-md-12"><h3 class="text-center"><span class="text-success">ATTENTION!!</span> What to do next?</h3></div>
            <div class="col-md-12 lead text-center">Install the following plugins to make your site more awesome<br/><br/></div>
            <div class="col-md-6">
                <div class="panel panel-success">
                    <div style="overflow: hidden" class="panel-body">
                        <div class="media">
                            <div class="pull-left">
                                <img width="96px" src="<?php echo WPDM_BASE_URL ?>assets/images/liveforms-logo.png" />
                            </div>
                            <div class="media-body">
                                <b><a target="_blank" href="<?php echo admin_url('/plugin-install.php?s=liveforms&tab=search&type=term') ?>">WordPress Contact Form Builder</a></b>
                                <p>Ultimate Solution For Building Any Form. Try this even if you are already using another plugin. I'm sure, you will thank me for this later!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-success">
                    <div style="overflow: hidden" class="panel-body">
                        <div class="media">
                            <div class="pull-left">
                                <img width="96px" src="<?php echo WPDM_BASE_URL ?>assets/images/attire-blocks.png" />
                            </div>
                            <div class="media-body">
                                <b><a target="_blank" href="<?php echo admin_url('/plugin-install.php?s=attire-blocks&tab=search&type=term') ?>">Gutenberg Blocks and Page Layouts</a></b>
                                <p>Create Pages in a fast and easy way. Attire Blocks makes building pages and posts easier than ever with beautifully designed Gutenberg blocks and page layouts.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div style="overflow: hidden" class="panel-body">
                        <div class="media">
                            <div class="pull-left">
                                <img width="96px" src="<?php echo WPDM_BASE_URL ?>assets/images/wpdm-gb.png" />
                            </div>
                            <div class="media-body">
                                <b><a target="_blank" href="https://www.wpdownloadmanager.com/download/gutenberg-blocks/">Gutenberg Blocks</a></b>
                                <p class="m-0">WordPress Download Manager shortcode helper for the block editor.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4" >
                <div class="panel panel-default">
                    <div style="overflow: hidden" class="panel-body">
                        <div class="media">
                            <div class="pull-left">
                                <img width="96px" src="<?php echo WPDM_BASE_URL ?>assets/images/wpdmpp.png" />
                            </div>
                            <div class="media-body">
                                <b><a href="https://www.wpdownloadmanager.com/download/premium-package-complete-digital-store-solution/">Premium Package</a></b>
                                <p class="m-0">Sell digital product securely and easily, accept onetime or recurring payment.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div style="overflow: hidden" class="panel-body">
                        <div class="media">
                            <div class="pull-left">
                                <img width="86px" style="padding: 5px" src="<?php echo WPDM_BASE_URL ?>assets/images/wpdm-es.png" />
                            </div>
                            <div class="media-body">
                                <b><a target="_blank" href="https://www.wpdownloadmanager.com/download/wpdm-extended-short-codes/">Extended Short-codes</a></b>
                                <p class="m-0">WPDM Extended Short-codes add-on will give you better experience in using WordPress</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div style="clear: both"></div>


            <?php //}

            ?>

            <div class="col-md-6">
                <div class="well">
                    <div class="media">
                        <div class="pull-right">
                            <a href="https://www.wpdownloadmanager.com/downloads/free-add-ons/" class="btn btn-success btn-lg">Explore Free Add-ons <i class="fa fa-angle-double-right"></i></a>
                        </div>
                        <div class="media-body">
                            <b>Free Add-ons</b>
                            There are more free add-ons
                        </div> </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="well">
                    <div class="media">
                        <div class="pull-right">
                            <a href="https://www.wpdownloadmanager.com/download/attire-allinone-wordpress-theme/" class="btn btn-info btn-lg">Download Now! <i class="fa fa-angle-double-right"></i></a>
                        </div>
                        <div class="media-body">
                            <b>Need a better theme?</b>
                            Free WordPress Theme for Digital Shops.
                        </div> </div>
                </div>
            </div>

            <!-- div class="col-md-12">
                <br/>
                <div class="panel panel-default cpgpanel">
                    <div class="panel-heading">
                        Create Pages:
                    </div>
                    <table class="table table-striped">
                        <tr>
                            <td>Login Page [wpdm_login_form]</td>
                            <td class="text-right"><button class="btn btn-primary btn-sm btn-cpg" data-shortcode="[wpdm_login_form]">Create</button></td>
                        </tr>
                        <tr>
                            <td>Signup Page [wpdm_reg_form]</td>
                            <td class="text-right"><button class="btn btn-primary btn-sm btn-cpg" data-shortcode="[wpdm_reg_form]">Create</button></td>
                        </tr>
                        <tr>
                            <td>User Dashboard Page [wpdm_user_dashboard]</td>
                            <td class="text-right"><button class="btn btn-primary btn-sm btn-cpg" data-shortcode="[wpdm_user_dashboard]">Create</button></td>
                        </tr>
                    </table>
                </div>
            </div -->

            <div class="col-md-12 lead">
                <hr/>
                <h3>What's New?</h3>
                What new with WordPress Download Manager v3.2.x:
                <hr/>
            </div>

            <div class="col-md-4 r">
                <b>Media Protection</b>
                Protection media library files quickly using password, control who can access or not.

            </div>
            <div class="col-md-4 r">

                <b>Asset Manager</b>
                Added awesome asset management option, get control over all files.Upload, edit, delete or update directly from wp admin.

            </div>
            <div class="col-md-4 r">

                <b>More Features</b>
                Improved admin UI and front-end short-code templates, one click updates for wpdm add-ons, improved gutenberg compatibility.
            </div>


            <div class="col-md-12 lead">
                <hr/>
                Lets start: Admin Menu <i class="fa fa-angle-double-right"></i> <a href="<?php echo admin_url('edit.php?post_type=wpdmpro'); ?>">Downloads</a> <i class="fa fa-angle-double-right"></i> <a href="<?php echo admin_url('post-new.php?post_type=wpdmpro'); ?>">Add New</a>
            </div>

        </div>

    </div>
    <div class="modal fade" id="addonmodal" tabindex="-1" role="dialog" aria-labelledby="addonmodalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Add-On Installer</h4>
                </div>
                <div class="modal-body" id="modalcontents">
                    <i class="fas fa-sun  fa-spin"></i> Please Wait...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <a type="button" id="prcbtn" target="_blank" href="https://www.wpdownloadmanager.com/cart/" class="btn btn-success" style="display: none" onclick="jQuery('#addonmodal').modal('hide')">Checkout</a>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    jQuery(function(){


        jQuery(".btn-install, .btn-purchase").click(function(){
            jQuery('#modalcontents').html('<i class="fas fa-sun  fa-spin"></i> Please Wait...');
        });
        jQuery('#addonmodal').on('shown.bs.modal', function (e) {
            if(jQuery(e.relatedTarget).hasClass('btn-install')){
                jQuery('.modal-dialog').css('width','500px');
                jQuery('.modal-footer .btn-danger').html('Close');
                jQuery('#modalcontents').css('padding','20px').css('background','#ffffff');
                jQuery.post(ajaxurl,{action:'wpdm-install-addon', addon: e.relatedTarget.rel}, function(res){
                    notice = "<div class='alert alert-info'>For any reason, if auto installation failed, close this popup, click on add-on title, download the add-on from our site, then install manually as you do for regular plugins.</div>"
                    jQuery('#modalcontents').html(res.replace('Return to Plugin Installer','')+notice);
                })
            }

            if(jQuery(e.relatedTarget).hasClass('btn-purchase')){
                jQuery('.modal-dialog').css('width','800px');
                jQuery('.modal-footer').css('margin',0);
                jQuery('.modal-footer .btn-danger').html('<i class="fas fa-sun  fa-spin"></i> Please Wait...');
                jQuery('#modalcontents').css('padding',0).css('background','#f2f2f2').html("<iframe onload=\"jQuery('.modal-footer .btn-danger').html('Continue Shopping...');jQuery('#prcbtn').show();\" style='width: 100%;padding-top: 20px; background: #f2f2f2;height: 300px;border: 0' src='https://www.wpdownloadmanager.com/?addtocart="+e.relatedTarget.rel+"'></iframe>");
            }
        })


    });
</script>


