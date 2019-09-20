<?php  namespace Bot;


class Telegram
{
    const API_URL = 'https://api.telegram.org/bot';

    protected $chat_id;

    protected $token;


    /**
     * @return mixed
     */
    public function getData()
    {
        $data =  json_decode(file_get_contents('php://input'));

        $this->chat_id = $data->message->chat->id;

        return $data->message;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken(string $token)
    {
        $this->token = $token;
        return $this;
    }


    /**
     * @param $id
     * @return $this
     */
    public function setChatId($id)
    {
        $this->chat_id = $id;
        return $this;
    }


    /**
     * @param $url
     * @return bool|string
     */
    public function setWebhook($url)
    {
        return $this->request('setWebhook',[
            'url' => $url
        ]);
    }


    /**
     * @param $url
     * @return bool|string
     */
    public function deleteWebhook($url)
    {
        return $this->request('deleteWebhook',[
            'url' => $url
        ]);
    }

    /**
     * @param string $message
     * @return bool|string
     */
    public function sendMessage(string $message)
    {
        $this->request('sendMessage',[
            'chat_id' => $this->chat_id,
            'text' => $message
        ]);
    }


    /**
     * @param string $url
     * @param string $caption
     * @return bool|string
     */
    public function sendPhoto($url,$caption = '')
    {
        return $this->request('sendPhoto',[
            'chat_id' => $this->chat_id,
            'photo'=> $url,
            'caption' => $caption
        ]);
    }


    /**
     * @param string $url
     * @param string $caption
     * @return bool|string
     */
    public function sendVideo($url,$caption = '')
    {
        return $this->request('sendVideo',[
            'chat_id' => $this->chat_id,
            'video'=> $url,
            'caption' => $caption
        ]);
    }


    /**
     * @param string $method
     * @param array $data
     * @return bool|string
     */
    public function request(string $method, array $data)
    {
        $ch = curl_init();

        $url = self::API_URL.$this->token.'/'.$method;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = [];
        array_push($headers,'Content-Type: application/x-www-form-urlencoded');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }



}


$telegram = new Telegram();

$telegram->setToken('824775211:AAFMzeKu5AKn-iYK-iNI7y51Tm_Gyvw5Vvk');



echo $telegram->setWebhook('https://telegram.azertag.az');


// $message = $telegram->getData();

// switch(strtolower($message->text))
// {
//     case '/resmi_xronika':
//         $telegram->sendMessage('resmi xronika xeberleri');
//         break;
//     default:
//         $telegram->sendMessage('I can\'t answer this question yet');
//         break;
// }



/*

$response = json_decode(
    $telegram
    //channel id
    ->setChatId('@samir_test')
    //text message
    ->sendMessage('https://azertag.az/xeber/Turkiye_vetendaslari_bu_gunden_Azerbaycana_vizasiz_gele_bilecekler-1324269')
    //send photo
    //->sendPhoto('https://mymodernmet.com/wp/wp-content/uploads/2017/11/Afro-art-creative-soul-photography-6.jpg','this is caption')
    //send video (max size 50mb)
    //->sendVideo('https://video.azertag.az/files/video/2019/3/15673244521372824311.mp4', 'this is caption')

 ?? '{}');

if(
    json_last_error() == JSON_ERROR_NONE &&
    (empty((array)$response) || $response->ok === true)
) 
{
   //echo 'success';
    
} else {
   // echo 'error';
}

 */
