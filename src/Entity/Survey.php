<?php

namespace App\Entity;

use App\Controller\SurveyController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SurveyRepository")
 */
class Survey
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=10)
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="surveys")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $question;

    /**
     * @ORM\Column(type="array")
     *
     * @example: Answers have this format:
     *  [
     *      ["Yes"] => [15, 35, 78] // Numbers are user' id
     *      ["No"] => [103, 67, 48] // But when we fetch the survey, they are replaced by entities
     *  ]
     */
    private $answers;

    /**
     * @example: Answers with fetched users, they have this format:
     *  [
     *      ["Yes"] => [User 15, User 35, User 78] // These are user entities
     *      ["No"] => [User 103, User 67, User 48]
     *  ]
     */
    private $_answers;

    /**
     * @ORM\Column(type="array")
     *
     * @example: Answer's color have this format:
     *  [
     *      ["Yes"] => "55FF99"
     *      ["No"] => "FF0000"
     *  ]
     */
    private $color;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Post", inversedBy="surveys")
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Comment", inversedBy="surveys")
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Laws", inversedBy="survey", cascade={"persist", "remove"})
     */
    private $law;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Report", inversedBy="survey", cascade={"persist", "remove"})
     */
    private $report;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $expiration_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $role;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->question = "Survey";
        $this->answers = array();
        $this->color = array();
        $this->date = new \Datetime();
        $this->role = "ROLE_USER";
    }

    public function __toString() {
        return 'Survey: '.$this->id;
    }

    public function browse()
    {
        $result = array();

        foreach ($this as $key => $value) {
            if ($key !== '_answers') {
                if ($key == 'answers') {
                    $answers = $this->getAnswers();
                    $processed = '';
                    foreach ($answers as $answername => $answervalues) {
                        $processed .= $answername . " => [";
                        foreach ($answervalues as $user) {
                            $processed .= $user . ", ";
                        }
                        if ($processed !== $answername . " => [") {
                            $processed = substr($processed, 0, -2);
                        }
                        $processed .= "] / ";
                    }
                    $processed = substr($processed, 0, -3);
                    $result[$key] = $processed;
                } else if ($value !== NULL) {
                    $result[$key] = $value;
                } else {
                    $result[$key] = 'NULL';
                }
            }
        }

        return $result;
    }

    public function isOrphaned() {
        if ($this->posts->isEmpty() == true &&
            $this->comments->isEmpty() == true &&
            $this->law == NULL &&
            $this->report == NULL) {
            return true;
        }

        return false;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getRawAnswers(): ?array
    {
        return $this->answers;
    }

    public function setDoneAnswers(array $answers): self
    {
        $this->_answers = $answers;

        return $this;
    }

    public function getAnswers(): ?array
    {
        return $this->_answers;
    }

    public function getAnswersByPercentage(): ?array
    {
        $answers = $this->answers;
        $total = 0;
        foreach ($answers as $value) {
            $total += count($value);
        }
        if ($total != 0) {
            foreach ($answers as $key => $value) {
                $answers[$key] = (count($value) / $total) * 100;
            }
        } else {
            foreach ($answers as $key => $value) {
                $answers[$key] = (1 / count($answers)) * 100;
            }
        }
        $total = 0;
        foreach ($answers as $value) {
            $total += $value;
        }
        arsort($answers);
        return $answers;
    }

    public function getAnswersTotal()
    {
        $answers = $this->answers;
        $total = 0;
        foreach ($answers as $value) {
            $total += count($value);
        }
        return $total;
    }

    public function addAnswer(string $key, User $user): self
    {
        $this->removeAnswer($user);
        $this->answers[$key][] = $user->getId();

        return $this;
    }

    public function removeAnswer(User $user): self
    {
        foreach ($this->answers as $i => $answerOption) {
            foreach ($answerOption as $j => $value) {
                if ($value == $user->getId()) {
                    unset($this->answers[$i][$j]);
                }
            }
            $this->answers[$i] = array_values($this->answers[$i]);
        }

        return $this;
    }

    public function addAnswerOption(string $key, string $color): self
    {
        if (!array_key_exists($key, $this->answers)) {
            $this->answers[$key] = [];
            $this->color[$key] = $color;
        }

        return $this;
    }

    public function removeAnswerOption(string $key, string $color): self
    {
        if (array_key_exists($key, $this->answers)) {
            unset($this->answers[$key]);
            unset($this->color[$key]);
        }

        return $this;
    }

    public function getColor(): ?array
    {
        return $this->color;
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
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
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
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
        }

        return $this;
    }

    public function getLaw(): ?Laws
    {
        return $this->law;
    }

    public function setLaw(?Laws $law): self
    {
        $this->law = $law;

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(?\DateTimeInterface $expiration_date): self
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
