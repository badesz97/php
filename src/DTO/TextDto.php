<?php


namespace App\DTO;


use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TextDto extends DtoBase
{
    /** @var string */
    private $textContent = "";

    public function __construct(FormFactoryInterface $formFactory, Request $request)
    {
        parent::__construct($formFactory, $request);
    }

    public function getForm(): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $this);
        $builder->add("textContent", TextareaType::class);
        //$builder->add("saveToSession", SubmitType::class);
        $builder->add("Add", SubmitType::class );
        return $builder->getForm();
    }

    /**
     * @return string
     */
    public function getTextContent(): string
    {
        return $this->textContent;
    }

    /**
     * @param string $textContent
     */
    public function setTextContent(string $textContent): void
    {
        $this->textContent = $textContent;
    }




}