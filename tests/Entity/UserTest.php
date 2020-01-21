<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 12:46
 */

namespace Tests\He110\Coral\Bot\Entity;

use He110\Coral\Bot\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var User */
    private $user;

    private $demoData = array(
        'id' => 1,
        'name' => 'Demo user',
        'country' => 'RU',
        'currency' => 'RUB',
        'lastOffer' => 1000
    );

    public function testToArray()
    {
        $user = &$this->user;
        $ar = $user->toArray();

        $this->assertNotEmpty($ar);
        $this->assertEquals($this->demoData, $ar);
    }

    public function testFromArray()
    {
        $user = new User();
        $user->fromArray($this->demoData);
        $ar = $user->toArray();

        $this->assertNotEmpty($ar);
        $this->assertEquals($this->demoData, $ar);
    }

    public function setUp()
    {
        $this->user = new User();
        $this->user->setName($this->demoData['name'])
            ->setId($this->demoData['id'])
            ->setCountry($this->demoData['country'])
            ->setCurrency($this->demoData['currency'])
            ->setLastOffer($this->demoData['lastOffer']);
    }

    public function tearDown()
    {
        unset($this->user);
        $this->user = null;
    }
}
