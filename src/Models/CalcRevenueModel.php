<?php

namespace App\Models;

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class CalcRevenueModel
{

    /*     public function __construct()
    {
        
    } */
    
    public function nbLesson(): int
    {
        $filePath = __DIR__ . '/../../public/export.xls';

        // Créer un objet Reader pour charger le fichier Excel
        $reader = IOFactory::createReader('Xls');
        $spreadsheet = $reader->load($filePath);

        // Sélectionner la feuille de calcul
        $sheet = $spreadsheet->getActiveSheet();

        // Initialiser la somme à zéro
        $nbLesson = 0;

        // Numéro de la colonne à sommer (dans cet exemple, la colonne L correspond à la colonne 12)
        $columnIndex = 12;

        // Nombre de lignes dans la feuille de calcul
        $highestRow = $sheet->getHighestRow();

        // Parcourir chaque ligne et ajouter la valeur de la colonne à la somme
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('L'.$row)->getValue();

            
            // Ajouter la valeur à la somme (assurez-vous que la valeur est numérique)
            if (is_numeric($cellValue)) {
                $nbLesson += $cellValue;
            }
        }
        return $nbLesson;
    }
}