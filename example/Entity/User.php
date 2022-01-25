<?php

declare(strict_types=1);

namespace MyApp\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User.
 */
class User
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    private $username;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    private $fullname;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): User
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): User
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): User
    {
        $this->fullname = $fullname;

        return $this;
    }
}
