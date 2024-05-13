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
        echo $this->model->nbLesson();
    }

    
}