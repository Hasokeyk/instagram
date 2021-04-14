<?php
    
    namespace instagram;
    
    class instagram_medias extends instagram_request{
        
        public $username;
        public $password;
        public $functions;
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
        
        public function get_post_likes($post_id = null){
            
            if($post_id != null){
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/likers/';
                
                $json = $this->request($url);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
        public function get_comment_post($post_id = null){
            
            if($post_id != null){
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/comments/';
                
                $json = $this->request($url);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
        public function get_permalink_by_shortcode($post_id = null){
            
            if($post_id != null){
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/permalink/?share_to_app=share_sheet';
                
                $json = $this->request($url);
                $json = json_decode($json['body']);
                
                return $json;
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
        
        public function like($post_id){
            
            if($post_id != null){
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/like/';
                
                $post_data = [
                    'container_module' => 'feed_contextual_profile',
                    'delivery_class'   => 'organic',
                    'radio_type'       => 'wifi-none',
                    'feed_position'    => '0',
                    'media_id'         => $post_id,
                    '_csrftoken'       => $this->get_csrftoken(),
                    '_uuid'            => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $json = $this->request($url, 'POST', $post_data);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
        public function unlike($post_id){
            
            if($post_id != null){
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/unlike/';
                
                $post_data = [
                    'container_module' => 'feed_contextual_profile',
                    'delivery_class'   => 'organic',
                    'radio_type'       => 'wifi-none',
                    'feed_position'    => '0',
                    'media_id'         => $post_id,
                    '_csrftoken'       => $this->get_csrftoken(),
                    '_uuid'            => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $json = $this->request($url, 'POST', $post_data);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
        public function save($post_id){
            
            if($post_id != null){
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/save/';
                
                $post_data = [
                    'module_name' => 'feed_timeline',
                    'radio_type'  => 'wifi-none',
                    '_csrftoken'  => $this->get_csrftoken(),
                    '_uuid'       => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $json = $this->request($url, 'POST', $post_data);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
        public function unsave($post_id){
            
            if($post_id != null){
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/unsave/';
                
                $post_data = [
                    'module_name' => 'feed_timeline',
                    'radio_type'  => 'wifi-none',
                    '_csrftoken'  => $this->get_csrftoken(),
                    '_uuid'       => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $json = $this->request($url, 'POST', $post_data);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
        public function send_comment_post($post_id, $comment = 'hi'){
            
            if($post_id != null){
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/comment/';
                
                $post_data = [
                    'comment_text'      => $comment,
                    'container_module'  => 'comments_v2_feed_contextual_profile',
                    'delivery_class'    => 'organic',
                    'idempotence_token' => '455f2f7e-7abf-4236-b527-8f422f84bab0',
                    '_csrftoken'        => $this->get_csrftoken(),
                    '_uuid'             => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $json = $this->request($url, 'POST', $post_data);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
        public function delete_comment_post($post_id = null, $comment_id = null, $auto_find_comment_id = false){
            
            if($post_id != null){
                
                if($auto_find_comment_id == true){
                    $get_comment_posts = $this->get_comment_post($post_id);
                    $me_user_id        = $this->functions->user->get_user_id();
                    $comment_id        = 0;
                    foreach($get_comment_posts->comments as $comment){
                        if($me_user_id == $comment->user_id){
                            $comment_id = $comment->pk;
                            break;
                        }
                    }
                    if($comment_id == 0){
                        return false;
                    }
                }
                
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/comment/bulk_delete/';
                
                $post_data = [
                    'comment_ids_to_delete' => $comment_id,
                    'container_module'      => 'comments_v2_feed_contextual_profile',
                    '_csrftoken'            => $this->get_csrftoken(),
                    '_uuid'                 => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $json = $this->request($url, 'POST', $post_data);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
        public function share_media_inbox($post_id = null, $username = null){
            
            if($post_id != null and $username != null){
                
                $get_thread_id = $this->functions->user->get_create_inbox_thread($username);
                
                $url = 'https://i.instagram.com/api/v1/direct_v2/threads/broadcast/media_share/?media_type=video';
                
                $post_data = [
                    'action'           => 'send_item',
                    'is_shh_mode'      => '0',
                    'send_attribution' => 'comments_v2_feed_contextual_profile',
                    'thread_ids'       => '['.$get_thread_id->thread->thread_id.']',
                    'media_id'         => $post_id,
                    '_csrftoken'       => $this->get_csrftoken(),
                    '_uuid'            => $this->get_guid(),
                ];
                
                $json = $this->request($url, 'POST', $post_data);
                $json = json_decode($json['body']);
                
                return $json;
            }
            
            return false;
            
        }
        
    }