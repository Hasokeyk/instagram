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
                
                $post_hashquery = $this->functions->request->get_post_queryhash();
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
        
    }