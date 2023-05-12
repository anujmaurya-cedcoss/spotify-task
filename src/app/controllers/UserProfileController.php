<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;
use App\Controllers\IndexController;

session_start();

class UserProfileController extends Controller
{
    public function indexAction()
    {
        $data = $this->getUserDetails();
        echo "<pre>";
        print_r($data); die;
    }

    public function getUserDetails()
    {
        $url = "https://api.spotify.com/v1/me";
        $token = IndexController::getTokenAction();
        $accToken = $token['access_token'];
        // $accToken = "BQDQ3tkeSs8vWjXMNx-csuAMQUnFvFX_LuLpnPhU08s8-FHqLcbdAgprAJoSfEdnnxmSKrchHljJz7O6fGGvFIfa3pQMOlH4o_dE3Im649Njum5Cbdw3O-uxC6UsX6gov_gqer1nHqUNQjp3kr6kvtzFobHDRsNNIvR4OkK7_FkutE8C8B1tCKo6YUoCB2VF8WpmaPQ";
        // $url .= "?token=$accToken";
        // echo $url; die;
        $ch = curl_init();
        $header = ["Authorization: Bearer $accToken"];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $result;
    }
}