<?php
    
    namespace instagram;
    
    class instagram_login extends instagram_request{
        
        public $username;
        public $password;
        public $functions;
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
        
        public function login2($username = null, $password = null){
            
            $username       = $username??$this->username;
            $password       = $password??$this->password;
            $password       = $this->encrypt($password);
            $this->username = $username;
            
            $url = 'https://i.instagram.com/api/v1/accounts/login/';
            
            $post_data = [
                'phone_id'            => "832f3947-2366-42c7-a49e-88136c36f7ad",
                'enc_password'        => $password,
                '_csrftoken'          => 'YBck6oCPqG3e2YtLRxeiebBfVQawQm04',
                'username'            => $username,
                'adid'                => "f5904e04-349a-48ca-8516-8555ae99660c",
                'guid'                => "f1c270c3-8663-40ef-8612-3dc8853b3459",
                'device_id'           => "android-daa21d4b02905ea0",
                'google_tokens'       => '[]',
                'login_attempt_count' => '0',
            ];
            $post_data = ['signed_body' => (string) 'SIGNATURE.{"username":"hayatikodla"}'];
            
            $header = [
                'User-Agent' => 'Instagram 177.0.0.30.119 Android (22/5.1.1; 160dpi; 540x960; Google/google; google Pixel 2; x86; qcom; tr_TR; 276028050)',
            ];
            
            $cookie = [
                'mid'       => 'YB2r4AABAAERcl5ESNxLjr_tt4Q5',
                'csrftoken' => 'YBck6oCPqG3e2YtLRxeiebBfVQawQm04',
            ];
            
            /*
            $client = new \GuzzleHttp\Client([
                'verify' => false,
                'headers' => $header
            ]);
    
            $result = $client->post($url,
                [
                    'form_params' => $post_data
                ]
            );
            */
            
            $result = $this->request($url, 'POST', $post_data, $header);
            print_r($result);
            
        }
        
        public function login($username = null, $password = null){
            
            $username       = $username??$this->username;
            $password       = $password??$this->password;
            $this->username = $username;
            
            $cookie = $this->cache($username.'-sessionid');
            if($cookie == false){
                
                $url       = 'https://i.instagram.com/api/v1/accounts/login/';
                $password  = $this->encrypt($password);
                $post_data = [
                    'jazoest'             => '22250',
                    'country_codes'       => '[{"country_code":"90","source":["sim","network","default","sim"]}]',
                    'phone_id'            => $this->get_phone_id(),
                    'enc_password'        => $password,
                    '_csrftoken'          => $this->get_csrftoken(),
                    'username'            => $username,
                    'adid'                => $this->get_adid(),
                    'guid'                => $this->get_guid(),
                    'device_id'           => $this->get_device_id(),
                    'google_tokens'       => '[]',
                    'login_attempt_count' => '0',
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $cookie = [
                    'mid'       => 'YB2r4AABAAERcl5ESNxLjr_tt4Q5',
                    'csrftoken' => $this->get_csrftoken(),
                ];
                
                $result = $this->request($url, 'POST', $post_data, null, $cookie);
                return $this->login_check($result);
                
            }
            else{
                return true;
            }
            
        }
        
        private function login_check($json = null){
            if($json != null){
                $json_body = json_decode($json['body']);
                if($json_body->status == 'ok'){
                    $username = $json_body->logged_in_user->username;
                    $cookie   = $this->cache($username.'-sessionid');
                    if($cookie == false){
                        foreach($json['headers']['Set-Cookie'] as $cookie){
                            if($this->start_with($cookie, 'sessionid')){
                                preg_match('|sessionid=(.*?);|is', $cookie, $session_id);
                                $this->cache($username.'-sessionid', [$session_id[1]]);
                                break;
                            }
                        }
                    }
                    return true;
                }
            }
            return false;
        }
        
        public function login_control($username = null){
            
            $this->username = $username??$this->username;
            
            try{
                
                $url         = 'https://i.instagram.com/api/v1/direct_v2/get_presence/';
                $result      = $this->request($url);
                $result_body = json_decode($result['body']);
                if($result_body->status == 'ok'){
                    return true;
                }
                
                return false;
            }
            catch(\Exception $err){
                return false;
            }
            
        }
        
        private function encrypt($password){
            
            $keys          = $this->get_sync();
            $public_key    = $keys->pub_key;
            $public_key_id = $keys->pub_key_id;
            
            $key  = openssl_random_pseudo_bytes(32);
            $iv   = openssl_random_pseudo_bytes(12);
            $time = time();
            
            openssl_public_encrypt($key, $encryptedAesKey, base64_decode($public_key));
            $encrypted = openssl_encrypt($password, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, strval($time));
            
            $payload = base64_encode("\x01" | pack('n', intval($public_key_id)).$iv.pack('s', strlen($encryptedAesKey)).$encryptedAesKey.$tag.$encrypted);
            
            return sprintf('#PWD_INSTAGRAM:4:%s:%s', $time, ($payload));
            
        }
        
        public function get_sync(){
            
            $keys = $this->cache('app_key');
            if($keys == false){
                
                $url       = 'https://i.instagram.com/api/v1/qe/sync/';
                $post_data = [
                    '_csrftoken'              => $this->get_csrftoken(),
                    'id'                      => 'F2CD7326-EA40-44F8-9FC3-71A0A5E1F55B',
                    'server_config_retrieval' => '1',
                    'experiments'             => "ig_growth_android_profile_pic_prefill_with_fb_pic_2,ig_account_identity_logged_out_signals_global_holdout_universe,ig_android_caption_typeahead_fix_on_o_universe,ig_android_retry_create_account_universe,ig_android_gmail_oauth_in_reg,ig_android_quickcapture_keep_screen_on,ig_android_smartlock_hints_universe,ig_android_reg_modularization_universe,ig_android_login_identifier_fuzzy_match,ig_android_passwordless_account_password_creation_universe,ig_android_security_intent_switchoff,ig_android_sim_info_upload,ig_android_device_verification_fb_signup,ig_android_reg_nux_headers_cleanup_universe,ig_android_direct_main_tab_universe_v2,ig_android_nux_add_email_device,ig_android_fb_account_linking_sampling_freq_universe,ig_android_device_info_foreground_reporting,ig_android_suma_landing_page,ig_android_device_verification_separate_endpoint,ig_android_direct_add_direct_to_android_native_photo_share_sheet,ig_android_device_detection_info_upload,ig_android_device_based_country_verification",
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $headers = [
                    'User-Agent'       => $this->user_agent,
                    'DEBUG-IG-USER-ID' => 0,
                    'Host'             => 'i.instagram.com',
                ];
                $res     = $this->request($url, 'POST', $post_data, $headers, null, false);
                
                $result = (object) [
                    'pub_key_id' => $res['headers']['ig-set-password-encryption-key-id'][0],
                    'pub_key'    => $res['headers']['ig-set-password-encryption-pub-key'][0],
                ];
                $this->cache('app-key', $result);
                //$this->get_launcher_sync();
            }
            else{
                $result = $keys;
            }
            return $result;
        }
        
        public function get_launcher_sync(){
            
            $keys = $this->cache('app_key');
            if($keys == false){
                
                $url       = 'https://i.instagram.com/api/v1/launcher/sync/';
                $post_data = [
                    '_csrftoken'              => 'khugUa357Qq939C5NQ2fReWGZXUraEzZ',
                    'id'                      => 'F2CD7326-EA40-44F8-9FC3-71A0A5E1F55B',
                    'server_config_retrieval' => '1',
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                
                $headers = [
                    'User-Agent'       => $this->user_agent,
                    'DEBUG-IG-USER-ID' => 0,
                    'Host'             => 'i.instagram.com',
                ];
                $res     = $this->request($url, 'POST', $post_data, $headers, null, false);
                
                $result = (object) [
                    'pub_key_id' => $res['headers']['ig-set-password-encryption-key-id'][0],
                    'pub_key'    => $res['headers']['ig-set-password-encryption-pub-key'][0],
                ];
                $this->cache('launcheR_sync', $result);
                
            }
            else{
                $result = $keys;
            }
            return $result;
        }
        
        public function logout($username = null){
            
            $username = $username??$this->username;
            
            $url         = 'https://i.instagram.com/api/v1/accounts/logout/';
            $post_data   = [
                'phone_id'          => $this->get_phone_id(),
                '_csrftoken'        => $this->get_csrftoken(),
                'guid'              => $this->get_guid(),
                'device_id'         => $this->get_device_id(),
                '_uuid'             => $this->get_guid(),
                'one_tap_app_login' => 'true',
            ];
            $result      = $this->request($url, 'POST', $post_data);
            $result_body = json_decode($result['body']);
            if($result_body->status == 'ok'){
                if(file_exists($this->cache_path.$username)){
                    $this->del_tree($this->cache_path.$username, '');
                }
                return true;
            }
            
            return false;
        }
        
        public static function del_tree($dir){
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach($files as $file){
                (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        
    }