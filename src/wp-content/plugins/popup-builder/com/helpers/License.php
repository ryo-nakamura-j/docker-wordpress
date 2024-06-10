<?php

namespace sgpb;


class License
{
	public function getLicenses()
	{
		return $this->setRegisteredExtensionsLicenses();
	}

	public function setRegisteredExtensionsLicenses()
	{
		$registered = AdminHelper::getOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS);
		$registered = json_decode($registered, true);

		if(empty($registered)) {
			return [];
		}
		$licenses = array();
		foreach($registered as $register) {

			if(empty($register['options']['licence'])) {
				continue;
			}

			$licenses[] = $register['options']['licence'];
		}

		return $licenses;
	}
}
