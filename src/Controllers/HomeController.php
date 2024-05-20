<?php

namespace App\Controllers;
use App\Models\CalcRevenueModel;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class HomeController
{
    private CalcRevenueModel $model;
    private Environment $twig;

    public function __construct()
    {
        //Twig launch
        $loader = new FilesystemLoader(__DIR__ . '/../Views/');
        $this->twig = new Environment($loader);

        //Call to model class
        $this->model = new CalcRevenueModel();
    }
    
    public function showRevenue()
    {
       $tabData = $this->model->calcRevenues();
        //var_dump($tabData);

       echo $this->twig->render(
            'index.html.twig', 
            ['tabData' => $tabData]
        );

    }

    
}