<?php


namespace App\Controller;


use App\DTO\DtoBase;
use App\DTO\LoginDto;
use App\DTO\TextDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditorController extends AbstractController
{
    private $passFile = "../templates/editor/users.txt";
    private $dataFile = "../templates/editor/data.txt";

    /** @var FormFactoryInterface */
    private $formFactory;

    /**
     * EditorController constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @Route(name="editor_create", path="/editor/create")
     * @param Request $request
     * @return Response
     */
    public function createPwFileAction(Request $request) : Response {
        $str = "";
        $str .= "bill\t".password_hash("billpass", PASSWORD_DEFAULT)."\n";
        $str .= "joe\t".password_hash("joepass", PASSWORD_DEFAULT)."\n";
        $str .= "admin\t".password_hash("adminpass", PASSWORD_DEFAULT)."\n";

        file_put_contents($this->passFile, $str);
        return new Response(nl2br($str));
    }

    /**
     * @Route(name="editor_logout", path="/editor/logout")
     * @param Request $request
     * @return Response
     */
    public function logoutAction(Request $request) : Response {
        $this->get('session')->clear();
        $this->addFlash("notice","LOGGED OUT");
        return $this->redirectToRoute("editor");
    }

    /**
     * @Route(name="editor", path="/editor")
     * @param Request $request
     * @return Response
     */
    public function editorAction(Request $request) : Response {
        $twigParams = ["filetext"=>"", "sessiontext"=>"", "form"=>null];
        // return $this->render("editor/editor.html.twig", $twigParams);

        $twigParams["sessiontext"] = $this->get('session')->get('customText');
        if (file_exists($this->dataFile)) {
            $twigParams["filetext"] = file_get_contents($this->dataFile);
        }

        $sessionUser = $this->get('session')->get('userName');
        if ($sessionUser) {
            $dto = new TextDto($this->formFactory, $request);
        } else {
            $dto = new LoginDto($this->formFactory, $request);
        }
        /** @var DtoBase $dto */
        $form = $dto->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($sessionUser) {
                $this->processTextInput($dto,$form);
            } else {
                $this->processLoginInput($dto);
            }
            return $this->redirectToRoute("editor");
        }
        $twigParams["form"] = $form->createView();
        return $this->render("editor/editor.html.twig",$twigParams);
    }

    private function processTextInput(TextDto $dto, FormInterface $form) : void
    {
        $text = $dto->getTextContent();
        if ($form->get("saveToSession")->isClicked()) {
            $this->get('session')->set('customText', $text);
            $this->addFlash('notice','SAVED TO SESSION');
        } else {
            file_put_contents($this->dataFile, $text);
            $this->addFlash('notice','SAVED TO FILE');
        }
    }

    private function processLoginInput(LoginDto $dto) : void
    {
        $uname = $dto->getUserName();
        $upass = $dto->getUserPassword();
        $pwfile = file($this->passFile, FILE_IGNORE_NEW_LINES);

        foreach ($pwfile as $line) {
            $arr = explode("\t", $line);
            if ($uname==$arr[0] && $isPassOk = password_verify($upass, $arr[1])) {
                $this->get('session')->set('userName', $arr[0]);
                $this->addFlash('notice',"LOGIN OK");
            }

        }
        $this->addFlash('notice',"LOGIN FAILED");
    }
}