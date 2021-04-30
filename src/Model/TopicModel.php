<?php


namespace App\Model;
/*
 * MVC model.
 */

class TopicModel
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TopicModel
     */
    public function setId(int $id): TopicModel
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TopicModel
     */
    public function setName(string $name): TopicModel
    {
        $this->name = $name;
        return $this;
    }


}