<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail_addr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_sign;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mail_conf;

    // Construct Method

    public function __construct()
    {
        $this->date_sign = new \Datetime();
        $this->mail_conf = false;
    }

    // Getters & setters

    public function getId()
    {
        return $this->id;
    }

    public function getFirst_name()
    {
        return $this->first_name;
    }

    public function setFirst_name($first_name)
    {
        $this->first_name = $first_name;
    }

    public function getLast_name()
    {
        return $this->last_name;
    }

    public function setLast_name($last_name)
    {
        $this->last_name = $last_name;
    }

    public function getMail_addr()
    {
        return $this->mail_addr;
    }

    public function setMail_addr($mail_addr)
    {
        $this->mail_addr = $mail_addr;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getDate_sign()
    {
        return $this->date_sign;
    }

    public function getMail_conf()
    {
        return $this->mail_conf;
    }

    public function setMail_conf($mail_conf)
    {
        $this->mail_conf = $mail_conf;
    }
}
