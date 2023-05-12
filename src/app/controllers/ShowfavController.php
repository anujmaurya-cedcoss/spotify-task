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
        $user = $_SESSION['user'];
        $sql = $this->db->fetchAll(
            "SELECT * FROM `favorites` where `user_id` = $user",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );

        $output = "<tr><td>";
        foreach ($sql as $key => $value) {
            $data = $this->getData($value['type'], $value['spotify_id']);
            $image = $data['images'][0]['url'];
            $singer = $data['artists'][0]['name'];
            $title = $data['name'];
            $type = $value['type'];
            $output .= "<tr>
                <td><img src = \"$image\"/ height = 50px></td>
                <td>$title</td>
                <td>$singer</td>
                <td>$type</td>
                <td><a type = 'submit' href = '/showfav/delete?id=$value[id]' class = 'btn btn-danger'>Delete</a></td>
            </tr>";
        }
        $output .= "</td></tr>";
        $this->view->data = $output;
    }

    public function getData($type, $id)
    {
        $token = IndexController::getTokenAction();
        $accToken = $token['access_token'];
        $url = "https://api.spotify.com/v1/$type/$id";
        $ch = curl_init();
        $header = ["Authorization: Bearer $accToken"];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $result;
    }

    public function deleteAction() {
        $id = $_GET['id'];
        $sql = $this->db->execute(
            "DELETE FROM `favorites` where `id` = $id",
        );
        $this->response->redirect('/showfav/');
    }
}
