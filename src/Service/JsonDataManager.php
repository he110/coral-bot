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

    public function __construct()
    {
        $this->getFilePath();
    }

    /**
     * Проверяет наличие файла данных. В случае отстутствия создает
     * Возвращает полный путь к файлу
     *
     * @return string - Путь к файлу с данными
     */
    private function getFilePath(): string
    {
        $dir = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : __DIR__."/../../../";
        $file = $dir.$this->getFilename();
        if (!file_exists($file))
            $this->saveAllData([]);
        return realpath($file);
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
        if ($filename != $this->filename) {
            $this->filename = $filename;
            $this->getFilePath();
        }
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function save(string $key, array $data): bool
    {
        $data = $this->getAllData();
        $data[$key] = $data;
        return $this->saveAllData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $key): array
    {
        $data = $this->getAllData();
        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function reset(string $key): bool
    {
        $data = $this->getAllData();
        if (isset($data[$key])) {
            unset($data[$key]);
            return $this->saveAllData($data);
        }
        return true;
    }

    private function getAllData(): array
    {
        return json_decode(file_get_contents($this->getFilePath()), true);
    }

    private function saveAllData(array $data): bool
    {
        return (bool)file_put_contents($this->getFilePath(), json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}