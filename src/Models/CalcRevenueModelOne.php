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
   * 1. pour chaque fichier excel 
   * 1bis. trouver : les dates et les stocker
   * 2. calculer le CA  : nb clients * 25e
   * 3. deduire le montant des annulations/remboursements du CA 
   * 4. renvoyer le CA réel (et les dates)
   */

class CalcRevenueModelOne
{

    private string $filePath = __DIR__ . '/../Data/export-6-7-04-2024.xls';
    private int $pricePerStudent1 = 20;
    private int $pricePerStudent2 = 25;
    private array $tab = [];
    private string $dateModifPriceString = '20/03/2024 12:00';
    private DateTime $dateModifPriceDt;

    public function __construct() {

        //Tranforme en objet datetime la date dateModifPriceString pour pouvoir la comparer
        $this->dateModifPriceDt = DateTime::createFromFormat('d/m/Y H:i', $this->dateModifPriceString);

        if ($this->dateModifPriceDt === false) {
            throw new Exception('Invalid date format');
        }
    }



    public function findNameExcelfile()
    {
        
        // Utilisation de pathinfo() pour obtenir le nom du fichier sans l'extension
        $fileNameWithoutExtension = pathinfo($this->filePath, PATHINFO_FILENAME);
        
        return $fileNameWithoutExtension;
    }
    
    public function excelConnexion(): ?Worksheet
    {

        // Créer un objet Reader pour charger le fichier Excel
        $reader = IOFactory::createReader('Xls');
        $spreadsheet = $reader->load($this->filePath);
        $sheet = $spreadsheet->getActiveSheet();

        return $sheet;

    }

    public function calcRevenues(): array
    {

        $findNameExcelfile = $this->findNameExcelfile();

        $sheet = $this->excelConnexion();//Connexion au fichier excel

        if (!$sheet) {

            die("Connexion au fichier Excel impossible.");
        }

        $highestRow = $sheet->getHighestRow();//compter le nb de lignes

        /**
         * 
         * Lire et Selectionner les dates 
         * Colonne Date Réservée
         * 
        */

        $uniqueDates = [];

        for ($row = 2; $row <= $highestRow; $row++) { 

            $cellValue = $sheet->getCell('D' . $row)->getValue();

            $uniqueDates[$cellValue] = true;
            //var_dump($uniqueDates);   
        }

        //var_dump($uniqueDates);     
        
        /**
         * 
         * Calcul le nombre de cours et le Chiffres d'affaire
         * Colonne Nb personnes
         * Check si annulation et remboursement
         * 
         */
        
        $nbLesson = 0;
        $ca = 0;
        

        for ($row = 2; $row <= $highestRow; $row++) 
        {

            $cellValuePaiementStatus = $sheet->getCell('O'.$row)->getValue();
            $cellValueNbStudents = $sheet->getCell('L'.$row)->getValue();
            $cellValueTotal = $sheet->getCell('M'.$row)->getValue();
            $cellValueReservationStatus = $sheet->getCell('N'.$row)->getValue();
            $cellValueCreationDate = $sheet->getCell('C'.$row)->getValue();

            if ($cellValueReservationStatus === 'Terminée' && $cellValuePaiementStatus === 'Payé') {

                $ca += $cellValueTotal;
                $nbLesson += $cellValueNbStudents;
                //var_dump($ca, $nbLesson);
            
                  
             }
             if ($cellValueReservationStatus === 'Terminée' && $cellValuePaiementStatus === 'Aucun'){

                
                
                $cellValueCreationDateDt = DateTime::createFromFormat('d/m/Y H:i', $cellValueCreationDate);//Transform string in DateTime format
                $ca += $cellValueNbStudents * ($cellValueCreationDateDt < $this->dateModifPriceDt ? $this->pricePerStudent1 : $this->pricePerStudent2);

                $nbLesson += $cellValueNbStudents;
                //var_dump( $cellValueCreationDateDt, $this->dateModifPriceDt);

            }
            if ($cellValueReservationStatus === 'Terminée' && $cellValuePaiementStatus === 'Gratuit'){

                /* 'Aucun' signifie pas aucun paiement mais que c'est payé par un abonnement donc 25e*/
                $ca += $cellValueNbStudents * 0;
                $nbLesson += $cellValueNbStudents;
                //var_dump($ca, $nbLesson);

            }
            if($cellValueReservationStatus === 'Annulé'){

                continue;

            }


           

            
           

        }

        return $tab = [$ca, $nbLesson, $uniqueDates, $findNameExcelfile];
        

    }
}