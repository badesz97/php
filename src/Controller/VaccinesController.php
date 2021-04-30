<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VaccinesController
 * @package App\Controller
 * @Route(path="vaccines/")
 */
class VaccinesController {

    /**
     * @param Request $request
     * @return Response
     * @Route(path="data",name="getDataRoute")
     */
    public function displayTableAction(Request $request) : Response {
        $str = $this->getOverviewPage();
        return new Response($str);
    }

    private function getOverviewPage() : string {
        $table = $this->getVaccinesTable();
        $list = $this->getGeneralQuestionsList();
        $randTable = $this->getRandTable();
        $compList = $this-> getComplexQuestionsList();

        $tpl_page = file_get_contents("../templates/TSV/vaccinesOverviewPage.html");
        $output=$tpl_page;
        $output = str_replace("{{ table }}", $table, $output);

        $output = str_replace("{{ generalQuestions }}", $list, $output);

        $output = str_replace("{{ randTable }}", $randTable, $output);

        $output = str_replace("{{ complexQuestions }}", $compList, $output);

        return $output;
    }

    private function getVaccinesTable() : string
    {
        $tpl_table = file_get_contents("../templates/TSV/table.html");
        $tpl_rowsep = file_get_contents("../templates/TSV/rowsep.html");
        $tpl_normalcell = file_get_contents("../templates/TSV/cellnormal.html");

        $vaccines = $this->getDataFromFile();

        $rows = "";
        //table
        foreach ($vaccines as $key => $record) {
            foreach ($record as $key2 => $value) {
                $rows .= str_replace("{{ data }}", $value, $tpl_normalcell);
            }
            $rows .= $tpl_rowsep;
        }

        $output = $tpl_table;
        $output = str_replace("{{ rows }}", $rows, $output);

        return $output;
    }

    private function getRandomizedVaccines(array $vaccines) : array {
        $randVaccines = array();
        for ($i =0;$i<20;$i++) {
            $randVaccine = array();
            array_push($randVaccine, $vaccines[rand(0,9)][0]);
            array_push($randVaccine, $vaccines[rand(0,9)][1]);
            array_push($randVaccine, $vaccines[rand(0,9)][2]);
            array_push($randVaccine, $vaccines[rand(0,9)][3]);
            array_push($randVaccine, $vaccines[rand(0,9)][4]);
            array_push($randVaccine, $vaccines[rand(0,9)][5]);

            array_push($randVaccines,$randVaccine);
        }

        return $randVaccines;
    }

    private function getRandTable() : string
    {
        $tpl_table = file_get_contents("../templates/TSV/table.html");
        $tpl_rowsep = file_get_contents("../templates/TSV/rowsep.html");
        $tpl_normalcell = file_get_contents("../templates/TSV/cellnormal.html");

        $vaccines = $this->getRandomizedVaccines($this->getDataFromFile());

        $rows = "";
        //table
        foreach ($vaccines as $key => $record) {
            foreach ($record as $key2 => $value) {
                $rows .= str_replace("{{ data }}", $value, $tpl_normalcell);
            }
            $rows .= $tpl_rowsep;
        }

        $output = $tpl_table;
        $output = str_replace("{{ rows }}", $rows, $output);

        return $output;
    }

    private function getGeneralQuestionsList() : string {
        $tpl_list = file_get_contents("../templates/TSV/generalQuestionsList.html");
        $questions = $this->getGeneralQuestions();
        $answers = $this->getGeneralAnswers();

        $tpl_list = str_replace("{{ q1 }}", $questions[0], $tpl_list);
        $tpl_list = str_replace("{{ a1 }}", $answers[0], $tpl_list);

        $tpl_list = str_replace("{{ q2 }}", $questions[1], $tpl_list);
        $tpl_list = str_replace("{{ a2 }}", $answers[1], $tpl_list);

        $tpl_list = str_replace("{{ q3 }}", $questions[2], $tpl_list);
        $tpl_list = str_replace("{{ a3 }}", $answers[2], $tpl_list);

        $tpl_list = str_replace("{{ q4 }}", $questions[3], $tpl_list);
        $tpl_list = str_replace("{{ a4 }}", $answers[3], $tpl_list);

        $tpl_list = str_replace("{{ q5 }}", $questions[4], $tpl_list);
        $tpl_list = str_replace("{{ a5 }}", $answers[4], $tpl_list);


        return $tpl_list;

    }

    private function getComplexQuestionsList() : string {
        $tpl_list = file_get_contents("../templates/TSV/complexQuestionsList.html");
        $questions = $this->getComplexQuestions();
        $answers = $this->getComplexAnswers();

        $tpl_list = str_replace("{{ q1 }}", $questions[0], $tpl_list);
        $tpl_list = str_replace("{{ a1 }}", $answers[0], $tpl_list);

        $tpl_list = str_replace("{{ q2 }}", $questions[1], $tpl_list);
        $tpl_list = str_replace("{{ a2 }}", $answers[1], $tpl_list);

        $tpl_list = str_replace("{{ q3 }}", $questions[2], $tpl_list);
        $tpl_list = str_replace("{{ a3 }}", $answers[2], $tpl_list);

        return $tpl_list;

    }

    private function  getGeneralQuestions() : array {
        $q = array();
        $q[0] = "Which is the most efficient vaccine?";
        $q[1] = "Which is the least efficient vaccine?";
        $q[2] = "Which country produces most of the type of the vaccines?";
        $q[3] = "Is there any vector vaccine?";
        $q[4] = "How many kind of Chinese vaccines are there?";

        return $q;
    }

