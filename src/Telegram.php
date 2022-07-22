<?php
/** @noinspection PhpUnused */

namespace Srustamov\Telegram;

use JsonException;

class Telegram
{
    public const PARSE_MODE_TEXT = 'text';

    public const PARSE_MODE_HTML = 'HTML';

    public const PARSE_MODE_MARKDOWNV2 = 'MarkdownV2';

    public const PARSE_MODE_MARKDOWN = 'Markdown';

    protected int $chat_id;

    protected string $token;

    public function __construct(array $config = [])
    {
        if (isset($config['chat_id'])) {
            $this->setChatId($config['chat_id']);
        }
        if (isset($config['token'])) {
            $this->setToken($config['token']);
        }
    }

    public static function create(array $config = []): self
    {
        return new static($config);
    }

    public static function channel(TelegramChannelInterface $channel): self
    {
        return self::create()->setChatId(
            $channel->getChatId()
        )->setToken($channel->getToken());
    }


    public function createChannel(string $channel): Telegram
    {
        return self::channel(new $channel());
    }

    public static function send($message, string $parseMode = self::PARSE_MODE_TEXT): bool|string
    {
        return self::create()->sendMessage($message, $parseMode);
    }


    /**
     * @throws JsonException
     */
    public function getData()
    {
        $data = json_decode(
            file_get_contents('php://input'),
            false,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->chat_id = $data->message->chat->id;

        return $data->message;
    }


    public function setToken(string $token): Telegram
    {
        $this->token = $token;

        return $this;
    }


    public function setChatId(int $id): Telegram
    {
        $this->chat_id = $id;

        return $this;
    }


    public function setWebhook(string $url): bool|string
    {
        return $this->request('setWebhook', [
            'url' => $url,
        ]);
    }


    public function deleteWebhook(string $url): bool|string
    {
        return $this->request('deleteWebhook', [
            'url' => $url,
        ]);
    }

    public function sendMessage(array|string $message, string $parseMode = self::PARSE_MODE_TEXT): bool|string
    {
        return $this->request(
            'sendMessage',
            [
                'chat_id' => $this->chat_id,
                'text'    => $message,
            ] + ($parseMode === self::PARSE_MODE_TEXT ? [] : ['parse_mode' => $parseMode])
        );
    }


    public function sendPhoto(string $url, string $caption = ''): bool|string
    {
        return $this->request('sendPhoto', [
            'chat_id' => $this->chat_id,
            'photo'   => $url,
            'caption' => $caption,
        ]);
    }


    public function sendVideo(string $url, string $caption = ''): bool|string
    {
        return $this->request('sendVideo', [
            'chat_id' => $this->chat_id,
            'video'   => $url,
            'caption' => $caption,
        ]);
    }


    public function request(string $method, array $data): bool|string
    {
        $ch = curl_init();

        $url = 'https://api.telegram.org/bot' . $this->token . '/' . $method;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new TelegramException(curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }
}
