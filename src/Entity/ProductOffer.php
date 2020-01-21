<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 10:29
 */

namespace He110\Coral\Bot\Entity;


use He110\Coral\Bot\Service\ArraySerializer;

class ProductOffer extends ArraySerializer
{
    /** @var null|string */
    protected $code = null;
    /** @var string  */
    protected $name = '';
    /** @var null|string  */
    protected $form = null;
    /** @var string  */
    protected $description = '';
    /** @var null|string  */
    protected $thumbnail = null;
    /** @var float */
    protected $basePrice = 0.0;
    /** @var float */
    protected $clubPrice = 0.0;
    /** @var int */
    protected $bonus = 0;
    /** @var null|string */
    protected $link = null;
    /** @var string */
    protected $currency = 'RUB';

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return ProductOffer
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     * @return ProductOffer
     */
    public function setLink(?string $link): self
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return ProductOffer
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ProductOffer
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getForm(): ?string
    {
        return $this->form;
    }

    /**
     * @param string|null $form
     * @return ProductOffer
     */
    public function setForm(?string $form): self
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ProductOffer
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string|null $thumbnail
     * @return ProductOffer
     */
    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * @return float
     */
    public function getBasePrice(): float
    {
        return $this->basePrice;
    }

    /**
     * @param float $basePrice
     * @return ProductOffer
     */
    public function setBasePrice(float $basePrice): self
    {
        $this->basePrice = $basePrice;
        return $this;
    }

    /**
     * @return float
     */
    public function getClubPrice(): float
    {
        return $this->clubPrice;
    }

    /**
     * @param float $clubPrice
     * @return ProductOffer
     */
    public function setClubPrice(float $clubPrice): self
    {
        $this->clubPrice = $clubPrice;
        return $this;
    }

    /**
     * @return int
     */
    public function getBonus(): int
    {
        return $this->bonus;
    }

    /**
     * @param int $bonus
     * @return ProductOffer
     */
    public function setBonus(int $bonus): self
    {
        $this->bonus = $bonus;
        return $this;
    }
}
