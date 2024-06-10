<?php
namespace sgpb;
require_once(dirname(__FILE__).'/SGPopup.php') ;
class HtmlPopup extends SGPopup
{

	public function getOptionValue($optionName, $forceDefaultValue = false)
	{
		return parent::getOptionValue($optionName, $forceDefaultValue);
	}

	public function getPopupTypeOptionsView()
	{
		return array();
	}

	public function getPopupTypeMainView()
	{
		return array();
	}

	public function getPopupTypeContent()
	{
		$htmlContent = '';
		$popupContent = $this->getContent();
		$htmlContent .= '<div class="sgpb-main-html-content-wrapper">';
		$htmlContent .= $popupContent;
		$htmlContent .= '</div>';

		return $htmlContent;
	}

	public function getExtraRenderOptions()
	{
		return array();
	}
}

