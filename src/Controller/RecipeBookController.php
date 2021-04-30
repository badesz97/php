<?php


namespace App\Controller;


use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeBookController extends AbstractController
{
    private $ingredients_file = "../templates/rb/ingredients.txt";
    private $recipes_file = "../templates/rb/recipes.txt";

    /**
     * @param Request $request
     * @return Response
     * @Route(name="rbListIngredients", path="/rb")
     */
    public function rbListIngredientAction(Request $request) : Response {
        $twig_param = ["ingredients" => array()];

        if (file_exists($this->ingredients_file))
        {
            $ingredients = file($this->ingredients_file, FILE_IGNORE_NEW_LINES);
            $ingredient = ["name"=>""];

            foreach ($ingredients as $line) {
                $ingredient["name"] = $line;
                $twig_param["ingredients"][] = $ingredient;
            }
        }

        return $this->render("rb/list.html.twig", $twig_param);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="rbAddIngredient", path="/rb/addIngredient")
     */
    public function addIngredientAction(Request $request) : Response {

       $ingredient = "\n" . $request->request->get("ingredient_name");

        if(file_exists($this->ingredients_file) && strlen($ingredient)>1) {
            file_put_contents($this->ingredients_file, $ingredient, FILE_APPEND);
        }
        return $this->redirectToRoute("rbListIngredients");
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="rbAdd", path="/rb/add")
     */
    public function rbAddAction(Request $request) : Response {
        $all = $request->request->all();
        $recipe="";

        $name = str_replace(" ","_", $all["recipe_name"]);
        $recipe .= strtoupper($name) . "|";
        unset($all["recipe_name"]);

        foreach ($all as $key => $item) {
            if ( str_starts_with($key, "ingredient")) {
                $recipe .= $item;
                $recipe .= "-";
            }
            else if( str_starts_with($key, "entry_num")) {
                $recipe .= $item;
                $recipe .= ";";
            }
        }
        $recipe .= "\n";
        if(file_exists($this->recipes_file)) {
            file_put_contents($this->recipes_file, $recipe, FILE_APPEND);
        }

        return $this->redirectToRoute("rbRecipes");
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="rbRecipes", path="/rb/form")
     */
    public function rbFormAction(Request $request) : Response {
        $twig_param = ["recipes" => array()];

        if (file_exists($this->recipes_file))
        {
            $recipes = file($this->recipes_file, FILE_IGNORE_NEW_LINES);
            $recipe = ["name"=>"", "ingredients"=>""];

            foreach ($recipes as $line) {

                $pos = strpos($line, "|");
                $name = substr($line,0,$pos);
                $rest = substr($line, $pos+1);

                $recipe = ["name"=>"", "ingredients"=>""];
                $recipe["name"] = $name;
                $ings = str_replace(";",",", $rest);

                $recipe["ingredients"] = substr($ings,0,strlen($ings)-1) ;
                $twig_param["recipes"][] = $recipe;
            }
        }

        return $this->render("rb/form.html.twig", $twig_param);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="rbSetRecipes", path="/rb/setRecipes")
     */
    public function rbSetRecipes(Request $request) : Response {
        $twig_param = ["datas" => array()];

        $name = $request->request->get("entry_name");
        $num = $request->request->get("entry_ingredient_number");

        if (is_numeric($num) && is_string($name)) {
            $data = ["name" =>"", "number"=>""];
            $data["name"] = $name;
            $data["number"] = $num;
            $twig_param["datas"][] = $data;

            if (file_exists($this->ingredients_file))
            {
                $ingredients = file($this->ingredients_file, FILE_IGNORE_NEW_LINES);
                $ingredient = ["name"=>""];

                foreach ($ingredients as $line) {
                    $ingredient["name"] = $line;
                    $ingredient["number"] = "";
                    $twig_param["ingredients"][] = $ingredient;

                }
            }
            return $this->render("rb/recipe.html.twig", $twig_param);
        }

        return $this->redirectToRoute("rbRecipes");
    }
}