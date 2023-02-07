<?php

namespace GdWebpConverter\Providers;

class GdDefaultServiceProvider implements Provider
{
    public function __construct()
    {
        add_filter( 'wp_image_editors', [$this, 'gd_webp_converter_pagely_default_to_gd'] );
    }

    public function register()
    {
        //
    }

    public function gd_webp_converter_pagely_default_to_gd() {
        return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
    }
}
