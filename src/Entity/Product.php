<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 10:28
 */

namespace He110\Coral\Bot\Entity;


use He110\Coral\Bot\Service\ArraySerializer;

class Product extends ArraySerializer
{
    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $description;

    /** @var ProductOffer[] */
    protected $offers = array();

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return Product
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Product
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Product
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return ProductOffer[]
     */
    public function getOffers(): array
    {
        return $this->offers;
    }

    /**
     * @param ProductOffer[] $offers
     * @return Product
     */
    public function setOffers(array $offers): self
    {
        $this->offers = $offers;
        return $this;
    }
}
