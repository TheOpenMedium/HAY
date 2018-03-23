<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Status Entity
 *
 * List of column:
 * * id
 * * id_user
 * * content
 * * date_content
 * * color
 * * size
 * * font
 * * comments
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusRepository")
 */
class Status
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="status")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $id_user;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_content;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $font;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="id_status")
     */
    private $comments;

    // Construct Method

    public function __construct()
    {
        $this->date_content = new \Datetime();
        $this->comments = new ArrayCollection();
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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getDateContent()
    {
        return $this->date_content;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getFont()
    {
        return $this->font;
    }

    public function setFont($font)
    {
        $this->font = $font;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

}
