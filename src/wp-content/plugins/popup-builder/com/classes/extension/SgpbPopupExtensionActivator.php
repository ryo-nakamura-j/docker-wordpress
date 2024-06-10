<?php
namespace sgpb;

class PopupExtensionActivator
{
	public function activate()
	{
		$extensions = get_option('sgpbExtensionsInfo');

		if (empty($extensions)) {
			return false;
		}

		foreach ($extensions as $folderName => $extension) {
			if (empty($extension['key'])) {
				continue;
			}
			$key = $extension['key'];

			if ($folderName == 'popupbuilder-woocommerce') {
				$key = $folderName.'/popupbuilderWoocommerce.php';
			}
			else if ($folderName == 'popupbuilder-restriction') {
				$key = $folderName.'/PopupBuilderAgerestriction.php';
			}
			else if ($folderName == 'popupbuilder-aweber') {
				$key = $folderName.'/PopupBuilderAWeber.php';
			}
			else if ($folderName == 'popupbuilder-adblock') {
				$key = $folderName.'/PopupBuilderAdBlock.php';
			}

			activate_plugin($key);
		}

		return true;
	}

	private function getExtensionsInfo()
	{
		$extensionsInfo = array();
		if (!file_exists(SG_POPUP_BUILDER_PATH.'extensions')) {
			return $extensionsInfo;
		}
		$it = new \RecursiveDirectoryIterator(SG_POPUP_BUILDER_PATH.'extensions', \RecursiveDirectoryIterator::SKIP_DOTS);


		foreach ($it as $path => $fileInfo) {
			if (empty($fileInfo)) {
				continue;
			}
			$extensionFolderName = $fileInfo->getFilename();
			$extensionMainFile = $this->getExtensionMainFile($extensionFolderName);
			$extensionKey = $extensionFolderName.'/'.$extensionMainFile;

			$extensionsInfo[$extensionFolderName] = array('key' => $extensionKey, 'mainFileName' => $extensionMainFile);
		}

		return $extensionsInfo;
	}

	public function install()
	{
		$extensionsInfo = $this->getExtensionsInfo();

		if (!get_option('sgpbExtensionsInfo')) {
			update_option('sgpbExtensionsInfo', $extensionsInfo);
		}
		$this->moveExtensionToPluginsSection($extensionsInfo);
	}

	private function moveExtensionToPluginsSection($extensionsInfo)
	{
		foreach ($extensionsInfo as $extensionFolder => $extensionsDetail) {
			$passedExtension =  WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$extensionFolder.DIRECTORY_SEPARATOR;
			$originalExtension = SG_POPUP_BUILDER_PATH.'extensions'.DIRECTORY_SEPARATOR.$extensionFolder.DIRECTORY_SEPARATOR;
			@rename($originalExtension,$passedExtension);
		}
	}

	private function getExtensionMainFile($folderName)
	{
		if (empty($folderName)) {
			return '';
		}

		$explodedData = explode('-', $folderName);

		if (empty($explodedData)) {
			return '';
		}
		$explodedData = array_filter(array_values($explodedData), array($this, 'ucifirstElements'));
		$fileName = implode('', $explodedData);

		return $fileName.'.php';
	}

	private function ucifirstElements(&$element)
	{
		if ($element == 'popupbuilder') {
			$element = 'PopupBuilder';
			return $element;
		}
		$element = ucfirst($element);

		return $element;
	}
}
