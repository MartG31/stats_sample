<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *      fields = {"email"},
 *      message = "L'adresse email existe déjà."
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotNull
     * @Assert\Email(
     *      message = "Veuillez entrer une adresse email valide."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull
     * @Assert\Length(
            min = 5,
            max = 50,
            minMessage = "Le pseudo doit contenir au moins 5 caractères.",
            maxMessage = "Le pseudo doit contenir moins de 50 caractères."
     * )
     */
    private $pseudo;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=255)
     */
    private $password;
    
    /**
     * @Assert\NotNull
     * @Assert\Length(
            min = 8,
            max = 30,
            minMessage = "Le mot de passe doit contenir au moins 8 caractères.",
            maxMessage = "Le mot de passe doit contenir moins de 30 caractères."
     * )
     */
    private $plain_password;
    
    /**
     * @Assert\NotNull
     * @Assert\EqualTo(
     *      propertyPath = "plain_password",
     *      message = "Les deux mots de passe doivent être identiques."
     * )
     */
    private $password_confirm;

    private $password_check;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime_register;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
            min = 2,
            max = 50,
            minMessage = "Le nom doit contenir au moins 2 caractères.",
            maxMessage = "Le nom doit contenir moins de 50 caractères."
     * )
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
            min = 2,
            max = 50,
            minMessage = "Le prénom doit contenir au moins 2 caractères.",
            maxMessage = "Le prénom doit contenir moins de 50 caractères."
     * )
     */
    private $first_name;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birth_date;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Assert\Length(
            min = 5,
            max = 180
     * )
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $phone;



    public function getId(): ?int
    {
        return $this->id;
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
        // $roles[] = 'ROLE_USER';

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

    public function getPlainPassword(): string
    {
        return (string) $this->plain_password;
    }

    public function setPlainPassword(string $plain_password): self
    {
        $this->plain_password = $plain_password;

        return $this;
    }

    public function getPasswordConfirm(): string
    {
        return (string) $this->password_confirm;
    }

    public function setPasswordConfirm(string $password_confirm): self
    {
        $this->password_confirm = $password_confirm;

        return $this;
    }

    public function getPasswordCheck(): string
    {
        return (string) $this->password_check;
    }

    public function setPasswordCheck(string $password_check): self
    {
        $this->password_check = $password_check;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getDatetimeRegister(): ?\DateTimeInterface
    {
        return $this->datetime_register;
    }

    public function setDatetimeRegister(\DateTimeInterface $datetime_register): self
    {
        $this->datetime_register = $datetime_register;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(?\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }
}
