<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 10:29
 */

namespace He110\Coral\Bot\Entity;


use He110\Coral\Bot\Service\ArraySerializer;

class User extends ArraySerializer
{
    /** @var string */
    protected $name;

    /** @var string|null */
    protected $country;

    /** @var string|null */
    protected $currency;

    /** @var int|null */
    protected $id;

    /** @var int|null */
    protected $member;

    /** @var array */
    protected $options = array();

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     * @return User
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     * @return User
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return User
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMember(): ?int
    {
        return $this->member;
    }

    public function isAuthorized(): bool
    {
        return !is_null($this->member);
    }

    /**
     * @param int|null $member
     * @return User
     */
    public function setMember(?int $member): self
    {
        $this->member = $member;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getOption(string $key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * @param string $key
     * @param $value
     * @return User
     */
    public function setOption(string $key, $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return User
     */
    public function removeOption(string $key): self
    {
        if (isset($this->options[$key]))
            unset($this->options[$key]);
        return $this;
    }
}