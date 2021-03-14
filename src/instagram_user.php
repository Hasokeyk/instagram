<?php
    
    namespace instagram;
    
    class instagram_user extends instagram_request{
        
        public $username;
        public $password;
        public $functions;
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
        
        public function get_user_id($username = null){
            
            $username = $username??$this->username;
            if($username != null){
                
                $url = 'https://www.instagram.com/web/search/topsearch/?query='.$username;
                
                $json = $this->request($url);
                $json = json_decode($json['body']);
                
                $user_id = 0;
                foreach($json->users as $user){
                    if($username == $user->user->username){
                        $user_id = $user->user->pk;
                    }
                }
                
                return $user_id;
            }
            
            return false;
            
        }
        
        public function get_user_posts($username = null){
            
            $cache = $this->cache('posts');
            if($cache == false){
                
                $post_hashquery = $this->get_post_queryhash();
                $user_id        = $this->get_user_id();
                $url            = 'https://www.instagram.com/graphql/query/?query_hash='.$post_hashquery.'&variables={"id":"'.$user_id.'","first":50}';
                $json           = $this->request($url);
                $json           = json_decode($json['body'])->data->user;
                
                $this->cache('posts', $json);
                
                $result = $json;
            }
            else{
                $result = $cache;
            }
            
            return $result;
        }
        
        public function change_profil_pic($image_path = null){
            
            $upload_id         = $this->functions->upload->get_upload_id();
            $upload_session_id = $this->functions->upload->get_upload_session_id($upload_id);
            $url               = 'https://i.instagram.com/rupload_igphoto/'.$upload_session_id;
            
            $file      = file_get_contents($image_path);
            $file_size = strlen($file);
            
            $header = [
                "Content-Type"               => "application/octet-stream",
                "X-Entity-Type"              => "image/jpeg",
                "X-Entity-Name"              => $upload_session_id,
                "Offset"                     => "0",
                "X-Entity-Length"            => $file_size,
                "Cookie"                     => $this->create_cookie(),
                "X-Instagram-Rupload-Params" => $this->functions->upload->rupload_params($upload_id),
            ];
            
            $json = $this->request($url, 'UPLOAD', ['body' => $file], $header);
            $json = json_decode($json['body']);
            if($json->status == 'ok'){
                $result = $this->_change_profil_pic($upload_id);
                if($result->status == 'ok'){
                    return true;
                }
            }
            
            return false;
        }
        
        protected function _change_profil_pic($upload_id){
            
            $url       = 'https://i.instagram.com/api/v1/accounts/change_profile_picture/';
            $post_data = [
                '_csrftoken'     => $this->get_csrftoken(),
                '_uuid'          => $this->get_guid(),
                'use_fbuploader' => 'true',
                'upload_id'      => $upload_id,
            ];
            $json = $this->request($url, 'POST', $post_data);
            return json_decode($json['body']);
            
        }
        
    }