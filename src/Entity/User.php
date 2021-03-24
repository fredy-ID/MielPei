<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @Groups("user")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 35,
     *      minMessage = "{{ limit }} caractères minimum requis",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     * @Groups("public")
     */
    private $firstName;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 1,
     *      max = 35,
     *      minMessage = "{{ limit }} caractère minimum requis",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     * @Groups("public")
     */
    private $lastName;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=10)
     */
    private $number;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=255)
     */
    private $adress;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=255)
     */
    private $secAdress;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=5)
     */
    private $postcode;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=30)
     */
    private $region;

    /**
     * @Groups("user")
     * @ORM\Column(type="string", length=30)
     */
    private $country;


    /**
     * 
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * 
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups("user")
     * @ORM\OneToMany(targetEntity=Command::class, mappedBy="customer")
     */
    private $commands;

    /**
     * @Groups("user")
     * @ORM\OneToOne(targetEntity=Producer::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $producerProfile;

    /**
     * @ORM\OneToOne(targetEntity=Cart::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $cart;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=ProducerRequest::class, mappedBy="user")
     */
    private $producerRequests;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->commands = new ArrayCollection();
        $this->producerRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }
    
    public function getSecAdress(): ?string
    {
        return $this->secAdress;
    }

    public function setSecAdress(string $secAdress): self
    {
        $this->secAdress = $secAdress;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $command->setCustomer($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getCustomer() === $this) {
                $command->setCustomer(null);
            }
        }

        return $this;
    }

    public function getProducerProfile(): ?Producer
    {
        return $this->producerProfile;
    }

    public function setProducerProfile(Producer $producerProfile): self
    {
        // set the owning side of the relation if necessary
        if ($producerProfile->getUser() !== $this) {
            $producerProfile->setUser($this);
        }

        $this->producerProfile = $producerProfile;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): self
    {
        // set the owning side of the relation if necessary
        if ($cart->getUser() !== $this) {
            $cart->setUser($this);
        }

        $this->cart = $cart;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|ProducerRequest[]
     */
    public function getProducerRequests(): Collection
    {
        return $this->producerRequests;
    }

    public function addProducerRequest(ProducerRequest $producerRequest): self
    {
        if (!$this->producerRequests->contains($producerRequest)) {
            $this->producerRequests[] = $producerRequest;
            $producerRequest->setUser($this);
        }

        return $this;
    }

    public function removeProducerRequest(ProducerRequest $producerRequest): self
    {
        if ($this->producerRequests->removeElement($producerRequest)) {
            // set the owning side to null (unless already changed)
            if ($producerRequest->getUser() === $this) {
                $producerRequest->setUser(null);
            }
        }

        return $this;
    }
}
