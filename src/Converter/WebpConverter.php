<?php

namespace GdWebpConverter\Converter;

class WebpConverter {

    private $file_path;
    private $file_dirname;
    private $file_ext;
    private $file_name_no_ext;

    private $array_of_sizes_to_be_converted = array();
    private $array_of_sizes_to_be_deleted   = array();

    public function __construct( $attachment_id ) {

        $this->file_path = get_attached_file( $attachment_id );
        // error_log(print_r( $this->file_path, true ));

        // https://stackoverflow.com/questions/2183486/php-get-file-name-without-file-extension/19040276
        $this->file_dirname = pathinfo( $this->file_path, PATHINFO_DIRNAME );
        // error_log(print_r( $this->file_dirname, true ));

        $this->file_ext = strtolower( pathinfo( $this->file_path, PATHINFO_EXTENSION ) );
        // error_log(print_r( $this->file_ext, true ));

        $this->file_name_no_ext = pathinfo( $this->file_path, PATHINFO_FILENAME );
        // error_log(print_r( $this->file_name_no_ext, true ));
    }

    public function check_file_exists( $attachment_id ) {

        $file = get_attached_file( $attachment_id );

        if ( ! file_exists( $file ) ) {
            $message = 'The uploaded file does not exist on the server. Encoding not possible.';
            // error_log(print_r( $message, true ));
            throw new Exception( 'The uploaded file does exist on the server. Encoding not possible.', 1 );
        }

    }

    public function check_mime_type() {

        // https://www.php.net/manual/en/function.finfo-file.php
        $finfo = finfo_open( FILEINFO_MIME_TYPE );

        $this->file_mime_type = finfo_file( $finfo, $this->file_path );

        finfo_close( $finfo );
        // error_log(print_r( $this->file_mime_type, true ));

        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types
        if(isset($this->allowed_mime_type)){
            if ( in_array( $this->file_mime_type, $this->allowed_mime_type, true ) ) {
                $this->allowed_mime_type = array( 'image/jpeg', 'image/png' );
            }
        }

        // if ( ! in_array( $this->file_mime_type, $this->allowed_mime_type, true ) ) {

        //     $message = 'MIME type of file not supported';
        //     // error_log(print_r( $message, true ));
        //     throw new Exception( 'MIME type of file not supported', 1 );

        // }
    }

    public function create_array_of_sizes_to_be_converted( $metadata ) {
        // error_log(print_r( $this->array_of_sizes_to_be_converted, true ));

        // push original file to the array
        array_push( $this->array_of_sizes_to_be_converted, $this->file_path );

        if(!empty($metadata['sizes'])) {
            // push all created sizes of the file to the array
            foreach ( $metadata['sizes'] as $value ) {
                // error_log(print_r( $value['file'], true ));
                array_push( $this->array_of_sizes_to_be_converted, $this->file_dirname . '/' . $value['file'] );
            }
        }
        // else {
        //     error_log(print_r( $metadata, true ));
        // }
    }

    public function convert_array_of_sizes() {

        // error_log(print_r( $this->array_of_sizes_to_be_converted, true ));

        switch ( $this->file_ext ) {
            case 'jpeg':
            case 'jpg':
                foreach ( $this->array_of_sizes_to_be_converted as $key => $value ) {

                    $image = imagecreatefromjpeg( $value );

                    if ( 0 === $key ) {

                        imagewebp( $image, $this->file_dirname . '/' . $this->file_name_no_ext . '.webp', 80 );

                    } else {

                        $current_size = getimagesize( $value );
                        // error_log(print_r( $current_size, true ));
                        imagewebp( $image, $this->file_dirname . '/' . $this->file_name_no_ext . '-' . $current_size[0] . 'x' . $current_size[1] . '.webp', 80 );

                    }

                    imagedestroy( $image );
                }
                break;

            case 'png':
                foreach ( $this->array_of_sizes_to_be_converted as $key => $value ) {

                    $image = imagecreatefrompng( $value );
                    imagepalettetotruecolor( $image );
                    imagealphablending( $image, true );
                    imagesavealpha( $image, true );

                    if ( 0 === $key ) {

                        imagewebp( $image, $this->file_dirname . '/' . $this->file_name_no_ext . '.webp', 80 );

                    } else {

                        $current_size = getimagesize( $value );
                        // error_log(print_r( $current_size, true ));
                        imagewebp( $image, $this->file_dirname . '/' . $this->file_name_no_ext . '-' . $current_size[0] . 'x' . $current_size[1] . '.webp', 80 );

                    }

                    imagedestroy( $image );

                }
                break;

            // animated GIF to WebP not supported by GD - imagecreatefromgif
            // case 'gif':
            //  foreach ( $this->array_of_sizes_to_be_converted as $key => $value ) {

            //      $image = imagecreatefromgif( $value );

            //      if ( 0 === $key ) {

            //          imagewebp( $image, $this->file_dirname . '/' . $this->file_name_no_ext . '.webp', 80 );

            //      } else {

            //          $current_size = getimagesize( $value );
            //          // error_log(print_r( $current_size, true ));
            //          imagewebp( $image, $this->file_dirname . '/' . $this->file_name_no_ext . '-' . $current_size[0] . 'x' . $current_size[1] . '.webp', 80 );

            //      }

            //      imagedestroy( $image );

            //  }
            //  break;

            default:
                return false;
        }

    }

    public function create_array_of_sizes_to_be_deleted( $attachment_id ) {

        // error_log(print_r( $attachment_id, true ));

        $this->attachment_metadata_of_file_to_be_deleted = wp_get_attachment_metadata( $attachment_id );

        // push original file to the array
        array_push( $this->array_of_sizes_to_be_deleted, $this->file_dirname . '/' . $this->file_name_no_ext . '.webp' );

        // error_log(print_r( $this->attachment_metadata_of_file_to_be_deleted, true ));

        if(!empty($this->attachment_metadata_of_file_to_be_deleted['sizes'])) {
            // error_log(print_r( $this->array_of_sizes_to_be_converted, true ));
            
            // push all created sizes of the file to the array
            foreach ( $this->attachment_metadata_of_file_to_be_deleted['sizes'] as $value ) {

                // error_log(print_r( $value, true ));

                $this->value_file_name_no_ext = pathinfo( $value['file'], PATHINFO_FILENAME );
                // error_log(print_r( $this->value_file_name_no_ext, true ));

                array_push( $this->array_of_sizes_to_be_deleted, $this->file_dirname . '/' . $this->value_file_name_no_ext . '.webp' );
            }
            // error_log(print_r( $this->array_of_sizes_to_be_deleted, true ));
        }
    }

    public function delete_array_of_sizes() {

        // error_log(print_r( $this->array_of_sizes_to_be_deleted, true ))

        foreach ( $this->array_of_sizes_to_be_deleted as $key => $value ) {

            if(is_file($value)) {
                // error_log(print_r( $value, true ))
                unlink( $value );
            }

        }
    }

}
