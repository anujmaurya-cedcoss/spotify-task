<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;

session_start();

class IndexController extends Controller
{
    public function indexAction()
    {
        // redirected to view
    }
    public function searchHomeAction()
    {
        // redirected to view
    }

    public function searchAction()
    {
        $author = $this->request->getPost('name');
        $author = str_replace(' ', '%20', $author);
        $input .= "?q=$author&type=";
        $found = false;
        if (isset($_POST['album'])) {
            $found = true;
            $input .= "album";
        }

        if (isset($_POST['playlist'])) {
            if ($found) {
                $found = true;
                $input .= ",";
            }
            $found = true;
            $input .= "playlist";
        }

        if (isset($_POST['track'])) {
            if ($found) {
                $found = true;
                $input .= ",";
            }
            $found = true;
            $input .= "track";
        }

        if (isset($_POST['show'])) {
            if ($found) {
                $found = true;
                $input .= ",";
            }
            $found = true;
            $input .= "show";
        }

        if (isset($_POST['artist'])) {
            if ($found) {
                $found = true;
                $input .= ",";
            }
            $found = true;
            $input .= "artist";
        }

        if (isset($_POST['episode'])) {
            if ($found) {
                $found = true;
                $input .= ",";
            }
            $found = true;
            $input .= "episode";
        }

        $endpoint = "https://api.spotify.com/v1/search";
        $endpoint .= $input;
        $_SESSION['search'] = $endpoint;
        $this->response->redirect('index/getResults');
    }


    public function getResultsAction()
    {

        $token = $this->getTokenAction();
        $accToken = $token['access_token'];

        $url = $_SESSION['search'];
        $ch = curl_init();
        $header = ["Authorization: Bearer $accToken"];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $output = "";
        $output .= $this->tag->linkTo(
            ['showfav', "Show Favorites", 'class' => 'm-1 btn btn-success btn-lg float-right']
        );
        foreach ($result as $type => $items) {
            // give id separately
            $output .= "<h1>$type</h1>
            <div class=\"card align-items-center row\" style='margin-inline: auto; padding:auto;'>";
            $output .= "<div class = 'row'>";
            foreach ($items['items'] as $value) {
                $id = "";
                if ($type == 'tracks' || $type == 'albums') {
                    $id = $value['id'];
                } else {
                    $id = $value['artists'][0]['id'];
                }
                $output .= "<div class=\"align-items-center card col-3 p-2 border-success  m-3\">
                <div class=\"card-header bg-transparent border-success\">Content</div>
                <div class=\"card m-3\">
                <span class = 'h5 m-2'>Singer :
                <a href = " . $value['artists'][0]['href'] . " class = 'h5'>" .
                    $value['artists'][0]['name'] . "</a></span>
                <div class=\"card-body text-success\">
                <img class=\"card-img-top col-12\" src=" .
                    $value['images'][0]['url'] . $value['album']['images'][0]['url'] . ">
                  <h5 class=\"card-title\">$value[name]</h5>
                </div></div>
                <div class = 'card-footer bg-transparent'> <h6>Release Date : $value[release_date]</h6>
                <h6>Type : $value[type]</h6>";
                $output .= $this->tag->linkTo(
                    ['index/add' . "?type=$type&id=" . $id, "Add $type", 'class' => 'm-1 p-0 btn btn-success']
                );
                $output .= "<a class = 'btn p-0 btn-info' target=”_blank” href = " .
                    $value['external_urls']['spotify'] . ">Open on Spotify</a>
                </div></div>";
            }
            $output .= "</div></div>";
        }
        $this->view->data = $output;
    }

    public function getTokenAction()
    {
        $ch = curl_init();
        $url = "https://accounts.spotify.com/api/token";
        $client_id = "14c192325c77402c87924cc33cdd3370";
        $client_secret = "decd9879ce3848ebbaf8bb75deb9b749";
        $header = ["Content-Type: application/x-www-form-urlencoded"];
        $data = http_build_query([
            "grant_type" => "client_credentials",
            "client_id" => $client_id,
            "client_secret" => $client_secret
        ]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return json_decode(curl_exec($ch), true);
    }

    public function addAction()
    {
        if (!isset($_SESSION['user'])) {
            $this->response->redirect('signup/login');
        } else {
            $user_id = $_SESSION['user'];
            $type = $_GET['type'];
            $spotify_id = $_GET['id'];
            $spotify = new \Favorites();
            $spotify->spotify_id = $spotify_id;
            $spotify->type = $type;
            $spotify->user_id = $user_id;
            $spotify->save();
            $this->response->redirect('/showfav/');
        }
    }
}