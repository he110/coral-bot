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
use TelegramBot\Api\Types\Chat;
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

    protected $baseUrl;

    const PAUSES = 1;

    const EVENT_START = 'start';
    const EVENT_LOGIN = 'login';
    const EVENT_SEARCH = 'search';
    const EVENT_SET_COUNTRY = 'setCountry';
    const EVENT_SET_CURRENCY = 'setCurrency';
    const EVENT_GET_COUNTRY_LIST = 'getCountryList';
    const EVENT_GET_PRODUCT = 'getProduct';
    const EVENT_GET_OFFER = 'getOffer';

    public function __construct(string $token, string $baseUrl)
    {
        $this->token = $token;
        $this->baseUrl = $baseUrl;
        $this->service = new Client($token);
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
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
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Application
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function isUserAuthorized(): bool
    {
        return !is_null($this->getUser()) && $this->getUser()->isAuthorized();
    }

    public function run(): void
    {
        $app = &$this;

        $this->getService()->command('start', function(Message $message) use ($app) {
            $app->setEvent(Application::EVENT_START);
            $app->fetchDataFromMessage($message);
        });

        $this->getService()->on(function(Update $update) use ($app) {
            if (!$message = $update->getMessage())
                return;
            $app->fetchDataFromMessage($message);

            if ($app->isUserAuthorized() && $app->getUser()->getCountry() && $app->isOfferCode($app->getContent()))
                $app->setEvent(Application::EVENT_GET_OFFER); //Если авторизован, выбрана страна и пришло число - ищет по артикулу
            elseif (!$this->isUserAuthorized())
                $app->setEvent(Application::EVENT_LOGIN); // Если не авторизован, отправляем в на авторизацию
            elseif ($this->isUserAuthorized() && is_null($app->getUser()->getCountry()))
                $app->setEvent(Application::EVENT_GET_COUNTRY_LIST); //Если авторизован, но не выбрал страну, отправляем на выбор стран
            elseif ($this->isUserAuthorized() && !is_null($app->getUser()->getCountry()))
                $app->setEvent(Application::EVENT_SEARCH); // Если авторизован и выбрал страну, но пришло не число, значит что-то ищет
        }, function (Update $update) { return true; });

        $this->getService()->callbackQuery(function(CallbackQuery $query) use ($app) {
            $message = $query->getMessage();

            $app->fetchDataFromMessage($message);

            preg_match('/\!(.*?)=(.*)/', $query->getData(), $commandMatch);
            if (!isset($commandMatch[1]) || !isset($commandMatch[2]))
                return;

            list($command, $value) = array_slice($commandMatch, 1);
            $app->setContent($value);

            switch ($command) {
                case 'product':
                    $app->setEvent(Application::EVENT_GET_PRODUCT);
                    break;
                case 'offer':
                    $app->setEvent(Application::EVENT_GET_OFFER);
                    break;
                case 'country':
                    $app->setEvent(Application::EVENT_SET_COUNTRY);
                    break;
                case 'currency':
                    $app->setEvent(Application::EVENT_SET_CURRENCY);
                    break;
                default:
                    break;
            }
        });

        $this->getService()->run();

        if (is_null($this->getEvent()))
            throw new UnknownEventException("Got an unknown event");

        $this->triggerEvent($this->getEvent());
    }

    public function triggerEvent(string $event)
    {
        if (!method_exists($this, $event))
            throw new UnknownEventException("Can't find handler for event ".$event);
        $this->{$event}($this);
    }

    public function fetchDataFromMessage(Message $message): void
    {
        $this->chatId = $message->getChat()->getId();
        $this->content = $message->getText();
        if ($this->getDataManager() && is_null($this->getUser())) {
            $user = new User();
            if ($userData = $this->getDataManager()->load($message->getFrom()->getId())) {
                $user->fromArray($userData);
                $this->user = $user;
            }
            $user->setId($message->getFrom()->getId())
                ->setName(trim($message->getFrom()->getFirstName()." ".$message->getFrom()->getLastName()));
            $this->getDataManager()->save($message->getFrom()->getId(), $user->toArray());
            $this->user = $user;

        }
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
     * @return DataManager|null
     */
    public function getDataManager(): ?DataManager
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