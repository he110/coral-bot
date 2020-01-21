<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 12:29
 */

namespace He110\Coral\Bot\Service;


use He110\Coral\Bot\Interfaces\DataManager;

/**
 * Класс для хранения и обработки информации в виде json файла
 *
 * Class JsonDataManager
 * @package He110\Coral\Bot\Service
 */
class JsonDataManager implements DataManager
{
    /** @var string */
    private $filename = 'json_data_manager_content.json';

    public function __construct(string $filePath = 'jsonDataManager.json')
    {
        $this->setFilename($filePath);
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return JsonDataManager
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        if (!file_exists($filename))
            file_put_contents($filename, json_encode([]));
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function save(string $key, array $data): bool
    {
        $loaded = $this->getAllData();
        if (!isset($loaded[$key]))
            $loaded[$key] = $data;
        else {
            foreach($data as $prop => $value)
                $loaded[$key][$prop] = $value;
        }
        return $this->saveAllData($loaded);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $key): ?array
    {
        $loaded = $this->getAllData();
        return isset($loaded[$key]) ? $loaded[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function reset(string $key): bool
    {
        $loaded = $this->getAllData();
        if (isset($loaded[$key])) {
            unset($loaded[$key]);
            return $this->saveAllData($loaded);
        }
        return true;
    }

    private function getAllData(): array
    {
        return json_decode(file_get_contents($this->getFilename()), true);
    }

    private function saveAllData(array $data): bool
    {
        return (bool)file_put_contents($this->getFilename(), json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}