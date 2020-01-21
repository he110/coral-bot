<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 10:42
 */

namespace He110\Coral\Bot\Interfaces;


interface DataManager
{
    /**
     * Метод сохранения данных по ключу
     *
     * @param string $key
     * @param array $data
     * @return bool
     */
    public function save(string $key, array $data): bool;

    /**
     * Метод получения данных по ключу
     *
     * @param string $key
     * @return array
     */
    public function load(string $key): array;
}