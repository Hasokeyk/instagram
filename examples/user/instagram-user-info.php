<?php

    use Hasokeyk\Instagram\Instagram;

    require "../../vendor/autoload.php";

    $username = 'username';
    $password = 'password';

    $instagram = new Instagram($username, $password);
    $instagram->login->login();

    $login = $instagram->login->login_control();
    if($login){

        //USER INFO
        $me = $instagram->user->get_user_info('hasokeyk');
        print_r($me);
        //USER INFO

    }
    else{
        echo 'Login Fail';
    }
