<?php

namespace GdWebpConverter\Providers;

class GdDefaultServiceProvider implements Provider
{
    public function register()
    {
        function gd_webp_converter_pagely_default_to_gd() {
            return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
        }
        add_filter( 'wp_image_editors', __NAMESPACE__ . '\\gd_webp_converter_pagely_default_to_gd' );
    }
}
