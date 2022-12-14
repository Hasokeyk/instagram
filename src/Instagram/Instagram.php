<?php

    namespace Hasokeyk\Instagram;

    use Hasokeyk\Instagram\InstagramLogin;
    use Hasokeyk\Instagram\InstagramMedias;
    use Hasokeyk\Instagram\InstagramRegister;
    use Hasokeyk\Instagram\InstagramRequest;
    use Hasokeyk\Instagram\InstagramSmartEvents;
    use Hasokeyk\Instagram\InstagramStatistics;
    use Hasokeyk\Instagram\InstagramUpload;
    use Hasokeyk\Instagram\InstagramUser;

    class Instagram{

        public $username = null;
        public $password = null;

        public \Hasokeyk\Instagram\InstagramRequest $request;
        public \Hasokeyk\Instagram\InstagramMedias $medias;
        public \Hasokeyk\Instagram\InstagramUser $user;

        public function __construct($username = null, $password = null){

            $this->username = $username;
            $this->password = $password;

            $this->request = $this->InstagramRequest();
            $this->medias  = $this->InstagramMedias();
            $this->user    = $this->InstagramUser();

        }

        private function InstagramRequest(): InstagramRequest{
            return new InstagramRequest($this->username, $this->password);
        }

        private function InstagramMedias(): InstagramMedias{
            return new InstagramMedias($this->username, $this->password, $this->request);
        }

        private function InstagramUser(): InstagramUser{
            return new InstagramUser($this->username, $this->password, $this->request);
        }

    }
