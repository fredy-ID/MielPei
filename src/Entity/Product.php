<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @Groups("products")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("products")
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @Groups("products")
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Groups("products")
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @Groups("products")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfPurchases;


    /**
     * @ORM\OneToMany(targetEntity=Command::class, mappedBy="product")
     */
    private $commands;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $productionArea;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Producer::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getNumberOfPurchases(): ?int
    {
        return $this->numberOfPurchases;
    }

    public function setNumberOfPurchases(?int $numberOfPurchases): self
    {
        $this->numberOfPurchases = $numberOfPurchases;

        return $this;
    }

    /**
     * @return Collection|Command[]
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setProduct($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getProduct() === $this) {
                $command->setProduct(null);
            }
        }

        return $this;
    }

    public function getProductionArea(): ?string
    {
        return $this->productionArea;
    }

    public function setProductionArea(?string $productionArea): self
    {
        $this->productionArea = $productionArea;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getOwner(): ?Producer
    {
        return $this->owner;
    }

    public function setOwner(?Producer $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
