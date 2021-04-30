<?php


namespace App\Model;


use Symfony\Component\Form\FormView;

class TopicListModel
{
    /** @var FormView */
    private $topicForm;

    /** @var TopicModel[] */
    private $topicList;

    /**
     * @return FormView
     */
    public function getTopicForm(): FormView
    {
        return $this->topicForm;
    }

    /**
     * @param FormView $topicForm
     * @return TopicListModel
     */
    public function setTopicForm(FormView $topicForm): TopicListModel
    {
        $this->topicForm = $topicForm;
        return $this;
    }

    /**
     * @return TopicModel[]
     */
    public function getTopicList(): array
    {
        return $this->topicList;
    }

    /**
     * @param TopicModel[] $topicList
     * @return TopicListModel
     */
    public function setTopicList(array $topicList): TopicListModel
    {
        $this->topicList = $topicList;
        return $this;
    }
}