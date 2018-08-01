<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * User Entity
 *
 * List of column:
 * * id
 * * first_name
 * * last_name
 * * email
 * * username
 * * password
 * * date_sign
 * * mail_conf
 * * posts
 * * comments
 * * notifications
 * * friendRequests
 * * requestedFriends
 * * friends
 * * url
 * * alt
 * * settings
 *
 * List of extra variables:
 * * rememberme
 * * salt
 * * conf_password
 * * file
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="user", orphanRemoval=true)
     */
    private $notifications;

    private $rememberme;

    private $salt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FriendRequest", mappedBy="from_user", orphanRemoval=true)
     */
    private $friendRequests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FriendRequest", mappedBy="to_user", orphanRemoval=true)
     */
    private $requestedFriends;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private $friends;

    private $conf_password;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alt;

    private $file;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $settings;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    public function __construct()
    {
        $this->date_sign = new \Datetime();
        $this->mail_conf = false;
        $this->salt = null;
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->friendRequests = new ArrayCollection();
        $this->requestedFriends = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->url = '/ressources/icon.svg';
        $this->alt = 'Profile picture';
        $this->roles = array('ROLE_USER');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDateSign(): ?\DateTimeInterface
    {
        return $this->date_sign;
    }

    public function setDateSign(\DateTimeInterface $date_sign): self
    {
        $this->date_sign = $date_sign;

        return $this;
    }

    public function getMailConf(): ?bool
    {
        return $this->mail_conf;
    }

    public function setMailConf(bool $mail_conf): self
    {
        $this->mail_conf = $mail_conf;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getRememberme()
    {
        return $this->rememberme;
    }

    public function setRememberme($rememberme)
    {
        $this->rememberme = $rememberme;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt()
    {
        $this->salt = $salt;
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->salt
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt
        ) = unserialize($serialized);
    }

    /**
     * @return Collection|FriendRequest[]
     */
    public function getFriendRequests(): Collection
    {
        return $this->friendRequests;
    }

    public function addFriendRequest(FriendRequest $friendRequest): self
    {
        if (!$this->friendRequests->contains($friendRequest)) {
            $this->friendRequests[] = $friendRequest;
            $friendRequest->setFromUser($this);
        }

        return $this;
    }

    public function removeFriendRequest(FriendRequest $friendRequest): self
    {
        if ($this->friendRequests->contains($friendRequest)) {
            $this->friendRequests->removeElement($friendRequest);
            // set the owning side to null (unless already changed)
            if ($friendRequest->getFromUser() === $this) {
                $friendRequest->setFromUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FriendRequest[]
     */
    public function getRequestedFriends(): Collection
    {
        return $this->requestedFriends;
    }

    public function addRequestedFriend(FriendRequest $requestedFriend): self
    {
        if (!$this->requestedFriends->contains($requestedFriend)) {
            $this->requestedFriends[] = $requestedFriend;
            $requestedFriend->setToUser($this);
        }

        return $this;
    }

    public function removeRequestedFriend(FriendRequest $requestedFriend): self
    {
        if ($this->requestedFriends->contains($requestedFriend)) {
            $this->requestedFriends->removeElement($requestedFriend);
            // set the owning side to null (unless already changed)
            if ($requestedFriend->getToUser() === $this) {
                $requestedFriend->setToUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(User $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
        }

        return $this;
    }

    public function removeFriend(User $friend): self
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
        }

        return $this;
    }

    public function getConfPassword()
    {
        return $this->conf_password;
    }

    public function setConfPassword($conf_password)
    {
        $this->conf_password = $conf_password;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function setSettings($settings): self
    {
        $this->settings = $settings;

        return $this;
    }

    public function getRoles()
    {
        // I have absolutely no idea why, but it works only if we do this... ¯\_(ツ)_/¯

        foreach ($this->roles as $role) {
            $rolesList[] = $role;
        }

        return $rolesList;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $newRole): self
    {
        $this->roles[] = $newRole;

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (in_array($role, $this->roles)) {
            array_splice($this->roles, array_search($role, $this->roles), 1);
        }

        return $this;
    }
}
