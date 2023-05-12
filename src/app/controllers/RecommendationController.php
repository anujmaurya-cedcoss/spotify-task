<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;
use App\Controllers\IndexController;

class RecommendationController extends Controller
{
    public function indexAction()
    {
        $data = $this->getRecommendationAction();
        echo "<pre>";
        $output = "<hr><h1>Recommendations</h1>";
        foreach ($data as $datas) {
            $output .= "<hr>
            <div class = 'd-flex row'>";
            foreach ($datas as $type => $value) {
                $output .= "<div class=\"col-4 card bg-light mb-3\" style=\"max-width: 18rem;\">
                <div class=\"card-header\"><h5>".$value['album']['artists'][0]['name']."</h3></div>
                <div class=\"card-body\">
                <img src = ".$value['album']['images'][0]['url']." alt = 'img'style=\"max-width: 15rem;\">
                  <h5 class=\"card-title\">".$value['album']['name']."</h5>
                  <p class=\"card-text\">Release Date :".$value['album']['release_date']."</p>
                </div>
                <a target=\"_blank\" href =".$value['album']['external_urls']['spotify'].
                    " class = 'btn btn-danger'>Play
                </a>
              </div>";
            }
            $output .= "</div>";
            break;
        }
        $this->view->rec = $output;
        $this->dispatcher->forward(['controller' => 'index','action' => 'searchHome', 'rec' => $output]);
    }

    public function getRecommendationAction() {
        $url = "https://api.spotify.com/v1/recommendations";
        $s_artist = "4NHQUGzhtTLFvgF5SZesLK";
        $s_genre = "classical,country";
        $s_track = "0c6xIDDpzE81m2q797ordA";

        $token = IndexController::getTokenAction();
        $accToken = $token['access_token'];

        $url .= "?seed_artists=$s_artist&seed_genres=$s_genre&seed_tracks=$s_track";
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
