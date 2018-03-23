<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification Entity
 *
 * List of column:
 * * id
 * * notification_type
 * * id_user
 * * content
 * * date_send
 * * url
 * * url_id
 *
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
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
    private $notification_type;

    /*

    Please list here all notifications types:
    0: Comment send on a status

    */

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
    private $date_send;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $url_id;

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

    public function getNotificationType()
    {
        return $this->notification_type;
    }

    public function setNotificationType($notification_type)
    {
        $this->notification_type = $notification_type;
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

    public function getDateSend()
    {
        return $this->date_send;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrlId(): ?int
    {
        return $this->url_id;
    }

    public function setUrlId(?int $url_id): self
    {
        $this->url_id = $url_id;

        return $this;
    }
}
