<?php
/**
 * User: shahnuralam
 * Date: 17/11/18
 * Time: 1:06 AM
 */

namespace WPDM\__;


class __MailUI
{



    static function panel($heading = '', $content = array(), $footer = ''){
        $template = new Template();
        return $template->assign('heading', $heading)
            ->assign('content', $content)
            ->assign('footer', $footer)
            ->fetch("email-templates/ui-blocks/panel.php", __DIR__.'/views');
    }

    static function table($thead, $data, $css){
        $template = new Template();
        return $template->assign('thead', $thead)
            ->assign('data', $data)
            ->assign('css', $css)
            ->fetch("email-templates/ui-blocks/table.php", __DIR__.'/views');
    }

	static function box($content)
	{
		return '<div style="padding: 20px; background: #ffffff; border: 1px solid #efefef;border-radius: 4px;margin: 10px 0">' . $content . '</div>';
	}


	static function button( $link, $label, $bg = '#0085d8', $padding = '10px 20px', $radius = 4, $width = 'block', $margin = '10px 0') {
		$link   = esc_attr( $link );
		$button = "<a href='{$link}' style='background: {$bg}; color: #ffffff; border-radius: {$radius}px;display: {$width}; padding: {$padding};margin: {$margin}'>{$label}</a>";
		return $button;
	}

}
