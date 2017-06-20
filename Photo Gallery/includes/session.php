<?php
/*
A class to help work with sessions
Primarily to manage logging users in and output_add_rewrite_var
Generally inadvisable to store DB related objects here
*/
class Session
{
    private $logged_in = false;
    public $user_id;

    function __construct()
    {
        session_start();
        $this->check_login();
    }

    public function is_logged_in()
    {
        return $this->logged_in;
    }

    public function login($user)
    {
        //database should find the user based on username/password
        if($user)
        {
            $this->user_id = $_SESSION['user_id'] = $user->id;
            $this->logged_in = true;
        }
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($this->user_id);
        $this->logged_in = false;
    }

    private function check_login()
    {
        if(isset($_SESSION['user_id']))
        {
            $this->user_id = $_SESSION['user_id'];
            $this->logged_in = true;
        } else{
            unset($this->user_id);
            $this->logged_in = false;
        }
    }
}

$session = new Session();
?>