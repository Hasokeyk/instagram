<?php

    namespace Hasokeyk\Instagram;

    class Instagram{

        public InstagramRequest     $request;
        public InstagramLogin       $login;
        public InstagramRegister    $register;
        public InstagramUser        $user;
        public InstagramStatistics  $statistics;
        public InstagramMedias      $medias;
        public InstagramSmartEvents $smart;
        public InstagramUpload      $upload;

        public function __construct($username = null, $password = null){
            $this->request    = new InstagramRequest($username, $password, $this);
            $this->login      = new InstagramLogin($username, $password, $this);
            $this->register   = new InstagramRegister($username, $password, $this);
            $this->user       = new InstagramUser($username, $password, $this);
            $this->statistics = new InstagramStatistics($username, $password, $this);
            $this->upload     = new InstagramUpload($username, $password, $this);
            $this->medias     = new InstagramMedias($username, $password, $this);
            $this->smart      = new InstagramSmartEvents($username, $password, $this);
        }
    }
