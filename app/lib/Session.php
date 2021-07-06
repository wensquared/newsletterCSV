<?php 

namespace app\lib;
class Session
{
    private static $instanz = NULL;

    private function __construct(){ }

    public static function init(){
        if(is_null(self::$instanz)){
            self::$instanz = new Session;
            self::sessionStart();
        }
        return self::$instanz;   
    }

    private static function sessionStart(){
        if(session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    public function setLoginData(){ 
        $_SESSION['login'] = $_SERVER['REMODE_ADDR'].' '.$_SERVER['HTTP_USER_AGENT'];
    }

    public function checkLoginData(){
         return $_SESSION['login'] == $_SESSION['login'] = $_SERVER['REMODE_ADDR'].' '.$_SERVER['HTTP_USER_AGENT'] ? true : false;
    }

    public function __set($key,$value){
        $_SESSION[$key] = $value;
    }

    public function __get($key){
        return $_SESSION[$key] ?? '';
    }

    public function sessionDelete(){
        $this->instanz = NULL;
        session_destroy();  
        $_SESSION = [];  
    }

    public function setCsrf(){
        $_SESSION['_token'] =  bin2hex(random_bytes(32));
        return $_SESSION['_token'];
    }

    public function getCsrf(){
        return $_SESSION['_token'] ?? false;
    }
}
