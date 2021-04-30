<?php


namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;

/**
 * Class Choice
 * @package App\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="choices")
 * @ORM\HasLifecycleCallbacks
 */
class Choice
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ch_id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $ch_inserted;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $ch_modified;

    /**
     * @var bool|null
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ch_visible;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    private $ch_text;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true, length=100)
     */
    private $ch_numvotes;

    /**
     * @var Question|null
     * @ORM\JoinColumn(name="ch_question", referencedColumnName="q_id")
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="q_choices")
     */
    private $ch_question;

    public function __toString()
    {
        $question = $this->ch_question ? $this->ch_question->getQText() : "N/A";
        return "{$question} / {$this->ch_text} / {$this->ch_numvotes}";
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimeStamps()
    {
        $this->ch_modified = new \DateTime();
        if($this->ch_inserted==null)
        {
            $this->ch_inserted = new \DateTime();
        }
    }

    /**
     * @return int
     */
    public function getChId(): int
    {
        return $this->ch_id;
    }

    /**
     * @return \DateTime
     */
    public function getChInserted(): \DateTime
    {
        return $this->ch_inserted;
    }

    /**
     * @param \DateTime $ch_inserted
     * @return Choice
     */
    public function setChInserted(\DateTime $ch_inserted): Choice
    {
        $this->ch_inserted = $ch_inserted;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getChModified(): \DateTime
    {
        return $this->ch_modified;
    }

    /**
     * @param \DateTime $ch_modified
     * @return Choice
     */
    public function setChModified(\DateTime $ch_modified): Choice
    {
        $this->ch_modified = $ch_modified;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getChVisible(): ?bool
    {
        return $this->ch_visible;
    }

    /**
     * @param bool|null $ch_visible
     * @return Choice
     */
    public function setChVisible(?bool $ch_visible): Choice
    {
        $this->ch_visible = $ch_visible;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getChText(): ?string
    {
        return $this->ch_text;
    }

    /**
     * @param string|null $ch_text
     * @return Choice
     */
    public function setChText(?string $ch_text): Choice
    {
        $this->ch_text = $ch_text;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getChNumvotes(): ?int
    {
        return $this->ch_numvotes;
    }

    /**
     * @param int|null $ch_numvotes
     * @return Choice
     */
    public function setChNumvotes(?int $ch_numvotes): Choice
    {
        $this->ch_numvotes = $ch_numvotes;
        return $this;
    }

    /**
     * @return Question|null
     */
    public function getChQuestion(): ?Question
    {
        return $this->ch_question;
    }

    /**
     * @param Question|null $ch_question
     * @return Choice
     */
    public function setChQuestion(?Question $ch_question): Choice
    {
        $this->ch_question = $ch_question;
        return $this;
    }


}