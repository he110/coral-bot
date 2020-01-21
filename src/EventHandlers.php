<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 11:26
 */

namespace He110\Coral\Bot;


use He110\Coral\Bot\Entity\User;
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
    public function start(Application $application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();
        $bot->sendChatAction($application->getChatId(), 'typing');
        sleep(Application::PAUSES);

        if ($application->getUser() && $application->getDataManager()) {
            $application->getDataManager()->reset($application->getUser()->getId());
        }

        $bot->sendMessage($application->getChatId(), 'Добро пожаловать! Пожалуйста, введите ваш клубный номер');
    }

    public function login(Application $application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();
        $bot->sendChatAction($application->getChatId(), 'typing');
        sleep(Application::PAUSES);

        //@TODO(Илья Зобенько): Провести валидацию номера через API
        $isValid = is_numeric($this->getContent());

        if ($isValid) {
            $user = new User();
        }

        $bot->sendMessage($application->getChatId(), 'Получен код: '.$application->getContent());
    }
}