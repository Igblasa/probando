<?php

namespace App\Entity\intranet;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="int_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class IntUser implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=false)
     */
    private $username;
    
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    
    /**
     * @ORM\Column(type="string", length=250, unique=false)
     */
    private $imagen;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=250)
     */
    private $password;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    private $salt;
    
    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $google_access_token;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $google_token_expiration;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    
    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }
    
    public function getRolesObjects(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    
    public function getImagen(): string
    {
        return (string) $this->imagen;
    }

    public function setImagen(string $imagen): self
    {
        $this->imagen = $imagen;

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
    public function getGoogleAccessToken(): string
    {
        return (string) $this->googleAccessToken;
    }

    public function setGoogleAccessToken(string $googleAccessToken): self
    {
        $this->googleAccessToken = $googleAccessToken;

        return $this;
    }
    
    public function getGoogleTokenExpiration(): ?\DateTimeInterface
    {
        return $this->googleTokenExpiration;
    }

    public function setGoogleTokenExpiration(?\DateTimeInterface $googleTokenExpiration): self
    {
        $this->googleTokenExpiration = $googleTokenExpiration;

        return $this;
    }
    
    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

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
    
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->roles,
        ));
    }
    
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->roles,
        ) = unserialize($serialized);
    }
}
