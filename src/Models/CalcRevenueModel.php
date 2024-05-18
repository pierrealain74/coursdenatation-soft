<?php

namespace App\Models;

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

  /**
   * Objectifs classe : 
   * 
   * 1. trouver : les dates et les stocker
   * 2. calculer le CA  : nb clients * 25e
   * 3. deduire le montant des annulations/remboursements du CA 
   * 4. renvoyer le CA réel (et les dates)
   */

class CalcRevenueModel
{

    private string $filePath = __DIR__ . '/../Data/export-2-3-03-2024.xls';
    private int $pricePerStudent = 25;
    
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
        $tab = [];

        for ($row = 1; $row <= $highestRow; $row++) 
        {

            $cellValuePaiementStatus = $sheet->getCell('O'.$row)->getValue();
            $cellValueNbStudents = $sheet->getCell('L'.$row)->getValue();
            $cellValueTotal = $sheet->getCell('M'.$row)->getValue();
            $cellValueReservationStatus = $sheet->getCell('N'.$row)->getValue();

            if (is_numeric($cellValueNbStudents) && $cellValuePaiementStatus !== 'Remboursé'){

                $nbLesson += $cellValueNbStudents;

            }

            if ($cellValuePaiementStatus === 'Payé' && $cellValueReservationStatus === 'Terminée') {

                $ca += $cellValueTotal;
                //var_dump($ca);
            
                  
             }
            if ($cellValuePaiementStatus === 'Aucun' && $cellValueReservationStatus === 'Terminée'){

                /* 'Aucun' signifie pas aucun paiement mais que c'est payé par un abonnement donc 25e*/
                $ca += $cellValueNbStudents * $this->pricePerStudent;
                //var_dump($cellValueNbStudents);

            }
            if ($cellValuePaiementStatus === 'Aucun' && $cellValueReservationStatus === 'Annulé'){

                /* 'Aucun' signifie pas aucun paiement mais que c'est payé par un abonnement donc 25e*/
                $ca += $cellValueNbStudents * $this->pricePerStudent;
                //var_dump($cellValueNbStudents);

            }
            if($cellValuePaiementStatus === 'Remboursé' && $cellValueReservationStatus === 'Annulé'){

                continue;

            }


           

            
           

        }

        return $tab = [$ca, $nbLesson, $uniqueDates];
        

    }
}