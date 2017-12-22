<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
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
}
