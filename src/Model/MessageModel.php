<?php


namespace App\Model;


class MessageModel
{
    /** @var string */
    private $userName;

    /** @var string */
    private $timestamp;

    /** @var string */
    private $text;

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return MessageModel
     */
    public function setUserName(string $userName): MessageModel
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     * @return MessageModel
     */
    public function setTimestamp(string $timestamp): MessageModel
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return MessageModel
     */
    public function setText(string $text): MessageModel
    {
        $this->text = $text;
        return $this;
    }



}