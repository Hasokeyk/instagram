<?php

    namespace Hasokeyk\Instagram;

    class InstagramLogin extends InstagramRequest{

        public $username;
        public $password;
        public $functions;

        function __construct($username, $password, $functions){
            parent::__construct($username, $password, $functions);

            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }

        public function login($username = null, $password = null){

            $username = $username ?? $this->username;
            $password = $password ?? $this->password;

            $this->username = $username;
            $this->password = $password;

            $cache = $this->cache('session_id');
            if($cache === false){

                $url       = $this->intagram_api_url.'/api/v1/accounts/login/';
                $password  = $this->encrypt($password);
                $post_data = [
                    'jazoest'             => '22453',
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
                    'mid'       => $this->get_mid(),
                    'csrftoken' => $this->get_csrftoken(),
                ];

                $result = $this->post($url, $post_data, $this->headers, $cookie);
                return $this->login_check($result);

            }
            else{
                return $this->login_control();
            }

        }

        public function two_factor_login($code = null, $two_factor_identifier = null,$verification_method = 2 ){

            if($code != null and $two_factor_identifier != null){

                $username = $this->username;

                $url       = $this->intagram_api_url.'/api/v1/accounts/two_factor_login/';
                $post_data = [
                    'verification_code'     => $code,
                    'phone_id'              => $this->get_phone_id(),
                    'two_factor_identifier' => $two_factor_identifier,
                    'trust_this_device'     => 1,
                    '_csrftoken'            => $this->get_csrftoken(),
                    'username'              => $username,
                    'adid'                  => $this->get_adid(),
                    'guid'                  => $this->get_guid(),
                    'device_id'             => $this->get_device_id(),
                    'verification_method'   => $verification_method,
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $result = $this->post($url, $post_data);
                return $this->login_check($result);

            }

            return false;

        }

        public function logout($username = null){

            $username = $username ?? $this->username;

            $url         = $this->intagram_api_url.'/api/v1/accounts/logout/';
            $post_data   = [
                'phone_id'          => $this->get_phone_id(),
                '_csrftoken'        => $this->get_csrftoken(),
                'guid'              => $this->get_guid(),
                'device_id'         => $this->get_device_id(),
                '_uuid'             => $this->get_guid(),
                'one_tap_app_login' => 'true',
            ];
            $result      = $this->post($url, $post_data);
            $result_body = json_decode($result['body']);
            if($result_body->status == 'ok'){
                if(file_exists($this->cache_path.$username)){
                    $this->username_delete($this->cache_path.$username);
                }
                return true;
            }

            return false;
        }

        private function login_check($json = null){
            if($json != null){
                $json_body = json_decode($json['body']);
                if(!isset($json_body->two_factor_required)){
                    if($json_body->status == 'ok'){
                        $cookie = $this->cache('session_id');
                        if($cookie == false){
                            foreach($json['headers']['ig-set-authorization'] as $cookie){
                                $this->cache('Bearer', $cookie);
                                preg_match('|Bearer IGT:(.*):(.*)|isu', $cookie, $session_json);
                                $session_json = json_decode(base64_decode($session_json[2]));
                                $this->cache('session_id', $session_json->sessionid);
                            }
                        }
                        return true;
                    }
                    else{
                        return $json_body->message;
                    }
                }
                else{
                    $two_factor_identifier = $json_body->two_factor_info->two_factor_identifier;
                    $verification_method   = 2;

                    return (object) [
                        'two_factor_identifier' => $two_factor_identifier,
                        'verification_method'   => $verification_method
                    ];
                }
            }
            return false;
        }

        public function login_control($username = null){

            $username = $username ?? $this->username;

            try{
                $url         = $this->intagram_api_url.'/api/v1/status/get_viewable_statuses/?include_authors=true';
                $result      = $this->get($url);
                $result_body = json_decode($result['body']);
                if(isset($result_body->status) and $result_body->status == 'ok'){
                    return true;
                }
                return false;
            }
            catch(\Exception $err){
                return false;
            }
        }

        public function encrypt($password){

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

    }
