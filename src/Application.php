<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 10:21
 */

namespace He110\Coral\Bot;

use He110\Coral\Bot\Entity\User;
use He110\Coral\Bot\Exception\UnknownEventException;
use He110\Coral\Bot\Interfaces\DataManager;
use He110\Coral\Bot\Service\ProductHelper;
use Psr\Log\LoggerInterface;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

class Application extends ProductHelper
{
    use EventHandlers;

    /** @var LoggerInterface|null */
    private $logger;

    /** @var string */
    private $event;

    /** @var string */
    private $token;

    /** @var Client */
    private $service;

    /** @var string|null */
    private $content = null;

    /** @var int|null */
    private $chatId;

    /** @var User|null */
    private $user;

    /** @var DataManager */
    private $dataManager;

    const EVENT_LOGIN = 'login';
    const EVENT_SEARCH = 'search';
    const EVENT_SET_COUNTRY = 'setCountry';
    const EVENT_SET_CURRENCY = 'setCurrency';
    const EVENT_GET_COUNTRY_LIST = 'getCountryList';
    const EVENT_GET_PRODUCT = 'getProduct';
    const EVENT_GET_OFFER = 'getOffer';

    public function __construct(\string $token)
    {
        $this->token = $token;
        $this->service = new Client($token);
    }

    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface|null $logger
     * @return Application
     */
    public function setLogger(?LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return Client
     */
    public function getService(): Client
    {
        return $this->service;
    }

    /**
     * @return string|null
     */
    private function getContent(): ?string
    {
        return $this->content;
    }

    public function run(): void
    {
        $this->getService()->command('start', function(Message $message) {
             $this->setEvent(self::EVENT_LOGIN);
             $this->fetchDataFromMessage($message);
        });

        $this->getService()->on(function(Update $update) {
            $message = $update->getMessage();
            $this->fetchDataFromMessage($message);

            if ($this->isOfferCode($this->getContent()))
                $this->setEvent(self::EVENT_GET_OFFER);
            elseif (is_null($this->getUser()))
                $this->setEvent(self::EVENT_LOGIN);
            elseif (!is_null($this->getUser()) && is_null($this->getUser()->getCountry()))
                $this->setEvent(self::EVENT_GET_COUNTRY_LIST);
            elseif (!is_null($this->getUser()) && !is_null($this->getUser()->getCountry()))
                $this->setEvent(self::EVENT_SEARCH);
        }, function (Update $update) { return true; });

        $this->getService()->callbackQuery(function(CallbackQuery $query) {
            $message = $query->getMessage();
            $this->fetchDataFromMessage($message);

            preg_match('/\!(.*?)=(.*)/', $query->getData(), $commandMatch);
            if (!isset($commandMatch[1]) || !isset($commandMatch[2]))
                return;

            list($command, $value) = array_slice($commandMatch, 1);
            $this->content = $value;

            switch ($command) {
                case 'product':
                    $this->setEvent(self::EVENT_GET_PRODUCT);
                    break;
                case 'offer':
                    $this->setEvent(self::EVENT_GET_OFFER);
                    break;
                case 'country':
                    $this->setEvent(self::EVENT_SET_COUNTRY);
                    break;
                case 'currency':
                    $this->setEvent(self::EVENT_SET_CURRENCY);
                    break;
                default:
                    break;
            }
        });

        $this->getService()->run();

        if (is_null($this->getEvent()) || !method_exists($this, $this->getEvent()))
            throw new UnknownEventException("Got an unknown event");

        $this->{$this->getEvent()}($this);
    }

    private function fetchDataFromMessage(Message $message): void
    {
        $this->chatId = $message->getChat()->getId();
        $this->content = $message->getText();
    }

    /**
     * @return int|null
     */
    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }


    /**
     * @return DataManager
     */
    public function getDataManager(): DataManager
    {
        return $this->dataManager;
    }

    /**
     * @param DataManager $dataManager
     * @return Application
     */
    public function setDataManager(DataManager $dataManager): self
    {
        $this->dataManager = $dataManager;
        return $this;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     * @return Application
     */
    public function setEvent(string $event): self
    {
        $this->event = $event;
        return $this;
    }
}