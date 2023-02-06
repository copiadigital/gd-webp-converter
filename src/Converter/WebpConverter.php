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
        // $this->debug( $this->file_path );

        // https://stackoverflow.com/questions/2183486/php-get-file-name-without-file-extension/19040276
        $this->file_dirname = pathinfo( $this->file_path, PATHINFO_DIRNAME );
        // $this->debug( $this->file_dirname );

        $this->file_ext = strtolower( pathinfo( $this->file_path, PATHINFO_EXTENSION ) );
        // $this->debug( $this->file_ext );

        $this->file_name_no_ext = pathinfo( $this->file_path, PATHINFO_FILENAME );
        // $this->debug( $this->file_name_no_ext );
    }

    public function debug( $info ) {
        $message = null;
    
        if ( is_string( $info ) || is_int( $info ) || is_float( $info ) ) {
            $message = $info;
        } else {
            $message = var_export( $info, true );
        }
    
        if ( $fh = fopen( ABSPATH . '/gdwebpconvert.log', 'a' ) ) {
            fputs( $fh, date( 'Y-m-d H:i:s' ) . " $message\n" );
            fclose( $fh );
        }
    }

    public function check_file_exists( $attachment_id ) {

        $file = get_attached_file( $attachment_id );

        if ( ! file_exists( $file ) ) {
            $message = 'The uploaded file does not exist on the server. Encoding not possible.';
            // $this->debug( $message );
            throw new Exception( 'The uploaded file does exist on the server. Encoding not possible.', 1 );
        }

    }

    public function check_mime_type() {

        // https://www.php.net/manual/en/function.finfo-file.php
        $finfo = finfo_open( FILEINFO_MIME_TYPE );

        $this->file_mime_type = finfo_file( $finfo, $this->file_path );

        finfo_close( $finfo );
        // $this->debug( $this->file_mime_type );

        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types
        $this->allowed_mime_type = array( 'image/jpeg', 'image/png' );

        if ( ! in_array( $this->file_mime_type, $this->allowed_mime_type, true ) ) {

            $message = 'MIME type of file not supported';
            // $this->debug( $message );
            throw new Exception( 'MIME type of file not supported', 1 );

        }
    }

    public function create_array_of_sizes_to_be_converted( $metadata ) {

        // push original file to the array
        array_push( $this->array_of_sizes_to_be_converted, $this->file_path );
        // $this->debug( $this->array_of_sizes_to_be_converted );

        // push all created sizes of the file to the array
        foreach ( $metadata['sizes'] as $value ) {
            // $this->debug( $value['file'] );
            array_push( $this->array_of_sizes_to_be_converted, $this->file_dirname . '/' . $value['file'] );
        }
        // // $this->debug( $this->array_of_sizes_to_be_converted );
    }

    public function convert_array_of_sizes() {

        // $this->debug( $this->array_of_sizes_to_be_converted );

        switch ( $this->file_ext ) {
            case 'jpeg':
            case 'jpg':
                foreach ( $this->array_of_sizes_to_be_converted as $key => $value ) {

                    $image = imagecreatefromjpeg( $value );

                    if ( 0 === $key ) {

                        imagewebp( $image, $this->file_dirname . '/' . $this->file_name_no_ext . '.webp', 80 );

                    } else {

                        $current_size = getimagesize( $value );
                        // $this->debug( $current_size );
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
                        // $this->debug( $current_size );
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
            //          // $this->debug( $current_size );
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

        // $this->debug( $attachment_id );

        $this->attachment_metadata_of_file_to_be_deleted = wp_get_attachment_metadata( $attachment_id );
        // $this->debug( $this->attachment_metadata_of_file_to_be_deleted );

        // push original file to the array
        array_push( $this->array_of_sizes_to_be_deleted, $this->file_dirname . '/' . $this->file_name_no_ext . '.webp' );
        // $this->debug( $this->array_of_sizes_to_be_converted );

        // push all created sizes of the file to the array
        foreach ( $this->attachment_metadata_of_file_to_be_deleted['sizes'] as $value ) {

            // $this->debug( $value );

            $this->value_file_name_no_ext = pathinfo( $value['file'], PATHINFO_FILENAME );
            // $this->debug( $this->value_file_name_no_ext );

            array_push( $this->array_of_sizes_to_be_deleted, $this->file_dirname . '/' . $this->value_file_name_no_ext . '.webp' );
        }
        // $this->debug( $this->array_of_sizes_to_be_deleted );
    }

    public function delete_array_of_sizes() {

        // $this->debug( $this->array_of_sizes_to_be_deleted );

        foreach ( $this->array_of_sizes_to_be_deleted as $key => $value ) {

            if(is_file($value)) {
                // $this->debug( $value );
                unlink( $value );
            }

        }
    }

}
