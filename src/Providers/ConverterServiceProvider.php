<?php

namespace GdWebpConverter\Providers;

use GdWebpConverter\Converter\WebpConverter;

class ConverterServiceProvider implements Provider
{
    public function register()
    {
        function gd_webp_converter( $metadata, $attachment_id ) {

            $gd_webp_converter = new WebpConverter( $attachment_id );
            $gd_webp_converter->check_file_exists( $attachment_id );
            $gd_webp_converter->check_mime_type();
            $gd_webp_converter->create_array_of_sizes_to_be_converted( $metadata );
            $gd_webp_converter->convert_array_of_sizes();

            return $metadata;
        }
        add_filter( 'wp_generate_attachment_metadata', __NAMESPACE__ . '\\gd_webp_converter', 10, 2 );

        function gd_webp_converter_delete_conversions( $attachment_id ) {

            $delete_webp_conversions = new WebpConverter( $attachment_id );
            $delete_webp_conversions->create_array_of_sizes_to_be_deleted( $attachment_id );
            $delete_webp_conversions->delete_array_of_sizes();

        }
        add_action( 'delete_attachment', __NAMESPACE__ . '\\gd_webp_converter_delete_conversions', 10 );
    }
}
