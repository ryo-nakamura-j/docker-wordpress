<?php
global $mo2f_dirName;

echo'  <div class="mo_twofa_footer"> 
  <div class="mo-2fa-mail-button">
  <img id= "mo_wpns_support_layout_tour" src="'.esc_url(dirname(plugin_dir_url(__FILE__))).'/includes/images/mo_support_icon.png" class="show_support_form"  onclick="openForm()">
  </div>
  <button type="button" class="mo-2fa-help-button-text" onclick="openForm()"">24x7 Support<br>Need Help? Drop us an Email</button>
  </div>';
?>


<div id="feedback_form_bg" > 
<div class="mo2f-chat-popup" id="myForm" style="display:none; width: 100%;padding: 1%; padding-top: 50%;background-color: rgba(0,0,0,0.61);">
  
  <div id ='mo_wpns_support_layout_tour_open' style="background-color: white;min-height: 370px;width: 45%; text-align: right;float: right;border-radius: 8px;">
    <div style="min-height: 600px;background-image: linear-gradient(to bottom right, #dffffd, #8feeea);width: 43%;float: left;padding: 10px; border-bottom-left-radius: 8px;border-top-left-radius: 8px;">
          <center>
            <?php
            echo '
              <img src="'.esc_url(dirname(plugin_dir_url(__FILE__))).'/includes/images/minirange-logo.png" style="width: 46%;">';
            ?>
        <h1 style="  font-family: 'Roboto',sans-serif !important;">Contact Information</h1>
      </center><br>
      <div style="text-align: left;padding: 3%;">
      <form name="f" method="post" action="" id="mo_wpns_query_form_close" >
          <table>
              <tr>
                  <td>
                      <span class="dashicons dashicons-email"></span>
                  </td>
                  <td><h3>2fasupport@xecurify.com</h3>
                  </td>
              </tr>
              <tr>
                  <td>
                      <span class="dashicons dashicons-email"></span>
                  </td>
                  <td><h3>info@xecurify.com</h3>
                  </td>
              </tr>
              <tr>
                  <td>
                      <span class="dashicons dashicons-admin-site-alt3"></span>
                  </td>
                  <td><h3><a href="https://miniorange.com/" target="_blank"> www.miniorange.com</a></h3>
                  </td>
              </tr>
              <tr>
                  <td>

                  </td>
              </tr>

          </table>
      </div>
  </div>
  <div class="mo2f-form-container">
      <span class="mo2f_rating_close" onclick="closeForm()">×</span>
    <h1 style="text-align: center;    font-family: 'Roboto',sans-serif !important;">24/7 Support</h1>
      

    
    <div style="width: 100%;">
                   <div id="low_rating" style="display: block; width: 100%;">
                    <div style=" float: left;">
                        <?php
                        echo '
                        <table class="mo2f_new_support_form_table">
                        <tr><td>
                          <input type="email" class="mo2f_new_support_form_input" id="query_email" name="query_email"  value="'.esc_attr($email).'" placeholder="Enter your email" required />
                          </td>
                        </tr>
                        <tr><td>
                          <input type="text" class="mo2f_new_support_form_input" name="query_phone" id="query_phone" value="'.esc_attr($phone).'" placeholder="Enter your Phone number"/>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <textarea id="query" name="query" class="mo2f_new_support_form_input" style="border-radius:25px ; border: 1px solid gray; resize: none;width:100%;" cols="52" rows="9" placeholder="Write your query here"></textarea>
                          </td>
                        </tr>
                        <tr>
                        <td colspan="2" style="text-align: center">
                        <div>
                        <input type="hidden" name="mo_2fa_plugin_configuration" value="mo2f_send_plugin_configuration"/>
                            
                            <input type="checkbox" id="mo2f_send_configuration"
                                   name="mo2f_send_configuration"
                                   value="1" checked/>
                                   <p>Send plugin Configuration</p>
                        </div>
                        
                        </td> </tr>
                      </table>
                        ';
                        ?>

                        <div id="send_button" style="display: block; text-align: center;">
                        <input type="button" name="miniorange_skip_feedback"
                               class="button button-primary button-large" value="Send" onclick="document.getElementById('mo_wpns_query_form_close').submit();"/>
                    </div>
                   
                    </div>
                    
                    </div>

    </div>
                <input type="hidden" name="option" value="mo_wpns_send_query"/>
          
    </form></td>
        </tr>
        </tbody>
        </table>

       
  </div>

</div>
</div>
</div>

<script>
    function moSharingSizeValidate(e){
        var t=parseInt(e.value.trim());t>60?e.value=60:10>t&&(e.value=10)
    }
    function moSharingSpaceValidate(e){
        var t=parseInt(e.value.trim());t>50?e.value=50:0>t&&(e.value=0)
    }
    function moLoginSizeValidate(e){
        var t=parseInt(e.value.trim());t>60?e.value=60:20>t&&(e.value=20)
    }
    function moLoginSpaceValidate(e){
        var t=parseInt(e.value.trim());t>60?e.value=60:0>t&&(e.value=0)
    }
    function moLoginWidthValidate(e){
        var t=parseInt(e.value.trim());t>1000?e.value=1000:140>t&&(e.value=140)
    }
    function moLoginHeightValidate(e){
        var t=parseInt(e.value.trim());t>50?e.value=50:35>t&&(e.value=35)
    }

function openForm() {
  document.getElementById("myForm").style.display = "block";
  document.getElementById("feedback_form_bg").style.display = "block";
}

function closeForm() {
  document.getElementById("myForm").style.display = "none";
  document.getElementById("feedback_form_bg").style.display = "none";

}

jQuery(document).ready(function () {
  jQuery( ".mo-2fa-mail-button" ).hover(
    function() {
      $('.mo-2fa-help-button-text').css('display','block');
    }, function() {
      $('.mo-2fa-help-button-text').css('display','none');
    }
  );
});

</script>