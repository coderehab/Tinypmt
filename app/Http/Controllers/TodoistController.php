<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use cURL;
use Illuminate\Http\Request;
use Redirect;

use GuzzleHttp\Client;

class TodoistController extends Controller
{

    public function test() {

        return redirect::to('https://todoist.com/oauth/authorize?client_id=de27417420bf4d14881b239ed8506e1d&scope=data:read_write&state=dce0445f47794e51ad85b70090524cb9');
    }

    public function authorized(Request $request){
        /*$client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://todoist.com',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        $response = $client->request('POST', '/oauth/access_token', [
            "form_params" => [
                "client_id"=>"de27417420bf4d14881b239ed8506e1d" ,
                "client_secret"=>"dce0445f47794e51ad85b70090524cb9" ,
                "code"=> $request->input('code')
            ]
        ]);

        $token = json_decode($response->getBody()->getContents())->access_token;
        var_dump($token);*/

        $client = new Client();
        $response = $client->request('POST', 'https://todoist.com/API/v6/sync', [
            "form_params" => [
                "token"=>"008d2b4c885c1cfa2519476394b4df431320971f",
                "seq_no"=>"0" ,
                "resource_types"=> '["all"]'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents());

        echo "<pre>";
        //print_r($data->Items);
        echo "</pre>";

        $projects = array_values(array_sort($data->Projects, function($value){
            $value = (array) $value;
            return $value['item_order'];
        }));

        $vdata = array();
        $vdata['projects'] = $projects;
        $vdata['items'] = $data->Items;

        return response()->view('todoist', $vdata);
    }
}
