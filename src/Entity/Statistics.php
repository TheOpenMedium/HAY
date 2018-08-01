<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatisticsRepository")
 */
class Statistics
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $visits;

    /**
     * @ORM\Column(type="integer")
     */
    private $requests;

    /**
     * @ORM\Column(type="integer")
     */
    private $new_users;

    /**
     * @ORM\Column(type="integer")
     */
    private $new_posts;

    /**
     * @ORM\Column(type="integer")
     */
    private $new_comments;

    public function __construct()
    {
        $this->date = new \Datetime();
        $this->visits = 0;
        $this->requests = 0;
        $this->new_users = 0;
        $this->new_posts = 0;
        $this->new_comments = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(int $visits): self
    {
        $this->visits = $visits;

        return $this;
    }

    public function getRequests(): ?int
    {
        return $this->requests;
    }

    public function setRequests(int $requests): self
    {
        $this->requests = $requests;

        return $this;
    }

    public function getNewUsers(): ?int
    {
        return $this->new_users;
    }

    public function setNewUsers(int $new_users): self
    {
        $this->new_users = $new_users;

        return $this;
    }

    public function getNewPosts(): ?int
    {
        return $this->new_posts;
    }

    public function setNewPosts(int $new_posts): self
    {
        $this->new_posts = $new_posts;

        return $this;
    }

    public function getNewComments(): ?int
    {
        return $this->new_comments;
    }

    public function setNewComments(int $new_comments): self
    {
        $this->new_comments = $new_comments;

        return $this;
    }
}
