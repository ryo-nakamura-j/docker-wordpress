<?php
namespace sgpb;

/**
 * Popup Builder Includer
 *
 * @since 2.5.6
 *
 * Register and enqueue popup styles and js files
 *
 */
class ScriptsIncluder
{

	/**
	 * Popup register style
	 *
	 * @since 2.5.6
	 *
	 * @param string $fileName file address
	 * @param array $args wordpress register  style args dep|ver|media|dirUrl
	 *
	 * @return void
	 */
	public static function registerStyle($fileName, $args = array())
	{
		if(empty($fileName)) {
			return;
		}

		$dep = array();
		$ver = SG_POPUP_VERSION;
		$media = 'all';
		$dirUrl = SG_POPUP_CSS_URL;

		if(!empty($args['dep'])) {
			$dep = $args['dep'];
		}

		if(!empty($args['ver'])) {
			$ver = $args['ver'];
		}

		if(!empty($args['media'])) {
			$media = $args['media'];
		}

		if(!empty($args['dirUrl'])) {
			$dirUrl = $args['dirUrl'];
		}

		wp_register_style($fileName, $dirUrl.''.$fileName, $dep, $ver, $media);
	}

	/**
	 * Popup register style
	 *
	 * @since 2.5.6
	 *
	 * @param string $fileName file address
	 *
	 * @return void
	 */
	public static function enqueueStyle($fileName)
	{
		if(empty($fileName)) {
			return;
		}

		wp_enqueue_style($fileName);
	}

	/**
	 * Popup register style
	 *
	 * @since 2.5.6
	 *
	 * @param string $fileName file address
	 * @param array $args wordpress register  script args dep|ver|inFooter|dirUrl
	 *
	 * @return void
	 */
	public static function registerScript($fileName, $args = array())
	{
		if(empty($fileName)) {
			return;
		}

		$dep = array();
		$ver = SG_POPUP_VERSION;
		$inFooter = false;
		$dirUrl = SG_POPUP_JS_URL;

		if(!empty($args['dep'])) {
			$dep = $args['dep'];
		}

		if(!empty($args['ver'])) {
			$ver = $args['ver'];
		}

		if(!empty($args['inFooter'])) {
			$inFooter = $args['inFooter'];
		}

		if(!empty($args['dirUrl'])) {
			$dirUrl = $args['dirUrl'];
		}

		wp_register_script($fileName, $dirUrl.''.$fileName, $dep, $ver, $inFooter);
	}

	/**
	 * Popup register style
	 *
	 * @since 2.5.6
	 *
	 * @param string $fileName file address
	 *
	 * @return void
	 */
	public static function enqueueScript($fileName)
	{
		if(empty($fileName)) {
			return;
		}

		wp_enqueue_script($fileName);
	}
	/**
	 * Popup localize scripts data
	 *
	 * since wp 4.5 use this method
	 *
	 * @param string $handle
	 * @param string $name
	 * @param array $data
	 *
	 * @return void
	 */
	public static function localizeScript($handle, $name, $data)
	{
		wp_localize_script($handle, $name, $data);
	}

	/**
	 * Popup add line scripts
	 *
	 * after wp 4.5 use this method
	 *
	 * @param string $handle
	 * @param string $data
	 * @param string $before
	 *
	 * @return void
	 */
	public static function addInlineScripts($handle, $data, $before = 'before')
	{
		wp_add_inline_script($handle, $data, $before);
	}
}
