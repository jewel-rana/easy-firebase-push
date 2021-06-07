<?php

namespace Rajtika\Firebase\Services;

use Illuminate\Support\Facades\Config;

class Firebase
{
    protected static $fcmUrl;
    protected static $batchUrl;
    protected static $API_KEY;
    protected static $sound;
    protected static $type;
    protected static $ids;
    protected static $topic;
    protected static $config;
    protected static $params;
    protected static $batchID;
    protected static $payload;

    public function __construct()
    {
        self::init();
    }

    private static function init()
    {
        self::$config = config()->get('firebase');
        self::$batchUrl = self::$config['batch_url'];
        self::$fcmUrl = self::$config['fcm_url'];
        self::$API_KEY = self::$config['api_key'];
        self::$sound = true;
        self::$type = 'notification';
        self::$payload = 'notification';
    }

    private static function getHeader()
    {
//        dd(self::$API_KEY);
        return [
            'Authorization: key=' . self::$API_KEY,
            'Content-Type: application/json'
        ];
    }

    public static function to($ids)
    {
        self::$ids = $ids;
        return new static;
    }

    public static function sound($sound = true)
    {
        self::$sound = $sound;
        return new static;
    }

    //set the ID
    public static function setID($id = '')
    {
        self::$params['ID'] = $id;
        return new static;
    }

    //set the title
    public static function setTitle($title = '')
    {
        self::$params['title'] = $title;
        return new static;
    }

    //set notification body
    public static function setBody($body = '')
    {
        self::$params['body'] = $body;
        return new static;
    }

    public static function setImage($image = null)
    {
        self::$params['image'] = $image;
        return new static;
    }

    //set notification body
    public static function setData(array $data)
    {
        self::$params = $data;
        return new static;
    }

    public static function setType($type = 'notification')
    {
        self::$type = $type;
        return new static;
    }

    public static function setPayload($payload = 'notification')
    {
        self::$payload = $payload;
        dd(self::$payload);
        return new static;
    }

    public static function setTopic($topic)
    {
        self::$topic = $topic;
        self::$type = 'topic';
        return new static;
    }

    private static function get()
    {
        return [
            'to' => self::$ids,
            self::$payload => [
                'id' => self::$params['ID'],
                'title' => self::$params['title'],
                'body' => self::$params['body'],
                'image' => self::$params['image'] ?? null,
                'type' => self::$type
            ]
        ];
    }

    private static function topic()
    {
        return [
            'to' => '/topics/' . self::$batchID,
            'notification' => [
                'id' => self::$params['ID'],
                'title' => self::$params['title'],
                'body' => self::$params['body']
            ]
        ];
    }

    public static function setNotificationKey($batchID)
    {
        self::$batchID = $batchID;
    }

    public static function generateKey($ID, $length = 30)
    {
        $len = strlen(( string )$ID);
        $length = $length - $len;
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        self::$batchID = $str . $ID;

        return self::$batchID;
    }

    private static function validate()
    {
        $params = array_diff_key(array_flip([
            'ID',
            'title',
            'body'
        ]), self::$params);

        if($params){
            dd(ucwords(implode(', ', array_keys($params))) . ' is missing in your params.');
        }

        $configKeys = array_diff_key(array_flip([
            'api_key',
            'fcm_url',
            'batch_url'
        ]), self::$config);

        if($configKeys){
            dd(ucwords(implode(', ', array_keys($configKeys))) . ' is missing in your firebase configuration file.');
        }

        return true;
    }

    public static function send($payload = 'notification')
    {
        self::$payload = $payload;
        if(self::validate() && self::$ids) {
            // Open connection
            $ch = curl_init();

            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, self::$fcmUrl);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, self::getHeader());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode((self::$type == 'topic') ? self::topic() : self::get()));

            // Execute post
            $result = curl_exec($ch);

            // Close connection
            curl_close($ch);

            return $result;
        }
    }

    public static function subscribe()
    {
        if(self::$batchID && self::$ids && self::$batchUrl) {
            $fields = array(
                'to' => '/topics/' . self::$batchID,
                'registration_tokens' => self::$ids
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$batchUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, self::getHeader());
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
        return false;
    }

    public static function dump()
    {
        dd('dumping from firebase');
    }
}
