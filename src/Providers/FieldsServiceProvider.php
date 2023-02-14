<?php

namespace GdWebpConverter\Providers;

class FieldsServiceProvider implements Provider
{
    public function __construct()
    {
        add_action('acf/init', [$this, 'gd_webp_converter_fields']);
    }
    
    public function register()
    {
       //
    }

    public function gd_webp_converter_fields() {
        if (!function_exists('acf_add_options_page')) {
            return;
        }

        acf_add_local_field_group(array(
            'key'   => 'group_gd_webp_converter_media_settings',
            'title' => 'GD Webp Media Settings',
            'fields'    => array (),
            'position'  => 'normal',
            'menu_order'    => 0,
            'label_placement'   => 'top',
            'style' => 'default',
            'active'    => true,
            'description'   => '',
            'location'  => array (
                array (
                    array (
                        'param' => 'attachment',
                        'operator'  => '==',
                        'value' => 'image/jpeg',
                    ),
                ),
                array(
                    array(
                        'param' => 'attachment',
                        'operator' => '==',
                        'value' => 'image/png',
                    ),
                ),
            ),
        ));

            acf_add_local_field(array(
                'key'          => 'field_gd_webp_converter_media_settings_gd_webp_mime_type_switcher',
                'label'        => 'Use original image?',
                'name'         => 'gd_webp_mime_type_switcher',
                'type'         => 'true_false',
                'required'     => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => 0,
                'ui' => 1,
                'ui_on_text' => '',
                'ui_off_text' => '',
                'parent'    => 'group_gd_webp_converter_media_settings',
            ));
    }
}
