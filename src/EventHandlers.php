<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 11:26
 */

namespace He110\Coral\Bot;


use He110\Coral\Bot\Controller\UserController;
use He110\Coral\Bot\Entity\User;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

trait EventHandlers
{
    /**
     * Обработчик события, возникающего при обращении неавторизованного пользователя
     *
     * @param Application $application
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function start(Application &$application)
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

    public function login(Application &$application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();
        $bot->sendChatAction($application->getChatId(), 'typing');
        $user = $application->getUser();
        $controller = new UserController($application->getBaseUrl(), $user->getCountry() ?? 'RU');
        $isValid = $controller->validate($application->getContent());

        if ($isValid) {
            $user->setMember($application->getContent());
            if ($manager = $application->getDataManager())
                $manager->save($user->getId(), $user->toArray());
            $application->triggerEvent(Application::EVENT_GET_COUNTRY_LIST);
        } else {
            $bot->sendMessage($application->getChatId(), "Для продолжения необходимо ввести корректный клубный номер");
        }
    }

    public function getCountryList(Application &$application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();

        $user = $application->getUser();
        $controller = new UserController($application->getBaseUrl(), $user->getCountry() ?? 'RU');

        if ($countries = $controller->countryList()) {

            $keyboardMarkup = array();
            if (count($countries) > 1) {
                $chunks = array_chunk($countries, 3);
                foreach($chunks as $chunk) {
                    $list = array();
                    foreach($chunk as $code => $country) {
                        $list[] = array(
                            'text' => $country['NAME'],
                            'callback_data' => '!country='.$country['ALPHA_2']
                        );
                    }
                    $keyboardMarkup[] = $list;
                }
                $keyboard = new InlineKeyboardMarkup($keyboardMarkup);
                $bot->sendMessage($application->getChatId(), 'Пожалуйста, выберите страну', false, null, false, $keyboard);
            } else {
                $country = current($countries);
                $bot->sendMessage($application->getChatId(), 'Страна выбрана автоматически: '.$country['NAME']);
                $bot->sendMessage($application->getChatId(), 'Все готово! Введите артикул товара или поисковой запрос для начала работы');
                $user->setCountry($country['ALPHA_2']);
                if ($manager = $application->getDataManager())
                    $manager->save($user->getId(), $user->toArray());
            }
        } else {
            $bot->sendMessage($application->getChatId(), 'Не удалось загрузить список стран. Пожалуйста, повторите попытку позже');
            $application->triggerEvent(Application::EVENT_START);
        }
    }

    public function setCountry(Application &$application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();
        $user = $application->getUser();

        $user->setCountry($application->getContent());
        if ($manager = $application->getDataManager())
            $manager->save($user->getId(), $user->toArray());

        $bot->sendMessage($application->getChatId(), 'Все готово! Введите артикул товара или поисковой запрос для начала работы');
    }
}