<?php
class tpConfigLoader {
	public static function loadDateConfig( $sectionConfig ) {
		$minDateType = get_sub_field("min_date_type");
		$maxDateType = get_sub_field("max_date_type");

		$sectionConfig['date_config'] = array(
			"min_date_type" => $minDateType,
			"min_date_val" => get_sub_field("min_date_" . $minDateType),
			"max_date_type" => $maxDateType,
			"max_date_val" => get_sub_field("max_date_" . $maxDateType),
			"max_scu" => get_sub_field("max_scu")
		);
		// PHP5 still pass array by value rather than reference
		return $sectionConfig;
	}
}
?>