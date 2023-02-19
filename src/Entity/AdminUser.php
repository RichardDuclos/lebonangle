<?php

namespace App\Entity;

use App\Repository\AdminUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('email', message: 'Cet email est déjà utilisé')]
#[UniqueEntity('username', message: 'Ce nom d\'utilisateur est déjà utilisé')]
#[ORM\Entity(repositoryClass: AdminUserRepository::class)]
class AdminUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner un nom d\'utilisateur')]
    #[Assert\Length(max: 20, maxMessage: 'Le nom d\'utilisateur ne peut pas faire plus de 20 caractères')]
    #[ORM\Column(length: 20, unique: true)]
    private ?string $username = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre adresse e-mail')]
    #[Assert\Email(message: 'Veuillez renseigner une adresse e-mail valide')]
    #[Assert\Length(max: 100, maxMessage: 'Votre adresse e-mail doit faire moins de 100 caractères')]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;
    #[Assert\Length(max: 128, maxMessage: 'Votre mot de passe doit faire moins de 128 caractères')]
    private ?string $plainPassword = null;

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER', 'ROLE_ADMIN'];
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
