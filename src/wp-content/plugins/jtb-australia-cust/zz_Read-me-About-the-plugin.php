<?php /*


TP PLUGIN TEMPLATE ADD IMG TXT
tp plugin template 


$imgtemp=get_sub_field('image');
$imgjtb2 = $imgtemp['url'];
if(get_sub_field('imagetxt')){
$imgjtb2 = get_sub_field('imagetxt');
}




checkout -

<a href="https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-pass-collection/">Office locations</a>

This plugin adds custom CSS and Javascript to the site,
Along with extra WorpDress functions and shortcodes.

In this way - if the TourPlan theme is changed/ updated - all our JTB Australia modifications can stay as they are.

 - -

 Update TP Theme:

> replace page.php, search.php, single.php, tickets.php-(deleted?), tickets-2nd-lvl, day-tour-template-in-folder (copy over day-tour-template-contact)

tickets.php
add to hide some items:

if ( get_sub_field('texturl');  =="hidden"){
	continue;
}

txt image







if ( get_sub_field('texturl') =="hidden"){
	continue;
}


	$url2 = "/";
    if((get_sub_field('texturl')!="" )&&(get_sub_field('texturl')!=null )&&(get_sub_field('texturl')!=false )){
    	$url2=get_sub_field('texturl');
    }else{
    	$url2=get_sub_field('box_link');
    }
$imgurl2 = get_sub_field('box_image');
if(get_sub_field('imgtext')){
	$imgurl2 = get_sub_field('imgtext');
}



			        // display a sub field value
					?>

					  <div class="col-sm-3">
						<div class="thumbnail">
							<div class="caption"><a href="<?php echo $url2; ?>">
								<h4><?php the_sub_field('box_title') ?></h4></a>
							</div>
					  		<a href="<?php echo $url2; ?>"><img src="<?php echo $imgurl2;  ?>" alt=""></a>
						</div>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>






 


HEADER _ need to extend the menu1 because we added agent link
5, 7 -> 1 11 (in the div classes)
          <div class="col-sm-6 col-md-1">
            <div class="row">
              <div class="col-xs-3">
????????????????????????????????????????????????????????? below = live now?
                 <div class="col-sm-6 col-md-7">
            <div class="row">
              <div class="col-xs-5">



                <?php if (get_option('tp_site_search_enabled')) { ?>
                <form role="search" method="get" class="form-search" id="search-forms" action="<?php echo esc_url( site_url( '/' ) ); ?>" >
                    <input class="input-search" id="search-forms" type="" type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Search" /><!--
                    --><button type="submit" class="btn-search" id="searchsubmit"><i class="glyphicon glyphicon-search"></i></button>
                </form>
                <?php } ?>
              </div>
              <div class="col-xs-11">
??????????????????????????????
<div class="col-xs-12">



> footer >> replace all above div and footer wp
<?php do_action("print_jtb_footer"); ?>
</div><!-- #wrap -->
<?php wp_footer(); ?>
>>delete g fonts loading (added by me in header)

> add to header: font, social-media, Delete-gFonts
<link href='https://fonts.googleapis.com/css?family=Roboto:400,700,500,300,900|Signika:400,600,700|Droid+Sans:400,700|Material+Icons' rel='stylesheet' type='text/css'>
>>
<div id="social" class="round-social-grey hidden-xs">
<?php do_action("print_social_buttons"); ?>
</div>

new logo and hyperlink phone #
<div class="brand">
	<a href="/" class="header-logo"><img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/07/jtb-logo.png"></a>
	<div class="header-info">
	<p class="country-name" style="">Australia</p>
	<p class="phone-number"><a href="tel:1300739330">1300 739 330</a></p>
	</div>
</div>

//mobile logo url >>>>> img class="brand-logo" sr
https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/07/jtb-logo.png


> /tp_plugin_template (add the action above the $section line)
do_action('jr_links');
$section = 0;
while (have_rows('sections')) : the_row();

> update /partials/rail_pass.php (add to top of file)
global $jrheader;
global $jrheadercount;
$jrheadercount += 1;
global $post_id2;
if($jrheadercount==1){
	$post_id2->id = $wp_query->post->ID;
}

>>ALSO SAME FILE - replace the div below
ducts = count(get_sub_field('produc
<div id ="jranchor<?php echo $jrheadercount; ?>" class="row section-<?php echo $section; ?> rail-product">

>>add this after this
<div class="tourplan_plugin_section">
$jrheader .= '<div class="col-xs-12 col-md-4 col-lg-4"><a style=" background: '.$group_colour.'; " href="#jranchor'.$jrheadercount.'">'.get_sub_field('group_title').'</a></div>';




was this removed?
>>> TP-config - price.JS change DX mail to $8





HOMEPAGE-2 HIDE HIDDEN STUFF


<h3>Hot Deals</h3>
		<div class="ribon-red-desktop"></div>
		<div class="row hot-deals-home">

			<!-- Four grid -->

			<?php
			// check if the repeater field has rows of data
			if( have_rows('four_grid_boxes') ):

			 	// loop through the rows of data
			    while ( have_rows('four_grid_boxes') ) : the_row();

			        // display a sub field value
			    	$link_url22="";
			    	if(get_sub_field('box_link')){
			    		$link_url22 = get_sub_field('box_link');
			    	}
			    	if(($link_url22=="hidden")||($link_url22=="hide")||($link_url22=="")){
			    		continue;
			    	}

					?>
					  <div class="col-sm-3 col-xs-12">
						<div class="thumbnail">
							<div class="caption"><a href="<?php echo $link_url22; ?>">
								<h4><?php the_sub_field('box_title'); ?></h4></a>
							</div>
					  		<a href="<?php echo $link_url22; ?>"><img src="<?php the_sub_field('box_image'); ?>" alt="..."></a>
						</div>
					</div>
					<?php ;
			    endwhile;









EWAY

<CancelUrl>

WP Mail SMTP



DMARC

dmarc@nx.jtbtravel.com.au
9Fx^md#zRHp1Hn6D^M2yjxq#o7W*w7

v=DMARC1; p=quarantine; sp=none; ruf=mailto:dmarc@nx.jtbtravel.com.au; rf=afrf; pct=100; ri=86400


AUTO DEL EMAILS CPANEL
https://www.sellwebhost.com/knowledgebase/28/How-do-I-auto-delete-old-emails-on-CPanel--.html









TTL
14400

Name
_dmarc

v=DMARC1; p=quarantine; sp=none; ruf=dmarc@nx.jtbtravel.com.au; rf=afrf; pct=100; ri=86400



Add to folders - filder on
wp@koreaski.com.au
mailer@japanski.com.au
mailer@nx.jtbtravel.com.au
dmarc@japantravel.travel
dmarc@otakuanime.com.au
dmarc@australiatravelcentre.com.au

dmarc@japanski.com.au
dmarc@jtbaustralia.com.au
dmarc@japantravel.com.au
dmarc@jtboi.com.au

dmarc@nx.jtbtravel.com.au



587
TLS
wp@koreaski.com.au
cuJdMspp6E1mK*eTIW5Ev2^Zz^Z$p#
mail.koreaski.com.au

mailer@nx.jtbtravel.com.au

mail.jtbaustralia.com.au
mailer@jtbaustralia.com.au
NQPxTL6vyffzc5!SVL33EGkO!bpyc9

mailer@japanski.com.au
x^2dlZ8!Dn8m^Z9*m%4ljLn*m64k%e






Disable WP auto-upgrade emails


add_filter( 'auto_core_update_send_email', 'wpb_stop_auto_update_emails', 10, 4 );

function wpb_stop_update_emails( $send, $type, $core_update, $result ) {
if ( ! empty( $type ) && $type == 'success' ) {
return false;
}
return true;
}

PANTHUR IN MX
14400 10 mx.mailfilter.net.au
14400 20 mx2.mailfilter.net.au
mail

default._domainkey.jtb.com.au.
14400
TXT
v=DKIM1; k=rsa; p=MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtnopAflIMJkhkJdX8c+PP1GwgUzdh6uJ7yz0fVhRhsPXnkVKu/UtKGRy4OQkkV4m54oHApyMnUeAjvzhadm+GPvDWqWw7kSFSwPXxDZ6F065DbCO6GpAtxREQDCujlKgu3kG7wUUtNbiDW2hVVH8v+l1p586M5BBQxesaJ71K9uridG1GjlM7DkIpTmigaFpj7+rTf8rCB6upFCa4+OpuOmt88/ASoF1p+IOsfqEPKH5bklLULhieaRb3lXlokrg/rXXNo27W8Ug83m5+FLWxWmDB93eR8LuOklyaRkUj9j59iN8uOK7KfR2kk84L1fIHw4LdT7dX0KB2bBdosipPQIDAQAB;

14400
TXT
v=spf1 +a +mx +ip4:118.127.46.88 +ip4:118.127.47.253 -all




mail@emailtest.pushka.com
2hB5Zhhjkqw8hFe#*Q767z@Utk


https://new.nx.jtbtravel.com.au
https://test.nx.jtbtravel.com.au

---



old.jtb

http://agent.nx.jtbtravel.com.au
https://agent.jtbxtravel.com.au

agent.nx.jtbtravel.com.au
agent.jtbxtravel.com.au


agent.jtbxtravel.com.au
agent.nx.jtbtravel.com.au


marketing japan luxury products - using for our test website japantravel.com.au - it will be realeased as a new website in late 2017



587
TLS
mail.jtbaustralia.com.au
mailer@jtbaustralia.com.au
NQPxTL6vyffzc5!SVL33EGkO!bpyc9

mailer@japanski.com.au
x^2dlZ8!Dn8m^Z9*m%4ljLn*m64k%e






Dynamic Pages:


SEASONAL

Sumo Tournament
1
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Tokyo%20Sumo%20Tournament%20(from%20Hamamatsucho)/?qty=1A&scu=1&productid=171
2
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Tokyo%20Sumo%20Tournament%20(from%20Hamamatsucho)/?qty=1A&scu=1&productid=171

Snow Monkey Day Tour From Hakuba
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Snow%20Monkey%20Tour%20from%20Hakuba/?qty=1A&scu=1&productid=9999999999

Snow Monkey Tokyo
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Snow%20Monkey%20Tour%20from%20Tokyo/?qty=1A&scu=1&productid=253

Snow Monkey Nagano
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Snow%20Monkey%20Tour%20from%20Nagano/?qty=1A&scu=1&productid=3230


Spec Interest


Maid tour tokyo
https://new.nx.jtbtravel.com.au/day-tours/day-tour/In-depth%20Tour%20of%20Akihabara%20with%20Maid%20Tour%20Guide/?qty=1A&scu=1&productid=7853

Samurai Kembu Swordplay Performance
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Samurai%20%27Kenbu%27%20Sword%20Play%20Performance/?qty=1A&scu=1&productid=6349

Walking Tokyo with Street Food: Edo Kagurazaka & Standing Bar
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Walking%20Tokyo%20with%20Street%20Food-%20Edo%20Kagurazaka%20&%20Standing%20Bar/?qty=1A&scu=1&productid=1828

Walking Tour with Street Food: Evening in Kabukicho & Shinjuku
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Walking%20Tour%20with%20Street%20Food%3A%20Kabukicho%20Evening%20Tour/?qty=1A&scu=1&productid=2016

Kabuki Performance & Kabukiza Gallery Tour
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Kabuki%20Performance%20%26%20Kabukiza%20Gallery%20Tour/?qty=1A&scu=1&productid=7816

Tsukiji Outer Fish Market & Sushi Workshop
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Tsukiji%20Outer%20Fish%20Market%20&%20Sushi%20Workshop/?qty=1A&scu=1&productid=250

Tea Ceremony Experience
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Tea%20Ceremony%20Experience/?qty=1A&scu=1&productid=2017

---

Tokyo Cherry Blossom 1 Day Tour
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Tokyo%20Cherry%20Blossom%201%20Day%20Tour/?qty=1A&scu=1&productid=7795

Tokyo Cherry Blosson & Night River Cruise
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Tokyo%20Cherry%20Blosson%20Night%20River%20Cruise/?qty=1A&scu=1&productid=7796


Kyoto Cherry Blossom 1 Day Tour
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Kyoto%20Cherry%20Blossom%201%20Day%20Tour/?qty=1A&scu=1&productid=6706

Nara Cherry Blossom 1 Day Tour
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Nara%20Cherry%20Blossom%201%20Day%20Tour/?qty=1A&scu=1&productid=6621



Snow Monkey Day Tour From Hakuba
https://new.nx.jtbtravel.com.au/day-tours/day-tour/Snow%20Monkey%20Day%20Tour%20From%20Hakuba/?qty=1A&scu=1&productid=7852



gallery (gallery)

1
img3 (gallery_0_3img_0_img3)

21097 (https://new.nx.jtbtravel.com.au/wp-content/uploads/2016/11/ghibli-hayao-miyazaki-museum-japan.jpg)
img3 (gallery_0_3img_1_img3)

21099 (https://new.nx.jtbtravel.com.au/wp-content/uploads/2016/11/ghibli-museum-totoro-tokyo.jpg)
img3 (gallery_0_3img_2_img3)

21095 (https://new.nx.jtbtravel.com.au/wp-content/uploads/2016/11/ghibli-museum-japan-jtb.jpg)


1.       Tokyo Cherry Blossom 1 Day Tour / GMT code: SUN1GSAK010 / 12 Mar – 16 Apr
2.       Tokyo Cherry Blossom & Night River Cruise / GMT code: SUN1GSAK020 / 24 Mar – 09 Apr
3.       Kyoto Cherry Blossom 1 Day Tour / GMT code: BUS1N55001NNS / 27 Mar – 15 Apr
4.       Nara Cherry Blossom 1 Day Tour / GMT code: GDT1N541K1NNS / 04 Apr – 15 Apr
5.       Sumo Tournament / GMT code: F550_ / 14-28 May & 10-24 Sep

1.       Sumo Tournament / GMT code: F550_ / 14-28 May & 10-24 Sep
2.       Snow Monkey Day Tour From Hakuba / GMT code: BUS1J12902CCC / 20 Feb – 17 Mar
3.       Snow Monkey Day Tour From Tokyo / GMT code: SUN1GF113 / 27 Feb – 22 Mar Mon, Wed, Fri
4.       Snow Monkey Day Tour From Nagano / GMT code: SUN1GF113N / 27 Feb – 22 Mar excluding Saturdays






600 x 250
1200 x 500

<img src="IMAGE-LINK" style="width:600px; height:auto;" />

if the large image is used, using this code, it will be forced to be the same size as the small version - but will be high-res.



Educational Tour Specialists

JTB has been organising school travel for over 75 years/ Japan as a destination is safe an dsecure. Our vast network of over 400 local offices means that your group travels with peace of mind.

Educational focus is important to school tours, and JTB will organise activities to meet these needs. Far from just a language study opportunity, tours to Japan can support subjects including history, visual arts, PE, music, environmental studies and more with teaching content that is both exciting and beneficial.

Whether organising a homestay tour for 50 or a short stay for five, JTB has the experience and knowledge to provide you with a quality, relevant tour at a competitive price.

Contact Us:

melres.au@jtbap.com
1300 739 330
www.nx.jtbtravel.com.au


600 pixels wide and 250 pixels tall


Hide the pickup location
label.pickupsLabel, div.pickupPointsDetail{display:none;}



japanski_austrav2

yWA2$J^NuER9NaUP%ouwS#qa3Fyojx




2017-05-14

https://new.nx.jtbtravel.com.au/day-tours/day-tour/Tokyo%20Sumo%20Tournament%20(from%20Hamamatsucho)/?qty=1A&scu=1&productid=171


https://new.nx.jtbtravel.com.au/day-tours/day-tour/Tokyo%20Sumo%20Tournament%20(from%20Hamamatsucho)/?qty=1A&scu=1&productid=7094

2697
7094

2018-01-08


~

https://new.nx.jtbtravel.com.au/japan-tours/guided/9-day-nara/
https://new.nx.jtbtravel.com.au/japan-tours/guided/9-day-takayama/

https://new.nx.jtbtravel.com.au/japan-tours/guided/9-day-nikko/
https://new.nx.jtbtravel.com.au/japan-tours/guided/9-day-hiroshima/



v=spf1 +a +mx +ip4:118.127.46.88 +ip4:118.127.47.253 +a:_spf.google.com ~all







#force ssl for whole site
#RewriteCond %{HTTP_HOST} !https://www.nx.jtbtravel.com.au$
#RewriteRule ^(.*)$ https://www.nx.jtbtravel.com.au/$1 [R=301,L]

#RewriteCond %{HTTPS} on
#RewriteCond %{HTTP_HOST} !^www\.
#RewriteCond %{REQUEST_URI} itinerary [OR]
#RewriteCond %{REQUEST_URI} remarks
#RewriteRule ^(.*)$ https://www.nx.jtbtravel.com.au/$1 [R=301,L]

#RewriteCond %{HTTPS} off
#RewriteCond %{HTTP_HOST} !^www\.
#RewriteCond %{REQUEST_URI} itinerary [OR]
#RewriteCond %{REQUEST_URI} remarks
#RewriteRule ^(.*)$ https://www.nx.jtbtravel.com.au/$1 [R=301,L]






itinerary 3 buttons backup
<div class="servicelineSection"></div>
	<div class="row">
		<div class="col-xs-12">
		<?php do_action("print_itinerary_messagebox"); ?>
			<span class="pull-right"><h4>Total Price <span class="cart_price price"></span><h4></span>
		</div>
	</div>
	<div class="row">
		<?php do_action("print_itinerary_buttons"); ?>
	</div>


*/
?>
