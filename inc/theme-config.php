<?php if (!function_exists('frameflow_configs')) {
    function frameflow_configs($value)
    {
        $configs = [
            'theme_colors' => [
                'primary'   => [
                    'title' => esc_html__('Primary', 'frameflow'),
                    'value' => frameflow()->get_opt('primary_color', '#EFDF5C')
                ],
                'secondary'   => [
                    'title' => esc_html__('Secondary', 'frameflow'),
                    'value' => frameflow()->get_opt('secondary_color', '#1A1A1A')
                ],
                'third'   => [
                    'title' => esc_html__('Third', 'frameflow'),
                    'value' => frameflow()->get_opt('third_color', '#555555')
                ],
                'four'   => [
                    'title' => esc_html__('Four', 'frameflow'),
                    'value' => frameflow()->get_opt('four_color', '#98CDEA')
                ],
                'five'   => [
                    'title' => esc_html__('Five', 'frameflow'),
                    'value' => frameflow()->get_opt('five_color', '#FFC29F')
                ],
                'six'   => [
                    'title' => esc_html__('Six', 'frameflow'),
                    'value' => frameflow()->get_opt('six_color', '#EFCCFF')
                ],
                'body_bg'   => [
                    'title' => esc_html__('Body Background Color', 'frameflow'),
                    'value' => frameflow()->get_opt('body_bg_color', '#fff')
                ]
            ],

            'link' => [
                'color' => frameflow()->get_opt('link_color', ['regular' => '#1A1A1A'])['regular'],
                'color-hover'   => frameflow()->get_opt('link_color', ['hover' => '#222'])['hover'],
                'color-active'  => frameflow()->get_opt('link_color', ['active' => '#222'])['active'],
            ],
            'gradient' => [
                'color-from' => frameflow()->get_opt('gradient_color', ['from' => '#A493FF'])['from'],
                'color-center' => frameflow()->get_opt('gradient_color_center', '#FFC29F'),
                'color-to' => frameflow()->get_opt('gradient_color', ['to' => '#72BEF9'])['to'],
            ],
            'gradient_two' => [
                'color-from' => frameflow()->get_opt('gradient_color_two', ['from' => '#8160C7'])['from'],
                'color-center' => frameflow()->get_opt('gradient_color_two_center', '#62489A'),
                'color-to' => frameflow()->get_opt('gradient_color_two', ['to' => '#503687'])['to'],
            ],
        ];
        return $configs[$value];
    }
}
if (!function_exists('frameflow_inline_styles')) {
    function frameflow_inline_styles()
    {

        $theme_colors      = frameflow_configs('theme_colors');
        $link_color        = frameflow_configs('link');
        $gradient_color        = frameflow_configs('gradient');
        $gradient_two_color    = frameflow_configs('gradient_two');
        ob_start();
        echo ':root{';

        foreach ($theme_colors as $color => $value) {
            printf('--%1$s-color: %2$s;', str_replace('#', '', $color),  $value['value']);
        }
        foreach ($theme_colors as $color => $value) {
            printf('--%1$s-color-rgb: %2$s;', str_replace('#', '', $color),  frameflow_hex_rgb($value['value']));
        }
        foreach ($link_color as $color => $value) {
            printf('--link-%1$s: %2$s;', $color, $value);
        }
        foreach ($gradient_color as $color => $value) {
            printf('--gradient-%1$s: %2$s;', $color, $value);
        }
        foreach ($gradient_two_color as $color => $value) {
            printf('--gradient-two-%1$s: %2$s;', $color, $value);
        }
        echo '}';

        return ob_get_clean();
    }
}
