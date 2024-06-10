
 <div id="mo2f_2fa_intro" class = "modal" style="display: block;">
            <div id="mo2f_2fa_intro_modal" class="modal-content" style="width: 40%;overflow: hidden;padding:50px;" >

            <div class="modal-header" style="border-bottom: none;">
               <h2 class="modal-title" style="text-align: center; font-size: 20px; color: #2980b9">
                   <span id="closeintromodal" class="close modal-span-close" onclick="skipintro();">X</span>
                </h2>
       </div>

            <div class="modal-body" style="height: auto;">
                <center>
                    <div class="checkmark-circle" >
                        <div class="background"></div>
                        <div class="checkmark draw"></div>
                    </div>
                    <h1>Awesome!!</h1>
                    <h2 style="color: black;font-size: 20px;">You are ready to use Two Factor.</h2>
                </center>
                <div >
                    <h3 style="color: black;display:none;" ><span style="color:red;">Logout</span> : You can logout and get the same experience as your users. </h3>

                </div>
            </div>

            <div class="modal-footer" style="border: 0px;">
                <button type="button" class="button button-primary button-large modal-button readytogo  mo2f_advance_setting" onclick="skipintro();">Advance Settings</button>
                <button type="button" class="button button-primary button-large modal-button mo2fa_tour_button"  title="Logout and check the user experience" onclick="mo2f_userlogout()">Logout and Configure</button>
                    <div class="mo2f_tooltip_addon logout button button-primary button-large modal-button " style="float: left;border: 2px solid black;border-left:none;width: 6%;box-shadow: none;text-decoration:none;background-color: #2271b1;margin-left: -5px;min-height: 50px;">
                        <span class="dashicons dashicons-info mo2f_info_tab" style="color: white;font-size: 27px;margin-top: 14px;margin-left: -12px;"></span>
                        <span class="mo2f_tooltiptext_addon mo2f_logout_and_configure_info" style="font-size: 20px;font-family: auto;   text-align: justify;font-weight: lighter;background-color: #2EB150; font-size: 20px;">
                            <ul style="list-style-type:square;margin: 10px 18px 10px 18px;"><li>This will logout you and will ask you to set your 2FA on next login.</li><li> New and existing users can set their 2FA on next login.</li></ul>
                        </span>
                        <span class="mo2f_tooltiptext_addon" style="color: #2EB150;background: none; margin-left: -200px; margin-top: -38px;">
                            <span class="dashicons dashicons-arrow-down" style="font-size: 300%;"></span>
                        </span>
                    </div>
                
            </div>
            </div>
        </div>
         <form name="f" id="mo2f_skiploginform" method="post" action="">
             <input type="hidden" name="mo2f_skiplogin_nonce" value="<?php echo esc_html(wp_create_nonce( 'miniorange-2-factor-skiplogin-failed-nonce' )); ?>"/>
             <input type="hidden" name="option" value="mo2f_skiplogin"/>
         </form>
        <form name="f" id="mo2f_userlogoutform" method="post" action="">
             <input type="hidden" name="mo2f_userlogout_nonce" value="<?php echo esc_html(wp_create_nonce( 'miniorange-2-factor-userlogout-failed-nonce' )); ?>"/>
             <input type="hidden" name="option" value="mo2f_userlogout"/>
         </form>

 <script>
     function mo2f_userlogout() {
         jQuery("#mo2f_userlogoutform").submit();
     }

     function skipintro() {
         jQuery("#mo2f_skiploginform").submit();
     }
     </script>

 <style>

     .modalhover:hover{
        border:2px solid #2EB150;
        background: #2EB150 !important;
        color:white !important;
     }
     .checkmark-circle {
         width: 150px;
         height: 150px;
         position: relative;
         display: inline-block;
         vertical-align: top;
     }
     .checkmark-circle .background {
         width: 150px;
         height: 150px;
         border-radius: 50%;
         background: #2EB150;
         position: absolute;
     }
     .checkmark-circle .checkmark {
         border-radius: 5px;
     }
     .checkmark-circle .checkmark.draw:after {
         -webkit-animation-delay: 100ms;
         -moz-animation-delay: 100ms;
         animation-delay: 100ms;
         -webkit-animation-duration: 1s;
         -moz-animation-duration: 1s;
         animation-duration: 1s;
         -webkit-animation-timing-function: ease;
         -moz-animation-timing-function: ease;
         animation-timing-function: ease;
         -webkit-animation-name: checkmark;
         -moz-animation-name: checkmark;
         animation-name: checkmark;
         -webkit-transform: scaleX(-1) rotate(130deg);
         -moz-transform: scaleX(-1) rotate(130deg);
         -ms-transform: scaleX(-1) rotate(130deg);
         -o-transform: scaleX(-1) rotate(130deg);
         transform: scaleX(-1) rotate(130deg);
         -webkit-animation-fill-mode: forwards;
         -moz-animation-fill-mode: forwards;
         animation-fill-mode: forwards;
     }
     .checkmark-circle .checkmark:after {
         opacity: 1;
         height: 75px;
         width: 37.5px;
         -webkit-transform-origin: left top;
         -moz-transform-origin: left top;
         -ms-transform-origin: left top;
         -o-transform-origin: left top;
         transform-origin: left top;
         border-right: 15px solid white;
         border-top: 15px solid white;
         border-radius: 2.5px !important;
         content: '';
         left: 25px;
         top: 75px;
         position: absolute;
     }

     @-webkit-keyframes checkmark {
         0% {
             height: 0;
             width: 0;
             opacity: 1;
         }
         20% {
             height: 0;
             width: 37.5px;
             opacity: 1;
         }
         40% {
             height: 75px;
             width: 37.5px;
             opacity: 1;
         }
         100% {
             height: 75px;
             width: 37.5px;
             opacity: 1;
         }
     }
     @-moz-keyframes checkmark {
         0% {
             height: 0;
             width: 0;
             opacity: 1;
         }
         20% {
             height: 0;
             width: 37.5px;
             opacity: 1;
         }
         40% {
             height: 75px;
             width: 37.5px;
             opacity: 1;
         }
         100% {
             height: 75px;
             width: 37.5px;
             opacity: 1;
         }
     }
     @keyframes checkmark {
         0% {
             height: 0;
             width: 0;
             opacity: 1;
         }
         20% {
             height: 0;
             width: 37.5px;
             opacity: 1;
         }
         40% {
             height: 75px;
             width: 37.5px;
             opacity: 1;
         }
         100% {
             height: 75px;
             width: 37.5px;
             opacity: 1;
         }
     }
     body{
         background-color: #e6e6e6;
         width: 100%;
         height: 100%;
     }
     #success_tic .page-body{
         max-width:300px;
         background-color:#FFFFFF;
         margin:10% auto;
     }
     #success_tic .page-body .head{
         text-align:center;
     }
     /* #success_tic .tic{
	   font-size:186px;
	 } */
     .close{
         opacity: 1;
         position: absolute;
         right: 0px;
         font-size: 30px;
         padding: 3px 15px;
         margin-bottom: 10px;
         float: right;
         font-size: 21px;
         font-weight: 700;
         line-height: 1;
         color: #000;
         text-shadow: 0 1px 0 #fff;
     }

 </style>
