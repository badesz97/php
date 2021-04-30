<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SongController
 * @package App\Controller
 * @Route(path="songs/")
 */
class SongController
{
    /**
     * @param Request $request
     * @return Response
     * @Route(path="form",name="getFormRoute")
     */
    public function getFormAction (Request $request) : Response {
        $str = $this->getSongForm();
        return new Response($str);
    }

    private function getSongForm() : string {
        $tpl_form = file_get_contents("../templates/songs/form.html");
        $tpl_option = file_get_contents("../templates/songs/option.html");
        $songs = $this->getSongs();

        $rows = "";

        for ($i=0;$i<count($songs);$i++) {
            $rows.=str_replace("{{ OPTION }}",$songs[$i],$tpl_option);
        }
        $tpl_form = str_replace("{{ OPTIONS }}",$rows,$tpl_form);

        return $tpl_form;
    }

    private function getSongs() : array {
        $lines = file("../templates/songs/songs.txt");
        return  $lines;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="vote",name="getVoteRoute")
     */
    public function getVoteAction(Request $request) : Response {
        $name=$request->request->get("name");
        $email=$request->request->get("email");
        $song=$request->request->get("song");

        

        if ($name!=null && $email!=null && $song!=null) {
            file_put_contents("../templates/songs/songvotes.txt","$name\n",FILE_APPEND);
            file_put_contents("../templates/songs/songvotes.txt","$email\n",FILE_APPEND);
            file_put_contents("../templates/songs/songvotes.txt","$song\n",FILE_APPEND);
        }

        return new RedirectResponse("list");
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="list",name="getListRoute")
     */
    public function getListAction() : Response {
        $html = $this->getSongsList();
        return new Response($html);
    }

    private function getSongsList() : string {
        $tpl_list=file_get_contents("../templates/songs/list.html");
        $tpl_row=file_get_contents("../templates/songs/row.html");

        $songs_votes = $this->getVotes();
        $rows = "";
        foreach ($songs_votes as $key => $value) {
            $str = "$key [$value]";
            $rows.= str_replace("{{ ROW }}", $str, $tpl_row);
        }

        $output = $tpl_list;
        $output = str_replace("{{ ROWS }}", $rows, $output);
        return $output;
    }

    private function getVotes() : array {
        $songs = $this->getSongs();
        $votes = $this->getVotesList();
        $result = array();

        for ($i=0; $i < count($songs); $i++) { 
            $count =0;
            for ($j=0; $j < count($votes); $j++) { 
                if ($songs[$i] == $votes[$j]) {
                    $count++;
                }
            }
            $result[$songs[$i]] = $count; 
        }

        return $result;
    }

    private function getVotesList() : array {
        $lines = file("../templates/songs/songvotes.txt");
        return  $lines;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="lottery",name="lotteryRoute")
     */
    public function lotteryAction() : Response {
        $winner = $this->getWinner();
        return new Response($winner);
    }

    private function getWinner() : string{
        $votes = $this->getVotesList();
        $voters = array();
        if (count($votes)>0) {
            for ($i=0; $i < count($votes); $i++) { 
                if ($i%4==0) {
                    $str = $votes[$i];
                    $str.=" - ";
                    $str.= $votes[$i+1];
                    array_push($voters,$str);
                }
            }
        } else {
            return "No voter";
        }

        $idx = rand(1,count($voters));
        $winner = $voters[$idx-1];
        return $winner;
    }

}