    private function  getGeneralAnswers() : array {
        $a = array();
        $vaccines = $this->getDataFromFile();

        $a[0] = $this->getMostEfficient($vaccines);
        $a[1] = $this->getLeastEfficient($vaccines);
        $a[2] = $this->getMostSelledCountry($vaccines);
        $a[3] = $this->getVaccineWithVector($vaccines);
        $a[4] = $this->getChineseCnt($vaccines);

        return $a;
    }

    private function  getComplexQuestions() : array {
        $q = array();
        $q[0] = "What is the average efficiency of the vaccines?";
        $q[1] = "What is the average efficiency of the Chinese vaccines?";
        $q[2] = "How many vaccines are better than the average efficiency?";

        return $q;
    }

    private function  getComplexAnswers() : array {
        $a = array();
        $vaccines = $this->getDataFromFile();
        $randVaccines = $this->getRandomizedVaccines($vaccines);

        $a[0] = $this->getAverage($vaccines, $randVaccines);
        $a[1] = $this->getChineseAverage($vaccines, $randVaccines);
        $a[2] = $this->getBetterThanAverage($vaccines,$randVaccines,$this->getChineseAverage($vaccines, $randVaccines));

        return $a;
    }

    private function getAverage($vaccines, $randVaccines) : string {
        $all = array();

        foreach ($vaccines as $key=>$vaccine) {
            array_push($all, $vaccine);
        }

        foreach ($randVaccines as $key=>$vaccine) {
            array_push($all, $vaccine);
        }

        $sum=0;
        foreach ($all as $key=>$vaccine) {
            $sum = $sum + intval($vaccine[4]);
        }

        return round($sum / count($all));
    }

    private function getBetterThanAverage($vaccines, $randVaccines, $avg) : string {
        $all = array();

        foreach ($vaccines as $key=>$vaccine) {
            array_push($all, $vaccine);
        }

        foreach ($randVaccines as $key=>$vaccine) {
            array_push($all, $vaccine);
        }

        $cnt=0;
        foreach ($all as $key=>$vaccine) {
            if ($vaccine[4] > $avg)
                $cnt++;
        }

        return $cnt;
    }

    private function getChineseAverage($vaccines, $randVaccines) : string {
        $all = array();

        foreach ($vaccines as $key=>$vaccine) {
            array_push($all, $vaccine);
        }

        foreach ($randVaccines as $key=>$vaccine) {
            array_push($all, $vaccine);
        }

        $sum=0;
        $cnt=0;

        for ($i =1;$i<count($all);$i++){
            for ($j=0;$j<6;$j++) {
                if ($j==2 && $all[$i][$j]=="CHI") {
                    $sum = $sum + $all[$i][4];
                    $cnt=$cnt+1;
                }
            }
        }

        return round($sum / $cnt);
    }

    private function getMostSelledCountry(array $vaccines) : string {
        $countries = array();
        for ($i =1;$i<count($vaccines);$i++){
            for ($j=0;$j<6;$j++) {
               if ($j==2) {
                   array_push($countries, $vaccines[$i][$j]);
               }
            }
        }

        $cntArray = array();

        foreach ($countries as $key1=>$country1) {
            $cnt=0;
            foreach ($countries as $key2=>$country2) {
                if ($country1==$country2) {
                    $cnt++;
                }
            }
            array_push($cntArray, $cnt);
        }

        $maxIdx=$cntArray[0];
        for ($i=0;$i<count($cntArray);$i++) {
            if ($cntArray[$i]>$maxIdx) {
                $maxIdx=$cntArray[$i];
            }
        }
        return $countries[$maxIdx];
    }

    private function getChineseCnt(array $vaccines) : string {
        $cnt=0;
        for ($i =1;$i<count($vaccines);$i++){
            for ($j=0;$j<6;$j++) {
                if ($j==2 && $vaccines[$i][$j]=="CHI") {
                    $cnt++;
                }
            }
        }
        return $cnt;
    }

    private function getMostEfficient(array $vaccines) : string {
        $x = $vaccines[0];
        for ($i =1;$i<count($vaccines);$i++){
            for ($j=0;$j<6;$j++) {
                if ($j==4) {
                    if ($vaccines[$i][$j] > $x[$j]) {
                        $x = $vaccines[$i];
                    }
                }
            }
        }

        return $x[0];
    }

    private function getVaccineWithVector(array $vaccines) : string {
        for ($i =1;$i<count($vaccines);$i++){
            for ($j=0;$j<6;$j++) {
                if ($j==3 && $vaccines[$i][$j]=="vektor") {
                    return "van";
                }
            }
        }
        return "nincs";
    }

    private function getLeastEfficient(array $vaccines) : string {
        $x = $vaccines[0];
        for ($i =1;$i<count($vaccines);$i++){
            for ($j=0;$j<6;$j++) {
                if ($j==4) {
                    if ($vaccines[$i][$j] < $x[$j]) {
                        $x = $vaccines[$i];
                    }
                }
            }
        }

        return $x[0];
    }

    private function getDataFromFile() : array {

        $path='../templates/TSV/database.csv';
        $database = array();
        $rows_array=file($path, FILE_IGNORE_NEW_LINES);
        foreach ($rows_array as $key=>$row) {
            $database[]=explode(";",$row);
        }

        return $database;
    }

}