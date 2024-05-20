<?php

namespace App\Controllers;
use App\Models\CalcRevenueModelAll;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class HomeController
{
    private CalcRevenueModelAll $model;
    private Environment $twig;

    public function __construct()
    {
        //Twig launch
        $loader = new FilesystemLoader(__DIR__ . '/../Views/');
        $this->twig = new Environment($loader);

        //Call to model class
        $this->model = new CalcRevenueModelAll();
    }
    
    public function showRevenue()
    {
       //$tabData = $this->model->calcRevenuesOne();
       $tabData = $this->model->calcRevenuesAll();
       //var_dump($tabData);

       $totalCa = 0;
        foreach ($tabData as $data) {
            $totalCa += $data['ca'];
        }

        $totalNbLesson = 0;
        foreach($tabData as $data)
        {
            $totalNbLesson += $data['nbLesson'];
        }

       echo $this->twig->render(
            'index.html.twig', 
            [
                'tabData' => $tabData,
                'totalCA' => $totalCa,
                'totalNbLesson' => $totalNbLesson
            ]
        );

    }

    
}