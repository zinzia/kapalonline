<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once( BASEPATH.'libraries/Upload.php' );

class CI_Tupload extends CI_Upload {

    private $data_resize = array();

    /**
     * Perform the images upload
     *
     * @return	message
     */
    public function do_upload_image ($field = 'image', $new_width = FALSE,  $new_height = FALSE) {
        // upload file first
        if ($this->do_upload($field)) {
            // resize file
            if($this->is_image()) {
                // file path
                $file_path = trim($this->upload_path, '/') . '/' . $this->file_name;
                $new_file_path = $file_path;
                // cek file
                if(is_file($file_path)) {
                    // assign width
                    if($new_width == FALSE) {
                        $new_width = $this->image_width;
                        // resize height
                        if($new_height != FALSE) {
                            $new_width = $new_height * $this->image_width / $this->image_height;
                            $new_width = ceil($new_width);
                        } else {
                            $new_height = $this->image_height;
                        }
                    }else {
                        // resize height
                        if($new_height == FALSE) {
                            $new_height = $new_width * $this->image_height / $this->image_width;
                            $new_height = ceil($new_height);
                        }
                    }
                    // resize images
                    switch($this->file_ext) {
                        case '.pjpeg':
                            $uploaded_img = imagecreatefromjpeg($file_path);
                            $result_img = ImageCreateTrueColor($new_width, $new_height);
                            imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $new_width, $new_height, $this->image_width, $this->image_height);
                            imagejpeg ($result_img, $new_file_path);
                            break;
                        case '.jpeg':
                            $uploaded_img = imagecreatefromjpeg($file_path);
                            $result_img = ImageCreateTrueColor($new_width, $new_height);
                            imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $new_width, $new_height, $this->image_width, $this->image_height);
                            imagejpeg ($result_img, $new_file_path);
                            break;
                        case '.jpg':
                            $uploaded_img = imagecreatefromjpeg($file_path);
                            $result_img = ImageCreateTrueColor($new_width, $new_height);
                            imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $new_width, $new_height, $this->image_width, $this->image_height);
                            imagejpeg ($result_img, $new_file_path);
                            break;
                        case '.png':
                            $uploaded_img = imagecreatefrompng($file_path);
                            $result_img = ImageCreateTrueColor($new_width, $new_height);
                            // set transparancy
                            imagealphablending($result_img, false);
                            imagesavealpha($result_img, true);
                            $transparent = imagecolorallocatealpha($result_img, 255, 255, 255, 127);
                            imagefilledrectangle($result_img, 0, 0, $new_width, $new_height, $transparent);
                            // --
                            imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $new_width, $new_height, $this->image_width, $this->image_height);
                            imagepng ($result_img, $new_file_path);
                            break;
                        case '.gif':
                            $uploaded_img = imagecreatefromgif($file_path);
                            $result_img = ImageCreateTrueColor($new_width, $new_height);
                            // set transparancy
                            imagealphablending($result_img, false);
                            imagesavealpha($result_img, true);
                            $transparent = imagecolorallocatealpha($result_img, 255, 255, 255, 127);
                            imagefilledrectangle($result_img, 0, 0, $new_width, $new_height, $transparent);
                            // --
                            imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $new_width, $new_height, $this->image_width, $this->image_height);
                            imagegif ($result_img, $new_file_path);
                            break;
                        default:
                            $this->set_error('upload_image_resize_failed');
                            return FALSE;
                    }
                }
            }else {
                $this->set_error('upload_image_resize_failed');
                return FALSE;
            }
            // set new width and new height
            $this->image_width = $new_width;
            $this->image_height = $new_height;
            // return message
            return $this->data();
        }
    }

    /**
     * Perform the resize images
     *
     * @return	message
     */

    private function _get_file_name ($source) {
        $x = explode('/', $source);
        return end($x);
    }

    public function do_resize_image ($config = array()) {
        // inisialize
        $defaults = array(
                'source_file'   => "",
                'target_dir'    => "",
                'new_file_name' => "",
                'new_width'     => FALSE,
                'new_height'    => FALSE
        );
        if(empty($config)) {
            $this->set_error('upload_image_resize_config');
            return FALSE;
        }else {
            // assign
            foreach ($defaults as $key => $val) {
                if (isset($config[$key])) {
                    $defaults[$key] = $config[$key];
                }
            }
        }
        // $file_path, $new_file_path, $new_width = FALSE,  $new_height = FALSE
        // cek is file
        if(!is_file($defaults['source_file'])) {
            $this->set_error('upload_image_resize_no_source');
            return FALSE;
        }
        // set new file name
        if(!empty($defaults['new_file_name'])) {
            $defaults['new_file_name'] = $defaults['new_file_name'] . $this->get_extension($defaults['source_file']);
        }else {
            $defaults['new_file_name'] = $this->_get_file_name($defaults['source_file']);
        }
        // set new file
        $target_file = trim($defaults['target_dir'], '/') . '/' . $defaults['new_file_name'];
        /*
         * resize file
        */
        list($width, $height) = getimagesize($defaults['source_file']);
        // assign width
        if($defaults['new_width'] == FALSE) {
            $defaults['new_width'] = $width;
            // resize height
            if($defaults['new_height'] != FALSE) {
                $defaults['new_width'] = $defaults['new_height'] * $width / $height;
                $defaults['new_width'] = ceil($defaults['new_width']);
            } else {
                $defaults['new_height'] = $height;
            }
        }else {
            // resize height
            if($defaults['new_height'] == FALSE) {
                $defaults['new_height'] = $defaults['new_width'] * $height / $width;
                $defaults['new_height'] = ceil($defaults['new_height']);
            }
        }
        // resize images
        $file_ext = $this->get_extension($defaults['source_file']);
        switch($file_ext) {
            case '.pjpeg':
                $uploaded_img = imagecreatefromjpeg($defaults['source_file']);
                $result_img = ImageCreateTrueColor($defaults['new_width'], $defaults['new_height']);
                imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $defaults['new_width'], $defaults['new_height'], $width, $height);
                imagejpeg ($result_img, $target_file);
                break;
            case '.jpeg':
                $uploaded_img = imagecreatefromjpeg($defaults['source_file']);
                $result_img = ImageCreateTrueColor($defaults['new_width'], $defaults['new_height']);
                imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $defaults['new_width'], $defaults['new_height'], $width, $height);
                imagejpeg ($result_img, $target_file);
                break;
            case '.jpg':
                $uploaded_img = imagecreatefromjpeg($defaults['source_file']);
                $result_img = ImageCreateTrueColor($defaults['new_width'], $defaults['new_height']);
                imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $defaults['new_width'], $defaults['new_height'], $width, $height);
                imagejpeg ($result_img, $target_file);
                break;
            case '.png':
                $uploaded_img = imagecreatefrompng($defaults['source_file']);
                $result_img = ImageCreateTrueColor($defaults['new_width'], $defaults['new_height']);
                // set transparancy
                imagealphablending($result_img, false);
                imagesavealpha($result_img, true);
                $transparent = imagecolorallocatealpha($result_img, 255, 255, 255, 127);
                imagefilledrectangle($result_img, 0, 0, $defaults['new_width'], $defaults['new_height'], $transparent);
                // --
                imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $defaults['new_width'], $defaults['new_height'], $width, $height);
                imagepng ($result_img, $target_file);
                break;
            case '.gif':
                $uploaded_img = imagecreatefromgif($defaults['source_file']);
                $result_img = ImageCreateTrueColor($defaults['new_width'], $defaults['new_height']);
                // set transparancy
                imagealphablending($result_img, false);
                imagesavealpha($result_img, true);
                $transparent = imagecolorallocatealpha($result_img, 255, 255, 255, 127);
                imagefilledrectangle($result_img, 0, 0, $defaults['new_width'], $defaults['new_height'], $transparent);
                // --
                imagecopyresampled($result_img, $uploaded_img, 0, 0, 0, 0, $defaults['new_width'], $defaults['new_height'], $width, $height);
                imagegif ($result_img, $target_file);
                break;
            default:
                $this->set_error('upload_image_resize_failed');
                return FALSE;
        }
        // return message
        $this->data_resize = array(
                'source_file'   => $defaults['source_file'],
                'target_dir'    => $defaults['target_dir'],
                'new_file_name' => $defaults['new_file_name'],
                'new_width'     => $defaults['new_width'],
                'new_height'    => $defaults['new_height'],
                'image_width'       => $width,
                'image_height'      => $height
        );
        // true
        return TRUE;
    }

    // data resize message

    public function data_resize() {
        return $this->data_resize;
    }

    // --------------------------------------------------------------------
    // @OVERRIDE
    /**
     * Validate Upload Path
     *
     * Verifies that it is a valid upload path with proper permissions.
     *
     * @return	bool
     */
    public function validate_upload_path() {
        if ($this->upload_path == '') {
            $this->set_error('upload_no_filepath');
            return FALSE;
        }

        if (function_exists('realpath') AND @realpath($this->upload_path) !== FALSE) {
            $this->upload_path = str_replace("\\", "/", realpath($this->upload_path));
        }

        if ( ! @is_dir($this->upload_path)) {
            // make dir
            $this->make_dir($this->upload_path);
            // cek result
            if(! @is_dir($this->upload_path)) {
                $this->set_error('upload_no_filepath');
                return FALSE;
            }
        }

        if ( ! is_really_writable($this->upload_path)) {
            $this->set_error('upload_not_writable');
            return FALSE;
        }

        $this->upload_path = preg_replace("/(.+?)\/*$/", "\\1/",  $this->upload_path);
        return TRUE;
    }

    /**
     *
     * @param <type> $dir  direktori manajemen
     */

    // make dir
    public function make_dir($dir) {
        $dir = explode('/', $dir);
        $tmp = "";
        foreach($dir as $rec) {
            if(!empty($rec)) {
                $dest = $rec . '/';
                $tmp .= $dest;
                if(!is_dir($tmp)) {
                    mkdir($tmp, DIR_WRITE_MODE);
                }
            }
        }
    }

    // remove dir
    public function remove_dir($dir) {
        if(is_dir($dir)) {
            $folder = opendir($dir);
            while($file = readdir($folder)) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if(is_dir($dir.'/'.$file)) {
                    // remove dir child
                    $this->remove_dir($dir.'/'.$file);
                } else {
                    // remove file
                    unlink($dir.'/'.$file);
                }
            }
            closedir($folder);
            // remove dir
            rmdir($dir);
        }
    }

    // remove file
    public function remove_file($file) {
        if(is_file($file)) {
            // remove file
            unlink($file);
        }
    }
}