<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 11:26
 */

namespace He110\Coral\Bot;


use He110\Coral\Bot\Controller\ProductController;
use He110\Coral\Bot\Controller\UserController;
use He110\Coral\Bot\Entity\ProductOffer;
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
        $controller = new UserController($application);
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

    /**
     * Пользователь ввел
     *
     * @param Application $application
     * @throws Exception\UnknownEventException
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function getCountryList(Application &$application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();

        $user = $application->getUser();
        $controller = new UserController($application);

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

    /**
     * Пользователь нажал на кнопку выбора страны
     *
     * @param Application $application
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
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

    public function getOffer(Application &$application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();
        $user = $application->getUser();

        $controller = new ProductController($application);
        if ($offer = $controller->getOfferById($application->getContent(), $user->getCurrency() ?? 'RUB')) {
            $bot->sendChatAction($application->getChatId(), 'upload_photo');


            $keyboardMarkup = array(
                array(
                    array(
                        'text' => 'В магазин',
                        'url' => $offer->getLink()
                    ),
                ),
            );
            $keyboard = new InlineKeyboardMarkup($keyboardMarkup);

            $m = $bot->sendPhoto($application->getChatId(), $offer->getThumbnail(), $controller->renderOffer($offer), null, $keyboard);
            $user->setOption('lastOfferCode', $offer->getCode())
                ->setOption('lastMessageId', $m->getMessageId());
            if ($manager = $application->getDataManager())
                $manager->save($user->getId(), $user->toArray());
        } else {
            $bot->sendMessage($application->getChatId(), 'Не удалось найти товар по артикулу. Ищу по названию...');
            $application->triggerEvent(Application::EVENT_SEARCH);
        }
    }

    public function search(Application &$application)
    {
        /** @var BotApi $bot */
        $bot = $application->getService();
        $user = $application->getUser();

        $controller = new ProductController($application);

        if ($list = $controller->search($application->getContent(), $user->getCurrency() ?? 'RUB')) {
            switch (count($list)) {
                case 0:
                    $bot->sendMessage($application->getChatId(), 'По вашему запросу ничего не найдено');
                    break;
                case 1:
                    /** @var ProductOffer $offer */
                    $offer = current($list);
                    $application->setContent($offer->getCode());
                    $application->triggerEvent(Application::EVENT_GET_OFFER);
                    break;
                default:
                    $list = array_slice($list, 0, 10);

                    $keyboardMarkup = array();

                    $chunks = array_chunk($list, 1);
                    foreach($chunks as $chunk) {
                        $list = array();
                        foreach($chunk as $offer) {
                            /** @var ProductOffer $offer */
                            $list[] = array(
                                'text' => $controller->buildOfferName($offer),
                                'callback_data' => '!offer='.$offer->getCode()
                            );
                        }
                        $keyboardMarkup[] = $list;
                    }

                    $keyboard = new InlineKeyboardMarkup($keyboardMarkup);
                    $bot->sendMessage($application->getChatId(), 'Пожалуйста, выберите продукт', 'html', null, false, $keyboard);
                    break;
            }
        }
    }
}