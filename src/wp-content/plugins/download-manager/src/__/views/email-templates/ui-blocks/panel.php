<?php
/**
 * User: shahnuralam
 * Date: 17/11/18
 * Time: 1:09 AM
 */

use WPDM\__\__;

if (!defined('ABSPATH')) die();
?>
<div style="border:1px solid #D7E0E9;border-radius: 3px;overflow: hidden;">
    <?php if($heading != ''){ ?>
    <div style="border-bottom: 1px solid #D7E0E9;background: #EFF3F7;padding: 10px 15px;font-size: 12pt;font-weight: bold;letter-spacing: 0.5px;line-height: 1.5"><?php echo __::sanitize_var($heading, 'kses'); ?></div>
    <?php } ?>
    <?php if(count($content) > 0){
        foreach ($content as $html) {
        ?>
        <div style="border-bottom: 1px solid #D7E0E9;background: rgba(255,255,255,0.7);padding: 10px 15px;margin-bottom: -1px;letter-spacing: 0.5px;line-height: 1.5"><?php echo __::sanitize_var($html, 'kses'); ?></div>
    <?php }
    } ?>
    <?php if($footer != ''){ ?>
        <div style="background: #EFF3F7;padding: 10px 15px;margin-top: -1px !important;"><?php echo __::sanitize_var($footer, 'kses'); ?></div>
    <?php } ?>
</div>
