<?php
namespace sgpb;

class PopupGroupFilter
{
	private $popups = array();
	private $popupGroups = array();
	private $hasSubPopups = array('subscription', 'contactForm');

	public function getHasSubPopups()
	{
		return apply_filters('sgpbHasSubPopups', $this->hasSubPopups);
	}

	public function setGroups($popupGroups)
	{
		$this->popupGroups = $popupGroups;
	}

	public function getGroups()
	{
		return $this->popupGroups;
	}

	public function insertGroup($groupName,$group)
	{
		$this->popupGroups[$groupName] =  $group;
	}

	public function setPopups($popups)
	{
		$this->popups = $popups;
	}

	public function getPopups()
	{
		return $this->popups;
	}

	public function filter()
	{
		$splitedGroups = $this->splitToGroups();

		// When does not have any groups, then there is no need do filter
		if (empty($splitedGroups)) {
			return $this->filteredResults();
		}
		$groups = $this->filterGroups();

		if (empty($groups)) {
			return $this->filteredResults();
		}
		$unionPopups = $this->unionGroups();

		if (empty($unionPopups)) {
			return $this->filteredResults();
		}
		$this->extendPopups();

		return $this->filteredResults();
	}

	private function filteredResults()
	{
		$popups = $this->getPopups();

		if (empty($popups)) {
			return $popups;
		}
		$filteredResults = $this->filterByOrder();

		return $filteredResults;
	}

	public function filterByOrder()
	{
		$popups = $this->getPopups();
		$popupsByKeyOrder = array();

		foreach ($popups as $popup) {
			if (!$popup instanceof SGPopup) {
				continue;
			}
			$orderId = $popup->getOptionValue('sgpb-popup-order');
			$popupsByKeyOrder[$orderId][] = $popup;
		}
		krsort($popupsByKeyOrder);
		$orderPopups = array();
		foreach ($popupsByKeyOrder as $value) {
			$size = sizeof($value);
			if ($size == 1) {
				$orderPopups[] = $value[0];
				continue;
			}

			foreach ($value as $insideValue) {
				$orderPopups[] = $insideValue;
			}

		}
		$orderPopups = array_values($orderPopups);

		$this->setPopups($orderPopups);

		return $orderPopups;
	}

	public function splitToGroups()
	{
		$popups = $this->getPopups();
		$groups = array();

		if (empty($popups)) {
			return $popups;
		}

	 	$staticPopups = $popups;

		$groups['staticPopups'] = $staticPopups;

		$this->setGroups($groups);

		return $popups;
	}

	private function filterGroups()
	{
		$groups = $this->getGroups();

		$this->setGroups($groups);

		return $groups;
	}

	private function filterRandomPopups($groups)
	{
		$randomPopups = $groups['randomPopups'];
		$randomPopupKey = array_rand($randomPopups, 1);
		$randomPopup = $randomPopups[$randomPopupKey];
		// $randomPopup converted to array
		$groups['randomPopups'] = array($randomPopup);

		return $groups;
	}

	public function unionGroups()
	{
		$groups = $this->getGroups();
		$popups = array();

		$groupsPopups = array_values($groups);
		foreach ($groupsPopups as $groupsPopup) {
			$popups = array_merge($popups, $groupsPopup);
		}

		$this->setPopups($popups);

		return $popups;
	}

	private function extendPopups()
	{
		$popups = $this->getPopups();
		$insidePopups = array();

		if (empty($popups)) {
			return $popups;
		}

		$hasSubPopups = $this->getHasSubPopups();

		foreach ($popups as $popup) {
			if (empty($popup)) {
				continue;
			}
			$popupType = $popup->getType();
			$insidePopups = $popup->popupShortcodesInsidePopup();
			if (!empty($insidePopups)) {
				$popups = array_merge($popups, $insidePopups);
			}

			$subPopups = $popup->getSubPopupObj();

			if (!empty($subPopups)) {
				$popups = array_merge($popups, $subPopups);
			}
		}

		$this->setPopups($popups);

		return $popups;
	}
}
