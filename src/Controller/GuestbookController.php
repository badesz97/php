<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuestbookController extends AbstractController
{
    private $fname="../templates/gb/gb.txt";

    /**
     * @param Request $request
     * @return Response
     * @Route(name="gbList", path="/gb")
     */
    public function gbListAction(Request $request) : Response {
        $twig_params = ["entries" => array()];

        if (file_exists($this->fname))
        {
            $entries = file($this->fname, FILE_IGNORE_NEW_LINES);
            $entry = ["name"=>"", "email"=>"", "text"=>""];

            foreach ($entries as $line) {
                $first = substr($line,0,1);
                $rest = substr($line,1);

                if ($first == '#') {
                    if ($entry["text"]) $twig_params["entries"][] = $entry; //save prev entry
                    $entry = ["name"=>"", "email"=>"", "text"=>""];
                    $entry["name"]=$rest;
                } else if ($first == '@') {
                    $entry["email"]=$rest;
                } else {
                    $entry["text"] .= $line . "\n";
                }
            }
            if ($entry["text"]) $twig_params["entries"][] = $entry; //save prev entry
        }
        return $this->render("gb/list.html.twig", $twig_params);

    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="gbAdd", path="/gb/add")
     */
    public function gbAddAction(Request $request) : Response {
        $name = $request->request->get("entry_name");
        $email = $request->request->get("entry_email");
        $text = $request->request->get("entry_text");
        // SANITIZE!!! in case of hacking
        $text = str_replace(['#','@'], "", $text);
        $name = str_replace(["\n","\r"], "", $name);
        $email = str_replace(["\n","\r"], "", $email);
        $email = filter_var($email,FILTER_VALIDATE_EMAIL);
        if ($name && $email && $text) {
            $newentry = "#{$name}\n@{$email}\n{$text}\n";
            file_put_contents($this->fname, $newentry, FILE_APPEND);
            $this->addFlash("notice","ENTRY SAVED");
        } else {
            $this->addFlash("notice","DATA ERROR");
        }
        return $this->redirectToRoute("gbList");

        //return new Response("Hello, Add");
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="gbForm", path="/gb/form")
     */
    public function gbFormAction(Request $request) : Response {
        return $this->render("gb/form.html.twig",
            ["currentDate"=>date("Y.m.d.")]);
    }
}