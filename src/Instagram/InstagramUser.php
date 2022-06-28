<?php

    namespace Hasokeyk\Instagram;

    class InstagramUser extends InstagramRequest{

        public $username;
        public $password;
        public $functions;

        function __construct($username, $password, $functions){
            parent::__construct($username, $password, $functions);

            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }

        public function get_my_phone_number($username = null){

            $username = $username ?? $this->username;
            if($username != null){

                $cache = $this->cache('current_user');
                if($cache === false){
                    $url  = 'https://i.instagram.com/api/v1/accounts/current_user/?edit=true';
                    $json = $this->get($url);
                    $json = json_decode($json['body']);
                    $this->cache('current_user', $json);
                    return $json->user->phone_number;
                }
                else{
                    $result = $cache->user->phone_number;
                }

                return $result;
            }

            return false;
        }

        public function get_my_email($username = null){

            $username = $username ?? $this->username;
            if($username != null){

                $cache = $this->cache('current_user');
                if($cache === false){
                    $url  = 'https://i.instagram.com/api/v1/accounts/current_user/?edit=true';
                    $json = $this->get($url);
                    $json = json_decode($json['body']);
                    $this->cache('current_user', $json);
                    return $json->user->email;
                }
                else{
                    $result = $cache->user->email;
                }

                return $result;
            }

            return false;
        }

        public function get_my_fullname($username = null){

            $username = $username ?? $this->username;
            if($username != null){

                $cache = $this->cache('current_user');
                if($cache === false){
                    $url  = 'https://i.instagram.com/api/v1/accounts/current_user/?edit=true';
                    $json = $this->get($url);
                    $json = json_decode($json['body']);
                    $this->cache('current_user', $json);
                    return $json->user->full_name;
                }
                else{
                    $result = $cache->user->full_name;
                }

                return $result;
            }

            return false;
        }

        public function get_my_website($username = null){

            $username = $username ?? $this->username;
            if($username != null){

                $cache = $this->cache('current_user');
                if($cache === false){
                    $url  = 'https://i.instagram.com/api/v1/accounts/current_user/?edit=true';
                    $json = $this->get($url);
                    $json = json_decode($json['body']);
                    $this->cache('current_user', $json);
                    return $json->user->external_url;
                }
                else{
                    $result = $cache->user->external_url;
                }

                return $result;
            }

            return false;
        }

        public function get_my_biography($username = null){

            $username = $username ?? $this->username;
            if($username != null){

                $cache = $this->cache('current_user');
                if($cache === false){
                    $url  = 'https://i.instagram.com/api/v1/accounts/current_user/?edit=true';
                    $json = $this->get($url);
                    $json = json_decode($json['body']);
                    $this->cache('current_user', $json);
                    return $json->user->biography;
                }
                else{
                    $result = $cache->user->biography;
                }

                return $result;
            }

            return false;
        }

        public function get_user_id($username = null){
            $username = $username ?? $this->username;
            if($username != null){
                $cache = $this->cache('users/'.$username.'-id');
                if($cache === false){
                    $url = 'https://www.instagram.com/web/search/topsearch/?query='.$username;

                    $json = $this->get($url);
                    $json = json_decode($json['body']);

                    $user_id = 0;
                    foreach($json->users as $user){
                        if($username == $user->user->username){
                            $user_id = $user->user->pk;
                        }
                    }

                    $this->cache('users/'.$username.'-id', $user_id);
                }
                else{
                    $user_id = $cache;
                }
                return $user_id;
            }

            return false;
        }

        public function get_user_posts($username = null){
            $cache = $this->cache($username.'-posts');
            if($cache == false){
                $post_hashquery = $this->get_post_queryhash();
                $user_id        = $this->get_user_id($username);
                $url            = 'https://www.instagram.com/graphql/query/?query_hash='.$post_hashquery.'&variables={"id":"'.$user_id.'","first":50}';
                $json           = $this->request($url);
                $json           = json_decode($json['body'])->data->user;

                $this->cache($username.'-posts', $json);

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

            $json = $this->upload($url, $file, $header);
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
            $json      = $this->post($url, $post_data);
            return json_decode($json['body']);
        }

        public function get_user_info($username = null){

            $username = $username ?? $this->username;
            $user_id  = $this->get_user_id($username);

            $cache = $this->cache('users/'.$user_id);
            if(!$cache){
                if($user_id != null){
                    $url  = 'https://i.instagram.com/api/v1/users/'.$user_id.'/info/';
                    $json = $this->get($url);
                    $json = json_decode($json['body']);
                    $this->cache('users/'.$user_id, $json);
                    return $json;
                }
            }
            else{
                return $cache;
            }

            return false;
        }

        public function get_user_friendship_show($username = null){

            $username = $username ?? $this->username;
            $user_id  = $this->get_user_id($username);

            $cache = $this->cache('users/'.$user_id.'-friendship');
            if($cache === false){
                if($user_id != null){
                    $url  = 'https://i.instagram.com/api/v1/friendships/show/'.$user_id.'/';
                    $json = $this->get($url);
                    $json = json_decode($json['body']);
                    $this->cache('users/'.$user_id.'-friendship', $json);
                    return $json;
                }
            }
            else{
                return $cache;
            }

            return false;
        }

        public function get_multi_user_friendship_show($user_ids = []){
            if($user_ids != null){
                $user_ids  = implode(',', $user_ids);
                $url       = 'https://i.instagram.com/api/v1/friendships/show_many/';
                $post_data = [
                    'user_ids' => $user_ids,
                ];
                $json      = $this->post($url, $post_data);
                $json      = json_decode($json['body']);
                return $json;
            }

            return false;
        }

        public function get_my_surfaces(){

            $cache = $this->cache('surface');
            if($cache === false){
                $url  = 'https://i.instagram.com/api/v1/scores/bootstrap/users/?surfaces=["coefficient_direct_closed_friends_ranking","coefficient_direct_recipients_ranking_variant_2","coefficient_rank_recipient_user_suggestion","coefficient_besties_list_ranking","coefficient_ios_section_test_bootstrap_ranking","autocomplete_user_list"]';
                $json = $this->get($url);
                if($json['status'] == 'ok'){
                    $json = json_decode($json['body']);
                    $this->cache('surface', $json);
                    return $json;
                }
                else{
                    return $json;
                }
            }
            else{
                return $cache;
            }

            return false;
        }

        public function get_users_score(){
            $cache = $this->cache('user_score');
            if(!$cache){
                $username = $this->username;
                if($username != null){
                    $url  = 'https://i.instagram.com/api/v1/banyan/banyan/?views=["direct_user_search_keypressed","group_stories_share_sheet","reshare_share_sheet","direct_inbox_active_now","story_share_sheet","forwarding_recipient_sheet","direct_user_search_nullstate","threads_people_picker"]';
                    $json = $this->get($url);
                    if($json['status'] == 'ok'){
                        $json = json_decode($json['body']);
                        $this->cache('user_score', $json);
                        return $json;
                    }
                    else{
                        return $json;
                    }
                }
            }
            else{
                return $cache;
            }

            return false;
        }

        public function follow($username){
            if($username != null){
                $user_id    = $this->get_user_id($username);
                $me_user_id = $this->get_user_id();

                $url = 'https://i.instagram.com/api/v1/friendships/create/'.$user_id.'/';

                $post_data = [
                    'container_module' => 'self_following',
                    'radio_type'       => 'wifi-none',
                    'user_id'          => $user_id,
                    '_uid'             => $me_user_id,
                    '_csrftoken'       => $this->get_csrftoken(),
                    'device_id'        => $this->get_android_id(),
                    '_uuid'            => $this->get_device_id(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function unfollow($username){
            if($username != null){
                $user_id    = $this->get_user_id($username);
                $me_user_id = $this->get_user_id();

                $url = 'https://i.instagram.com/api/v1/friendships/destroy/'.$user_id.'/';

                $post_data = [
                    'container_module' => 'self_following',
                    'radio_type'       => 'wifi-none',
                    'user_id'          => $user_id,
                    '_csrftoken'       => $this->get_csrftoken(),
                    '_uid'             => $me_user_id,
                    '_uuid'            => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function accept_follow_request($username){
            if($username != null){
                $user_id    = $this->get_user_id($username);
                $me_user_id = $this->get_user_id();

                $url = 'https://i.instagram.com/api/v1/friendships/approve/'.$user_id.'/';

                $post_data = [
                    'radio_type' => 'wifi-none',
                    'user_id'    => $user_id,
                    '_csrftoken' => $this->get_csrftoken(),
                    '_uid'       => $me_user_id,
                    '_uuid'      => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function unfollow_me($username){
            if($username != null){
                $user_id    = $this->get_user_id($username);
                $me_user_id = $this->get_user_id();

                $url = 'https://i.instagram.com/api/v1/friendships/remove_follower/'.$user_id.'/';

                $post_data = [
                    'container_module' => 'self_following',
                    'radio_type'       => 'wifi-none',
                    'user_id'          => $user_id,
                    '_csrftoken'       => $this->get_csrftoken(),
                    '_uid'             => $me_user_id,
                    '_uuid'            => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function send_inbox_text($username, $text = 'Hello', $style = null){
            if($username != null){
                $user_id = $this->get_user_id($username);
                $url     = 'https://i.instagram.com/api/v1/direct_v2/threads/broadcast/text/';
                //$thread_id = $this->get_inbox_user_thread($username);
                $post_data = [
                    'text'                 => $text,
                    'action'               => 'send_item',
                    'is_shh_mode'          => '0',
                    'recipient_users'      => '[['.$user_id.']]',
                    //'thread_ids'           => '['.$thread_id['thread_id'].']',
                    'send_attribution'     => 'direct_thread',
                    'client_context'       => $this->generate_client_context(),
                    '_csrftoken'           => $this->get_csrftoken(),
                    'device_id'            => $this->get_device_id(),
                    'mutation_token'       => $this->generate_client_context(),
                    '_uuid'                => $this->get_guid(),
                    'offline_threading_id' => $this->generate_client_context(),
                ];

                if($style != null){
                    $post_data['power_up_data'] = '{"style":'.$style.'}';
                }

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function send_inbox_text_heart($username, $text = 'Hello'){
            if($username != null){
                return $this->send_inbox_text($username, $text, 1);
            }

            return false;
        }

        public function send_inbox_text_gift($username, $text = 'Hello'){
            if($username != null){
                return $this->send_inbox_text($username, $text, 2);
            }

            return false;
        }

        public function send_inbox_text_confetti($username, $text = 'Hello'){
            if($username != null){
                return $this->send_inbox_text($username, $text, 3);
            }

            return false;
        }

        public function send_inbox_text_fire($username, $text = 'Hello'){
            if($username != null){
                return $this->send_inbox_text($username, $text, 4);
            }

            return false;
        }

        public function send_inbox_heart($username){
            if($username != null){
                $user_id = $this->get_user_id($username);
                $url     = 'https://i.instagram.com/api/v1/direct_v2/threads/broadcast/like/';
                //$thread_id = $this->get_inbox_user_thread($username);
                $post_data = [
                    'action'               => 'send_item',
                    'is_shh_mode'          => '0',
                    'recipient_users'      => '[['.$user_id.']]',
                    //'thread_ids'           => '['.$thread_id['thread_id'].']',
                    'send_attribution'     => 'direct_thread',
                    'client_context'       => $this->generate_client_context(),
                    '_csrftoken'           => $this->get_csrftoken(),
                    'device_id'            => $this->get_device_id(),
                    'mutation_token'       => $this->generate_client_context(),
                    '_uuid'                => $this->get_guid(),
                    'offline_threading_id' => $this->generate_client_context(),
                ];

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function send_inbox_photo($username, $image_path = null){
            if($username != null and $image_path != null){
                //IMAGE UPLOAD
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

                $upload_json = $this->upload($url, $file, $header);
                $upload_json = json_decode($upload_json['body']);
                //IMAGE UPLOAD

                //IMAGE SEND
                $user_id = $this->get_user_id($username);
                $url     = 'https://i.instagram.com/api/v1/direct_v2/threads/broadcast/configure_photo/';
                //$thread_id = $this->get_inbox_user_thread($username);
                $post_data = [
                    'upload_id'               => $upload_json->upload_id,
                    'allow_full_aspect_ratio' => 'true',
                    'action'                  => 'send_item',
                    'is_shh_mode'             => '0',
                    'recipient_users'         => '[['.$user_id.']]',
                    //'thread_ids'              => '['.$thread_id['thread_id'].']',
                    'send_attribution'        => 'direct_thread',
                    'client_context'          => $this->generate_client_context(),
                    '_csrftoken'              => $this->get_csrftoken(),
                    'device_id'               => $this->get_device_id(),
                    'mutation_token'          => $this->generate_client_context(),
                    '_uuid'                   => $this->get_guid(),
                    'offline_threading_id'    => $this->generate_client_context(),
                ];
                $json      = $this->post($url, $post_data);
                $json      = json_decode($json['body']);
                //IMAGE SEND

                return $json;
            }

            return false;
        }

        public function send_inbox_post($username, $post_id = null){
            if($username != null and $post_id != null){

                $thread_id = $this->get_inbox_user_thread($username);
                $url       = 'https://i.instagram.com/api/v1/direct_v2/threads/broadcast/media_share/?media_type=photo';
                $post_data = [
                    'action'           => 'send_item',
                    'is_shh_mode'      => 0,
                    'thread_ids'       => '['.$thread_id->thread_id.']',
                    'send_attribution' => 'feed_timeline',
                    'media_id'         => $post_id,
                    'device_id'        => $this->get_android_id(),
                    '_uuid'            => $this->get_device_id(),
                ];
                $json      = $this->post($url, $post_data);
                $json      = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function send_inbox_story($username, $story_post_id = null){
            if($username != null and $story_post_id != null){

                $thread_id = $this->get_inbox_user_thread($username);
                $url       = 'https://i.instagram.com/api/v1/direct_v2/threads/broadcast/story_share/?media_type=video';
                $post_data = [
                    'action'               => 'send_item',
                    'is_shh_mode'          => 0,
                    'send_attribution'     => 'reel_highlight_profile',
                    'reel_id'              => 'highlight:17925501509094715',
                    'containermodule'      => 'reel_highlight_profile',
                    'send_as_moment'       => 0,
                    'thread_ids'           => '['.$thread_id->thread_id.']',
                    'offline_threading_id' => $thread_id->thread_v2_id,
                    'story_media_id'       => $story_post_id,
                    'device_id'            => $this->get_android_id(),
                    '_uuid'                => $this->get_device_id(),
                ];

                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function send_inbox_link($username, $link = 'https://hayatikodla.net', $style = null){
            if($username != null){
                $user_id = $this->get_user_id($username);
                $url     = 'https://i.instagram.com/api/v1/direct_v2/threads/broadcast/link/';

                $post_data = [
                    'link_text'            => $link,
                    'link_urls'            => '['.json_encode($link).']',
                    'action'               => 'send_item',
                    'is_shh_mode'          => '0',
                    'recipient_users'      => '[['.$user_id.']]',
                    //'thread_ids'           => '['.$thread_id['thread_id'].']',
                    'send_attribution'     => 'direct_thread',
                    'client_context'       => $this->generate_client_context(),
                    '_csrftoken'           => $this->get_csrftoken(),
                    'device_id'            => $this->get_device_id(),
                    'mutation_token'       => $this->generate_client_context(),
                    '_uuid'                => $this->get_guid(),
                    'offline_threading_id' => $this->generate_client_context(),
                ];

                if($style != null){
                    $post_data['power_up_data'] = '{"style":'.$style.'}';
                }

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function get_inbox_user_thread($username = null, $group = false){
            if($username){
                $threads_id      = null;
                $user_id         = $this->get_user_id($username);
                $threads_id_list = $this->get_inbox_threads();
                if($threads_id_list->inbox->threads != null){
                    foreach($threads_id_list->inbox->threads as $thread){
                        if($group === true and count($thread->users) > 0){
                            foreach($thread->users as $user){
                                if($user->pk == $user_id){
                                    $threads_id = (object) [
                                        'thread_id'    => $thread->thread_id,
                                        'thread_v2_id' => $thread->thread_v2_id,
                                    ];
                                    break;
                                }
                            }
                        }
                        else{
                            if($thread->users[0]->pk == $user_id){
                                $threads_id = (object) [
                                    'thread_id'    => $thread->thread_id,
                                    'thread_v2_id' => $thread->thread_v2_id,
                                ];
                                break;
                            }
                        }
                    }

                    if($threads_id == null){
                        $thread     = $this->get_create_inbox_thread($username);
                        $threads_id = (object) [
                            'thread_id'    => $thread->thread->thread_id,
                            'thread_v2_id' => $thread->thread->thread_v2_id,
                        ];
                    }
                }
                else{
                    $thread     = $this->get_create_inbox_thread($username);
                    $threads_id = (object) [
                        'thread_id'    => $thread->thread->thread_id,
                        'thread_v2_id' => $thread->thread->thread_v2_id,
                    ];
                }

                return $threads_id;
            }

            return false;
        }

        public function get_inbox_threads(){
            $url  = 'https://i.instagram.com/api/v1/direct_v2/inbox/?visual_message_return_type=unseen&thread_message_limit=10&persistentBadging=true&limit=20&push_disabled=true&fetch_reason=manual_refresh';
            $json = $this->get($url);
            $json = json_decode($json['body']);
            return $json;
        }

        public function get_create_inbox_thread($username){
            $user_id = $this->get_user_id($username);
            $url     = 'https://i.instagram.com/api/v1/direct_v2/threads/get_by_participants/?recipient_users=%5B'.$user_id.'%5D&seq_id=1573&limit=20';
            $json    = $this->get($url);
            $json    = json_decode($json['body']);
            return $json;
        }

        public function get_me_least_interacted_with(){
            $url  = 'https://i.instagram.com/api/v1/friendships/smart_groups/least_interacted_with/?search_surface=follow_list_page&query=&enable_groups=true&rank_token=e667dad2-ccf4-461a-ba53-d83f9007cc7f';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_me_most_seen_in_feed(){
            $url  = 'https://i.instagram.com/api/v1/friendships/smart_groups/most_seen_in_feed/?search_surface=follow_list_page&query=&enable_groups=true&rank_token=b66b8315-8421-427b-a9c8-c99a894775b6';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_my_statistic(){
            $url      = 'https://i.instagram.com/api/v1/ads/graphql/?locale=tr_TR&vc_policy=insights_policy&surface=account';
            $post_var = [
                'variables' => '{"query_params":{"access_token":"","id":"7573271439"},"timezone":"Asia/Bahrain"}',
                'doc_id'    => '1706456729417729',
            ];
            $json     = $this->post($url, $post_var);
            $json     = json_decode($json['body']);

            return $json;
        }

        public function get_my_notification(){
            $url  = 'https://i.instagram.com/api/v1/news/inbox/?mark_as_seen=false&timezone_offset=10800&push_disabled=true';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_my_pending_inbox(){
            $url  = 'https://i.instagram.com/api/v1/direct_v2/pending_inbox/?visual_message_return_type=unseen&persistentBadging=true&push_disabled=true';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_my_inbox(){
            $url  = 'https://i.instagram.com/api/v1/direct_v2/inbox/?visual_message_return_type=unseen&thread_message_limit=100&persistentBadging=true&limit=20&push_disabled=true&fetch_reason=manual_refresh';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_my_followers(){
            $user_id = $this->get_user_id();

            $url  = 'https://i.instagram.com/api/v1/friendships/'.$user_id.'/followers/?includes_hashtags=true&search_surface=follow_list_page&query=&enable_groups=true&rank_token=4c6947e0-bebe-4f69-a7bf-24be28dc4990';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_my_following(){
            $user_id = $this->get_user_id();

            $url  = 'https://i.instagram.com/api/v1/friendships/'.$user_id.'/following/?includes_hashtags=true&search_surface=follow_list_page&query=&enable_groups=true&rank_token=4c6947e0-bebe-4f69-a7bf-24be28dc4990';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_user_followers($username = null){
            $user_id = $this->get_user_id($username);

            $url  = 'https://i.instagram.com/api/v1/friendships/'.$user_id.'/followers/?includes_hashtags=true&search_surface=follow_list_page&query=&enable_groups=true';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_user_following($username = null){
            $user_id = $this->get_user_id($username);

            $url  = 'https://i.instagram.com/api/v1/friendships/'.$user_id.'/following/?includes_hashtags=true&search_surface=follow_list_page&query=&enable_groups=true&rank_token=4c6947e0-bebe-4f69-a7bf-24be28dc4990';
            $json = $this->get($url);
            $json = json_decode($json['body']);

            return $json;
        }

        public function get_multiple_accout_detected($username = null){

            $username = $username ?? $this->username;
            $user_id  = $this->get_user_id($username);

            $cache = $this->cache('users/multiple-account-'.$user_id);
            if(!$cache){
                if($user_id != null){
                    $url  = 'https://i.instagram.com/api/v1/multiple_accounts/get_featured_accounts/?target_user_id='.$user_id;
                    $json = $this->get($url);
                    $json = json_decode($json['body']);
                    $this->cache('users/multiple-account-'.$user_id, $json);
                    return $json;
                }
            }
            else{
                return $cache;
            }

            return false;
        }

        public function set_my_status($status_text = 'I love ', $emoji = '😍'){
            $url      = 'https://i.instagram.com/api/v1/status/set_status/';
            $post_var = [
                'status_type' => 'manual',
                'expires_at'  => strtotime('+1 days'),
                'text'        => $status_text,
                '_uuid'       => '0ff470bf-e663-4a1d-a327-38603a77a1bc',
                'emoji'       => $emoji,
                'audience'    => 'mutual_follows',
            ];

            $json = $this->post($url, $post_var);
            $json = json_decode($json['body']);

            return $json;
        }

        public function set_status_reply($status_id = 'I love ', $thread_id = '', $message = 'Status Message'){
            $url      = 'https://i.instagram.com/api/v1/direct_v2/threads/broadcast/status_reply/';
            $post_var = [
                'status_id'        => $status_id,
                'thread_id'        => $thread_id,
                'action'           => 'send_item',
                'is_shh_mode'      => 0,
                'thread_ids'       => '['.$thread_id.']',
                'send_attribution' => '',
                'client_context'   => '6875500892161881179',
                'reply_type'       => 'text',
                'device_id'        => $this->get_android_id(),
                'mutation_token'   => '6875500892161881179',
                '_uuid'            => $this->get_device_id(),
                'reply'            => $message,
                'status_key'       => 0,
                'status_author_id' => $this->get_user_id(),
            ];

            $json = $this->post($url, $post_var);
            $json = json_decode($json['body']);

            return $json;
        }

    }
