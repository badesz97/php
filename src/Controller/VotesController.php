<?php


namespace App\Controller;

use App\DTO\TextDto;
use App\Entity\Choice;
use App\Entity\Question;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VotesController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route(path="/votes", name="votesListQuestions")
     */
    public function listQuestionsAction(Request $request) : Response {
        // SHOULD NOT USE DOCTRINE IN CONTROLLER! USE AS A SERVICE
        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();
        $dto = new TextDto($this->get('form.factory'), $request);

        $form = $dto->getForm();
        $form->handleRequest($request);
        $twig_params = ["questions"=>$questions, "form"=>$form];

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processTxtInputQ($dto, $form);
            return $this->redirectToRoute("votesListQuestions");
        }
        $twig_params["form"] = $form->createView();
        return $this->render("votes/questions.html.twig", $twig_params);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/votes/question/{question}", name="votesListChoices", requirements={"question": "\d+"})
     */
    public function listChoicesAction(Request $request, int $question) : Response {
        /** @var Question $questionInstance */
        $questionInstance = $this->getDoctrine()->getRepository(Question::class)->find($question);
        if (!$questionInstance) throw $this->createNotFoundException();

        $dto = new TextDto($this->get('form.factory'), $request);

        $form = $dto->getForm();
        $form->handleRequest($request);
        $twig_params = ["choices"=>$questionInstance->getQChoices(), "form"=>$form, "question"=>$question];

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processTxtInputC($dto, $form, $question);
            //return $this->render("votes/questions.html.twig", $twig_params);
            return $this->redirectToRoute("votesListChoices", $twig_params);
        }
        $twig_params["form"] = $form->createView();

        return $this->render("votes/choices.html.twig", $twig_params);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/votes/vote/{choice}", name="votesVote", requirements={"choice": "\d+"})
     */
    public function voteAction(Request $request, int $choice) : Response {
        /** @var Choice $choiceInstance */
        $choiceInstance = $this->getDoctrine()->getRepository(Choice::class)->find($choice);
        if (!$choiceInstance) throw $this->createNotFoundException();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository(Choice::class)->createQueryBuilder("c")
            ->update()
            ->set("c.ch_numvotes","c.ch_numvotes+1")
            ->where("c.ch_id = :choiceId")
            ->setParameter("choiceId", $choice)
            ->getQuery();

        $rows = $query->execute();
        $this->addFlash("notice", "VOTED FOR '{$choiceInstance}', AFFECTED:{$rows}");
        return $this->redirectToRoute("votesListChoices", ["question"=>$choiceInstance->getChQuestion()->getQId()]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/votes/choice/delete/{choice}", name="votesDeleteChoice", requirements={"choice": "\d+"})
     */
    public function deleteChoiceAction(Request $request, int $choice) : Response {
        /** @var Choice $choiceInstance */
        $choiceInstance = $this->getDoctrine()->getRepository(Choice::class)->find($choice);
        if (!$choiceInstance) throw $this->createNotFoundException();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $em->remove($choiceInstance);
        $em->flush();
        return $this->redirectToRoute("votesListChoices", ["question"=>$choiceInstance->getChQuestion()->getQId()]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/votes/question/delete/{question}", name="votesDeleteQuestion", requirements={"question": "\d+"})
     */
    public function deleteQuestionAction(Request $request, int $question) : Response {
        $qs = $this->getDoctrine()->getRepository(Question::class)->findAll();
        /** @var Question $questionInstance */
        $questionInstance = $this->getDoctrine()->getRepository(Question::class)->findOneBy(["q_id"=>$question]);
        if (!$questionInstance) throw $this->createNotFoundException();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Choice[] $choices */
        $choices = $this->getDoctrine()->getRepository(Choice::class)->findAll();
        foreach ($choices as $choice) {
            if ($choice->getChQuestion()->getQId()==$question) {
                $em->remove($choice);
                $em->flush();
            }
        }

        $em->remove($questionInstance);
        $em->flush();
        return $this->redirectToRoute("votesListQuestions");
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/question/add", name="questionAdd")
     */
    public function addQuestionAction(Request $request) : Response {

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $question = new Question();
        $q_text = $request->request->get("question_text");
        $question->setQText($q_text);
        $em->persist($question);
        $em->flush();

        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();
        return $this->render("votes/questions.html.twig", ["questions"=>$questions]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/choice/add", name="choiceAdd")
     */
    public function addChoiceAction(Request $request) : Response {

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $choice = new Choice();
        $ch_text = $request->request->get("choice_text");
        $choice->setChText($ch_text);
        $em->persist($choice);
        $em->flush();

    }


    private function processTxtInputQ(TextDto $dto, FormInterface $form) : void
    {
        $text = $dto->getTextContent();
        if ($form->get("Add")->isClicked()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $question = new Question();
            $question->setQText($text);
            $em->persist($question);
            $em->flush();
        }
    }

    private function processTxtInputC(TextDto $dto, FormInterface $form, int $question)
    {
        $text = $dto->getTextContent();
        $questionInstance = $this->getDoctrine()->getRepository(Question::class)->find($question);
        if ($form->get("Add")->isClicked()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $choice = new Choice();
            $choice->setChText($text);
            $choice->setChQuestion($questionInstance);
            $choice->setChNumvotes(0);
            $em->persist($choice);
            $em->flush();
        }
    }
}