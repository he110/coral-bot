<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 15:20
 */

namespace He110\Coral\Bot\Interfaces;


use He110\Coral\Bot\Application;

interface AppControllerInterface
{
    public function __construct(Application &$app);
}