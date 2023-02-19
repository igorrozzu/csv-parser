<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Product Entity
 *
 * @ORM\Table(name="importTest", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="product_code_idx", columns={"productCode"})
 * })
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @Assert\Expression(
 *     "this.getCost() >= 5 or this.getStock() >= 10",
 *     message="Cost should be more or equals 5 and Stock should be more or equals 10", groups={"costAndStockConstraint"}
 * )
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="intProductDataId", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Product name should not be blank");
     * @Assert\Type(type="string");
     *
     * @ORM\Column(name="strProductName", type="string", length=50)
     */
    private $productName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Product description should not be blank");
     * @Assert\Type(type="string")
     * @ORM\Column(name="productDesc", type="string", length=255)
     */
    private $productDesc;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Product code should not be blank")
     * @Assert\Type(type="string")
     *
     * @ORM\Column(name="productCode", type="string", length=10, unique=true)
     */
    private $productCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime(message="This property should be a DateTime")
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $dateDiscontinued;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Stock should not be blank")
     * @Assert\Type(type="numeric", message="Stock must be of type integer")
     *
     * @ORM\Column(name="stock", type="integer", options={"unsigned"=true})
     */
    private $stock;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Cost should not be blank")
     * @Assert\Type(type="numeric", message="Cost must be numeric and has type float")
     * @Assert\LessThan(value=1000, message="Cost should be less then 1000")
     *
     * @ORM\Column(name="cost", type="decimal", options={"unsigned"=true})
     */
    private $cost;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->doStuffOnPreUpdate();
    }

    /** @ORM\PreUpdate */

    public function doStuffOnPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     */
    public function setProductName(string $productName)
    {
        $this->productName = $productName;
    }

    /**
     * @return string
     */
    public function getProductDesc(): string
    {
        return $this->productDesc;
    }

    /**
     * @param string $productDesc
     */
    public function setProductDesc(string $productDesc)
    {
        $this->productDesc = $productDesc;
    }

    /**
     * @return string
     */
    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     */
    public function setProductCode(string $productCode)
    {
        $this->productCode = $productCode;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getDateDiscontinued(): \DateTime
    {
        return $this->dateDiscontinued;
    }

    /**
     * @param \DateTime $dateDiscontinued
     */
    public function setDateDiscontinued(\DateTime $dateDiscontinued)
    {
        $this->dateDiscontinued = $dateDiscontinued;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock(int $stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return double
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @param double $cost
     */
    public function setCost(float $cost)
    {
        $this->cost = $cost;
    }
}