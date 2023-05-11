<?php
namespace App\Controllers;
use Phalcon\Mvc\Controller;
use App\Controllers\IndexController;

session_start();

class ShowfavController extends Controller
{
    public function indexAction()
    {
        // show the user's playlist
        $user = 1;
        $sql = $this->db->fetchAll(
            "SELECT * FROM `favorites` where `user_id` = $user",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );

        $output = "<tr><td></td></tr>";

        foreach ($sql as $key => $value) {
            $data = $this->getData($value['type'], $value['spotify_id']);

        }
        // die;
    }

    public function getData($type, $id) {
        $token = IndexController::getTokenAction();
        $accToken = $token['access_token'];
        $url = "https://api.spotify.com/v1/$type/$id";
        $ch = curl_init();
        $header = ["Authorization: Bearer $accToken"];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch), true);
        echo "<pre>";
        print_r($result); die;
        curl_close($ch);
    }
}
