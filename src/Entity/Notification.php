<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification Entity
 *
 * List of column:
 * * id
 * * type
 * * user
 * * content
 * * date_send
 * * url
 * * url_id
 *
 * All notifications types:
 * * 0: Comment send on a status
 *
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=10)
     */
    private $id;

    /**
     * All notifications types:
     * * 0: Comment send on a status
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $url_id;

    public function __construct()
    {
        $this->date_send = new \Datetime();
    }

    public function __toString() {
        return 'Notification: '.$this->id.' | ['.$this->user.'] '.$this->type;
    }

    public function browse()
    {
        $result = array();

        foreach ($this as $key => $value) {
            if ($value !== NULL) {
                $result[$key] = $value;
            } else {
                $result[$key] = 'NULL';
            }
        }

        return $result;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDateSend(): ?\DateTimeInterface
    {
        return $this->date_send;
    }

    public function setDateSend(\DateTimeInterface $date_send): self
    {
        $this->date_send = $date_send;

        return $this;
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

    public function getUrlId(): ?string
    {
        return $this->url_id;
    }

    public function setUrlId(?string $url_id): self
    {
        $this->url_id = $url_id;

        return $this;
    }
}
