<?php

namespace Rajtika\Firebase;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FirebaseController extends Controller
{
    public $fcp;
    public $batchApiUrl;
    public $notification_key;
    public $API_KEY;
    public $ID;
    public $title;
    public $body;
    public $moredata;
    public $sound;
    public $type;
    public $toOne;
    public $toMany;
    public $headers;
    public $topic;

    public function __construct()
    {
        $this->batchApiUrl = 'https://iid.googleapis.com/iid/v1:batchAdd';
        $this->fcp = 'https://fcm.googleapis.com/fcm/send';
    	$this->API_KEY = ( env('FIREBASE_API_KEY') ) ? env('FIREBASE_API_KEY') : config('firebase_api_key');

        //set headers
        $this->headers = array (
            'Authorization: key=' . $this->API_KEY,
            'Content-Type: application/json'
        );

        $this->sound = true;
    }

    public function sound( $sound = true )
    {
    	$this->sound = $sound;
    }

    //set the ID
    public function setID( $notification_id = '' )
    {
        $this->id = $notification_id;
    }

    //set the title
    public function setTitle( $title = '' )
    {
        $this->title = $title;
    }

    //set notification body
    public function setBody( $body = '' )
    {
        $this->body = $body;
    }

    public function setType( $type = 'notification' )
    {
        $this->type = $type;
    }

    public function setTopic( $topic )
    {
        $this->topic = $topic;
    }

    public function getSingle()
    {
        return [
            'to' => $this->toOne,
            'data' => [
                'title' => $this->title,
                'body' => $this->body
            ]
        ];
    }

    public function toOne( $id )
    {
        $this->toOne = $id;
    }

    public function toMany( $ids )
    {
        $this->toMany = $ids;
    }

    public function getMultiple()
    {
        return [
            'to' => $this->toMany,
            'notification' => [
                'title' => $this->title,
                'body' => $this->body
            ]
        ];
    }

    public function getTopic()
    {
        return [
            'to' => '/topic/' . $this->topic,
            'notification' => [
                'id' => $this->ID,
                'title' => $this->title,
                'body' => $this->body
            ]
        ];
    }

    public function generateKey( $groupId, $length = 30 )
    {
        $len = strlen( ( string ) $groupId );
        $length = $length - $len;
        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        $this->notification_key = $str . $groupId;

        return $this->notification_key;
    }

    public function send()
    {
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->fcp);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // Disabling SSL Certificate support temporarly
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->getSingle()));

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

    public function sendMultiple()
    {
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->fcp);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->getMultiple()));

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

    public function sendTopic()
    {
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->fcp);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->getTopic()));

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

    private function _curl()
    {
        
    }

    public function batchAdd( $regIds )
    {

        $fields = array(
            'to' => '/topics/'.$this->notification_key,
            'registration_tokens' => $regIds
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->batchApiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute Post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        return $result;
    }
}
