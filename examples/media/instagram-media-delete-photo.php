<?php

use Hasokeyk\Instagram\Instagram;

require "../../vendor/autoload.php";

$username = 'username';
$password = 'password';

$instagram = new Instagram($username, $password);
$instagram->login->login();

$login = $instagram->login->login_control();
if ($login) {
    //DELETE PHOTO
    $post_id = '123456';
    $users = $instagram->medias->del_photo($post_id);
    print_r($users);
//DELETE PHOTO
} else {
    echo 'Login Fail';
}
