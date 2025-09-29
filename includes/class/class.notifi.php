<?php

    class Notifi {

        private $user;
        private $pass;
        function __construct($config){
            include dirname(__FILE__).'/lib/swift_required.php';
            $this->user = $config['user'];
            $this->pass = $config['pass'];
        }

        public function send($to,$from,$subject,$body) {
            
            if(empty($this->user) || empty($this->pass))
                return false;
            
            try {
                $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
                $transport->setUsername('inuhaha006@gmail.com');
                $transport->setPassword('trangtrang');
                $swift = Swift_Mailer::newInstance($transport);

                $message = Swift_Message::newInstance($subject);
                $message->setBody($body, 'text/html');
                $message->setFrom($from);
                $message->setTo($to);

                if (!$swift->send($message)) {
                    return false;
                } else {
                    return true;
                }
            } catch(Exception $e) {
               return false;
            }

        }
    }


?>