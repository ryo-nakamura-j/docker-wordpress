<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Page Tour 2023
 */

get_header(); ?>



<div id="top-banner23"  class="footer header_footer">

<?
//<div class="footer__inner01 clearfix"> 


    $at = shortcode_atts( array(
        'n' => '1',
        ), $atts );
    $gal = $at['n'];
    $temp = "";
    $img11 = "";
    $alt11="";
    $paddtop="";
    if ($gal!="1"){
        $paddtop=" marginboth";
    }
    $temp .= '<div class="row image3list'.$paddtop.'">';
    $counter=0;
    $counterlvl2=0;
    $cmod=0;
	$slidecaption7 = "";
	$slidecaption8 = "";
	$slidecaption9 = "";
	$slidecaption10 = "";
	

/*  // SLIDE - Gallery 

    while ( have_rows('gallery') ) : the_row();
        $counterlvl2 +=1;
        while ( have_rows('3img') ) : the_row();
            if( (string) $counterlvl2 != $gal){
                continue; //if this is gallery 2, print only 2nd in the loop ~ 
            }
            $imgtemp=get_sub_field('img3');
            $img11 = $imgtemp['url'];
            if ($img11 == ''){
                $img11 = 'https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/01/blank-tour-jtb-image-gallery.jpg';
                }
            $alt11 = $imgtemp['alt'];
            if($alt11 == ''){
                $alt11 = 'JTB Australia Tour';
            } 
            $counter +=1;
            $cmod = (($counter-1)%3)+1;
            $temp .= '<div class=" '.$cmod.'"><div class="post"><div class="entry">';
            $temp .= '<img src="'.$img11.'" alt="'.$alt11.'"';
            if ($alt11 != 'JTB Australia Tour')
                { $temp .= 'title="'.$alt11.'" ';}
            $temp .= '>';
            $temp .= '</div></div></div>';
			break;
        endwhile; 
    endwhile; 
	
	*/
	
	
	
	  while ( have_rows('slides') ) : the_row();
        $counterlvl2 +=1;
      //  while ( have_rows('slide') ) : the_row();
          /*  if( (string) $counterlvl2 != $gal){
                continue; //if this is gallery 2, print only 2nd in the loop ~ 
            }*/
            $imgtemp=get_sub_field('slide');
			$slidecaption7 = get_field('slideshow_caption');
			$slidecaption8 = get_field('slideshow_caption2');
			$slidecaption9 = get_field('slideshow_caption3');
			$slidecaption10 = get_field('slideshow_caption4');
			$slidecaption11 = get_field('slideshow_caption5');
			$slidecaption12 = get_field('slideshow_caption6');
            $img11 = $imgtemp;//['url']
            if ($img11 == ''){
                $img11 = 'https://www.nx.jtbtravel.com.au/wp-content/uploads/2023/06/blank-jtb-image.jpg';
                }
            $alt11 = $imgtemp['alt'];
            if($alt11 == ''){
                $alt11 = 'JTB Australia Tour';
            } 
            $counter +=1;
            $cmod = (($counter-1)%3)+1;
            $temp .= '<div class=" '.$cmod.'"><div class="post"><div class="entry">';
            $temp .= '<img src="'.$img11.'" alt="'.$alt11.'"';
            if ($alt11 != 'JTB Australia Tour')
                { $temp .= 'title="'.$alt11.'" ';}
            $temp .= '>';
            $temp .= '</div></div></div>';
			break;
      //  endwhile; 
    endwhile; 
	
	
	if ( function_exists('yoast_breadcrumb') ) {
		$temp .= '<div class="footer__inner01 clearfix"><div class="col-sm-12">';
		$temp .= 		 yoast_breadcrumb('<p id="breadcrumbs">','</p>',0);
		$temp .=  '<div class="width50">' . $slidecaption7 . '</div><div class="width50right">'. $slidecaption8 . '</div>';
					$temp .= '</div></div>';
					
				} 
	

    $temp .= '</div>';
    //wp_reset_query(); 
    echo  $temp;


//</div>
?>

 


</div>

<div class="container   ">
<div class="footer__inner01 clearfix">
<div class="row">

<?php
echo $slidecaption9;
?>

</div>
</div>
</div>



<div class="container   greyboxtour">
<div class="footer__inner01 clearfix footer__inner">
<div class="row">
<div class="width50">
<?php
echo $slidecaption10;
echo '</div><div class="width50right">';
echo $slidecaption11;
?>
</div>
</div>
</div>
</div>




	<section id="content" class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post">
					<h1><?php the_title(); ?></h1>
					<div class="ribon-red-desktop"></div>
					<div class="entry">
						<?php the_field('content'); 
					the_content();   ?>
					</div>
				</div>
				<?php endwhile; endif; ?>
			</div>
		</div>
	</section>

<?php get_footer(); ?>
