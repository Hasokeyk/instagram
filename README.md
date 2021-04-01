[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

<!-- PROJECT LOGO -->
<br />
<p align="center">
<a href="https://github.com/hasokeyk/instagram/">
<img src="https://cdn.cdnlogo.com/logos/i/4/instagram.svg" alt="Logo" width="80" height="80" />
</a>

<h3 align="center">Hasokeyk / Instagram</h3>

<p align="center">
    Bu PHP kütüphanesi ile instagram mobil uygulamasının tüm özelliklerini kullanabilirsiniz.
    <br />
    <a href="#">Demo</a>
    ·
    <a href="https://github.com/hasokeyk/instagram/issues">Geri Bildirim</a>
</p>

<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary><h2 style="display: inline-block">Başlıklar</h2></summary>
  <ol>
    <li>
      <a href="#proje-hakkında">Proje Hakkında</a>
    </li>
    <li>
      <a href="#kullanmaya-başlayın">Kullanmaya Başlayın</a>
      <ul>
        <li><a href="#gereksinimler">Gereksinimler</a></li>
        <li><a href="#kurulum">Kurulum</a></li>
      </ul>
    </li>
    <li><a href="#kullanım">Kullanım</a></li>
    <li><a href="#yol-haritası">Yol Haritası</a></li>
    <li><a href="#katkı-sağlayanlar">Katkı Sağlayanlar</a></li>
    <li><a href="#lisans">Lisans</a></li>
    <li><a href="#iletişim">İletişim</a></li>
    <li><a href="#donation">Bağış Yapın</a></li>
  </ol>
</details>

## Proje Hakkında

Bu proje instagram mobil uygulamasının kabiliyetlerini PHP kütüphanesinde kullanabilmek amacıyla yapılmıştır. Mobil
uygulamadaki sorguların birebir kopyalanarak instagram sunucularına sorgu yapıp cevapları almaktadır.

<!-- GETTING STARTED -->

## Kullanmaya Başlayın

Lütfen burayı dikkatle okunuyun.

### Gereksinimler

- Bilgisayarınızda "composer" uygulaması kurulu olması gerekmektedir. Kurulum için https://getcomposer.org/download/
- PHP 7.4 ve üstü

### Kurulum

## Composer ile kurulum

* Çalışma klasörünüzü belirledikten sonra o klasörde terminal açıp aşağıdaki komutu yazıp entere basın.
  ```sh
  composer require hasokeyk/instagram
  ```

## Repoyu indirerek kullanma

1. İlk önce repoyu indirin
   ```sh
   git clone https://github.com/hasokeyk/instagram.git
   ```
2. Gerekli kütüphaneleri indirmek için aşağıdaki komutu kullanın.
   ```sh
   composer install
   ```

<!-- USAGE EXAMPLES -->

## Örnek Kodlar

# Login işlemi

Her işlemden önce kullanıcı girişi yapmalısınız. 1 Kere giriş yaptıktan sonra sistem önbelleğe alacaktır ve bundan
sonraki işlemleriniz daha hızlı bir şekilde çalışacaktır.

```php
<?php

    use instagram\instagram;
    
    require "/vendor/autoload.php";
    
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

```

# Kullanıcı Paylaşımlarını Getirme

Aşağıdaki kodları çalıştırğınızda giriş yaptığınız kullanıcının son 50 paylaşımını getireceksiniz. Başka birinin
paylaşımlarını getirmek için get_user_posts('hasokeyk') yazmanız yeterlidir.

```php
<?php
    
    use instagram\instagram;
    
    require "../vendor/autoload.php";
    
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

```

<!-- ROADMAP -->

## Yol Haritası

## Kullanıcı İşlemleri

| İşlemler  | Çalışıyor | Örnek Dosya |
| ------------- | ------------- | ------------- |
| Kullanıcı Girişi  | :heavy_check_mark: | [instagram-user-login.php](https://github.com/Hasokeyk/instagram/blob/main/examples/instagram-user-login.php) | 
| Giriş Yapmış Kullanıcı Bilgisi Getirme  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Giriş Yapmış Kullanıcı İstatistik Getirme  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Profil Resmi Değiştirme  | :heavy_check_mark: | [instagram-user-change-profil-pic.php](https://github.com/Hasokeyk/instagram/blob/main/examples/instagram-user-change-profil-pic.php) |
| Kullanıcı Takip Etme  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Kullanıcı Takipten Çıkma  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Kullanıcı İstatistikleri  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |

## Paylaşım İşlemleri

| İşlemler  | Çalışıyor | Örnek Dosya |
| ------------- | ------------- | ------------- |
| Paylaşım Getirme  | :heavy_check_mark: | [instagram-user-get-posts.php](https://github.com/Hasokeyk/instagram/blob/main/examples/instagram-user-get-posts.php) |
| Paylaşım İstatistikleri Getirme  | :heavy_check_mark: | [instagram-user-get-posts-statistics.php](https://github.com/Hasokeyk/instagram/blob/main/examples/instagram-user-get-posts-statistics.php) |
| Görsel Paylaşma  | :x: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Video Paylaşma  | :x: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Story Paylaşma  | :x: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Carousel Paylaşma  | :x: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Paylaşım Beğenme  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Paylaşım Beğenmekten Çıkma  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |

## Mesajlaşma İşlemleri

| İşlemler  | Çalışıyor | Örnek Dosya |
| ------------- | ------------- | ------------- |
| Yazı Olarak Mesaj Atma  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Link Olarak Mesaj Atma  | :x: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Kalp Atma  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |
| Görsel Atma  | :heavy_check_mark: | [HAZIRLANIYOR](https://github.com/Hasokeyk/instagram/tree/main/examples) |

<!-- CONTRIBUTING -->

## Katkı Sağlayanlar

<!-- LICENSE -->

## Lisans

Bu proje geliştirme aşamasında olduğu sürece indirebilir ve kullanabilirsiniz. Başka amaçlar için kullanılırsa bu
kodları yazan kişinin sorumluluğu bulunmamaktadır. Bu projeyi indirip kullanıdığınızda bunu kabul etmiş sayılırsınız.


## Bağış Yapın

patreon: HASOKEYK


## İletişim

Hasan Yüksektepe - [INSTAGRAM](https://instagram/hasokeyk)

Web Sitem : [https://hayatikodla.net](https://hayatikodla.net)

[contributors-shield]: https://img.shields.io/github/contributors/hasokeyk/instagram.svg?style=for-the-badge

[contributors-url]: https://github.com/hasokeyk/instagram/graphs/contributors

[forks-shield]: https://img.shields.io/github/forks/hasokeyk/instagram.svg?style=for-the-badge

[forks-url]: https://github.com/hasokeyk/instagram/network/members

[stars-shield]: https://img.shields.io/github/stars/hasokeyk/instagram.svg?style=for-the-badge

[stars-url]: https://github.com/hasokeyk/instagram/stargazers

[issues-shield]: https://img.shields.io/github/issues/hasokeyk/instagram.svg?style=for-the-badge

[issues-url]: https://github.com/hasokeyk/instagram/issues

[license-shield]: https://img.shields.io/github/license/hasokeyk/instagram.svg?style=for-the-badge

[license-url]: https://github.com/hasokeyk/instagram/blob/master/LICENSE.txt

[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555

[linkedin-url]: https://www.linkedin.com/in/hasan-yuksektepe/
