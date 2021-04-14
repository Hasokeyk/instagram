<?php
    
    use instagram\instagram;
    
    require "../../vendor/autoload.php";
    
    $username = 'username';
    $password = 'password';
    
    $instagram = new instagram($username,$password);
    $instagram->login->login();
    
    $login = $instagram->login->login_control();
    if($login){
    
        //INBOX SEND TEXT
        $user = $instagram->user->send_inbox_text('yazilimvegirisim','Hi! How are you?');
        print_r($user);
        //INBOX SEND TEXT
        
        //INBOX SEND IMAGE
        $file_path = 'image.jpg';
        $user = $instagram->user->send_inbox_photo('yazilimvegirisim',$file_path);
        print_r($user);
        //INBOX SEND IMAGE
        
        //INBOX SEND LIKE
        $user = $instagram->user->send_inbox_like('yazilimvegirisim');
        print_r($user);
        //INBOX SEND LIKE
        
    }else{
        echo 'Login Fail';
    }
