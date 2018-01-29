<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $id_status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $id_user;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_send;

    // Construct Method

    public function __construct()
    {
        $this->date_send = new \Datetime();
    }

    // Getters & setters

    public function getId()
    {
        return $this->id;
    }

    public function getIdUser()
    {
        return $this->id_user;
    }

    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }

    public function getIdStatus()
    {
        return $this->id_status;
    }

    public function setIdStatus($id_status)
    {
        $this->id_status = $id_status;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getDateSend()
    {
        return $this->date_send;
    }
}
