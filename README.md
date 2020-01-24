# CoralBot [![Build Status](https://travis-ci.com/he110/coral-bot.svg?branch=master)](https://travis-ci.com/he110/coral-bot) [![Latest Stable Version](https://img.shields.io/packagist/v/he110/coral-bot.svg)](https://packagist.org/packages/he110/coral-bot)


Corporate telegram-bot designed for Coral Club. Allows to access CCI Online Shop content easily

## Installation

Install the latest version with

```bash
$ composer require he110/coral-bot
```

## Usage

### Basic usage

```php
<?php

    use He110\Coral\Bot\Application;
   
    $baseDomain = 'example.domain'; //Base domain, which'll be extended with country code ( us.example.domain ) 
    
    $application = new Application('<YOUR-TELEGRAM-BOT-TOKEN>', $baseDomain);
    $application->run();

```

### Session saving

This section is extremely important to keep user's session. If you won't build your bot in this way, every request
will be processed like from new user.

```php
<?php

    use He110\Coral\Bot\Application;
    use He110\Coral\Bot\Service\JsonDataManager;
   
    $baseDomain = 'example.domain'; //Base domain, which'll be extended with country code ( us.example.domain ) 
    $manager = new JsonDataManager("path-to-your/json-data.json");
    
    $application = new Application('<YOUR-TELEGRAM-BOT-TOKEN>', $baseDomain);
    $application->setDataManager($manager)
        ->run();

```

### Logging

You can use any PSR-3 loggers. (example: [Monolog](https://packagist.org/packages/monolog/monolog))

```php
<?php

    use He110\Coral\Bot\Application;
    use He110\Coral\Bot\Service\JsonDataManager;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
   
    $logger = new Logger('loggerName');
    $logger->pushHandler(new StreamHandler('path/to/your.log', Logger::DEBUG));
    
    $baseDomain = 'example.domain'; //Base domain, which'll be extended with country code ( us.example.domain ) 
    $manager = new JsonDataManager("path-to-your/json-data.json");
    
    $application = new Application('<YOUR-TELEGRAM-BOT-TOKEN>', $baseDomain);
    $application->setDataManager($manager)
        ->setLogger($logger)
        ->run();

```

### Custom Event Handlers

#### Events

You can use these events to automate your bot dialogues:

* **start** - On command /start (`Application::EVENT_START`)

* **login** - When user enters their member id (`Application::EVENT_LOGIN`)

* **search** - When user is authorized and entered text message (`Application::EVENT_SEARCH`)

* **setCountry** - When user clicked on country button (`Application::EVENT_SET_COUNTRY`)

* **setCurrency** - When user clicked on currency button (`Application::EVENT_SET_CURRENCY`)

* **getCountryList** - On command /country or after **login** event (`Application::EVENT_GET_COUNTRY_LIST`)

* **getOffer** - User entered offer code or clicked on offer button (`Application::EVENT_GET_OFFER`)

* **version** - On command /version (`Application::EVENT_VERSION`)

#### Handlers

```php
<?php

    use He110\Coral\Bot\Application;
    use He110\Coral\Bot\Service\JsonDataManager;
   
    
    $baseDomain = 'example.domain'; //Base domain, which'll be extended with country code ( us.example.domain ) 
    $manager = new JsonDataManager("path-to-your/json-data.json");
    
    $application = new Application('<YOUR-TELEGRAM-BOT-TOKEN>', $baseDomain);
    $application->setDataManager($manager)
        ->setEventHandler(Application::VERSION, function(Application &$application) {
            /** @var \TelegramBot\Api\BotApi $bot */
            $bot = $application->getService();  
            $bot->sendMessage($application->getChatId(), 'This is custom event handler');
        })
        ->run();
```

## About

### Requirements

- CoralBot works with PHP 7.1 or above.
- Other decencies you can find at **requires** section [here](https://packagist.org/packages/he110/coral-bot).

### Submitting bugs and feature requests

Bugs and feature request are tracked on [GitHub](https://github.com/he110/coral-bot/issues)

### Author

Ilya S. Zobenko - <ilya@zobenko.ru> - <http://twitter.com/he110_todd>
