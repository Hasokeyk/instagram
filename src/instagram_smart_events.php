<?php
    
    
    namespace instagram;
    
    
    class instagram_smart_events extends instagram_request{
        
        public $username;
        public $password;
        public $functions;
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
        
        public function get_fake_following_profile($username = null){
            
            $username = $username??$this->username;
            $user_id  = $this->functions->user->get_user_id($username);
            $url      = 'https://i.instagram.com/api/v1/friendships/'.$user_id.'/following/?includes_hashtags=false&search_surface=follow_list_page&order=date_followed_earliest&query=&enable_groups=true&rank_token=650f704c-8711-47a8-a7f5-a7c90d8e23d8';
            $json     = $this->request($url);
            $json     = json_decode($json['body']);
            
            $fake_following = [];
            foreach($json->users as $user){
                if($user->has_anonymous_profile_picture == true){
                    $fake_following[] = $user;
                }
            }
            
            return $fake_following;
            
        }
        
        public function get_fake_followers_profile($username = null, $page = 1){
            
            $username   = $username??$this->username;
            $user_id    = $this->functions->user->get_user_id($username);
            $url        = 'https://i.instagram.com/api/v1/friendships/'.$user_id.'/followers/?includes_hashtags=false&search_surface=follow_list_page&order=date_followed_lates&query=&enable_groups=true&rank_token=650f704c-8711-47a8-a7f5-a7c90d8e23d8';
            $json       = $this->request($url);
            $main_users = json_decode($json['body']);
            $max_id     = $main_users->next_max_id;
            
            if($page > 1){
                for($i = 0; $i < $page; $i++){
                    $url                 = 'https://i.instagram.com/api/v1/friendships/'.$user_id.'/followers/?search_surface=follow_list_page&max_id='.$max_id.'&query=&enable_groups=true&rank_token=83b852da-cc4a-4eff-8877-2f436ebf223c';
                    $json                = $this->request($url);
                    $users               = json_decode($json['body']);
                    foreach($users->users as $user){
                        $main_users->users[] = $user;
                    }
                    $max_id              = $users->next_max_id;
                }
                sleep(1);
            }
            
            $fake_following = [];
            foreach($main_users->users as $user){
                if($user->has_anonymous_profile_picture == true){
                    $fake_following[] = $user;
                }
            }
            
            return $fake_following;
            
        }
        
    }