<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 10:52
 */

namespace He110\Coral\Bot\Service;


class ProductHelper
{
    /**
     * Проверяет, является ли введенный текст - числовым кодом
     *
     * @param string $string
     * @return bool
     */
    protected function isOfferCode(string $string): bool
    {
        return !is_null($this->filterOfferCode($string));
    }

    /**
     * Если возможно, вытаскивает из текста все возможные числовые значения
     *
     * @param string $string
     * @return int|null
     */
    protected function filterOfferCode(string $string): ?int
    {
        preg_match_all('!\d+!', $string, $matches);
        if (count($matches) > 0) {
            return intval(trim(implode('', current($matches))));
        }
        return null;
    }
}