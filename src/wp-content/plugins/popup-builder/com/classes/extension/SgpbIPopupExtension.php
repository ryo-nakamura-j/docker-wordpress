<?php
interface SgpbIPopupExtension
{
	/*It's Admin scripts*/
	public function getScripts($page, $data);
	public function getStyles($page, $data);
	/*It's frontend scripts*/
	public function getFrontendScripts($page, $data);
	public function getFrontendStyles($page, $data);
}