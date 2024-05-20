<?php

namespace App\Models;

require_once __DIR__ . '/../../vendor/autoload.php';

use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

  /**
   * Objectifs class : 
   * 
   * pour chaque fichier Excel 
   * 1. trouver : les dates et les stocker
   * 2. calculer le CA (deduire, les annulations, les remboursements,..)
   * 3. compter le nb lesson 
   */

   class CalcRevenueModelAll
   {
       private string $directoryPath = __DIR__ . '/../../public/Data/';
       private int $pricePerStudent1 = 20;
       private int $pricePerStudent2 = 25;
       private string $dateModifPriceString = '20/03/2024 12:00';
       private DateTime $dateModifPriceDt;
   
       public function __construct() {

           // Transforme en objet datetime la date dateModifPriceString pour pouvoir la comparer
           $this->dateModifPriceDt = DateTime::createFromFormat('d/m/Y H:i', $this->dateModifPriceString);
   
           if ($this->dateModifPriceDt === false) {
               throw new Exception('Invalid date format');
           }
       }
   
       public function findExcelFiles(): array {

           // Retourne tous les fichiers Excel dans le répertoire $directoryPath
           return glob($this->directoryPath . '*.xls');
       }
   
       public function excelConnexion(string $filePath): ?Worksheet {
           // Créer un objet Reader pour charger le fichier Excel
           $reader = IOFactory::createReader('Xls');
           $spreadsheet = $reader->load($filePath);
           return $spreadsheet->getActiveSheet();
       }
   
       public function calcRevenuesAll(): array {

           $files = $this->findExcelFiles();
           $allData = [];
   
           foreach ($files as $file) {





                //Ouvrir le ficheir excel
               $sheet = $this->excelConnexion($file);
   
               if (!$sheet) {
                   die("Connexion au fichier Excel impossible.");
               }
   




                // Compter le nombre de lignes
               $highestRow = $sheet->getHighestRow();
   
               
               
               
               
               
               
                // Lire et sélectionner les dates dans la colonne D
                $uniqueDates = [];
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('D' . $row)->getValue();
                    if ($cellValue) {
                        $uniqueDates[$cellValue] = true;
                    }
                }

                // Trier les dates par ordre décroissant
                krsort($uniqueDates);

                //var_dump($uniqueDates);




   
               // Calcul du nombre de cours et du chiffre d'affaire
               $nbLesson = 0;
               $ca = 0;
   
               for ($row = 2; $row <= $highestRow; $row++) {
                   $cellValuePaiementStatus = $sheet->getCell('O' . $row)->getValue();
                   $cellValueNbStudents = $sheet->getCell('L' . $row)->getValue();
                   $cellValueTotal = $sheet->getCell('M' . $row)->getValue();
                   $cellValueReservationStatus = $sheet->getCell('N' . $row)->getValue();
                   $cellValueCreationDate = $sheet->getCell('C' . $row)->getValue();
   
                   if ($cellValueReservationStatus === 'Terminée' && $cellValuePaiementStatus === 'Payé') {
                       $ca += $cellValueTotal;
                       $nbLesson += $cellValueNbStudents;
                   }
                   if ($cellValueReservationStatus === 'Terminée' && $cellValuePaiementStatus === 'Aucun') {
                       $cellValueCreationDateDt = DateTime::createFromFormat('d/m/Y H:i', $cellValueCreationDate); // Transform string in DateTime format
                       $ca += $cellValueNbStudents * ($cellValueCreationDateDt < $this->dateModifPriceDt ? $this->pricePerStudent1 : $this->pricePerStudent2);
                       $nbLesson += $cellValueNbStudents;
                   }
                   if ($cellValueReservationStatus === 'Terminée' && $cellValuePaiementStatus === 'Gratuit') {
                       $ca += $cellValueNbStudents * 0;
                       $nbLesson += $cellValueNbStudents;
                   }
                   if ($cellValueReservationStatus === 'Annulé') {
                       continue;
                   }
               }




   
               $allData[] = [
                   'file' => basename($file),
                   'ca' => $ca,
                   'nbLesson' => $nbLesson,
                   'uniqueDates' => array_keys($uniqueDates),
               ];
           }





            // Trier les données par la première date décroissante
            usort($allData, function ($a, $b) {
                $dateA = DateTime::createFromFormat('d/m/Y', $a['uniqueDates'][0]);
                $dateB = DateTime::createFromFormat('d/m/Y', $b['uniqueDates'][0]);
                return $dateB <=> $dateA;
            });





   
           return $allData;





       }
   }
   