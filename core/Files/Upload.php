<?php
/**
 * ABS MVC Framework
 *
 * @created      2023
 * @version      1.0.1
 * @author       abdursoft <support@abdursoft.com>
 * @copyright    2024 abdursoft
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
*/

namespace Core\Files;

use System\Plugins\S3;

class Upload {

    /**
     * Content Upload
     * @param content name of the file input
     * will upload file in the resource direcotry
     */
    public static function putContent( $content ) {
        if ( !empty( $content["name"] ) ) {
            $type      = array( 'png', 'jpg', 'jpeg', 'webp', 'bmp', 'gif', 'mp4', 'webm', 'ogg', 'mov', 'aac', 'mp4', 'wav', 'pdf', 'docs', 'txt', 'xlxs', 'json' );
            $name      = $content['name'];
            $tmp       = $content['tmp_name'];
            $size      = $content['size'];
            $ext       = explode( '.', $name );
            $extention = strtolower( end( $ext ) );
            $path      = "public/resource/";

            if ( in_array( $extention, $type ) ) {
                if ( UPLOAD_SERVER == 'storage' ) {
                    if ( !is_dir( $path ) ) {
                        mkdir( $path, 0777, true );
                    }
                    $filename = $path . '/' . time() . '.' . $extention;
                    if ( move_uploaded_file( $tmp, $filename ) ) {
                        return "/" . $filename;
                    } else {
                        return false;
                    }
                } else{
                    $s3       = new S3();
                    $filename = $path . '/' . time() . '.' . $extention;
                    $upload   = $s3->awsUpload( $filename, $tmp );
                    $upload ? $filename : false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * create image and compress
     * @param source source of the image file
     * @param destination image upload directory
     * @param quality image compress quality 1-9
     */
    public static function compressImage( $source, $destination, $quality ) {
        $imgInfo = getimagesize( $source );
        $mime    = $imgInfo['mime'];
        switch ( $mime ) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg( $source );
            imagejpeg( $image, $destination, $quality );
            break;
        case 'image/png':
            $image = imagecreatefrompng( $source );
            imagepng( $image, $destination, $quality );
            break;
        case 'image/gif':
            $image = imagecreatefromgif( $source );
            imagegif( $image, $destination, $quality );
            break;
        default:
            $image = imagecreatefromjpeg( $source );
            imagejpeg( $image, $destination, $quality );
        }
        return $destination;
    }


    /**
     * image upload with compress
     * @param content name of the input files
     * @param path upload destination for the files
     * @param quality image compress quality 1-9
     */
    public static function putImage( $content, $path, $quality ) {
        if ( !empty( $content["name"] ) ) {
            $type      = array( 'png', 'jpg', 'jpeg', 'webp', 'bmp', 'gif' );
            $name      = $content['name'];
            $tmp       = $content['tmp_name'];
            $ext       = explode( '.', $name );
            $extention = strtolower( end( $ext ) );
            $path      = "public/resource/" . $path;

            if(in_array($extention,$type)){
                if(UPLOAD_SERVER == 'storage'){
                    if ( !is_dir( $path ) ) {
                        mkdir( $path, 0777, true );
                    }
                    $filename = $path . '/' . time() . '.' . $extention;
                    if ( in_array( $extention, $type ) ) {
                        $compressedImage = self::compressImage( $tmp, $filename, $quality );
                        return "/" . $compressedImage;
                    } else {
                        return false;
                    }
                }else{
                    $s3 = new S3();
                    $filename = $path . '/' . time() . rand( 10000, 999999999999 ) . '.' . $extention;
                    $upload = $s3->awsUpload($filename,$tmp);
                    $upload ? $filename : false;
                }
            }
            
        } else {
            return false;
        }
    }


    /**
     * image upload many images with compress
     * @param content name of the input files
     * @param path upload destination for the files
     * @param quality image compress quality 1-9
     */
    public static function putManyImage( $content, $path, $quality ) {

        foreach ( $_FILES[$content]['name'] as $key => $val ) {
            $name      = $_FILES[$content]['name'][$key];
            $temp      = $_FILES[$content]['tmp_name'][$key];
            $type      = array( 'png', 'jpg', 'jpeg', 'webp', 'bmp', 'gif' );
            $ext       = explode( '.', $name );
            $extention = strtolower( end( $ext ) );
            $path      = "public/resource/" . $path;

            if(in_array($extention,$type)){
                if(UPLOAD_SERVER == 'storage'){
                    if ( !is_dir( $path ) ) {
                        mkdir( $path, 0777, true );
                    }
                    $filename = $path . '/' . time() . rand( 10000, 999999999999 ) . '.' . $extention;
                    if ( in_array( $extention, $type ) ) {
                        $compressedImage = self::compressImage( $temp, $filename, $quality );
                        return "/" . $compressedImage;
                    } else {
                        return false;
                    }
                }else{
                    $s3 = new S3();
                    $filename = $path . '/' . time() . rand( 10000, 999999999999 ) . '.' . $extention;
                    $upload = $s3->awsUpload($filename,$temp);
                    $upload ? $filename : false;
                }
            }

            
        }
    }


    /**
     * video upload
     * @param name name of the input files
     * @param temp temprory name of the input file
     * @param path upload destination for the file
     */
    public static function putVideo( $name, $temp, $path ) {
        $type      = array( 'mp4', 'webm', 'ogg' );
        $ext       = explode( '.', $name );
        $extention = strtolower( end( $ext ) );
        $path      = "public/resource/" . $path;

        if ( in_array( $extention, $type ) ) {
            if ( UPLOAD_SERVER == 'storage' ) {
                if ( !is_dir( $path ) ) {
                    mkdir( $path, 0777, true );
                }

                $filename = $path . '/' . time() . rand( 1000, 9999999 ) . '.' . $extention;
                if ( in_array( $extention, $type ) ) {
                   $upload = move_uploaded_file( $temp, $filename );
                   $upload ? "/".$filename : false;
                } else {
                    return false;
                }
            }else{
                $s3 = new S3();
                $filename = $path . '/' . time() . rand( 1000, 9999999 ) . '.' . $extention;
                $upload = $s3->awsUpload($filename,$temp);
                $upload ? $filename : false;
            }
        }

    }


    /**
     * many videos upload
     * @param name name of the input files
     * @param path upload destination for the file
     */
    public static function putManyVideo( $content, $path ) {
        foreach ( $content['name'] as $key => $val ) {
            $name      = $_FILES['file_temp']['name'][$key];
            $temp      = $_FILES['file_temp']['tmp_name'][$key];
            $type      = array( 'mp4', 'webm', 'ogg','mov' );
            $ext       = explode( '.', $name );
            $extention = strtolower( end( $ext ) );
            $path      = "public/resource/" . $path;

            if(in_array($extention,$type)){
                if(UPLOAD_SERVER == 'storage'){
                    if ( !is_dir( $path ) ) {
                        mkdir( $path, 0777, true );
                    }
                    $filename = $path . '/' . time() . rand( 10000, 999999999999 ) . '.' . $extention;
                    if ( in_array( $extention, $type ) ) {
                        $upload = move_uploaded_file( $temp, $filename );
                        $upload ? "/".$filename : false;
                    } else {
                        return false;
                    }
                }else{
                    $s3 = new S3();
                    $filename = $path . '/' . time() . rand( 10000, 999999999999 ) . '.' . $extention;
                    $upload = $s3->awsUpload($filename,$temp);
                    $upload ? $filename : false;
                }
            }else{
                return false;
            }
        }
    }
}