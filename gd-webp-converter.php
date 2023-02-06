<?php
/**
* Plugin Name:  GD WebP Converter
* Description:  After uploaded an image it will be converted to WebP format using the GD image engine. <a target="_blank" href="https://developer.wordpress.org/reference/classes/wp_image_editor_gd/">WP GD Image Engine</a> If the file is deleted form the Media Library the created WebP conversions will also be deleted.
* Version:      1.0.0
* Author:       Copia Digital
* Author URI:   https://www.copiadigital.com/
* License:      MIT License
*/

require_once __DIR__.'/../../../../vendor/autoload.php';

$clover = new GdWebpConverter\Providers\GdWebpConverterServiceProvider;
$clover->register();

add_action('init', [$clover, 'boot']);