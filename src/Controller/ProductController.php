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

    private $currencies = array(
        'RUB',
        'EUR',
        'USD'
    );

    public function __construct(Application &$app)
    {
        $this->baseUrl = $app->getBaseUrl();
        $this->countryCode = $app->getUser() ? $app->getUser()->getCountry() ?? "RU" : "RU";
        $this->application = $app;
    }

    public function getOfferById(string $id, string $currency = 'RUB'):?ProductOffer
    {
        $item = CoralRestClient::get($this->countryCode, $this->baseUrl, "catalog/product/{$id}");
        if ($item && isset($item['NAME']) && !empty($item['NAME'])) {
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
        $render[] = $base;
        $render[] = "";
        $render[] = 'Клубная цена:';
        $render[] = $club;
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

        $offer->fromArray(array(
            'code'      => $item['CODE'],
            'name'      => $item['NAME'],
            'bonus'     => $item['BB'],
            'thumbnail' => $item['PREVIEW_PICTURE'],
            'description' => $item['DETAIL_TEXT'],
            'link'      => $item['REFFERAL_LINK'],
            'form'      => $item['FORM'],
            'currency'  => $currency,
            'basePrice' => $item['PRICE_BASE'],
            'clubPrice' => $item['PRICE_CLUB']
        ));
        return $offer;
    }

    /**
     * @param string|null $current
     * @return array
     */
    function getCurrencies(string $current = null): array
    {
        $result = $this->currencies;
        if (!is_null($current)) {
            $result = array_filter($result, function($item) use ($current) {
                return $item != $current;
            });
        }
        return $result;
    }
}