<?php
class UserAgent{
	private $ua;
	private $device;
	public function set(){
		$this->ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($this->ua,'iphone') !== false){
			$this->device = 'mobile';
		}elseif(strpos($this->ua,'ipod') !== false){
			$this->device = 'mobile';
		}elseif((strpos($this->ua,'android') !== false) && (strpos($this->ua, 'mobile') !== false)){
			$this->device = 'mobile';
		}elseif((strpos($this->ua,'windows') !== false) && (strpos($this->ua, 'phone') !== false)){
			$this->device = 'mobile';
		}elseif((strpos($this->ua,'firefox') !== false) && (strpos($this->ua, 'mobile') !== false)){
			$this->device = 'mobile';
		}elseif(strpos($this->ua,'blackberry') !== false){
			$this->device = 'mobile';
		}elseif(strpos($this->ua,'ipad') !== false){
			$this->device = 'tablet';
		}elseif((strpos($this->ua,'windows') !== false) && (strpos($this->ua, 'touch') !== false && (strpos($this->ua, 'tablet pc') == false))){
			$this->device = 'tablet';
		}elseif((strpos($this->ua,'android') !== false) && (strpos($this->ua, 'mobile') === false)){
			$this->device = 'tablet';
		}elseif((strpos($this->ua,'firefox') !== false) && (strpos($this->ua, 'tablet') !== false)){
			$this->device = 'tablet';
		}elseif((strpos($this->ua,'kindle') !== false) || (strpos($this->ua, 'silk') !== false)){
			$this->device = 'tablet';
		}elseif((strpos($this->ua,'playbook') !== false)){
			$this->device = 'tablet';
		}else{
			$this->device = 'others';
		}
		return $this->device;
	}
}
