<?php 

class TpSession {
	var $TIMEOUT_IDLE = 'timeout_idle';
	var $TP_TIMEOUT_IDLE = 'tp_timeout_idle';

	private function __construct() {}
	public static function inst() {
		static $inst = null;
		if ( $inst === null )
			$inst = new TpSession();
		return $inst;
	}

	public function start( $context ) {
		// error_log( 'tp-session start. context (' . $context . ')');
		$this->sessionStartWithTimeoutCheck( $this->getTimeoutIdle() );
	}

	public function close( $context ) {
		// error_log( 'tp-session closed. context (' . $context . ')');
		session_write_close();
		unset( $_SESSION );
	}

	private function getTimeoutIdle() {
		if (!isset($_SESSION[ $this->TP_TIMEOUT_IDLE ]))
		{
		    $tpidle = get_option( $this->TP_TIMEOUT_IDLE ); // eg 1440 == 20mins
		    $_SESSION[ $this->TP_TIMEOUT_IDLE ] = isset($tpidle) && is_numeric($tpidle) ? intval($tpidle) : 0;
		}
		return $_SESSION[ $this->TP_TIMEOUT_IDLE ];
	}

	private function sessionStartWithTimeoutCheck($tp_timeout_idle)
	{
		if (isset($tp_timeout_idle) && is_int($tp_timeout_idle) && $tp_timeout_idle > 0)
		{
			if (!isset($_SESSION[ $this->TIMEOUT_IDLE ])) {
				// error_log( "tp-session sessionStartWithTimeoutCheck() start session" );
				session_start();
			}
			if (!isset($_SESSION[ $this->TIMEOUT_IDLE ]))
			{
				error_log( "tp-session sessionStartWithTimeoutCheck() setup new session" );
				session_destroy();
				session_start();
				session_regenerate_id();
				$_SESSION = array();
			}
			$_SESSION[ $this->TIMEOUT_IDLE ] = time() + $tp_timeout_idle;
		}
	}
}
?>