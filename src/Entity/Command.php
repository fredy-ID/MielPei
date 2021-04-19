<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    /**
     * @Groups("command")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $serialNumber;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commands")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;


    /**
     * @Groups("command")
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="commands")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @Groups("command")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @Groups("command")
     * @ORM\Column(type="string", length=255)
     */
    private $invoice;

    /**
     * @Groups("command")
     * @ORM\Column(type="string", length=20)
     */
    private $etat;

    /**
     * @Groups("command")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deliveryDateAt;

    /**
     * @Groups("command")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Producer::class, inversedBy="commands")
     * @ORM\JoinColumn(nullable=false)
     */
    private $producer;

    /**
     * @Groups("command")
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @Groups("command")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adress;

    /**
     * @Groups("command")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $secAdress;

    /**
     * @Groups("command")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $postcode;

    /**
     * @Groups("command")
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $region;

    /**
     * @Groups("command")
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getInvoice(): ?string
    {
        return $this->invoice;
    }

    public function setInvoice(string $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDeliveryDateAt(): ?\DateTimeInterface
    {
        return $this->deliveryDateAt;
    }

    public function setDeliveryDateAt(?\DateTimeInterface $deliveryDateAt): self
    {
        $this->deliveryDateAt = $deliveryDateAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getProducer(): ?Producer
    {
        return $this->producer;
    }

    public function setProducer(?Producer $producer): self
    {
        $this->producer = $producer;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getSecAdress(): ?string
    {
        return $this->secAdress;
    }

    public function setSecAdress(?string $secAdress): self
    {
        $this->secAdress = $secAdress;

        return $this;
    }

    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    public function setPostcode(?int $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }
}
