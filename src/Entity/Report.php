<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 */
class Report
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reporter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reported")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reported_user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="reported")
     */
    private $reported_post;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comment", inversedBy="reported")
     */
    private $reported_comment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $law;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reporter_msg;

    /**
     * @ORM\Column(type="integer")
     */
    private $emergency_level;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="processed_reports")
     */
    private $moderators;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $validated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $punishment;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $punishment_expiration_date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $moderator_msg;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_limit_contest;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $contested;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $contest_result;

    public function __construct()
    {
        $this->date = new \Datetime();
        $this->moderators = new ArrayCollection();
    }

    public function __toString() {
        return 'Report: '.$this->id.' | ['.$this->reporter.'] => ['.$this->reported_user.'] | Law: '.$this->law.' | Validated: '.(($this->validated != NULL) ? $this->validated : 'NULL');
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

    public function getId(): ?int
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

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): self
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getReportedUser(): ?User
    {
        return $this->reported_user;
    }

    public function setReportedUser(?User $reported_user): self
    {
        $this->reported_user = $reported_user;

        return $this;
    }

    public function getReportedPost(): ?Post
    {
        return $this->reported_post;
    }

    public function setReportedPost(?Post $reported_post): self
    {
        $this->reported_post = $reported_post;

        return $this;
    }

    public function getReportedComment(): ?Comment
    {
        return $this->reported_comment;
    }

    public function setReportedComment(?Comment $reported_comment): self
    {
        $this->reported_comment = $reported_comment;

        return $this;
    }

    public function getLaw(): ?string
    {
        return $this->law;
    }

    public function setLaw(string $law): self
    {
        $this->law = $law;

        return $this;
    }

    public function getReporterMsg(): ?string
    {
        return $this->reporter_msg;
    }

    public function setReporterMsg(?string $reporter_msg): self
    {
        $this->reporter_msg = $reporter_msg;

        return $this;
    }

    public function getEmergencyLevel(): ?int
    {
        return $this->emergency_level;
    }

    public function setEmergencyLevel(int $emergency_level): self
    {
        $this->emergency_level = $emergency_level;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getModerators(): Collection
    {
        return $this->moderators;
    }

    public function addModerator(User $moderator): self
    {
        if (!$this->moderators->contains($moderator)) {
            $this->moderators[] = $moderator;
        }

        return $this;
    }

    public function removeModerator(User $moderator): self
    {
        if ($this->moderators->contains($moderator)) {
            $this->moderators->removeElement($moderator);
        }

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(?bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getPunishment(): ?string
    {
        return $this->punishment;
    }

    public function setPunishment(?string $punishment): self
    {
        $this->punishment = $punishment;

        return $this;
    }

    public function getPunishmentExpirationDate(): ?\DateTimeInterface
    {
        return $this->punishment_expiration_date;
    }

    public function setPunishmentExpirationDate(?\DateTimeInterface $punishment_expiration_date): self
    {
        $this->punishment_expiration_date = $punishment_expiration_date;

        return $this;
    }

    public function getModeratorMsg(): ?string
    {
        return $this->moderator_msg;
    }

    public function setModeratorMsg(?string $moderator_msg): self
    {
        $this->moderator_msg = $moderator_msg;

        return $this;
    }

    public function getDateLimitContest(): ?\DateTimeInterface
    {
        return $this->date_limit_contest;
    }

    public function setDateLimitContest(?\DateTimeInterface $date_limit_contest): self
    {
        $this->date_limit_contest = $date_limit_contest;

        return $this;
    }

    public function getContested(): ?bool
    {
        return $this->contested;
    }

    public function setContested(?bool $contested): self
    {
        $this->contested = $contested;

        return $this;
    }

    public function getContestResult(): ?bool
    {
        return $this->contest_result;
    }

    public function setContestResult(?bool $contest_result): self
    {
        $this->contest_result = $contest_result;

        return $this;
    }
}
