<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 16:39
 */

namespace He110\Coral\Bot\Controller;


use He110\Coral\Bot\Application;
use He110\Coral\Bot\Entity\ProductOffer;
use He110\Coral\Bot\Interfaces\AppControllerInterface;
use He110\Coral\Bot\Service\CoralRestClient;

class ProductController implements AppControllerInterface
{
    private $baseUrl;

    private $countryCode;

    private $application;

    public function __construct(Application &$app)
    {
        $this->baseUrl = $app->getBaseUrl();
        $this->countryCode = $app->getUser() ? $app->getUser()->getCountry() ?? "RU" : "RU";
        $this->application = $app;
    }

    public function getOfferById(string $id, string $currency = 'RUB'):?ProductOffer
    {
        if ($item = CoralRestClient::get($this->countryCode, $this->baseUrl, "catalog/product/{$id}")) {
            return $this->offerFromApi($item, $currency);
        }

        return null;
    }

    public function search(string $query, string $currency = 'RUB'): array
    {
        $result = array();
        if ($response = CoralRestClient::get($this->countryCode, $this->baseUrl, 'catalog/search/context?p='.urlencode($query))) {
            foreach($response as $item) {
                $result[] = $this->offerFromApi($item, $currency);
            }
        }
        return $result;
    }


    function renderOffer(ProductOffer $offer): string
    {
        $render = array();
        $render[] = $offer->getName();
        $render[] = str_repeat("–", 30);
        $render[] = strip_tags($offer->getDescription());
        $render[] = str_repeat("–", 30);

        $base = $offer->getBasePrice();
        $club = $offer->getClubPrice();

        $render[] = 'Розничная цена:';
        $render[] = substr($base, 0, -2);
        $render[] = "";
        $render[] = 'Клубная цена:';
        $render[] = substr($club, 0, -2);
        $render[] = "";

        return implode("\n", $render);
    }

    function buildOfferName(ProductOffer $offer): string
    {
        $name = $offer->getName();
        preg_match_all('!\d+!', $offer->getForm(), $form);
        if ($form = current($form)) {
            $name = $form[0]." ".$name;
        }
        return $name;
    }

    function offerFromApi(array $item, string $currency): ProductOffer
    {
        $offer = new ProductOffer();

        preg_match_all('(\d*|\.|\,)', $item['PRICE_FORMATED'], $basePrice);
        $basePrice = array_filter($basePrice[0]);
        $basePrice = floatval(implode("", $basePrice));

        $offer->fromArray(array(
            'code'      => $item['CODE'],
            'name'      => $item['NAME'],
            'bonus'     => $item['BB'],
            'thumbnail' => $item['PREVIEW_PICTURE'],
            'description' => $item['DETAIL_TEXT'],
            'link'      => $item['REFFERAL_LINK'],
            'form'      => $item['FORM'],
            'currency'  => $currency,
            'basePrice' => $basePrice
        ));
        return $offer;
    }
}