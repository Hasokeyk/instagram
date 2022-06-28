<?php

    namespace Hasokeyk\Instagram;

    class InstagramRegister extends InstagramRequest{

        public $username;
        public $password;
        public $functions;

        function __construct($username, $password, $functions){
            parent::__construct($username, $password, $functions);

            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;

        }

    }
