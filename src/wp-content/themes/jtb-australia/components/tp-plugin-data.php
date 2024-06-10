<?php

$dataList = array();
while( have_rows('data_list')) {
	the_row();
	$r = get_row();
	$layout = get_row_layout();
	if ( $layout == 'image_data_list' ) {
		while( have_rows('image_data_list' ) ) {
			the_row();
			$identifier = get_sub_field("identifier");
			$dataList[ $identifier ] = $helper->getValueMapList( 'content_list', array(
					'image', 'title', 'url', 'description'
				));
		}
	}
	else if( $layout == 'product_data_list' ){
		$identifier = get_sub_field("identifier");
		$dataList[ $identifier ] = $helper->getValueMapList( 'content_list', array(
				'product_id'
			));
	}
	else if( $layout == 'text_data_list' ){
		$identifier = get_sub_field("identifier");
		$tmp1 = array();
		while( have_rows('content_list' ) ) {
			the_row();
			$sub_identifier = get_sub_field("sub_identifier");
			$tmp2 = array();
			while( have_rows('content' ) ) {
				the_row();
				array_push( $tmp2, $helper->getSubFieldList( array(
					'image', 'text', 'url'
				)));
			}
			$tmp1[ $sub_identifier ] = $tmp2;
		}
		$dataList[ $identifier ] = $tmp1;
	}
}
$labels = array();
while( have_rows('labels')) {
	the_row();
	$identifier = get_sub_field("identifier");
	$value = get_sub_field("value");
	$labels[ $identifier ] = $value;
}
$dataList[ 'labels' ] = $labels;

?>

<script type="text/javascript">
	$(document).ready(function() {
		Vue.mixin({
			data: function() { 
				return {
					helper: templatesHelper 
				};
			},
			computed: {
				dataList: {
					get: function() {
						var rlt = <?php echo json_encode($dataList); ?>;
						return rlt;
					}
				}
			},
			methods: {
				dataListLabel: function( key, defaultValue ) {
					if ( this.dataList == null || this.dataList.labels == null )
						return defaultValue;
					if ( this.dataList.labels[key] == null || this.dataList.labels[key] == "" )
						return defaultValue;
					if ( !_.isString(this.dataList.labels[key]) )
						return defaultValue;
					return this.dataList.labels[key];
				},
			},
		});
	});
</script>