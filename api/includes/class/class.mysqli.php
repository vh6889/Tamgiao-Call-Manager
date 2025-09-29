<?php

    class DB_mysqli {

        function __construct($config){
              $this->connect($config);
        }

        function connect($config){

            $this->mysqli = @new mysqli($config['host'].':'.$config['port'],$config['user'],$config['pass'],$config['name']);

            if($this->mysqli->connect_error){
                die("Không thể kết nối đến cơ sở dữ liệu.". $this->mysqli->connect_error);
            }

            $this->mysqli->query("SET NAMES 'utf8'");
            $this->mysqli->query("SET CHARACTER SET utf8");
            $this->mysqli->query("SET COLLATION_CONNECTION = 'utf8_general_ci'");

        }

        function disconnect(){
            if(!$this->mysqli->connect_error)
                $this->mysqli->close();
        }

        public function query($sql){

            $this->result = $this->mysqli->query($sql);
            return $this;
        }

        public function fetch($result = NULL){
            if($result == NULL){
                if ($this->result instanceof mysqli_result)
                    return $this->result->fetch_assoc();
                return NULL;
            }

            return $result->result->fetch_assoc();
        }

        function fetch_array($result = NULL){
            $array =array();
            if($result == NULL){
                if ($this->result instanceof mysqli_result)
                    while($r = $this->fetch()){
                        $array[] = $r;
                    }
            } else{
                while($r = $this->fetch($result->result)){
                    $array[] = $r;
                }
            }
            if($this->result)
            	$this->result->close();
            
            return $array;
        }

        function exec_query($sql){
            $this->query($sql);

            if($this->mysqli->affected_rows > 0)
                return $this->mysqli->affected_rows;
            else
                return false;
        }
        function insert_id(){
            return $this->mysqli->insert_id;
        }
        function num_rows($result = NULL){
            if($result == NULL){
                if ($this->result instanceof mysqli_result)
                    return $this->result->num_rows;
            }
            return $result->result->num_rows;
        }

        function real_escape_string($text){
            return $this->mysqli->real_escape_string($text);
        }

        function __destruct(){
            $this->disconnect();
        }
    }


?>