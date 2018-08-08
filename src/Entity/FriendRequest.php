<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FriendRequest Entity
 *
 * List of column:
 * * id
 * * from_user
 * * to_user
 *
 * @ORM\Entity(repositoryClass="App\Repository\FriendRequestRepository")
 */
class FriendRequest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="friendRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $from_user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="requestedFriends")
     * @ORM\JoinColumn(nullable=false)
     */
    private $to_user;

    public function __toString() {
        return 'FriendRequest: '.$this->id.' | ['.$this->from_user.'] => ['.$this->to_user.']';
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

    public function getId()
    {
        return $this->id;
    }

    public function getFromUser(): ?User
    {
        return $this->from_user;
    }

    public function setFromUser(?User $from_user): self
    {
        $this->from_user = $from_user;

        return $this;
    }

    public function getToUser(): ?User
    {
        return $this->to_user;
    }

    public function setToUser(?User $to_user): self
    {
        $this->to_user = $to_user;

        return $this;
    }
}
