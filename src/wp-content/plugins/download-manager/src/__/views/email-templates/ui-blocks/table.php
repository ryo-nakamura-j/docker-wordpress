<?php
/**
 * User: shahnuralam
 * Date: 18/11/18
 * Time: 10:13 PM
 */

use WPDM\__\__;

if (!defined('ABSPATH')) die();
?>
<table style="width: 100%;<?php if(isset($css, $css['table'])) echo esc_attr($css['table']); ?>" class="email <?php if(isset($tclass)) echo esc_attr($tclass); ?>" cellspacing="0" cellpadding="0">
    <?php if(isset($thead)){ ?>
    <thead>
        <tr>
            <?php foreach ($thead as $th) { ?>
                <th style="<?php if(isset($css, $css['th'])) echo esc_attr($css['th']); ?>"><?php echo __::sanitize_var($th, 'kses'); ?></th>
            <?php } ?>
        </tr>
    </thead>
    <?php } ?>
    <tbody>

        <?php $rn = 0; foreach ($data as $row){ ?>
            <tr>
                <?php $cn = 0; foreach ($row as $td) { ?>
                    <td style="<?php if(isset($css, $css['td'])) echo esc_attr($css['td']); ?><?php if(isset($css, $css['col'], $css['col'][$cn])) echo esc_attr($css['col'][$cn]); ?><?php if(isset($css, $css['row'], $css['row'][$rn])) echo esc_attr($css['row'][$rn]); ?>"><?php echo __::sanitize_var($td, 'kses'); ?></td>
                <?php $cn++; } ?>
            </tr>
        <?php $rn++; } ?>

    </tbody>

</table>

