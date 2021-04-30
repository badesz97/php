<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Question
 * @package App\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="questions")
 */
class Question
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $q_id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $q_text;

    /**
     * @var ArrayCollection|Choice[]
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="ch_question")
     */
    private $q_choices;

    public function __construct(){
        $this->q_choices = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->q_text;
    }

    /**
     * @return int
     */
    public function getQId(): int
    {
        return $this->q_id;
    }

    /**
     * @return string|null
     */
    public function getQText(): ?string
    {
        return $this->q_text;
    }

    /**
     * @param string|null $q_text
     * @return Question
     */
    public function setQText(?string $q_text): Question
    {
        $this->q_text = $q_text;
        return $this;
    }

    /**
     * @return Choice[]|ArrayCollection
     */
    public function getQChoices()
    {
        return $this->q_choices;
    }

    /**
     * @param Choice[]|ArrayCollection $q_choices
     * @return Question
     */
    public function setQChoices($q_choices)
    {
        $this->q_choices = $q_choices;
        return $this;
    }


}