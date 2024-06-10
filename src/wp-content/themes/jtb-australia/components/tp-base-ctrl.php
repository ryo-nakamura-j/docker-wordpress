<?php
class TpBaseCtrl {
	private $validated = false;
	public $VALIDATE_ID;
	public $controller_file;

	public function __construct( $VALIDATE_ID, $controller_file ) {
		$this->VALIDATE_ID = $VALIDATE_ID;
		$this->controller_file = $controller_file ;
	}

	// This function should call by templates to validate the control
	// By using this, we are getting track of which template is using the controller.
	// It also prevents using wrong templates for the controller
	public function expectedController( $id ) {
		if ( $id == $this->VALIDATE_ID )
			$this->validated = true;
	}

	public function initCtrl( ) {
		if ( $this->VALIDATE_ID == null || $this->VALIDATE_ID == "" || $this->validated )
			include( $this->controller_file );
		else
			echo "<h1 style=\"color:red\">Error: Please use templates validated for " . $this->VALIDATE_ID . "<h1>";
	}
}
?>