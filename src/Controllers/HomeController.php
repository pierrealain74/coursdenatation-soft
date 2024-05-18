<?php

namespace App\Controllers;
use App\Models\CalcRevenueModel;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomeController
{
    private CalcRevenueModel $model;

    public function __construct()
    {
        $this->model = new CalcRevenueModel();
    }
    
    public function showRevenue()
    {
       $tab = $this->model->calcRevenues();

        // Accès aux valeurs individuelles du tableau
        $ca = $tab[0];
        $nbLesson = $tab[1];
        $uniqueDates = $tab[2];

        // Affichage des valeurs
        echo "Chiffre d'affaires net (50% - par MNS) : " . $ca*(0.6)*(0.5) ."&euro;<br>";
        echo "Taux horaire : " . round($ca*(0.6)*(0.5) / 6.5) ."&euro;/h (6h30 de travail par weekend)<br>";
        echo "Chiffre d'affaires brut : " . $ca . "&euro;<br>";
        echo "Chiffre d'affaires net (60%) : " . $ca*(0.6) ."&euro;<br>";
        echo "Nombre d'élèves : " . $nbLesson . "<br>";
       


        // Affichage des dates du tableau uniqueDates
        echo "Dates : <br>";
        //print_r($uniqueDates);
        foreach ($uniqueDates as $date => $count) {
            echo $date . "<br>";
        }

    }

    
}