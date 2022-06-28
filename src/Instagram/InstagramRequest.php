<?php

    namespace Hasokeyk\Instagram;

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\GuzzleException;

    class InstagramRequest{

        public $intagram_api_url = 'https://i.instagram.com';

        public $username = null;
        public $password = null;
        public $functions;

        public $cache_path = (__DIR__).'/../cache/';
        public $cache_time = 10; //Minute

        public  $proxy      = 'http://185.122.200.168:8118';
        public  $client;
        public  $headers    = [];
        private $app_id     = '567067343352427';
        private $device_id;
        private $phone_id   = '01ec3ad7-f01e-4b4f-ba81-26ad8a444582';
        private $guid;
        private $adid;
        public  $user_agent = 'Instagram 219.0.0.12.117 Android (25/7.1.2; 320dpi; 900x1600; xiaomi; Redmi Note 8 Pro; d2q; qcom; tr_TR; 346138365)';

        function __construct($username, $password, $functions){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;

            $this->headers = [
                'X-IG-App-Locale'      => 'tr_TR',
                'X-IG-Device-Locale'   => 'tr_TR',
                'X-IG-Mapped-Locale'   => 'tr_TR',
                'X-IG-Connection-Type' => 'WIFI',
                'X-IG-Capabilities'    => '3brTvx8=',
                'Priority'             => 'u=3',
                'IG-INTENDED-USER-ID'  => 0,
                'Host'                 => 'i.instagram.com',
                'X-FB-HTTP-Engine'     => 'Liger',
                'X-FB-Client-IP'       => 'True',
                'X-FB-Server-Cluster'  => 'True',
                'User-Agent'           => $this->user_agent,
                'X-IG-App-ID'          => $this->app_id,
            ];

            $this->client = new Client([
                'verify'  => false,
                //'proxy'   => $this->proxy,
                'version' => 2,
            ]);
        }

        public function ready_header($user_cookie = false){

            $headers = [
                'X-IG-App-Locale'      => 'tr_TR',
                'X-IG-Device-Locale'   => 'tr_TR',
                'X-IG-Mapped-Locale'   => 'tr_TR',
                'X-IG-Connection-Type' => 'WIFI',
                'X-IG-Capabilities'    => '3brTvx8=',
                'Priority'             => 'u=3',
                'IG-INTENDED-USER-ID'  => 0,
                'Host'                 => 'i.instagram.com',
                'X-FB-HTTP-Engine'     => 'Liger',
                'X-FB-Client-IP'       => 'True',
                'X-FB-Server-Cluster'  => 'True',
                'X-IG-App-ID'          => $this->app_id,
                'X-Ig-Android-ID'      => $this->get_android_id(),
                'X-MID'                => $this->get_mid(),
                'X-IG-Device-ID'       => $this->get_device_id(),
                //'X-FB-Client-IP-Forwarded' => $this->get_my_ip(),
                'User-Agent'           => $this->user_agent,
            ];

            //if($user_cookie == true){
            $cookie                   = $cookie ?? $this->create_cookie(false, $user_cookie);
            $headers['Cookie']        = $cookie;
            $headers['Authorization'] = $this->cache('Bearer');
            //}

            return $headers;
        }

        public function get($url = '', $headers = null, $cookie = true){
            try{
                $headers = $headers ?? $this->ready_header();
                $options = [
                    'headers' => $headers,
                    'version' => 2,
                    'curl'    => [
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    ]
                ];

                $res = $this->client->get($url, $options);
                return [
                    'status'  => 'ok',
                    'headers' => $res->getHeaders(),
                    'body'    => $res->getBody()->getContents(),
                ];
            }
            catch(GuzzleException $exception){
                return [
                    'status'  => 'fail',
                    'message' => $exception->getMessage() ?? 'Empty',
                    'headers' => $exception->getResponse()->getHeaders() ?? null,
                    'body'    => $exception->getResponse()->getBody()->getContents() ?? null,
                ];
            }
        }

        public function post($url = null, $post_data = null, $headers = null){
            try{

                $headers = $headers ?? $this->ready_header();
                $options = [
                    'headers'     => $headers,
                    'form_params' => ($post_data ?? null),
                    'version'     => 2,
                    'curl'        => [
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    ]
                ];

                $res = $this->client->post($url, $options);
                return [
                    'status'  => 'ok',
                    'headers' => $res->getHeaders() ?? null,
                    'body'    => $res->getBody()->getContents(),
                ];
            }
            catch(GuzzleException $exception){
                return [
                    'status'  => 'fail',
                    'message' => $exception->getMessage() ?? 'Empty',
                    'headers' => $exception->getResponse()->getHeaders() ?? null,
                    'body'    => $exception->getResponse()->getBody()->getContents() ?? null,
                ];
            }
        }

        public function upload($url = null, $post_data = null, $headers = null){
            try{

                $headers = $headers ?? $this->ready_header();
                $options = [
                    'headers' => $headers,
                    'body'    => $post_data,
                    'version' => 2,
                    'curl'    => [
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    ]
                ];

                $res = $this->client->post($url, $options);
                return [
                    'status'  => 'ok',
                    'headers' => $res->getHeaders() ?? null,
                    'body'    => $res->getBody()->getContents(),
                ];
            }
            catch(GuzzleException $exception){
                return [
                    'status'  => 'fail',
                    'message' => $exception->getMessage() ?? 'Empty',
                    'headers' => $exception->getResponse()->getHeaders() ?? null,
                    'body'    => $exception->getResponse()->getBody()->getContents() ?? null,
                ];
            }
        }

        public function cache($cache_name, $content = false, $json = false, $rewrite = false){

            if($this->username != null){

                if(file_exists($this->cache_path.$this->username) === false){
                    $this->open_require_dir();
                }

                $cache_file_path = $this->cache_path.$this->username.'/';
                $cache_file      = $cache_file_path.($cache_name.'.json');

                if(file_exists($cache_file) and time() <= strtotime('+'.$this->cache_time.' minute', filemtime($cache_file))){
                    $content = file_get_contents($cache_file);
                    return json_decode($content);
                }
                else if($content !== false){
                    if($json == true){
                        file_put_contents($cache_file, $content);
                    }
                    else{
                        file_put_contents($cache_file, json_encode($content));
                    }
                    return $content;
                }

            }
            return false;

        }

        public function open_require_dir($username = null){

            $username = $username ?? $this->username;

            if(is_writable($this->cache_path) === false){
                mkdir($this->cache_path, 777);
                chmod($this->cache_path, 0777);
            }

            if(file_exists($this->cache_path.$this->username) === false){
                mkdir($this->cache_path.$this->username, 777);
                chmod($this->cache_path.$this->username, 0777);
            }

            $dir_list = [
                'users',
            ];
            foreach($dir_list as $dir){
                if(!file_exists($this->cache_path.$username.'/'.$dir)){
                    mkdir($this->cache_path.$username.'/'.$dir, 777);
                    chmod($this->cache_path.$username.'/'.$dir, 0777);
                }
                else{
                    chmod($this->cache_path.$username.'/'.$dir, 0777);
                }
            }

        }

        public function get_csrftoken(){

            $url        = 'https://www.instagram.com/';
            $cache_file = $this->cache('csrftoken');
            if($cache_file == false){
                $csrftoken_html = $this->get($url, $this->headers);
                preg_match('|{"config":{"csrf_token":"(.*?)"|is', $csrftoken_html['body'], $csrftoken);
                $csrftoken = $csrftoken[1];
                $this->cache('csrftoken', $csrftoken);
            }
            else{
                $csrftoken = $cache_file;
            }

            return $csrftoken;

        }

        public function get_sync(){

            $cache = $this->cache('app_key');
            if($cache === false){

                $url       = $this->intagram_api_url.'/api/v1/launcher/sync/';
                $post_data = [
                    '_csrftoken'              => $this->get_csrftoken(),
                    'id'                      => 'F2CD7326-EA40-44F8-9FC3-71A0A5E1F55B',
                    'server_config_retrieval' => '1',
                    'experiments'             => "ig_growth_android_profile_pic_prefill_with_fb_pic_2,ig_account_identity_logged_out_signals_global_holdout_universe,ig_android_caption_typeahead_fix_on_o_universe,ig_android_retry_create_account_universe,ig_android_gmail_oauth_in_reg,ig_android_quickcapture_keep_screen_on,ig_android_smartlock_hints_universe,ig_android_reg_modularization_universe,ig_android_login_identifier_fuzzy_match,ig_android_passwordless_account_password_creation_universe,ig_android_security_intent_switchoff,ig_android_sim_info_upload,ig_android_device_verification_fb_signup,ig_android_reg_nux_headers_cleanup_universe,ig_android_direct_main_tab_universe_v2,ig_android_nux_add_email_device,ig_android_fb_account_linking_sampling_freq_universe,ig_android_device_info_foreground_reporting,ig_android_suma_landing_page,ig_android_device_verification_separate_endpoint,ig_android_direct_add_direct_to_android_native_photo_share_sheet,ig_android_device_detection_info_upload,ig_android_device_based_country_verification",
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $res    = $this->post($url, $post_data, $this->headers);
                $result = (object) [
                    'pub_key_id' => $res['headers']['ig-set-password-encryption-key-id'][0] ?? null,
                    'pub_key'    => $res['headers']['ig-set-password-encryption-pub-key'][0] ?? null,
                ];
                $this->cache('app-key', $result);
            }
            else{
                $result = $cache;
            }
            return $result;
        }

        public function get_guid(){
            $cache = $this->cache('guid');
            if($cache === false){
                if(function_exists('com_create_guid')){
                    $guid = $this->guid = mb_strtolower(str_replace([
                        '{',
                        '}'
                    ], [
                        '',
                        ''
                    ], com_create_guid()), 'utf8');
                }
                else{
                    mt_srand((double) microtime() * 10000);//optional for php 4.2.0 and up.
                    $charid = strtoupper(md5(uniqid(rand(), true)));
                    $hyphen = chr(45);// "-"
                    $uuid   = chr(123)// "{"
                              .substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen.substr($charid, 12, 4).$hyphen.substr($charid, 16, 4).$hyphen.substr($charid, 20, 12).chr(125);// "}"
                    $guid   = $this->guid = $uuid;
                }
                $this->cache('guid', $guid);
                return $guid;
            }
            else{
                return $cache;
            }
        }

        public function get_adid(){
            $cache = $this->cache('adid');
            if($cache === false){
                if(function_exists('com_create_guid')){
                    $guid = $this->guid = mb_strtolower(str_replace([
                        '{',
                        '}'
                    ], [
                        '',
                        ''
                    ], com_create_guid()), 'utf8');
                }
                else{
                    mt_srand((double) microtime() * 10000);//optional for php 4.2.0 and up.
                    $charid = strtoupper(md5(uniqid(rand(), true)));
                    $hyphen = chr(45);// "-"
                    $uuid   = chr(123)// "{"
                              .substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen.substr($charid, 12, 4).$hyphen.substr($charid, 16, 4).$hyphen.substr($charid, 20, 12).chr(125);// "}"
                    $guid   = $this->guid = $uuid;
                }
                $this->cache('adid', $guid);
                return $guid;
            }
            else{
                return $cache;
            }
        }

        public function get_phone_id(){
            $cache = $this->cache('phone_id');
            if($cache === false){
                if(function_exists('com_create_guid')){
                    $guid = $this->guid = mb_strtolower(str_replace([
                        '{',
                        '}'
                    ], [
                        '',
                        ''
                    ], com_create_guid()), 'utf8');
                }
                else{
                    mt_srand((double) microtime() * 10000);//optional for php 4.2.0 and up.
                    $charid = strtoupper(md5(uniqid(rand(), true)));
                    $hyphen = chr(45);// "-"
                    $uuid   = chr(123)// "{"
                              .substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen.substr($charid, 12, 4).$hyphen.substr($charid, 16, 4).$hyphen.substr($charid, 20, 12).chr(125);// "}"
                    $guid   = $this->guid = $uuid;
                }
                $this->cache('phone_id', $guid);
                return $guid;
            }
            else{
                return $cache;
            }
        }

        public function get_device_id(){
            $cache = $this->cache('device_id');
            if($cache === false){
                if(function_exists('com_create_guid')){
                    $guid = $this->guid = mb_strtolower(str_replace([
                        '{',
                        '}'
                    ], [
                        '',
                        ''
                    ], com_create_guid()), 'utf8');
                }
                else{
                    mt_srand((double) microtime() * 10000);//optional for php 4.2.0 and up.
                    $charid = strtoupper(md5(uniqid(rand(), true)));
                    $hyphen = chr(45);// "-"
                    $uuid   = chr(123)// "{"
                              .substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen.substr($charid, 12, 4).$hyphen.substr($charid, 16, 4).$hyphen.substr($charid, 20, 12).chr(125);// "}"
                    $guid   = $this->guid = $uuid;
                }
                $this->cache('device_id', $guid);
                return $guid;
            }
            else{
                return $cache;
            }
        }

        public function get_android_id(){
            $cache = $this->cache('android_id');
            if($cache === false){
                $android_id = 'android-'.substr(md5(time()), 0, 16);
                $this->cache('android_id', $android_id);
                return $android_id;
            }
            else{
                return $cache;
            }
        }

        public function get_mid(){

            $cache = $this->cache('mid');
            if($cache === false){
                $url       = $this->intagram_api_url.'/api/v1/accounts/contact_point_prefill/';
                $post_data = [
                    'phone_id' => $this->phone_id,
                    'usage'    => 'prefill',
                ];
                $post_data = [
                    'signed_body' => 'SIGNATURE.'.json_encode($post_data)
                ];
                $res       = $this->post($url, $post_data, $this->headers);
                if(isset($res['headers']['ig-set-xmid'])){
                    $mid = $res['headers']['ig-set-x-mid'];
                }
                else{
                    if(isset($res['headers']['Set-Cookie'])){
                        foreach($res['headers']['Set-Cookie'] as $cookie){
                            if(strstr($cookie, 'mid=')){
                                preg_match('|mid=(.*?);|is', $cookie, $mid);
                                $mid = $mid[1];
                                break;
                            }
                        }
                    }
                    else{
                        $mid = 'YgP2EAABAAFQNAw9106CBrVuj7Mh';
                    }
                }

                $this->cache('mid', $mid);
                return $mid;
            }
            else{
                return $cache;
            }
        }

        public function get_my_ip(){
            $url = 'https://api.my-ip.io/ip.json';
            $res = $this->get($url, $this->headers);
            return json_decode($res['body'])->ip;
        }

        public function get_session_id($username = null){

            $username       = $username ?? $this->username;
            $this->username = $username;

            $cookie = $this->cache('session_id');
            if($cookie == false){
                $session_id = 0;
            }
            else{
                $session_id = $cookie;
            }

            return $session_id;
        }

        public function create_cookie($array = false, $session_id = true){

            $cookies_array = [
                'mid'       => 'YB2r4AABAAERcl5ESNxLjr_tt4Q5',
                'csrftoken' => $this->get_csrftoken(),
            ];

            if($session_id === true){
                $cookies_array['sessionid'] = $this->get_session_id();
            }

            if($array == false){
                $cookies = '';
                foreach($cookies_array as $cookie => $value){
                    $cookies .= $cookie.'='.$value.'; ';
                }
                return $cookies;
            }

            return $cookies_array;

        }

        public function username_delete($dir){
            $files = array_diff(scandir($dir), [
                '.',
                '..'
            ]);
            foreach($files as $file){
                (is_dir("$dir/$file")) ? $this->username_delete("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }

        public function generate_client_context(){
            return (round(microtime(true) * 1000) << 22 | random_int(PHP_INT_MIN, PHP_INT_MAX) & 4194303) & PHP_INT_MAX;
        }

        public function get_post_queryhash(){

            $url        = 'https://www.instagram.com/static/bundles/es6/Consumer.js/260e382f5182.js';
            $cache_file = $this->cache('post_queryhash');
            if($cache_file == false){

                $html = $this->get($url);
                preg_match('|l.pagination},queryId:"(.*?)"|is', $html['body'], $post_hashquery);

                $post_hashquery       = $post_hashquery[1];
                $this->post_hashquery = $post_hashquery;

                $this->cache('post_hashquery', [$post_hashquery]);
            }
            else{
                $post_hashquery = $cache_file;
            }

            return $post_hashquery;

        }
    }
