<?php
$p_menu = frameflow()->get_page_opt('p_menu');
if (!empty($p_menu)) {
    $settings['menu'] = $p_menu;
}
$menu_item_icon = '';
if (!empty($settings['pxl_icon']['value'])) {
    $menu_item_icon = $settings['pxl_icon']['value'];
}


$svg_icon = '<svg class="pxl-hide" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
  <path d="M14 6.99988C13.9999 7.24062 13.9042 7.47148 13.734 7.64171C13.5638 7.81194 13.3329 7.90761 13.0922 7.9077H8.20433C8.12569 7.90772 8.05029 7.93896 7.99468 7.99457C7.93908 8.05017 7.90783 8.12558 7.90781 8.20421V13.0922C7.90781 13.3329 7.81217 13.5638 7.64192 13.7341C7.47167 13.9043 7.24077 14 7 14C6.75923 14 6.52833 13.9043 6.35808 13.7341C6.18783 13.5638 6.09219 13.3329 6.09219 13.0922V8.20427C6.09218 8.12563 6.06094 8.05021 6.00535 7.9946C5.94975 7.93898 5.87434 7.90772 5.7957 7.9077H0.907813C0.667046 7.9077 0.43614 7.81205 0.265892 7.6418C0.0956442 7.47156 0 7.24065 0 6.99988C0 6.75912 0.0956442 6.52821 0.265892 6.35796C0.43614 6.18771 0.667046 6.09207 0.907813 6.09207H5.79578C5.87444 6.09207 5.94988 6.06082 6.00551 6.0052C6.06113 5.94958 6.09238 5.87414 6.09238 5.79547V0.907477C6.09238 0.66671 6.18802 0.435804 6.35827 0.265556C6.52852 0.0953086 6.75942 -0.000335693 7.00019 -0.000335693C7.24096 -0.000335693 7.47186 0.0953086 7.64211 0.265556C7.81236 0.435804 7.908 0.66671 7.908 0.907477V5.79545C7.908 5.87411 7.93925 5.94955 7.99488 6.00517C8.0505 6.06079 8.12594 6.09204 8.2046 6.09204H13.0925C13.3332 6.09223 13.5639 6.18795 13.7341 6.35817C13.9043 6.52839 13.9999 6.7592 14 6.99988Z" fill="black"/>
</svg>';

if (!function_exists('frameflow_add_submenu_icon_to_output')) {
    function frameflow_add_submenu_icon_to_output($menu_output, $svg_icon = '', $menu_item_icon = '')
    {
        if (strpos($menu_output, 'sub-menu') !== false || strpos($menu_output, 'children') !== false) {
            $menu_output = preg_replace_callback(
                '/(<ul[^>]*class=["\'][^"\']*(?:sub-menu|children)[^"\']*["\'][^>]*>.*?<\/ul>)/s',
                function ($matches) use ($svg_icon, $menu_item_icon) {
                    $submenu_html = $matches[0];
                    if (strpos($submenu_html, 'pxl-item-menu-icon') === false && strpos($submenu_html, '<svg') === false) {
                        $submenu_html = preg_replace(
                            '/(<a[^>]*>.*?)(<\/a>)/s',
                            '$1' . $svg_icon . '<span class="pxl-item-menu-icon pxl-hide ' . esc_attr($menu_item_icon) . '"></span>$2',
                            $submenu_html
                        );
                    }
                    return $submenu_html;
                },
                $menu_output
            );
        }

        return $menu_output;
    }
}

if (!empty($settings['menu'])) { ?>
    <div class="pxl-nav-menu pxl-nav-menu1 <?php echo esc_attr($settings['menu_mega_type'] . ' ' . $settings['menu_stype'] . ' ' . 'pxl-nav-' . $settings['menu_type'] . ' ' . $settings['hover_active_style'] . ' ' . $settings['sub_show_effect'] . ' ' . $settings['pxl_animate']); ?> <?php echo esc_attr($settings['hover_active_style_sub']); ?>" data-wow-delay="<?php echo esc_attr($settings['pxl_animate_delay']); ?>ms">
        <?php

        ob_start();
        wp_nav_menu(
            array(
                'theme_location' => 'primary',
                'menu_class' => 'pxl-menu-primary clearfix',
                'walker'     => class_exists('PXL_Mega_Menu_Walker') ? new PXL_Mega_Menu_Walker : '',
                'link_before'     => '<div class="menu-text pxl-menu-item-text">',
                'link_after'      => $svg_icon . '<span class="pxl-item-menu-icon pxl-hide ' . esc_attr($menu_item_icon) . '"></span></div>',
                'menu'        => wp_get_nav_menu_object($settings['menu']),
                'pxl_split_title' => true
            )
        );
        $menu_output = ob_get_clean();
        echo frameflow_add_submenu_icon_to_output($menu_output, $svg_icon, $menu_item_icon);

        ?>
        <?php if ($settings['hover_active_style'] == 'fr-style-divider') : ?>
            <div class="pxl-divider-move"></div>
        <?php endif; ?>
    </div>
<?php } elseif (has_nav_menu('primary')) { ?>
    <div class="pxl-nav-menu pxl-nav-menu1 <?php echo esc_attr($settings['menu_mega_type'] . ' ' . $settings['menu_stype'] . ' ' . 'pxl-nav-' . $settings['menu_type'] . ' ' . $settings['hover_active_style'] . ' ' . $settings['sub_show_effect']); ?> <?php echo esc_attr($settings['hover_active_style_sub']); ?>">
        <?php

        $attr_menu = array(
            'theme_location' => 'primary',
            'menu_class' => 'pxl-menu-primary clearfix',
            'link_before'     => '<div class="menu-text pxl-menu-item-text">',
            'link_after'      => $svg_icon . '<span class="pxl-item-menu-icon pxl-hide ' . esc_attr($menu_item_icon) . '"></span></div>',
            'walker'         => class_exists('PXL_Mega_Menu_Walker') ? new PXL_Mega_Menu_Walker : '',
            'pxl_split_title' => true
        );

        ob_start();
        wp_nav_menu($attr_menu);
        $menu_output = ob_get_clean();
        echo frameflow_add_submenu_icon_to_output($menu_output, $svg_icon, $menu_item_icon);

        ?>
        <?php if ($settings['hover_active_style'] == 'fr-style-divider') : ?>
            <div class="pxl-divider-move"></div>
        <?php endif; ?>
    </div>
<?php } ?>