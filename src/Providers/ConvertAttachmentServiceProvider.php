<?php

namespace GdWebpConverter\Providers;

class ConvertAttachmentServiceProvider implements Provider
{
    public function register()
    {
        function gd_webp_converter_image_attachment_src( $image, $id, $size, $icon ) {
            $getFile = get_attached_file($id);
            if($getFile) {
                $getFileInfo = pathinfo($getFile);
                $imgExtArr = ['jpg', 'jpeg', 'png'];
                if(in_array($getFileInfo['extension'], $imgExtArr)) {
                    if (file_exists($getFileInfo['dirname'] . '/' . $getFileInfo['filename'] . '.webp')) {
                        return str_replace(['.png', '.jpg', '.jpeg'], '.webp', $image);
                    }
                }
            }
            return $image;
        }
        add_filter('wp_get_attachment_image_src', __NAMESPACE__ . '\\gd_webp_converter_image_attachment_src', 10, 4);

        function gd_webp_converter_image_attachment_url( $url, $id ) {
            $getFile = get_attached_file($id);
            if($getFile) {
                $getFileInfo = pathinfo($getFile);
                $imgExtArr = ['jpg', 'jpeg', 'png'];
                if(in_array($getFileInfo['extension'], $imgExtArr)) {
                    if (file_exists($getFileInfo['dirname'] . '/' . $getFileInfo['filename'] . '.webp')) {
                        return str_replace(['.png', '.jpg', '.jpeg'], '.webp', $url);
                    }
                }
            }
            return $url;
        }
        add_filter('wp_get_attachment_url', __NAMESPACE__ . '\\gd_webp_converter_image_attachment_url', 10, 2);

        function gd_webp_converter_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
            $getFile = get_attached_file($attachment_id);
            if($getFile) {
                $getFileInfo = pathinfo($getFile);
                $imgExtArr = ['jpg', 'jpeg', 'png'];
                $images = [];
                if(in_array($getFileInfo['extension'], $imgExtArr)) {
                    if (file_exists($getFileInfo['dirname'] . '/' . $getFileInfo['filename'] . '.webp')) {
                        foreach($sources as $source) {
                            $src = str_replace(['.png', '.jpg', '.jpeg'], '.webp', $source['url']);
                            $images[] = [
                                'url' => $src,
                                'descriptor' => $source['descriptor'],
                                'value' => $source['value']
                            ];
                        }
                        return $images;
                    }
                }
            }
            return $sources;
        }
        add_filter('wp_calculate_image_srcset', __NAMESPACE__ . '\\gd_webp_converter_image_srcset', 10, 5);

        function gd_webp_converter_attachment_metadata($data, $attachment_id) {
            $getFile = get_attached_file($attachment_id);
            if($getFile) {
                $getFileInfo = pathinfo($getFile);
                $imgExtArr = ['jpg', 'jpeg', 'png'];
                if(in_array($getFileInfo['extension'], $imgExtArr)) {
                    if (file_exists($getFileInfo['dirname'] . '/' . $getFileInfo['filename'] . '.webp')) {
                        return str_replace(['.png', '.jpg', '.jpeg'], '.webp', $data);
                    }
                }
            }
            return $data;
        }
        add_filter('wp_get_attachment_metadata', __NAMESPACE__ . '\\gd_webp_converter_attachment_metadata', 10, 2);
    }
}
