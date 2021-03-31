<?php
    
    namespace instagram;
    
    use GuzzleHttp\Exception\GuzzleException;
    
    class instagram_request{
        
        public  $headers;
        private $app_id    = '567067343352427';
        private $phone_id  = '832f3947-2366-42c7-a49e-88136c36f7ad';
        private $device_id = 'android-daa21d4b02905ea0';
        private $guid      = 'f1c270c3-8663-40ef-8612-3dc8853b3459';
        private $adid      = 'f5904e04-349a-48ca-8516-8555ae99660c';
        
        public $cache_path   = (__DIR__).'/cache/';
        public $cache_prefix = 'insta';
        public $cache_time   = 10; //Minute
        
        public $user_agent = 'Instagram 177.0.0.30.119 Android (22/5.1.1; 160dpi; 540x960; Google/google; google Pixel 2; x86; qcom; tr_TR; 276028050)';
        
        public $username;
        public $password;
        
        public $functions;
        
        function __construct($username, $password, $functions = null){
            
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
            
        }
        
        public function get_csrftoken(){
            
            $url = 'https://www.instagram.com/';
            
            $cache_file = $this->cache('csrftoken');
            if($cache_file == false){
                
                $csrftoken_html = $this->request($url, 'GET', null, null, null, false);
                preg_match('|{"config":{"csrf_token":"(.*?)"|is', $csrftoken_html['body'], $csrftoken);
                
                $csrftoken = $csrftoken[1];
                
                $this->cache('csrftoken', [$csrftoken]);
            }
            else{
                $csrftoken = $cache_file[0];
            }
            
            return $csrftoken;
            
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
        
        public function cache($name, $desc = false, $json = false){
            
            if(!file_exists($this->cache_path.$this->username)){
                mkdir($this->cache_path.$this->username, 777);
            }
            
            $cache_file_path = $this->cache_path.$this->username.'/';
            $cache_file      = $cache_file_path.($this->cache_prefix.'-'.$name.'.json');
            
            if(file_exists($cache_file) and time() <= strtotime('+'.$this->cache_time.' minute', filemtime($cache_file))){
                return json_decode(file_get_contents($cache_file));
            }
            else if($desc !== false){
                if($json == true){
                    file_put_contents($cache_file, $desc);
                }
                else{
                    file_put_contents($cache_file, json_encode($desc));
                }
                return $desc;
            }
            else{
                return false;
            }
        }
        
        public function request($url = '', $type = 'GET', $data = null, $header = null, $cookie = null, $user_cookie = true){
            
            if($type == 'UPLOAD'){
                $type = 'POST';
                $data = $data;
            }
            else if($type == 'POST' and $data != null){
                $data = [
                    'form_params' => $data,
                ];
            }
            
            $headers_default = [
                'X-IG-App-Locale'      => 'tr_TR',
                'X-IG-Device-Locale'   => 'tr_TR',
                'X-IG-Mapped-Locale'   => 'tr_TR',
                'X-Pigeon-Session-Id'  => 'fd08fa6f-2d24-4514-8abc-5fd3ca620305',
                'X-IG-Connection-Type' => 'WIFI',
                'X-IG-Capabilities'    => '3brTvx8=',
                'Priority'             => 'u=3',
                'X-MID'                => 'YB2r4AABAAERcl5ESNxLjr_tt4Q5',
                'IG-INTENDED-USER-ID'  => 0,
                'Host'                 => 'i.instagram.com',
                'X-FB-HTTP-Engine'     => 'Liger',
                'X-FB-Client-IP'       => 'True',
                'X-FB-Server-Cluster'  => 'True',
                'X-IG-App-ID'          => $this->app_id,
                'X-IG-Device-ID'       => $this->guid,
                'X-IG-Android-ID'      => $this->device_id,
                'User-Agent'           => $this->user_agent,
            ];
            
            $headers = $header??$headers_default;
            
            if($user_cookie == true){
                $cookie            = $cookie??$this->create_cookie(false, $user_cookie);
                $headers['Cookie'] = $cookie;
            }
            
            try{
                $client = new \GuzzleHttp\Client([
                    'verify' => false,
                    'headers' => $headers,
                ]);
                
                if($type == 'POST'){
                    $res = $client->post($url,$data);
                }else{
                    $res = $client->get($url);
                }
                
                /*
                $res = $client->request($type, $url, [
                    'headers' => $headers,
                    $data??null,
                ]);
                */
                
                return [
                    'headers' => $res->getHeaders(),
                    'body'    => $res->getBody()->getContents(),
                ];
            }
            catch(GuzzleException $exception){
                return [
                    'status'  => 'fail',
                    'message' => $exception->getMessage(),
                    'headers' => $exception->getResponse()->getHeaders(),
                    'body'    => $exception->getResponse()->getBody()->getContents(),
                ];
            }
            
        }
        
        public function get_adid(){
            return $this->adid;
        }
        
        public function get_device_id(){
            return $this->device_id;
        }
        
        public function get_phone_id(){
            return $this->phone_id;
        }
        
        public function get_guid(){
            return $this->guid;
        }
        
        public function set_username($username){
            $this->username = $username;
        }
        
        public function get_session_id($username = null){
            
            $username       = $username??$this->username;
            $this->username = $username;
            
            $cookie = $this->cache($username.'-sessionid');
            if($cookie == false){
                $session_id = 0;
            }
            else{
                $session_id = $cookie[0];
            }
            
            return $session_id;
        }
        
        public function get_post_queryhash(){
            
            $url        = 'https://www.instagram.com/static/bundles/es6/Consumer.js/260e382f5182.js';
            $cache_file = $this->cache('post_queryhash');
            if($cache_file == false){
                
                $html = $this->request($url);
                preg_match('|l.pagination},queryId:"(.*?)"|is', $html['body'], $post_hashquery);
                
                $post_hashquery       = $post_hashquery[1];
                $this->post_hashquery = $post_hashquery;
                
                $this->cache('post_hashquery', [$post_hashquery]);
            }
            else{
                $post_hashquery = $cache_file[0];
            }
            
            return $post_hashquery;
            
        }
        
        //KELİME BAŞLIYORSA
        function start_with($samanlik, $igne){
            $length = strlen($igne);
            return (substr($samanlik, 0, $length) === $igne);
        }
        //KELİME BAŞLIYORSA
        
        //KELİME BİTİYORSA
        function end_with($samanlik, $igne){
            $length = strlen($igne);
            if($length == 0){
                return true;
            }
            
            return (substr($samanlik, -$length) === $igne);
        }
        //KELİME BİTİYORSA
        
    }