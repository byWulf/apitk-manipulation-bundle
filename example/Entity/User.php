<?php

namespace MyApp\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User.
 *
 * @package MyApp\Entity
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
class User
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     */
    private $fullname;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return User
     */
    public function setId(?int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param null|string $username
     *
     * @return User
     */
    public function setUsername(?string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     *
     * @return User
     */
    public function setEmail(?string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    /**
     * @param null|string $fullname
     *
     * @return User
     */
    public function setFullname(?string $fullname): User
    {
        $this->fullname = $fullname;

        return $this;
    }
}
