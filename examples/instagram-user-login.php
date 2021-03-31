<?php
    
    use instagram\instagram;
    
    require "../vendor/autoload.php";
    
    $username = 'username';
    $password = 'password';
    
    $instagram = new instagram($username,$password);
    $login = $instagram->login->login();
    if($login){
        echo 'Login success';
    }else{
        echo 'Login Fail';
    }
    
    //LOGIN CONTROL
    $login_control = $instagram->login->login_control();
    if($login_control){
        echo 'Login True';
    }else{
        echo 'Login False';
    }
    //LOGIN CONTROL
