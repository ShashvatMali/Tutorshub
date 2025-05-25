<?php
/**
 *
 * Class 'Tuturn_file_permission' file upload with permissions
 *
 * @package     Tuturn
 * @subpackage  Tuturn/includes
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
if (!class_exists('Tuturn_file_permission')){
    class Tuturn_file_permission{
  
        private static $instance = null;
        private static $encrpytion_salt  = '^^tbkey^^';
        public function __construct(){
           
        }

        /**
         * Returns the *Singleton* instance of this class.
         *
         * @return
         * @throws error
         * @author Amentotech <theamentotech@gmail.com>
         */
        public static function getInstance(){
            if (self::$instance==null){
                self::$instance = new Tuturn_file_permission();
            }
            return self::$instance;
        }

        /**
		 * uplaod attachments
		 *
		 * @since    1.0.0
		*/
		public static function uploadAttachment($params=array()){			
			$upload 			= wp_upload_dir();
			$upload_dir 		= $upload['path'].'/';
			$attachments 		= $json  = array();          
			$basedir 			= $upload['basedir'] . "/tuturn-temp/";
			$baseurl 			= $upload['baseurl'] . "/tuturn-temp/"; 
            
			$upload_dir 		= $basedir;
			$upload_url 		= $baseurl;
			$registered_sizes   = array();

			if( $params['sizes']){
				$registered_sizes 	= wp_get_registered_image_subsizes();
			}
			wp_mkdir_p($upload_dir);

			if(!empty($params['files'])){
                $file               = $params['files'];
                $file_info 		    = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], false);
                
                
                $attachmentType     = '';
                $name 				= preg_replace("/[^A-Z0-9._-]/i", "_", $file["name"]);
                //file type check
                $filetype 			= wp_check_filetype($file['name']);
                $not_allowed_types	= array('php','javascript','js','exe','text/javascript','html');
                $file_ext			= !empty($filetype['ext']) ? $filetype['ext'] : '';
                

                if(empty($file_ext) || in_array($file_ext,$not_allowed_types)){
                    $json['type']           = 'error';
                    $json['message_desc']   = esc_html__('These file types are not allowed!', 'tuturn');
                    return $json;
                    exit;
                }

                $i = 0;
                $parts = pathinfo($name);
                while (file_exists($upload_dir . $name)) {
                    $i++;
                    $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
                }
                
                if(preg_match('/image\/*/', $file['type'])){
                    $attachmentType = 'images';
                }elseif(preg_match('/video\/*/', $file['type'])){
                    $attachmentType = 'video';
                }elseif(preg_match('/audio\/*/', $file['type'])){
                    $attachmentType = 'audio';
                }elseif(preg_match('/pdf\/*/', $file['type']) 
                    || preg_match('/document\/*/', $file['type'])
                    || preg_match('/zip\/*/', $file['type'])
                    || preg_match('/powerpoint\/*/', $file['type'])
                    || preg_match('/text\/*/', $file['type'])
                    || preg_match('/vnd.ms-excel\/*/', $file['type'])
                    || preg_match('/spreadsheet\/*/', $file['type'])
                    ){
                    $attachmentType = 'file';
                }

                $size       	= $file['size'];
                $file_size  	= size_format($size, 2);

                //move file
                $newFile        = $upload_dir .$name;
                $is_moved       = move_uploaded_file($file["tmp_name"], $newFile);

                if($is_moved){
                    $filename   = basename($newFile);
                    $file       = $upload_url.sanitize_file_name($filename);
                    $sizes      = array();
                    
                    if( $attachmentType == 'images' && ! empty( $params['sizes']) ){
                        $image  = wp_get_image_editor( $newFile );
                        if (!is_wp_error( $image ) ) {
                            foreach($params['sizes'] as $size){ 
                                $newSize            = $image->make_subsize( $registered_sizes[$size] );
                                if(!is_wp_error( $newSize )){
                                    $generatedIamge = $upload_url . $newSize['file'];
                                    $sizes[$size] 	= $generatedIamge;
                                }else{
                                    $sizes[$size] 	= $file;
                                }
                            }
                        }
                    }

                    $attachments[] = array(
                        'file' 					=> $file,
                        'fileName'				=> sanitize_file_name($filename),
                        'sizes' 				=> $sizes,
                        'fileSize' 				=> esc_html($file_size),
                        'fileType' 				=> esc_html($filetype['ext']),
                        'attachmentType'		=> $attachmentType
                    );
                }
			}
            
			$json['type'] 			= 'success';
			$json['attachments'] 	= $attachments;
			return $json;
		}

        /**
         * Upload file in temp folder
         *
         * @return
         * @throws error
         * @author Amentotech <theamentotech@gmail.com>
         */
        public static function uploadFile($submitted_file){
            $response       = array();
            $upload         = wp_upload_dir();
            $upload_dir     = $upload['basedir'];
            $upload_dir     = $upload_dir . '/tuturn-temp/';
            $file_info      = wp_check_filetype_and_ext($submitted_file['tmp_name'], $submitted_file['name'], false);
            $ext_verify 	= empty($file_info['ext']) ? '' : $file_info['ext'];
            $type_verify 	= empty($file_info['type']) ? '' : $file_info['type'];

            if (!$ext_verify || !$type_verify) {
                $response['message'] = esc_html__('These file types are not allowed', 'tuturn');
                $response['type']    = 'error';
                return $response;
            }

            //create directory if not exists
            if (!is_dir($upload_dir)) {
                wp_mkdir_p($upload_dir);
            }
            
            $name = preg_replace("/[^A-Z0-9._-]/i", "_", $submitted_file["name"]);
            $i = 0;
            $parts = pathinfo($name);
            while (file_exists($upload_dir . $name)) {
                $i++;
                $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
            }      
            
            $filetype   = wp_check_filetype($name);
            $file_ext   = !empty($filetype['ext']) ? $filetype['ext'] : '';

            if(preg_match('/image\/*/', $file['type'])){
                $attachmentType = 'images';
            }elseif(preg_match('/video\/*/', $file['type'])){
                $attachmentType = 'video';
            }elseif(preg_match('/audio\/*/', $file['type'])){
                $attachmentType = 'audio';
            }elseif(preg_match('/pdf\/*/', $file['type']) 
                || preg_match('/document\/*/', $file['type'])
                || preg_match('/zip\/*/', $file['type'])
                || preg_match('/powerpoint\/*/', $file['type'])
                || preg_match('/text\/*/', $file['type'])
                || preg_match('/vnd.ms-excel\/*/', $file['type'])
                || preg_match('/spreadsheet\/*/', $file['type'])
                ){
                $attachmentType = 'file';
            }
            //move files
            $is_moved = move_uploaded_file($submitted_file["tmp_name"], $upload_dir . '/' . $name);

            if ($is_moved) {
                $size       = $submitted_file['size'];
                $file_size  = size_format($size, 2);
                $response['type']       = 'success';
                $response['message']    = esc_html__('File uploaded', 'tuturn');
                $url                    = $upload['baseurl'] . '/tuturn-temp/' . $name;
                $response['thumbnail']  = $upload['baseurl'] . '/tuturn-temp/' . $name;
                $response['name']       = $name;
                $response['url']        = $url;
                $response['size']       = $file_size;
            } else {
                $response['title']      = esc_html__('Error', 'tuturn');
                $response['message']    = esc_html__('Some errors occurred, please try again later', 'tuturn');
                $response['type']       = 'error';
            }
            return $response;
        }

        /**
         * Get encrypt file
         *
         * @return
         * @throws error
         * @author Amentotech <theamentotech@gmail.com>
         */
        public static function getEncryptFile($file, $post_id, $is_upload=false, $encrypt_file=true){
            $result     = array();
            $post_type	= get_post_type($post_id);
            $i          = time();
			
            if(($post_type == 'product' || $post_type == 'user-verification') && !empty($encrypt_file)) {
                $file_detail            = pathinfo($file);
                $extension 			    = $file_detail['extension'];
                $filename 			    = $file_detail['filename'];

                if($is_upload) {
                    $filename           = $file_detail['filename'].'-'.$i; 
                }
                $reverse_file_name      = strrev($filename);
                $new_file_name          = strrev(base64_encode($reverse_file_name.self::$encrpytion_salt.$post_id));
                $new_file_name          = $new_file_name. '.' . $extension;
                $result['url']          = $file_detail['dirname'].'/'.$new_file_name;
                $result['name']         = $new_file_name;
                $result['encrypt_file'] = $encrypt_file;
                return $result;
            } else {
                $file_detail        = pathinfo($file);
                $extension 			= $file_detail['extension'];
                $filename 			= $file_detail['filename'];
                
                if($is_upload) {
                    $new_file_name 	= $filename .'-'.$i.'.' . $extension;
                } else {
                    $new_file_name 	= $filename . '.' . $extension;
                }
                $result['url']      = $file_detail['dirname'].'/'.$new_file_name;
                $result['name']     = $new_file_name;                
                $result['encrypt_file']         = $encrypt_file;
                return $result;
            }
        } 

        public static function downloadFile($attachmentId){
            $post_id    = !empty($attachmentId) ? get_post_field('post_parent',$attachmentId,true) : '';
            $post_id    = !empty($post_id) ? $post_id : '';
            
            $json = array();
            $attachmentId = !empty($attachmentId) ? intval($attachmentId) : '';
          
            if (!empty($attachmentId)) {
                $post_data = get_post_meta($attachmentId);
                $destinationfile = false;
                if (!empty($post_data)) {

                    $filename        = $post_data['_wp_attached_file'][0];
                    $uploadspath     = wp_upload_dir();
                    $sourcefile      = $uploadspath['basedir'].'/'.$filename;
                    if(!file_exists($sourcefile)) {
                        $json['type']         = 'error';
                        $json['message']      = esc_html__('Oops!', 'tuturn');
                        $json['message_desc'] = esc_html__('Oh no! Looks like like there were no attachments', 'tuturn');
                        return $json;
                    }
                    $param = array();
                    $param['url']               = $filename;
                    $param['attachment_id']     = $attachmentId;
                    $file_detail     = self::getDecrpytFile($param);
                    $file            = pathinfo($file_detail['filename']);
                    $newfilename     = $file['filename'].'-'.time().'.'.$file['extension'];
                    $thisdir         = "/download";
                    $folderPath      = $uploadspath['basedir'].$thisdir."/"; //  directory with absolute path
                    $serverfilepath  = $uploadspath['baseurl'].$thisdir."/"; //  directory with server path
                    
                    if(!is_dir($folderPath)){
                        mkdir($folderPath, 0777, true);
                    }    
                    $destinationfile = $folderPath.$newfilename;
                    copy($sourcefile,$destinationfile);  
                    set_transient('temp_download_file_'.time(), serialize($destinationfile),5);
                    $destinationfile = $serverfilepath.$newfilename;
                
                } else {
                    $json['type'] = 'error';
                    $json['message'] = esc_html__('Oops!', 'tuturn');
                    $json['message_desc'] = esc_html__('Oh no! Looks like there were no attachments', 'tuturn');
                    return $json;
                }
                $json['type']           = 'success';
                $json['attachment']     = strrev(base64_encode($destinationfile));
                $json['message']        = esc_html__('WooHoo!', 'tuturn');
                $json['message_desc']   = esc_html__('Your download was successful', 'tuturn');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Oops!', 'tuturn');
                $json['message_desc'] = esc_html__('Looks like there was an error. Can you please try again?', 'tuturn');
                return $json;
            }
        }

        public static function downloadZipFile($post_id,$attachments){
            $json           = array();
            $post_type      = get_post_type($post_id);
            if (!empty($post_id) && $post_type === 'user-verification' && !empty($attachments)) {
                $post_data = get_post_meta($post_id, $meta, true);
                if (!empty($attachments)) {                    
                    $zip = new ZipArchive();
                    $uploadspath = wp_upload_dir();
                    $folderRalativePath = $uploadspath['baseurl'] . "/download";
                    $folderAbsolutePath = $uploadspath['basedir'] . "/download";
                    wp_mkdir_p($folderAbsolutePath);
                    $filename = round(microtime(true)) . '.zip';
                    $zip_name = $folderAbsolutePath . '/' . $filename;
                    $zip->open($zip_name, ZipArchive::CREATE);
                    $download_url = $folderRalativePath . '/' . $filename;
                    $param= array();
                    foreach ($attachments as $file) {
                        if(is_array($file)){
                            $response                   = wp_remote_get($file['url']);
                            $filedata                   = wp_remote_retrieve_body($response);
                            $param['url']               = $file['url'];
                            $param['attachment_id']     = $file['attachment_id'];
                            $file_detail                = self::getDecrpytFile($param);
                            $zip->addFromString($file_detail['filename'], $filedata);
                        }
                    }
                    $zip->close();
                    set_transient('temp_download_file_'.time(), serialize($download_url),5);
                } else {
                    $json['type'] = 'error';
                    $json['message'] = esc_html__('Oops!', 'tuturn');
                    $json['message_desc'] = esc_html__('Oh no! Looks like there were no attachments', 'tuturn');
                    return $json;
                }

                $json['type']           = 'success';
                $json['attachment']     = strrev(base64_encode($download_url));
                $json['message']        = esc_html__('WooHoo!', 'tuturn');
                $json['message_desc']   = esc_html__('Your download was successful', 'tuturn');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Oops!', 'tuturn');
                $json['message_desc'] = esc_html__('Looks like there was an error. Can you please try again?', 'tuturn');
                return $json;
            }
        }

        public static function getDecrpytFile($file){
            $result              = array();
            $file_detail         = pathinfo($file['url']);
            $attachment_id       = $file['attachment_id'];
            $extension 			 = $file_detail['extension']; 
            if(!empty($attachment_id)) {
                $parent_post_id = wp_get_post_parent_id($attachment_id);
                $post_type      = get_post_type($parent_post_id);
                $is_encrypted   = get_post_meta($attachment_id, 'is_encrypted', true);
                
                if($post_type == 'projects' || $post_type == 'user-verification' ) {
                    
                    if($is_encrypted) {
                        $file 	       = explode('^^',base64_decode(strrev($file_detail['filename'])));
                        $filename      = strrev($file[0]).'.'.$extension; 
                    } else {
                        $filename      = $file_detail['filename'].'.'.$extension; 
                    }
                }else {
                    $filename          = $file_detail['filename'].'.'.$extension;
                }
                $result['dirname']   = $file_detail['dirname']; 
                $result['filename']  = $filename;
            }
                      
            return $result;
            
        }

    }
}
