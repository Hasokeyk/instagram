<?php

namespace Hasokeyk\Instagram;

class Instagram
{
    public $functions = [];
    public $request = null;
    public $login = null;
    public $register = null;
    public $user = null;
    public $statistics = null;
    public $medias = null;
    public $smart = null;
    public $upload = null;

    public function __construct($username = null, $password = null)
    {
        $this->functions = (object)[
            'request' => new InstagramRequest($username, $password, $this->functions),
            'login' => new InstagramLogin($username, $password, $this->functions),
            'register' => new InstagramRegister($username, $password, $this->functions),
            'statistics' => new InstagramStatistics($username, $password, $this->functions),
            'user' => new InstagramUser($username, $password, $this->functions),
            'upload' => new InstagramUpload($username, $password, $this->functions),
            'medias' => new InstagramMedias($username, $password, $this->functions),
            'smart' => new InstagramSmartEvents($username, $password, $this->functions),
        ];

        $this->request = new InstagramRequest($username, $password, $this->functions);
        $this->login = new InstagramLogin($username, $password, $this->functions);
        $this->register = new InstagramRegister($username, $password, $this->functions);
        $this->user = new InstagramUser($username, $password, $this->functions);
        $this->statistics = new InstagramStatistics($username, $password, $this->functions);
        $this->upload = new InstagramUpload($username, $password, $this->functions);
        $this->medias = new InstagramMedias($username, $password, $this->functions);
        $this->smart = new InstagramSmartEvents($username, $password, $this->functions);
    }
}
