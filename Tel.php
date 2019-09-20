<?php


class Telegram
{
    const API_URL = 'https://api.telegram.org/bot';

    protected $chat_id;

    protected $token;

    protected $message;



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


    public function keyboard($keyboard,$text)
    {
        $keyboard = json_encode($keyboard);

        if(strpos($text,"\n")) {
            $text = urlencode($text);
        }

        $this->request('SendMessage',[
            'chat_id' => $this->chat_id,
            'text' => $text,
            'parse_mode'=>'Markdown',
            'reply_markup' => $keyboard 
        ]);
    }



    public function sendMessage(string $message)
    {
        
        $this->request('sendMessage',[
            'chat_id' => $this->chat_id,
            'text' => $message,
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

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
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

$telegram->setToken('');

//echo $telegram->setWebhook('https://rustemovv96.000webhostapp.com/');

$data = $telegram->getData();

$sections = [
    'Rəsmi Xronika' => 'official_chronicle',
    'Rəsmi Sənədlər' => 'official_documents',
    'Siyasət' => 'politics',
    'İqtisadiyyat' => 'economy',
    'Regionlar' => 'regionlar',
    'Cəmiyyət' => 'society',
    'Elm və Təhsil' => 'science_and_education',
    'Mədəniyyət' => 'culture',
    'İdman' => 'sports',
    'Dünya' => 'world',
    'Qan Yaddaşı' => 'bloody_memory',
];


if(strtolower($data->text) === '/start') {
    $keyboard = [
        ['Rəsmi Xronika', 'Rəsmi Sənədlər'],
        ['Siyasət', 'İqtisadiyyat'],
        ['Regionlar', 'Cəmiyyət'],
        ['Elm və Təhsil', 'Mədəniyyət'],
        ['İdman', 'Dünya', 'Qan Yaddaşı'],
    ];

    $key = [
        'keyboard' => $keyboard,
        'resize_keyboard' => true,
    ];

    $telegram->keyboard($key, 'Xəbər bölməsini seçin');
} 
else {
    $client = $data->text;

    if(array_key_exists($client,$sections)) {
        $section = $sections[$client];
        //code
        $telegram->sendMessage($section);
    } else {
        $telegram->sendMessage('Bölmə mövcud deyil');
    }
}
