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
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class ProductController implements AppControllerInterface
{
    private $baseUrl;

    private $countryCode;

    private $application;

    private $currencies = array(
        'RUB' => array(
            'code' => 'RUB',
            'name' => 'RUB',
            'text' => 'руб'
        ),
        'EUR' => array(
            'code' => 'EUR',
            'name' => 'ЕUR',
            'text' => 'EUR'
        ),
        'USD' => array(
            'code' => 'USD',
            'name' => 'USD',
            'text' => 'USD'
        )
    );

    public function __construct(Application &$app)
    {
        $this->baseUrl = $app->getBaseUrl();
        $this->countryCode = $app->getUser() ? $app->getUser()->getCountry() ?? "RU" : "RU";
        $this->application = $app;
    }

    public function getOfferById(string $id, string $currency = 'RUB'):?ProductOffer
    {
        $user = $this->application->getUser()->getMember();
        $item = CoralRestClient::get($this->countryCode, $this->baseUrl, "catalog/product/{$id}/{$user}");
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
        $lineCount = 40;
        $render = array();
        $render[] = $offer->getName();
        $render[] = str_repeat("–", $lineCount);
        $render[] = strip_tags($offer->getDescription());
        $render[] = str_repeat("–", $lineCount);

        $base = $offer->getBasePrice();
        $club = $offer->getClubPrice();

        $currency = $this->getCurrencies()[$offer->getCurrency()];

        if ($base) {
            $render[] = 'Розничная цена:';
            $render[] = number_format($base, 2)." ".$currency['text'];
            $render[] = "";
        }
        if ($club) {
            $render[] = 'Клубная цена:';
            $render[] = number_format($club, 2)." ".$currency['text'];
            $render[] = "";
        }
        if ($ref = $offer->getRefLink()) {
            $render[] = "";
            $render[] = $ref;
            $render[] = str_repeat("–", $lineCount);
        }

        return implode("\n", $render);
    }

    /**
     * @param ProductOffer $offer
     * @param string $currency
     * @return InlineKeyboardMarkup
     */
    function generateOfferButtons(ProductOffer $offer, string $currency = 'RUB'): InlineKeyboardMarkup
    {
        $buttons = array();

        if ($currencies = $this->getCurrencies($currency)) {
            foreach($currencies as $currency) {
                $buttons[] = array(
                    'text' => $currency['name'],
                    'callback_data' => '!currency='.$currency['code']
                );
            }
        }

        $keyboardMarkup = array(
            $buttons,
            array(
                array(
                    'text' => 'В магазин',
                    'url' => $offer->getLink()
                ),
            ),
        );
        return new InlineKeyboardMarkup($keyboardMarkup);
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

        $prices = $item['PRICES'][$currency];

        $offer->fromArray(array(
            'code'      => $item['CODE'],
            'name'      => $item['NAME'],
            'bonus'     => $item['BB'],
            'thumbnail' => $item['PREVIEW_PICTURE'],
            'description' => $item['DETAIL_TEXT'],
            'link'      => $item['REFFERAL_LINK'],
            'form'      => $item['FORM'] ?? null,
            'currency'  => $currency,
            'basePrice' => floatval($prices['base']) ?? 0.0,
            'clubPrice' => floatval($prices['club']) ?? 0.0,
            'refLink'   => $item['REFFERAL_LINK'] ?? null
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
                return $item['code'] != $current;
            });
        }
        return $result;
    }

    function convertPrices(float $base, float $club, string $fromCurrency): ?array
    {
        if ($response = CoralRestClient::get(
            $this->countryCode, $this->baseUrl,
            "general/convertPrice/{$fromCurrency}/{$base}/{$club}")
        )
            return $response;
        return null;
    }
}