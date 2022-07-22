<?php

namespace Srustamov\Telegram;

interface TelegramChannelInterface
{
    public function getToken() :string;

    public function getChatId() :int;
}
