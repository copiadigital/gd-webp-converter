<?php
/**
* Plugin Name:  GD WebP Converter
* Text Domain:  gd-webp-converter
* Description:  After uploaded an image it will be converted to WebP format using the GD image engine. <a target="_blank" href="https://developer.wordpress.org/reference/classes/wp_image_editor_gd/">WP GD Image Engine</a> If the file is deleted from the Media Library the created WebP conversions will also be deleted.
* Version:      1.0.0
* Author:       Copia Digital
* Author URI:   https://www.copiadigital.com/
* License:      MIT License
*/

$autoload_path = __DIR__.'/vendor/autoload.php';
if ( file_exists( $autoload_path ) ) {
    require_once( $autoload_path );
}

$clover = new GdWebpConverter\Providers\GdWebpConverterServiceProvider;
$clover->register();

add_action('init', [$clover, 'boot']);

add_action('plugins_loaded', function() {
    if (!class_exists('acf')) {
        deactivate_plugins('gd-webp-converter/gd-webp-converter.php');
        add_action( 'admin_notices', function() {
            $class = 'notice notice-error';
            $message = __( 'ACF Class not found!', 'gd-webp-converter' );
  
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        } );
    }
});