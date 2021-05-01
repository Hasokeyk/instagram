<?php
    
    namespace instagram;
    
    class instagram_register extends instagram_request{
        
        public $username;
        public $password;
        public $functions;
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
        
        public function username_check($username = null){
            
            $username = $username??$this->username;
            
            $url       = 'https://i.instagram.com/api/v1/users/check_username/';
            $post_data = [
                'username' => $username,
                '_uuid'    => $this->get_guid(),
            ];
            $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
            $json      = $this->request($url, 'POST', $post_data);
            if($json['status'] == 'ok'){
                $json = json_decode($json['body']);
            }
            return $json;
            
        }
        
        public function get_steps(){
            
            $url       = 'https://i.instagram.com/api/v1/dynamic_onboarding/get_steps/';
            $post_data = [
                'is_secondary_account_creation' => 'true',
                'fb_connected'                  => 'false',
                'seen_steps'                    => "[]",
                'progress_state'                => "prefetch",
                'fb_installed'                  => "false",
                'is_ci'                         => "false",
                'network_type'                  => "WIFI-UNKNOWN",
                'waterfall_id'                  => "97d01ad3-6555-49ac-b7f5-69da7f241367",
                'tos_accepted'                  => "false",
                'phone_id'                      => $this->get_phone_id(),
                '_uuid'                         => $this->get_guid(),
                'guid'                          => $this->get_guid(),
                '_csrftoken'                    => $this->get_csrftoken(),
                'android_id'                    => $this->get_device_id(),
            ];
            $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
            $json      = $this->request($url, 'POST', $post_data);
            if($json['status'] == 'ok'){
                $json = json_decode($json['body']);
            }
            return $json;
            
        }
        
        public function register($username = null, $password = null){
            
            $username = $username??$this->username;
            $password = $password??$this->password;
            
            $url       = 'https://i.instagram.com/api/v1/dynamic_onboarding/get_steps/';
            $post_data = [
                'is_secondary_account_creation' => 'true',
                'fb_connected'                  => 'false',
                'seen_steps'                    => "[]",
                'progress_state'                => "prefetch",
                'fb_installed'                  => "false",
                'is_ci'                         => "false",
                'network_type'                  => "WIFI-UNKNOWN",
                'waterfall_id'                  => "97d01ad3-6555-49ac-b7f5-69da7f241367",
                'tos_accepted'                  => "false",
                'phone_id'                      => $this->get_phone_id(),
                '_uuid'                         => $this->get_guid(),
                'guid'                          => $this->get_guid(),
                '_csrftoken'                    => $this->get_csrftoken(),
                'android_id'                    => $this->get_device_id(),
            ];
            $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
            $json      = $this->request($url, 'POST', $post_data);
            if($json['status'] == 'ok'){
                $json = json_decode($json['body']);
            }
            return $json;
            
        }
        
    }