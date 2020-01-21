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
    private $code = null;
    /** @var string  */
    private $name = '';
    /** @var null|string  */
    private $form = null;
    /** @var string  */
    private $description = '';
    /** @var null|string  */
    private $thumbnail = null;
    /** @var float */
    private $basePrice = 0.0;
    /** @var float */
    private $clubPrice = 0.0;
    /** @var int */
    private $bonus = 0;

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