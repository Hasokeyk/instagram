<?php
    
    use instagram\instagram;
    
    require "vendor/autoload.php";
    
    $username = 'username';
    $password = 'password';
    
    $instagram = new instagram($username,$password);
    
    $login = $instagram->login->login_control();
    if($login){
    
        $user_posts = $instagram->user->get_user_posts();
        print_r($user_posts);
        
    }else{
        echo 'Login Fail';
    }
