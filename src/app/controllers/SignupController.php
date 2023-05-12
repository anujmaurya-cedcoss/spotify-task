<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;
use App\Controllers\IndexController;

session_start();
class SignupController extends Controller
{
    public function indexAction()
    {
        // redirected to view
    }

    public function addAction()
    {
        $arr = array(
            'mail' => $this->escaper->escapeHTML($this->request->getPost('email')),
            'pass' => $this->escaper->escapeHTML($this->request->getPost('password')),
        );
        if ($_POST['repassword'] == $_POST['password']) {
            $this->db->execute(
                "INSERT INTO `users`(`email`, `password`)
                VALUES ('$arr[mail]', '$arr[pass]')"
            );
            $this->response->redirect('signup/login');
        } else {
            $this->response->redirect('signup/');
        }
    }
    public function loginAction()
    {
        // redirected to view
    }
    public function findloginAction()
    {
        if (isset($_POST)) {
            $arr = array(
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password')
            );
            $user = $this->db->fetchAll(
                "SELECT * FROM `users` WHERE `email` = '$arr[email]' AND `password` = '$arr[password]'",
                    \Phalcon\Db\Enum::FETCH_ASSOC
            );
            if (isset($user[0])) {
                $_SESSION['user'] = $user[0]['id'];
                if ($user[0]['spotify_access_token'] != '') {
                    $this->response->redirect('/signup/registerUser/');
                } else {
                    $this->response->redirect('/signup/updateToken/');
                }
            } else {
                $this->response->redirect('/signup/login/');
            }
        }
    }

    public function registerUserAction()
    {
        $token = IndexController::getTokenAction();

        $url = "https://accounts.spotify.com/authorize";
        $client_id = "14c192325c77402c87924cc33cdd3370";
        $url .= "?client_id=$client_id&response_type=code&redirect_uri=http://localhost:8080/index/searchHome/";
        $this->response->redirect($url);
    }
    public function updateTokenAction()
    {
        $token = IndexController::getTokenAction();
        $accToken = $token['access_token'];
        $upd = "UPDATE `users` SET `spotify_access_token` = \"$accToken\" WHERE `id` = $_SESSION[user]";
        $res = $this->db->execute($upd);
        if ($res) {
            $this->response->redirect('/index/searchHome');
        } else {
            echo "Oops! There was some error, try again";
            echo $this->tag->linkTo(
                ['/signup/login/', ('Try Again'), 'class' => 'btn btn-secondary col-4 mb-5 px-5']
            );
            die;
        }
    }
    public function logoutAction()
    {
        session_unset();
        session_destroy();
        $this->response->redirect('/signup/');
    }
}