<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 12:44
 */

namespace He110\Coral\Bot\Service;


class ArraySerializer
{
    public function toArray(): array
    {
        $result = array();
        foreach((array)$this as $property => $value) {
            $property = str_replace([get_class($this), ' ', '*'], '', $property);
            $result[$property] = $value;
        }
        return $result;
    }

    public function fromArray(array $data): void
    {
        foreach($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}