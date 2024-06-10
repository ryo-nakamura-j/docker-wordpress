<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SP_PC_ShortCode {
	/**
	 * @var SP_PC_ShortCode single instance of the class
	 *
	 * @since 2.0
	 */
	protected static $_instance = null;

	/**
	 * Main SP Instance
	 *
	 * @since 2.0
	 * @static
	 * @return self Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * SP_PC_ShortCode constructor.
	 */
	public function __construct() {
		add_shortcode( 'post-carousel', array( $this, 'post_carousel_shortcode' ) );
	}

	/**
	 * @param $attributes
	 *
	 * @return string
	 */
	public function post_carousel_shortcode( $attributes ) {
		extract(
			shortcode_atts(
				array(
					'id' => '',
				), $attributes, 'post-carousel'
			)
		);

		$post_id = $attributes['id'];

		$pc_number_of_total_posts    = intval( get_post_meta( $post_id, 'pc_number_of_total_posts', true ) );
		$pc_number_of_column         = intval( get_post_meta( $post_id, 'pc_number_of_column', true ) );
		$pc_number_of_column_desktop = intval( get_post_meta( $post_id, 'pc_number_of_column_desktop', true ) );
		$pc_number_of_column_tablet  = intval( get_post_meta( $post_id, 'pc_number_of_column_tablet', true ) );
		$pc_number_of_column_mobile  = intval( get_post_meta( $post_id, 'pc_number_of_column_mobile', true ) );

		$pc_auto_play_speed         = get_post_meta( $post_id, 'pc_auto_play_speed', true );
		$pc_nav_arrow_color         = get_post_meta( $post_id, 'pc_nav_arrow_color', true );
		$pc_nav_arrow_bg            = get_post_meta( $post_id, 'pc_nav_arrow_bg', true );
		$pc_pagination_color        = get_post_meta( $post_id, 'pc_pagination_color', true );
		$pc_pagination_active_color = get_post_meta( $post_id, 'pc_pagination_active_color', true );
		$pc_scroll_speed            = get_post_meta( $post_id, 'pc_scroll_speed', true );
		$pc_post_title_color        = get_post_meta( $post_id, 'pc_post_title_color', true );
		$pc_post_title_hover_color  = get_post_meta( $post_id, 'pc_post_title_hover_color', true );
		$pc_post_content_color      = get_post_meta( $post_id, 'pc_post_content_color', true );
		$pc_post_meta_color         = get_post_meta( $post_id, 'pc_post_meta_color', true );
		$pc_post_meta_hover_color   = get_post_meta( $post_id, 'pc_post_meta_hover_color', true );
		$pc_carousel_title_color    = get_post_meta( $post_id, 'pc_carousel_title_color', true );
		$pc_themes                  = get_post_meta( $post_id, 'pc_themes', true );

		$pc_auto_play            = $this->get_meta( $post_id, 'pc_auto_play', 'true' );
		$pc_pause_on_hover       = $this->get_meta( $post_id, 'pc_pause_on_hover', 'true' );
		$pc_show_navigation      = $this->get_meta( $post_id, 'pc_show_navigation', 'true' );
		$pc_show_pagination_dots = $this->get_meta( $post_id, 'pc_show_pagination_dots', 'true' );
		$pc_touch_swipe          = $this->get_meta( $post_id, 'pc_touch_swipe', 'true' );
		$pc_mouse_draggable      = $this->get_meta( $post_id, 'pc_mouse_draggable', 'true' );
		$pc_rtl                  = $this->get_meta( $post_id, 'pc_rtl', 'true' );
		$pc_post_title           = $this->get_meta( $post_id, 'pc_post_title', 'true' );
		$pc_post_content         = $this->get_meta( $post_id, 'pc_post_content', 'true' );
		$pc_post_author          = $this->get_meta( $post_id, 'pc_post_author', 'true' );
		$pc_post_date            = $this->get_meta( $post_id, 'pc_post_date', 'true' );
		$pc_carousel_title       = $this->get_meta( $post_id, 'pc_carousel_title', 'true' );

		$args = array(
			'post_type'      => 'post',
			'orderby'        => get_post_meta( $post_id, 'pc_posts_order_by', true ),
			'order'          => get_post_meta( $post_id, 'pc_posts_order', true ),
			'posts_per_page' => $pc_number_of_total_posts,
		);

		$que = new WP_Query( $args );

		$outline = '';

		$outline .= '
	    <script type="text/javascript">
	            jQuery(document).ready(function() {
				jQuery("#sp-post-carousel-' . $post_id . '").slick({
			        infinite: true,
			        pauseOnFocus: false,
			        dots: ' . $pc_show_pagination_dots . ',
			        pauseOnHover: ' . $pc_pause_on_hover . ',
			        slidesToShow: ' . $pc_number_of_column . ',
			        speed: ' . $pc_scroll_speed . ',
		            arrows: ' . $pc_show_navigation . ',
                    prevArrow: "<div class=\'slick-prev\'><i class=\'sp-pc-font-icon sp-pc-icon-angle-left\'></i></div>",
                    nextArrow: "<div class=\'slick-next\'><i class=\'sp-pc-font-icon sp-pc-icon-angle-right\'></i></div>",
                    slidesToScroll: 1,
                    autoplay: ' . $pc_auto_play . ',
                    autoplaySpeed: ' . $pc_auto_play_speed . ',
                    swipe: ' . $pc_touch_swipe . ',
	                draggable: ' . $pc_mouse_draggable . ',
                    rtl: ' . $pc_rtl . ',
					responsive: [
						    {
						      breakpoint: 1100,
						      settings: {
						        slidesToShow: ' . $pc_number_of_column_desktop . '
						      }
						    },
						    {
						      breakpoint: 990,
						      settings: {
						        slidesToShow: ' . $pc_number_of_column_tablet . '
						      }
						    },
						    {
						      breakpoint: 650,
						      settings: {
						        slidesToShow: ' . $pc_number_of_column_mobile . '
						      }
						    }
						  ]
		        });

		    });
	    </script>';

		$outline .= '<style type="text/css">';
		if ( $pc_show_navigation == 'true' ) {
			$outline .= '.sp-post-carousel-section #sp-post-carousel-' . $post_id . '.sp-post-carousel-area .slick-arrow{
				color:' . $pc_nav_arrow_color . ';
				background-color:' . $pc_nav_arrow_bg . ';
			}';
		}
		if ( $pc_show_navigation == 'true' && $pc_carousel_title == 'false' ) {
			$outline .= '
			div.sp-post-carousel-section-' . $post_id . '{
				padding-top:46px;
			}';
		}
		if ( $pc_carousel_title == 'true' ) {
			$outline .= '
			div.sp-post-carousel-section-' . $post_id . ' h2.sp-post-carousel-section-title{
				color:' . $pc_carousel_title_color . ';
			}';
		}
		if ( $pc_show_pagination_dots == 'true' ) {
			$outline .= '.sp-post-carousel-section-' . $post_id . ' .slick-dots li button{
				background-color: ' . $pc_pagination_color . ';
			}
			.sp-post-carousel-section-' . $post_id . ' .slick-dots li.slick-active button{
				background-color: ' . $pc_pagination_active_color . ';
			}';
		}
		if ( $pc_post_title == 'true' ) {
			$outline .= '.sp-post-carousel-section-' . $post_id . ' .sp-pc-post-title,
			.sp-post-carousel-section-' . $post_id . ' .sp-pc-post-title a{
				font-size: 18px;
			    color: ' . $pc_post_title_color . ';
			    line-height: 1.2;
			    font-weight: 600;
			    margin-bottom: 8px;
			    margin-top: 0;
			    padding: 0;
			}
			.sp-post-carousel-section-' . $post_id . ' .sp-pc-post-title a:hover{
				color: ' . $pc_post_title_hover_color . ';
			}
			';
		}
		if ( $pc_post_author == 'true' || $pc_post_date == 'true' ) {
			$outline .= '.sp-post-carousel-section-' . $post_id . ' .sp-pc-post-meta ul li, 
			.sp-post-carousel-section-' . $post_id . ' .sp-pc-post-meta ul li a{
				color: ' . $pc_post_meta_color . ';
                font-size: 13px;
			}
			.sp-post-carousel-section-' . $post_id . ' .sp-pc-post-meta ul li a:hover{
				color: ' . $pc_post_meta_hover_color . ';
			}';
		}
		if ( 'hide' !== $pc_post_content ) {
			$outline .= '.sp-post-carousel-section-' . $post_id . ' .sp-pc-content {
			    font-size: 14px;
			    color: ' . $pc_post_content_color . ';
			    line-height: 1.5;
			    margin-bottom: 5px;
			}';
		}
		$outline .= '</style>';

		if ( $pc_themes == 'carousel_one' ) {
			$outline .= '<div class="sp-post-carousel-section sp-post-carousel-section-' . $post_id . '">';
			if ( $pc_carousel_title == 'true' ) {
				$outline .= '<h2 class="sp-post-carousel-section-title">' . get_the_title( $post_id ) . '</h2>';
			}
			$outline .= '<div id="sp-post-carousel-' . $post_id . '" class="sp-post-carousel-area sp_pc_theme_' . $pc_themes . '">';
			if ( $que->have_posts() ) {
				while ( $que->have_posts() ) :
					$que->the_post();

					$outline .= '<div class="sp-pc-post">';
					if ( has_post_thumbnail( $que->post->ID ) ) {
						$outline .= '<div class="sp-pc-post-image">';
						$outline .= '<a href="' . get_the_permalink() . '">' . get_the_post_thumbnail( $que->post->ID, 'large', array( 'class' => 'sp-pc-post-img' ) ) . '</a>';
						$outline .= '</div>';
					}
					if ( $pc_post_title == 'true' ) {
						$outline .= '<h2 class="sp-pc-post-title">';
						$outline .= '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
						$outline .= '</h2>';
					}
					if ( $pc_post_author == 'true' || $pc_post_date == 'true' ) {
						$outline .= '<div class="sp-pc-post-meta"><ul>';
						if ( $pc_post_author == 'true' ) {
							$outline .= '<li><i class="sp-pc-font-icon sp-pc-icon-user"></i><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></li>';
						}
						if ( $pc_post_date == 'true' ) {
							$outline .= '<li><i class="sp-pc-font-icon sp-pc-icon-clock"></i>';
							$outline .= '<time class="entry-date published" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>';
							if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
								$outline .= '<time class="updated hidden" datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( get_the_modified_date() ) . '</time>';
							}
							$outline .= '</li>';
						}
						$outline .= '</ul></div>';
					}
					if ( 'hide' !== $pc_post_content ) {
						$trimexcerpt  = get_the_content();
						$shortexcerpt = wp_trim_words( $trimexcerpt, $num_words = 30, $more = '' );
						if ( 'full_content' == $pc_post_content ) {
							$outline .= "<p class='sp-pc-content'>" . do_shortcode( $trimexcerpt ) . '</p>';
						} else {
							$outline .= "<p class='sp-pc-content'>" . do_shortcode( $shortexcerpt ) . '</p>';
						}
					}

					$outline .= '</div>'; // sp-pc-post.

				endwhile;
			} else {
				$outline .= '<h2 class="sp-not-found-any-post">' . __( 'No posts found', 'post-carousel' ) . '</h2>';
			}
			$outline .= '</div>';
			$outline .= '</div>'; // sp-post-carousel-section.
		} elseif ( $pc_themes == 'carousel_two' ) {
			$outline .= '<div class="sp-post-carousel-section sp-post-carousel-section-' . $post_id . '">';
			if ( $pc_carousel_title == 'true' ) {
				$outline .= '<h2 class="sp-post-carousel-section-title">' . get_the_title( $post_id ) . '</h2>';
			}
			$outline .= '<div id="sp-post-carousel-' . $post_id . '" class="sp-post-carousel-area sp_pc_theme_' . $pc_themes . '">';
			if ( $que->have_posts() ) {
				while ( $que->have_posts() ) :
					$que->the_post();

					$outline .= '<div class="sp-pc-post">';
					$outline .= '<div class="sp-pc-post-box">';
					if ( has_post_thumbnail( $que->post->ID ) ) {
						$outline .= '<div class="sp-pc-post-image">';
						$outline .= '<a href="' . get_the_permalink() . '">' . get_the_post_thumbnail( $que->post->ID, 'large', array( 'class' => 'sp-pc-post-img' ) ) . '</a>';
						$outline .= '</div>';
					}
					if ( $pc_post_title == 'true' ) {
						$outline .= '<h2 class="sp-pc-post-title">';
						$outline .= '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
						$outline .= '</h2>';
					}
					if ( $pc_post_author == 'true' || $pc_post_date == 'true' ) {
						$outline .= '<div class="sp-pc-post-meta"><ul>';
						if ( $pc_post_author == 'true' ) {
							$outline .= '<li><i class="sp-pc-font-icon sp-pc-icon-user"></i><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></li>';
						}
						if ( $pc_post_date == 'true' ) {
							$outline .= '<li><i class="sp-pc-font-icon sp-pc-icon-clock"></i>';
							$outline .= '<time class="entry-date published updated" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>';
							if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
								$outline .= '<time class="updated hidden" datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( get_the_modified_date() ) . '</time>';
							}
							$outline .= '</li>';
						}
						$outline .= '</ul></div>';
					}
					if ( 'hide' !== $pc_post_content ) {
						$trimexcerpt  = get_the_content();
						$shortexcerpt = wp_trim_words( $trimexcerpt, $num_words = 30, $more = '' );
						if ( 'full_content' == $pc_post_content ) {
							$outline .= "<p class='sp-pc-content'>" . do_shortcode( $trimexcerpt ) . '</p>';
						} else {
							$outline .= "<p class='sp-pc-content'>" . do_shortcode( $shortexcerpt ) . '</p>';
						}
					}
					$outline .= '</div>'; // sp-pc-post-box.
					$outline .= '</div>'; // sp-pc-post.

				endwhile;
			} else {
				$outline .= '<h2 class="sp-not-found-any-post">' . __( 'No posts found', 'post-carousel' ) . '</h2>';
			}
			$outline .= '</div>';
			$outline .= '</div>'; // sp-post-carousel-section.
		}

		wp_reset_query();

		return $outline;
	}


	/**
	 * Get post meta by id and key
	 *
	 * @param $post_id
	 * @param $key
	 * @param $default
	 *
	 * @return string|void
	 */
	public function get_meta( $post_id, $key, $default = null ) {
		$meta = get_post_meta( $post_id, $key, true );
		if ( empty( $meta ) && $default ) {
			$meta = $default;
		}

		if ( $meta == 'zero' ) {
			$meta = '0';
		}
		if ( $meta == 'on' ) {
			$meta = 'true';
		}
		if ( $meta == 'off' ) {
			$meta = 'false';
		}

		return esc_attr( $meta );
	}

}

new SP_PC_ShortCode();
