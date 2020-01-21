<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 15:02
 */

namespace He110\Coral\Bot\Controller;


use He110\Coral\Bot\Interfaces\AppControllerInterface;
use He110\Coral\Bot\Service\CoralRestClient;

class UserController implements AppControllerInterface
{
    private $baseUrl;
    private $countryCode;

    public function __construct(string $baseUrl, string $countryCode)
    {
        $this->baseUrl = $baseUrl;
        $this->countryCode = $countryCode;
    }


    public function validate(string $memberId): bool
    {
        if (!is_numeric($memberId))
            return false;
        $check = CoralRestClient::get($this->countryCode, $this->baseUrl, "general/validateMember/{$memberId}");
        return is_array($check) && $check['result'];
    }

    public function countryList(): ?array
    {
        if ($list = CoralRestClient::get($this->countryCode, $this->baseUrl, 'country')) {
            $result = array();
            foreach ($list as $region) {
                foreach($region as $country)
                    $result[] = $country;
            }
            return $result;
        }
        return null;
    }
}