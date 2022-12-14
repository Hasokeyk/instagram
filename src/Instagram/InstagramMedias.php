<?php

    namespace Hasokeyk\Instagram;

    class InstagramMedias extends InstagramRequest{

        public $username;
        public $password;
        public $functions;

        function __construct($username, $password, $functions){
            parent::__construct($username, $password, $functions);

            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }

        public function get_post_likes($post_id = null){
            if($post_id != null){
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/likers/';

                $json = $this->get($url);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function get_permalink_by_post_id($post_id = null){
            if($post_id != null){
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/permalink/?share_to_app=share_sheet';

                $json = $this->get($url);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function get_user_posts($username = null){

            $cache = $this->cache('users/'.$username.'-posts');
            if($cache === false){
                $post_hashquery = $this->get_post_queryhash();
                $user_id        = $this->functions->user->get_user_id($username);
                $url            = 'https://www.instagram.com/graphql/query/?query_hash='.$post_hashquery.'&variables={"id":"'.$user_id.'","first":500}';
                $json           = $this->get($url);
                $json           = json_decode($json['body'])->data->user;

                $this->cache('users/'.$username.'-posts', $json);

                $result = $json;
            }
            else{
                $result = $cache;
            }

            return $result;
        }

        public function like($post_id){
            if($post_id != null){
                $url       = 'https://i.instagram.com/api/v1/media/'.$post_id.'/like/';
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

                $json = $this->post($url, $post_data);
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

                $json = $this->post($url, $post_data);
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

                $json = $this->post($url, $post_data);
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

                $json = $this->post($url, 'POST', $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function create_collection($name = null, $post_id = null){
            if($post_id != null){
                $url = 'https://i.instagram.com/api/v1/collections/create/';

                $post_data = [
                    'module_name'     => 'feed_contextual_profile',
                    'added_media_ids' => '["'.$post_id.'"]',
                    'name'            => $name,
                    '_uid'            => $this->functions->user->get_user_id(),
                    '_uuid'           => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function get_collection($colection_id = null){
            if($colection_id != null){
                $url  = 'https://i.instagram.com/api/v1/feed/collection/'.$colection_id.'/all/?include_clips_subtab=true&clips_subtab_first=true&include_collection_info=true';
                $json = $this->get($url);
                $json = json_decode($json['body']);
                return $json;
            }

            return false;
        }

        public function edit_collection($colection_id = null, $new_name = null){
            if($colection_id != null){
                $url = 'https://i.instagram.com/api/v1/collections/'.$colection_id.'/edit/';

                $post_data = [
                    'added_collaborator_ids' => '[]',
                    'name'                   => $new_name,
                    '_uid'                   => $this->functions->user->get_user_id(),
                    '_uuid'                  => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);
                return $json;
            }

            return false;
        }

        public function del_collection($colection_id = null){
            if($colection_id != null){
                $url = 'https://i.instagram.com/api/v1/collections/'.$colection_id.'/delete/';

                $post_data = [
                    '_uid'  => $this->functions->user->get_user_id(),
                    '_uuid' => $this->get_guid(),
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];

                $json = $this->post($url, $post_data);
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

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }#

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

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function get_comment_post($post_id = null){
            if($post_id != null){
                $url = 'https://i.instagram.com/api/v1/media/'.$post_id.'/comments/';

                $json = $this->get($url);
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

                $json = $this->post($url, $post_data);
                $json = json_decode($json['body']);

                return $json;
            }

            return false;
        }

        public function share_photo($image_path = null, $desc = null){
            $upload_id = $this->functions->upload->photo_upload($image_path);
            if($upload_id !== false){
                $url       = 'https://i.instagram.com/api/v1/media/configure/';
                $post_data = [
                    "scene_capture_type"         => "",
                    "timezone_offset"            => "10800",
                    "_csrftoken"                 => $this->get_csrftoken(),
                    "media_folder"               => "Camera",
                    "source_type"                => "4",
                    "_uid"                       => $this->functions->user->get_user_id(),
                    "device_id"                  => $this->get_device_id(),
                    "_uuid"                      => $this->get_guid(),
                    "creation_logger_session_id" => "cf4fea1e-a304-44d9-af56-62914c9d728e",
                    "caption"                    => $desc,
                    "upload_id"                  => $upload_id,
                    "multi_sharing"              => "1",
                    "device"                     => [
                        "manufacturer"    => "Google",
                        "model"           => "google+Pixel+2",
                        "android_version" => 22,
                        "android_release" => "5.1.1",
                    ],
                    "edits"                      => [
                        "crop_original_size" => [
                            640,
                            480,
                        ],
                        "crop_center"        => [
                            0,
                            -0,
                        ],
                        "crop_zoom"          => 1.3333334,
                    ],
                    "extra"                      => [
                        "source_width"  => 640,
                        "source_height" => 480,
                    ],
                ];
                $post_data = ['signed_body' => 'SIGNATURE.'.json_encode($post_data)];
                $json      = $this->post($url, $post_data);
                return json_decode($json['body']);
            }

            return false;
        }

        public function get_stories($username = null){
            $username = $this->functions->user->get_user_id($username ?? $this->username);
            $url      = 'https://i.instagram.com/api/v1/feed/user/'.$username.'/story/?supported_capabilities_new=%5B%7B%22name%22%3A%22SUPPORTED_SDK_VERSIONS%22%2C%22value%22%3A%22114.0%2C115.0%2C116.0%2C117.0%2C118.0%2C119.0%2C120.0%2C121.0%2C122.0%2C123.0%2C124.0%2C125.0%2C126.0%2C127.0%2C128.0%2C129.0%2C130.0%22%7D%2C%7B%22name%22%3A%22FACE_TRACKER_VERSION%22%2C%22value%22%3A%2214%22%7D%2C%7B%22name%22%3A%22segmentation%22%2C%22value%22%3A%22segmentation_enabled%22%7D%2C%7B%22name%22%3A%22COMPRESSION%22%2C%22value%22%3A%22ETC2_COMPRESSION%22%7D%2C%7B%22name%22%3A%22world_tracker%22%2C%22value%22%3A%22world_tracker_enabled%22%7D%2C%7B%22name%22%3A%22gyroscope%22%2C%22value%22%3A%22gyroscope_enabled%22%7D%5D';
            $json     = $this->get($url);
            $json     = json_decode($json['body']);

            return $json;
        }

        public function get_my_story_seen_list($media_id = null){

            if($media_id != null){
                $url  = 'https://i.instagram.com/api/v1/media/'.$media_id.'/list_reel_media_viewer/?supported_capabilities_new=[{"name":"SUPPORTED_SDK_VERSIONS","value":"114.0,115.0,116.0,117.0,118.0,119.0,120.0,121.0,122.0,123.0,124.0,125.0,126.0,127.0,128.0,129.0,130.0"},{"name":"FACE_TRACKER_VERSION","value":"14"},{"name":"segmentation","value":"segmentation_enabled"},{"name":"COMPRESSION","value":"ETC2_COMPRESSION"},{"name":"world_tracker","value":"world_tracker_enabled"},{"name":"gyroscope","value":"gyroscope_enabled"}]';
                $json = $this->get($url);
                $json = json_decode($json['body']);
                return $json;
            }

            return false;
        }

        public function get_tag_info($tag = null){

            $url  = 'https://i.instagram.com/api/v1/tags/'.urldecode($tag).'/info/';
            $json = $this->get($url);
            return json_decode($json['body']);

        }

        public function get_tag_post_recent($tag = null){

            $url       = 'https://i.instagram.com/api/v1/tags/'.urldecode($tag).'/sections/';
            $post_data = [
                'tab'                => 'recent',
                '_uuid'              => $this->get_device_id(),
                'include_persistent' => true
            ];
            $json      = $this->post($url, $post_data);
            return json_decode($json['body']);

        }

        public function get_tag_post_reels($tag = null){

            $url       = 'https://i.instagram.com/api/v1/tags/'.urldecode($tag).'/sections/';
            $post_data = [
                'tab'                => 'clips',
                '_uuid'              => $this->get_device_id(),
                'include_persistent' => true
            ];
            $json      = $this->post($url, $post_data);
            return json_decode($json['body']);

        }

        public function get_tag_post_tops($tag = null){

            $url       = 'https://i.instagram.com/api/v1/tags/'.urldecode($tag).'/sections/';
            $post_data = [
                'tab'                => 'top',
                '_uuid'              => $this->get_device_id(),
                'include_persistent' => true
            ];
            $json      = $this->post($url, $post_data);
            return json_decode($json['body']);

        }

        public function get_tag_post_all_tab($tag = null){

            $url       = 'https://i.instagram.com/api/v1/tags/'.urldecode($tag).'/sections/';
            $post_data = [
                'supported_tabs'     => '["top","recent","clips"]',
                '_uuid'              => $this->get_device_id(),
                'include_persistent' => true
            ];
            $json      = $this->post($url, $post_data);
            return json_decode($json['body']);

        }


    }
