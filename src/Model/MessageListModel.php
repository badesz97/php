<?php


namespace App\Model;


use Symfony\Component\Form\FormView;

class MessageListModel
{
    /** @var FormView */
    private $messageForm;

    /** @var MessageModel[] */
    private $messageList;

    /**
     * @return FormView
     */
    public function getMessageForm(): FormView
    {
        return $this->messageForm;
    }

    /**
     * @param FormView $messageForm
     * @return MessageListModel
     */
    public function setMessageForm(FormView $messageForm): MessageListModel
    {
        $this->messageForm = $messageForm;
        return $this;
    }

    /**
     * @return MessageModel[]
     */
    public function getMessageList(): array
    {
        return $this->messageList;
    }

    /**
     * @param MessageModel[] $messageList
     * @return MessageListModel
     */
    public function setMessageList(array $messageList): MessageListModel
    {
        $this->messageList = $messageList;
        return $this;
    }


}