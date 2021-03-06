<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
 * * roles
 * * laws
 *
 * List of extra variables:
 * * salt
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=100)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $child_name;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $settings;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Laws", mappedBy="user")
     */
    private $laws;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="reporter")
     */
    private $reports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="reported_user", orphanRemoval=true)
     */
    private $reported;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Report", mappedBy="moderators")
     */
    private $processed_reports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Survey", mappedBy="user")
     */
    private $surveys;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_child;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="parents")
     * @ORM\JoinTable(name="users_children",
     *      joinColumns={@ORM\JoinColumn(name="parent_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="child_id", referencedColumnName="id")})
     */
    private $children;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="children")
     */
    private $parents;

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
        $this->url = '/resources/icon.svg';
        $this->alt = 'Profile picture';
        $this->roles = array('ROLE_USER');
        $this->laws = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->reported = new ArrayCollection();
        $this->processed_reports = new ArrayCollection();
        $this->surveys = new ArrayCollection();
        $this->is_child= false;
        $this->children = new ArrayCollection();
        $this->parents = new ArrayCollection();
    }

    public function __toString() {
        return 'User: '.$this->id.' | '.$this->first_name.' '.$this->last_name;
    }

    public function browse()
    {
        $result = array();

        foreach ($this as $key => $value) {
            if ($key != 'password' && $key != 'salt') {
                if ($value !== NULL && $key != 'mail_conf') {
                    $result[$key] = $value;
                } elseif ($key == 'mail_conf') {
                    if ($value) {
                        $result[$key] = 'true';
                    } else {
                        $result[$key] = 'false';
                    }
                } else {
                    $result[$key] = 'NULL';
                }
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

    public function getChildName(): ?string
    {
        return $this->child_name;
    }

    public function setChildName(string $child_name): self
    {
        $this->child_name = $child_name;

        return $this;
    }

    public function getName(): ?string
    {
        if ($this->is_child == false) {
            return $this->first_name.' '.$this->last_name;
        }
        return $this->child_name;
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

    /**
     * @return Collection|Laws[]
     */
    public function getLaws(): Collection
    {
        return $this->laws;
    }

    public function addLaw(Laws $law): self
    {
        if (!$this->laws->contains($law)) {
            $this->laws[] = $law;
            $law->setUser($this);
        }

        return $this;
    }

    public function removeLaw(Laws $law): self
    {
        if ($this->laws->contains($law)) {
            $this->laws->removeElement($law);
            // set the owning side to null (unless already changed)
            if ($law->getUser() === $this) {
                $law->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setReporter($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getReporter() === $this) {
                $report->setReporter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReported(): Collection
    {
        return $this->reported;
    }

    public function addReported(Report $reported): self
    {
        if (!$this->reported->contains($reported)) {
            $this->reported[] = $reported;
            $reported->setReportedUser($this);
        }

        return $this;
    }

    public function removeReported(Report $reported): self
    {
        if ($this->reported->contains($reported)) {
            $this->reported->removeElement($reported);
            // set the owning side to null (unless already changed)
            if ($reported->getReportedUser() === $this) {
                $reported->setReportedUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getProcessedReports(): Collection
    {
        return $this->processed_reports;
    }

    public function addProcessedReport(Report $processedReport): self
    {
        if (!$this->processed_reports->contains($processedReport)) {
            $this->processed_reports[] = $processedReport;
            $processedReport->addModerator($this);
        }

        return $this;
    }

    public function removeProcessedReport(Report $processedReport): self
    {
        if ($this->processed_reports->contains($processedReport)) {
            $this->processed_reports->removeElement($processedReport);
            $processedReport->removeModerator($this);
        }

        return $this;
    }

    /**
     * @return Collection|Survey[]
     */
    public function getSurveys(): Collection
    {
        return $this->surveys;
    }

    public function addSurvey(Survey $survey): self
    {
        if (!$this->surveys->contains($survey)) {
            $this->surveys[] = $survey;
            $survey->setUser($this);
        }

        return $this;
    }

    public function removeSurvey(Survey $survey): self
    {
        if ($this->surveys->contains($survey)) {
            $this->surveys->removeElement($survey);
            // set the owning side to null (unless already changed)
            if ($survey->getUser() === $this) {
                $survey->setUser(null);
            }
        }

        return $this;
    }

    public function getIsChild(): ?bool
    {
        return $this->is_child;
    }

    public function setIsChild(bool $is_child): self
    {
        $this->is_child = $is_child;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(self $parent): self
    {
        if (!$this->parents->contains($parent)) {
            $this->parents[] = $parent;
            $parent->addChild($this);
        }

        return $this;
    }

    public function removeParent(self $parent): self
    {
        if ($this->parents->contains($parent)) {
            $this->parents->removeElement($parent);
            $parent->removeChild($this);
        }

        return $this;
    }
}
