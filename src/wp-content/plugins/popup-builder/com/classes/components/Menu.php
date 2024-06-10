<?php

namespace sgpb;

/**
 * Class SGPBMenu
 * @package sgpb
 */
class SGPBMenu
{
	/**
     * Singleton variable to save menu locations.
	 * @var
	 */
	public static $navMenuLocations;

	/**
     * Singleton variable to save menu items on which popup is set.
	 * @var
	 */
	public static $navMenuItems;

    /**
    * Menu constructor.
    */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize actions and filters.
     */
    public function init()
    {
        add_action('wp_nav_menu_item_custom_fields', array($this, 'fields'), 10, 4);
        add_action('wp_update_nav_menu_item', array($this, 'save'), 10, 2);

	    add_filter('nav_menu_css_class', array($this, 'addPopupTriggeringClass'), 10, 2);
	    add_filter('wp_setup_nav_menu_item', array($this, 'addCustomNavFields'));
	    add_filter('sgpbLoadablePopups', array($this, 'addPopupToLoad'));
    }

	/**
	 * @param $classes
	 * @param $menuItem
	 * @return mixed
	 */
	public function addPopupTriggeringClass($classes, $menuItem)
    {
	    if (!isset($menuItem->sgpbPopupId)) {
		    return $classes;
	    }
        $popupId = $menuItem->sgpbPopupId;
        if ($popupId && !in_array('sg-popup-id-'.$popupId, $classes)) {
	        array_push($classes, 'sg-popup-id-'.$popupId);
        }

	    return $classes;
    }

	/**
	 * @param $popups
	 * @return mixed
	 */
	public function addPopupToLoad($popups) {
	    $registeredMenus = $this->getNavMenuLocations();
	    /* Will only return menu items on which popup is set */
	    $menuItems = $this->getNavMenuItemsWithPopup($registeredMenus);

        foreach ($menuItems as $menuItem) {
            $menuPopupId = (int)$menuItem->sgpbPopupId;

            /* As $menuItems contains only menu items on which popup is set
            we can check if the popup should load on current page or not */
	        $popup = $this->shouldLoadPopupForMenuItem($menuPopupId, $popups);
            if (!$popup) {
	            $popup = SGPopup::find($menuPopupId);
	            if (!$popup instanceof SGPopup) continue;
	            /* We could set param everywhere as popup was not suppose to load on current page.
	             We could also load popup to current requested page but to avoid any confusion
	             it will be better to use everywhere */
//	            $popup->setTarget(array(
//	                    array('param' => 'everywhere')
//                ));
	            $popups[] = $popup;
            }

	        $popupOpeningEvents = $popup->getEvents();

	        /* If the popup is setup inside the menu but there is no onclick event we will add manually */
	        if (!$this->inMultiDimArray('click', $popupOpeningEvents)) {
		        $popup->setEvents(array('param' => 'click', 'value' => ''));
	        }
	        /* if the popup is set on menu item we don't need any limitation */
	        //$this->disablePopupLimitation($popup);
        }

        return $popups;
    }

	/**
	 * @param $popup
	 */
	private function disablePopupLimitation(&$popup)
    {
	    $popupOptions = $popup->getOptions();
	    unset($popupOptions['sgpb-show-popup-same-user']);
	    $popup->setOptions($popupOptions);
    }

	/**
	 * @param $item
	 * @param $array
	 * @return false|int
	 */
	private function inMultiDimArray($item , $array){
		return preg_match('/"'.preg_quote($item, '/').'"/i' , json_encode($array));
	}

	/**
	 * @param $menuPopupId
	 * @param $popups
	 * @return false|mixed
	 */
	private function shouldLoadPopupForMenuItem($menuPopupId, &$popups) {
	    foreach ($popups as $popup) {
		    $popupId = $popup->getId();
		    /* Menu item contains popup with current id and the popup is loaded */
		    if ($menuPopupId == $popupId) {
			    return $popup;
		    }
	    }
	    return false;
    }

	/**
	 * @param false $forceUpdate
	 * @return array
	 */
	public function getNavMenuLocations($forceUpdate = false)
    {
	    if (!isset(self::$navMenuLocations) && !$forceUpdate) {
		    self::$navMenuLocations = get_nav_menu_locations();
	    }

	    return isset(self::$navMenuLocations) ? self::$navMenuLocations : array();
    }

	/**
	 * @param array $navMenuLocations
	 * @param false $forceUpdate
	 * @return array of menu items where popup is set
	 */
	public function getNavMenuItemsWithPopup($navMenuLocations = array(), $forceUpdate = false)
    {
		if (!isset(self::$navMenuItems) && !$forceUpdate) {
			foreach ($navMenuLocations as $menuName => $menuId) {
				$menuItems = wp_get_nav_menu_items($menuId);

				if (!empty($menuItems)) {
					foreach ($menuItems as $menuItem) {
						$popupId = $menuItem->sgpbPopupId;
						if ($popupId > 0) {
							self::$navMenuItems[] = $menuItem;
						}
					}
                }
			}
		}

		return isset(self::$navMenuItems) ? self::$navMenuItems : array();
	}

	/**
	 * @param $menu_item
	 * @return mixed
	 */
	public function addCustomNavFields($menuItem)
    {
	    $menuItem->sgpbPopupId = get_post_meta($menuItem->ID, '_menu_sgpb_popup_id', true);
		return $menuItem;
	}

	/**
	 * @return array of popup objects
	 */
	public static function getPopups()
    {
        return SGPopup::getAllPopups();
    }

    /**
     * Adds custom fields to the menu item editor.
     *
     * @param $itemId
     * @param $item
     * @param $depth
     * @param $args
     */
    public function fields($itemId, $item, $depth, $args)
    { ?>
        <div class="description  description-wide">
            <label for="edit-menu-item-pb-<?php echo esc_attr($item->ID); ?>">
                <?php esc_html_e('Select a Popup', SG_POPUP_TEXT_DOMAIN); ?><br/>
                <select class="widefat" name="menu-item-pb[<?php echo esc_attr($item->ID); ?>][popup]"
                        id="edit-menu-item-pb-<?php echo esc_attr($item->ID); ?>">
                    <option value=""></option>
                    <?php foreach (self::getPopups() as $popup) : ?>
                        <option value="<?php echo esc_attr($popup->getId()); ?>" <?php selected($popup->getId(), (int)get_post_meta($itemId, '_menu_sgpb_popup_id', true)); ?>>
                            <?php echo esc_html($popup->getTitle()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="description"><?php esc_html_e('Open a popup once this item is clicked.', SG_POPUP_TEXT_DOMAIN); ?></span>
            </label>
        </div>
        <?php
    }

    /**
     * Processes the saving of menu items.
     *
     * @param $menu_id
     * @param $item_id
     */
    public function save($menu_id, $item_id)
    {
        delete_post_meta($item_id, '_menu_sgpb_popup_id');
        if (isset($_POST['menu-item-pb'][$item_id]['popup'])) {
            $popupId = (int)sanitize_text_field($_POST['menu-item-pb'][$item_id]['popup']);
            update_post_meta($item_id, '_menu_sgpb_popup_id', $popupId);
        }
    }
}
