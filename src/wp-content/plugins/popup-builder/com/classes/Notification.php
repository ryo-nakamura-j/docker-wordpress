<?php
namespace sgpb;

class Notification
{
	public $id;
	public $type;// notification, warning etc.
	public $priority;
	public $message;

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	public function getPriority()
	{
		return $this->priority;
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function render()
	{
		$id = $this->getId();
		$type = $this->getType();
		$color = '';
		switch ($type) {
			case 1:
				$color = '#01B9FF !important';
				break;
			case 2:
				$color = '#28a745 !important';
				break;
			case 3:
				$color = '#dc3545 !important';
				break;
		}

		$style = 'style="border-color: '.$color.';"';
		$priority = $this->getPriority();
		$message = $this->getMessage();
		$btnHtml = $this->getCloseBtnById($id);
		$content = '<div class="sgpb-single-notification-wrapper">
						<div class="sgpb-single-notification" '.$style.'>
						<span class="dashicons dashicons-no-alt sgpb-hide-notification-at-all" data-id="'.$id.'"></span>
						'.$message.'
						</div>
						<div class="sgpb-single-notification-close-btn">
							'.$btnHtml.'
						</div>
					</div>';

		return $content;
	}

	public function getCloseBtnById($id)
	{
		$dismissedNotification = SGPBNotificationCenter::getAllDismissedNotifications();
		if (isset($dismissedNotification[$id])) {
			return '<button data-id="'.$id.'" class="button dismiss sgpb-activate-notification-js"><span class="dashicons dashicons-hidden"></span></button>';
		}

		return '<button data-id="'.$id.'" class="button dismiss sgpb-dismiss-notification-js"><span class="dashicons dashicons-visibility"></span></button>';
	}
}
