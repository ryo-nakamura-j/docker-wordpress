    <div class="mo2f_table_layout">
        <div class="mo2f_vertical-submenu">
            <a id="defaultOpen" class="nav-tab"  onclick="openPage('rba', this, '#2271b1')" 
            ><?php echo __( 'Remember Device', 'miniorange-2-factor-authentication' ); ?></a>
            <a id="onclickOpen" class="nav-tab"  onclick="openPage('personal', this, '#2271b1')" ><?php echo __( 'Customize login Popups', 'miniorange-2-factor-authentication' ); ?></a>
            <a id="onclick" class="nav-tab"  onclick="openPage('shortcode', this, '#2271b1')" ><?php echo __( 'Shortcode', 'miniorange-2-factor-authentication' ); ?></a>
        </div>
        <br><br><br><br>
        <div class="mo2f_addon_spacing">
            <div id="rba" class="mo2f_addon">
              <?php mo2f_rba_description($mo2f_user_email); ?>
            </div>

            <div id="personal" class="mo2f_addon">
              <?php mo2f_personalization_description($mo2f_user_email);?>
              <br>
            </div>

            <div id="shortcode" class="mo2f_addon">
                <?php mo2f_shortcode_description($mo2f_user_email);?>
                <br>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function(){
            sessionStorage.setItem("code", "rba");
        });
        function openPage(pageName,elmnt,color) {
          var i, tabcontent, tablinks;
          tabcontent = document.getElementsByClassName("mo2f_addon");
          for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
            document.getElementById("defaultOpen").style.color = "black";
            document.getElementById("onclickOpen").style.color = "black";
            document.getElementById("onclick").style.color = "black";

          }
          tablinks = document.getElementsByClassName("nav-tab");
          for (i = 0; i < tablinks.length; i++) {
            tablinks[i].style.backgroundColor = "";
          }
          document.getElementById(pageName).style.display = "block";
          elmnt.style.backgroundColor = color;
          elmnt.style.color="white";
          sessionStorage.setItem("code", pageName);
        }

        // Get the element with id="defaultOpen" and click on it
        if(sessionStorage.getItem("code")=='personal')
            document.getElementById("onclickOpen").click();
        else if(sessionStorage.getItem("code")=='shortcode')
            document.getElementById("onclick").click();
        else
            document.getElementById("defaultOpen").click();
    </script>