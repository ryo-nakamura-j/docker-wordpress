<div class="wrap">


<h1>JTB Custom Plugin Options</h1>

<h3>Shortcodes</h3>
<p>The icons are added with [icon n="name" w="width"] - where name is the below icon name, and w is the width in pixels (default is 50px, which is seen below.)</p>

<?php
echo print_all_icon_shortcode();
?>
<div class="clear"></div>

<h3>Popup Boxes</h3>
<p>You can have buttons, links or banners which, when clicked, make a popup box appear.</p>
<p>use the shortcode:</p>
<p><input autocomplete="off" onclick="this.select()"  value='[popup n="Name of Button" t="Type" m="1,2,3,4,5-Multile on one page"]'  style="width:150px;"></p>
<p>Type:</p>
<ul>
<li>Defautl/ not specified = button</li>
<li>link = make the text into a text link, rather than a button</li>
<li>blank = setup the popup box, but don't output any button or link (for linking in with banner)</li>
</ul>


<div style="width: 180px;    position: relative;    float: left;    margin: 0 0 25px;">
<?php
echo do_shortcode( '[popup n="Name of Button"]Test[/popup]' );
?>
<p><input autocomplete="off" onclick="this.select()"  value='[popup n="Name of Button"]'  style="width:150px;"></p>
</div>
<div style="width: 180px;    position: relative;    float: left;    margin: 0 0 25px;">
 <?php
echo do_shortcode( '[popup n="Name of Link" t="link"]Test[/popup]' );
?>
<p><input autocomplete="off" onclick="this.select()"  value='[popup n="Name of Link" t="link"]'  style="width:150px;"></p>
</div>
<div style="width: 180px;    position: relative;    float: left;    margin: 0 0 25px;">
 <?php
echo do_shortcode( '[popup n="Name of Link" t="blank"]Test[/popup]' );
?>
<p><input autocomplete="off" onclick="this.select()"  value='[popup n="Name of Link" t="blank"]'  style="width:150px;"></p>
</div>

<div class="clear"></div>

<h3>3 Image Gallery</h3>
<p>This shortcode is used to add 3 images to the top of the page instead of a banner - first used in the Hawaii template</p>
<p>You can add more than one gallery per page, and you can add an unlimited number of images. On mobile - the 3 images collapse into a triangle of 3 images</p>



<div style="width: 180px;    position: relative;    float: left;    margin: 0 0 25px 25px;">
<p>Add this shortcode after the page title to add the 1st block of 3 images as a header</p>
<p><input autocomplete="off" onclick="this.select()"  value='[gallery3]'  style="width:150px;"></p>
</div>
<div style="width: 180px;    position: relative;    float: left;    margin: 0 0 25px 25px;">
<p>Add this shortcode anywhere in the page to add subsequent galleries into the page</p>
<p><input autocomplete="off" onclick="this.select()"  value='[gallery3 n="2"]'  style="width:150px;"></p>
</div>

<div class="clear"></div>




<h3>Sitemap</h3>
<p>The sitemap shows a list of every page on the website.</p>
<p>If you are logged in to admin - it shows page ID, edit link and hidden pages.</p>
<p>The menu order places pages in the different sections. Pages with a menu-order 0 are added to the end of the 'other-pages' list. Sub-pages can all have a menu-order 0 and will be added under their parent page. For Japan information and Locations, use 33 and 44 respectively to add them to those sections.</p>
<p>
<div class="clear"></div>




<h3>Drive Template</h3>
<p>For the Drive Products - add the beside-banner-content into the banner caption text box.</p>
<p>For the price formatting - set it to H6 - and make the 'from' text italic.</p>
<div class="clear"></div>



<h3>Add reviews to tour page</h3>
<p>To add reviews to a tour page use the format below - copy and paste the tag label from the reviews section</p>

<div style="width: 180px;    position: relative;    float: left;    margin: 0 0 25px 25px;">
<p>Add this shortcode anywhere in the page</p>
<p><input autocomplete="off" onclick="this.select()"  value='[jtb-widget f="reviews" n="TagName"]'  style="width:150px;"></p>
</div>
<div class="clear"></div>






<h3>Product list links</h3>
<p>To link to a 3rd party website - add the text link into the TEXTURL box - if there isn't one there - I can add it.</p>
<textarea rows="20" cols="80">
		    while ( have_rows('advertising_banner') ) : the_row();
		 		$newtab="";
		        // display a sub field value
				if(get_sub_field('texturl')!=""){
					if (get_sub_field('texturl')=="_blank")
				}
				$link2 = 0;
				//target="_blank"
				//texturl
				?>

			  	<div class="col-sm-3">
					<a href="<?php the_sub_field('link') ?>"><img src="<?php the_sub_field('image') ?>" class="img-responsive" alt=""></a>
				</div>
				<\?php ;
		    endwhile;
</textarea>
<div class="clear"></div>




<h3>Tour contact forms</h3>
<p>The tour page contact/ quote forms are inserted using this custom JTB plugin so that they can be replaced with an error message if the product is hidden from search/menus</p>

<p>The contact form is added depending on what the tour parent is (what type of tour it is.)</p>

<div style="width: 180px;    position: relative;    float: left;    margin: 0 0 25px 25px;">
<p>Add this shortcode to insert the contact form</p>
<p><input autocomplete="off" onclick="this.select()"  value='[jtb-widget f="contact-form"]'  style="width:150px;"></p>
</div>
<div class="clear"></div>




<h3>JR Pass booking flow charts</h3>
<p>Shortcode for inserting the flowchart (edit button will be attached if you're logged in to the admin section.</p>


<p>Regular chart</p>
<p><input autocomplete="off" onclick="this.select()"  value='[jtb-widget f="jr-pass-chart"]'  style="width:150px;"></p>

<p>eTicket chart</p>
<p><input autocomplete="off" onclick="this.select()"  value='[jtb-widget f="jr-pass-chart2"]'  style="width:150px;"></p>



<h3>Print cruise data</h3>
 

<p><input autocomplete="off" onclick="this.select()"  value='[cruise n="1"]'  style="width:150px;"></p>





<h3>Link to tab</h3>
 
<p>#tab2link - opens the page, switches to tab 2 & scrolls down to it</p>

<p>#tab2 - loads the page with tab2 open</p>





</div>