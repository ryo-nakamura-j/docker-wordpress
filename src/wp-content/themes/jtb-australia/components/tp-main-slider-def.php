<?php 

class TpMainSlide {
	var $link;
	var $slide;
	var $slide_mobile;
	var $url_banner;
}

class TpMainSlider {
	var $id;
	var $class;
	var $slideList; // A list of TpMainSlide
	var $usePremotion = false;
	var $promotions;

	public function init() {
		include("tp-main-slider-template.php");
	}

	public function calculatePremotion( $urlMatchingList ) {
		$this->usePremotion = true;
		// Calculate promotions and re-generate url_banner
		$this->promotions = array();
		foreach( $this->slideList as $slides ) {
			$include = $urlMatchingList;
			$url = $slides->link;
			$url = $url.rtrim($url,'/');
			$arrUrl = explode('/', $url);
		    $in = 1;
			// If it's rail-pass url, we use promotion intead of a redirect
			if (count(array_intersect($arrUrl, $include)) >= 1) {
				$promo = array(
							'id' => end($arrUrl),
							'name' => str_replace('-', ' ', end($arrUrl)),
							'url_promo' => $slides->link,
							'position'=> $in
							);
				$in++;
				array_push($this->promotions, $promo);
				$slides->url_banner = "javascript:onPromoClick(".json_encode($promo).")";
			}
		}
	}
}

?>