<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 11:26
 */

namespace He110\Coral\Bot;


use TelegramBot\Api\BotApi;

trait EventHandlers
{
    /**
     * Обработчик события, возникающего при обращении неавторизованного пользователя
     *
     * @param Application $application
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function login(Application $application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();
        $bot->sendMessage($application->getChatId(), 'Проверка связи');
    }
}