<?php
/* JR Pass Header*/
global $wp_query;
$post_id = $wp_query->post->ID;
global $jrheader;
$jrheader = str_replace('"','\"',$jrheader);
if(sizeof($jrheader)>0 && (strpos($jrheader, '#jranchor2') !== false)){
  echo '<script type="text/javascript"> document.getElementById("jranchors").style.display = "block"; document.getElementById("jranchors").innerHTML = "<div class=\"row\">'.$jrheader.'</div>";</script>';
}

if($post_id == 4608){//@@@
    echo '<script type="text/javascript">document.getElementById("tabbutton3").onclick = function() {   document.getElementById("tabs_container").className += " hidden";   window.location.href = "https://web.tifs.com.au/pa.php?page=login";}</script>';
}

?>

<script>
$( "table.itinerary tbody tr td table tbody tr:first-child" ).addClass('classone');
$( "table.itinerary ul li:nth-child(1)" ).addClass('one'); 
$( "table.itinerary ul li:nth-child(2)" ).addClass('two'); 
$( "table.itinerary ul li:nth-child(3)" ).addClass('three'); 
$( "table.itinerary ul li:nth-child(4)" ).addClass('four'); 
$( "table.itinerary ul li:nth-child(5)" ).addClass('five'); 
$( "table.itinerary ul li:nth-child(6)" ).addClass('six'); 
$( "table.itinerary ul li:nth-child(7)" ).addClass('seven'); 
$( "table.itinerary ol li:nth-child(1)" ).addClass('one'); 
$( "table.itinerary ol li:nth-child(2)" ).addClass('two'); 
$( "table.itinerary ol li:nth-child(3)" ).addClass('three'); 
$( "table.itinerary ol li:nth-child(4)" ).addClass('four'); 
$( "table.itinerary ol li:nth-child(5)" ).addClass('five'); 
$( "table.itinerary ol li:nth-child(6)" ).addClass('six'); 
$( "table.itinerary ol li:nth-child(7)" ).addClass('seven'); 
</script>

  <div class="footer container">
    <div class="row">
    <div class="col-xs-12"><div class="ribon-red-desktop"></div></div>
    </div>
    <div class="row"> 

      <div class="col-xs-12 col-sm-4">
        <div class="row">
          <div class="col-xs-12">
            <?php wp_nav_menu( array('menu'=>'footer-menu')) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <div id="social" class="round-social-grey">
                <?php do_action("print_social_buttons"); ?>
            </div>

    <!-- SSL Seal -->
    <table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose GeoTrust SSL for secure e-commerce and confidential communications.">
    <tr>
    <td width="135" align="center" valign="top"><script type="text/javascript" src="https://seal.geotrust.com/getgeotrustsslseal?host_name=https://www.nx.jtbtravel.com.au&amp;size=S&amp;lang=en"></script><br />
    <a href="http://www.geotrust.com/ssl/" target="_blank"  style="color:#000000; text-decoration:none; font:bold 7px verdana,sans-serif; letter-spacing:.5px; text-align:center; margin:0px; padding:0px;"></a></td>
    </tr>
    </table>

          </div>
        </div>
      </div>
      <?php
        $map1 = get_theme_mod("footer_map_1");
        $map2 = get_theme_mod("footer_map_2");
        $map3 = get_theme_mod("footer_map_3");
        $map4 = get_theme_mod("footer_map_4");

        if ($map2) { ?>
        <div class="col-sm-4 col-xs-12">
          <h3>JTB Sydney</h3>
          <iframe src="<?php echo $map2; ?>" width="300" height="200" frameborder="0" style="border:0"></iframe>
          <p><small><strong>Level 18, 456 Kent Street (Town Hall House), Sydney NSW 2000</strong></small></p>
        </div>
        <div class="col-sm-4 col-xs-12">
          <h3>JTB Melbourne</h3>
          <iframe src="<?php echo $map1; ?>" width="300" height="200" frameborder="0" style="border:0"></iframe>
          <p><small><strong>Level 6, 31 Queen Street, Melbourne VIC 3000</strong></small></p>
        </div>

        <?php
        } else { ?>

        <div class=" col-sm-offset-4 col-sm-4 col-xs-12">
          <iframe src="<?php echo $map1; ?>" width="300" height="200" frameborder="0" style="border:0"></iframe>
        </div>

        <?php
        }
      ?>

    </div>
    <?php
      if ($map3) { ?>
      <div class="row">
      <?php
        if ($map4) { ?>

        <div class="col-sm-offset-4 col-sm-4 col-xs-12">
          <iframe src="<?php echo $map4; ?>" width="300" height="200" frameborder="0" style="border:0"></iframe>
        </div>

        <div class="col-sm-4 col-xs-12">
          <iframe src="<?php echo $map3; ?>" width="300" height="200" frameborder="0" style="border:0"></iframe>
        </div>

        <?php
        } else { ?>

        <div class="col-sm-offset-8 col-sm-4 col-xs-12">
          <iframe src="<?php echo $map3; ?>" width="300" height="200" frameborder="0" style="border:0"></iframe>
        </div>

        <?php 
        } ?>
      </div>
      <?php
      }
    ?>

    <div class="footer-bottom">
        <div class="row">
        <div class="col-sm-12">
          <p align="right" style="margin-top: 5px; padding-right: 10px;"><?php echo get_theme_mod("footer-copyright"); ?></p>
        </div>
      </div>
    </div>
  </div>
