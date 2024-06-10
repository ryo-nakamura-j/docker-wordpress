
<?php
require_once( '/home/jtbtrave/public_html/wp-load.php' );
$isHidden=false;

$htmlCont = "";
$htmlContAdmin = "";

function printLayer($idNum,$hiddenParent,&$htmlCont,&$htmlContAdmin){

	$args = array(
		'post_type' => 'page',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'post_parent' => $idNum,
		'fields' => 'ids'
	);
	$theQ = new WP_Query( $args );
	if ($idNum==0){
		$htmlContAdmin .=  '<div class="row"><div class="col-xs-12 col-md-12"><div class="col-xs-12 col-md-6"> <h3>Product Pages</h3><ul class="sitemap">';
		$htmlCont .= '<div class="row"><div class="col-xs-12 col-md-12"><div class="col-xs-12 col-md-6"> <h3>Product Pages</h3><ul class="sitemap">';
	}else{
		$htmlContAdmin .=  '<ul class="sitemapLvl2">';
		$htmlCont .=  '<ul class="sitemapLvl2">';
	}

	if ( $theQ->have_posts() ) : while ( $theQ->have_posts() ) : $theQ->the_post();
		$Qid = get_the_ID();
		if($Qid==156){
			$htmlContAdmin .= '</ul></div>';
			$htmlCont .= '</ul></div>';
			$htmlContAdmin .= '<div class="col-xs-12 col-md-6"> <h3>Other Pages  <small class="green-text">Menu-order 0</small></h3><ul class="sitemap">';
			$htmlCont .= '<div class="col-xs-12 col-md-6"> <h3>Other Pages</h3><ul class="sitemap">';
		}
		if($Qid==258 ){ //33
			$htmlContAdmin .= '</ul><h3>Japan Information Pages <small class="green-text">Menu-order 33</small></h3><ul class="sitemap">';
			$htmlCont .= '</ul><h3>Japan Information Pages</h3><ul class="sitemap">';
		}
		if($Qid==275 ){ //44
			$htmlContAdmin .= '</ul><h3>Location Pages  <small class="green-text">Menu-order 44</small></h3><ul class="sitemap">';
			$htmlCont .= '</ul><h3>Location Pages</h3><ul class="sitemap">';
		}
		if($Qid==3675 ){ //55
			$htmlContAdmin .= '</ul><h3 class="red-text">Hidden Pages</h3><ul class="sitemap">';
		}
		
		if(get_post_meta($Qid, '_hide_from_search', true)=="1"   ){
			$isHidden=true;
		}
		if ($isHidden || $hiddenParent){
			$htmlContAdmin .='<li>';
			$htmlContAdmin .='<a  class="red-text"  href="'.get_the_permalink($Qid).'" rel="bookmark" >'. get_the_title($Qid).'</a> <span class="green-text">- ID: '.$Qid.' - <a href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post='.$Qid.'&action=edit">Edit</a>  </span>';
		}else{
			$htmlContAdmin .='<li>';
			$htmlContAdmin .='<a    href="'.get_the_permalink($Qid).'" rel="bookmark" >'. get_the_title($Qid).'</a>  <span class="green-text">- ID: '.$Qid.' - <a href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post='.$Qid.'&action=edit">Edit</a>  </span>'; 
			$htmlCont .='<li>';
			$htmlCont .='<a    href="'.get_the_permalink($Qid).'" rel="bookmark" >'. get_the_title($Qid).'</a>'; 

		}

		//if has children - print them ~ 
		$idList = get_pages( array( 'child_of' => $Qid,'exclude_tree' => '1', ));
		if (count( $idList ) != 0){
			printLayer($Qid,($isHidden&&$hiddenParent),$htmlCont,$htmlContAdmin);
		}
		if ($isHidden || $hiddenParent){
			$htmlContAdmin .= '</li>';
		}else{
			$htmlContAdmin .= '</li>';
			$htmlCont .= '</li>'; 
		}
		$isHidden=false;

	endwhile;
	endif;

	if ($idNum==0){
		$htmlContAdmin .= '</ul></div></div></div>';
		$htmlCont .= '</ul></div></div></div>';
	}else{
		$htmlContAdmin .=  '</ul>';
		$htmlCont .=  '</ul>';
	}
}

printLayer(0,false,$htmlCont,$htmlContAdmin);

wp_reset_query();

$hiddenIdArray = Array();

$args = array( 
'post_type'  => 'page',
'showposts' => '-1',
'fields' => 'ids',
'meta_query' => array( 
array(
'key'   => '_hide_from_search', 
'value' => '1'
    )
));
$page_template_query = new WP_Query($args); 
while($page_template_query->have_posts() ) : $page_template_query->the_post();
    $hiddenIdArray[] = get_the_ID();
endwhile;
wp_reset_query();

update_option("jtbau_sitemap", $htmlCont);

update_option("jtbau_sitemap_admin", $htmlContAdmin);

update_option("jtbau_hidden_pages_array", $hiddenIdArray);

/*
https://www.nx.jtbtravel.com.au/wp-content/plugins/jtb-australia-cust/docs/cron-job-generate-sitemap.php
update_option("jtbau_hidden_pages_array", $new_value);
get_option("jtbau_sitemap_admin")
get_option("jtbau_sitemap")
get_option("jtbau_hidden_pages_array");

List by template
array(
'key'   => '_wp_page_template', 
'value' => 'tp-no-sidebar-Tour-Search-Template.php'
    )
    
*/




?>
