<?php

require_once 'phpexcel/Classes/PHPExcel.php';

/**
 * 	Cette classes est utilisé pour retirer les informations dans un fichier Excel.
 *	Elle utilise la bibliothèque PHPExcel.
 *	Pour savoir plus, veuillez accèder http://phpexcel.codeplex.com/
 *
 * @author Fan ZHAO
 **/
class Analyseur{	
	
	/**
	 * $filename représente le nom du fichier excel.
	 *
	 * @var $filename
	 **/
	var $filename;
	
	/**
	 * Une variable constante sheetname représente le nom de la feuille dans la fichier excel.  
	 * Nous allons retirer les infos uniquement stockés dans la feuille $sheetname
	 *
	 **/
	const sheetname = "IRIS";
	
	/**
	 * L'objet de la class PHPExcel. Cet objet se charge de retirer les informations.
	 *
	 * @var $objExcel
	 **/
	var $objExcel;
	
	/**
	 * Cette variable représente la ligne où nous somme.
	 *
	 * @var $index;
	 **/
	var $index;


	/**
	 * Tableau qui enregistrer les noms des colomns.
	 *
	 * @var $nomCol
	 */
	var $nomCol;



	
	/**
	 * La constructeur. Elle prend le nom du fichier comme la paramètre.
	 *
	 * @param $filename
	 * @return void
	 * @author Fan ZHAO
	 **/

	function __construct($filename) {
		$this->filename = $filename;
		$this->objExcel = PHPExcel_IOFactory::load($filename);
		$index = 0;
	}
	
	/**
	 * Retirer les donnée dans un fichier excel. Cette fonction va renvoie toutes les données.
	 *
	 * @return Un tableau 2D.
	 * @author Fan ZHAO
	 **/
	function retirerDonnee(){
		$resultat = array();
		$worksheet = $this->objExcel->getSheetByName(self::sheetname);
		
		foreach ($worksheet->getRowIterator() as $row) {
			$index = $row -> getRowIndex();
			
			if ($index > 3){
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false);
				$colIndex = 0;
				$temp = array();
				foreach ($cellIterator as $cell) {
					if (!is_null($cell)){
						$val = $cell->getCalculatedValue();
						if ($val != ""){
							$temp[$this->nomCol[$colIndex]] = $val;
							$colIndex++;
							
						}
					}
				}
				array_push($resultat,$temp);
			}
		}
		print_r ($resultat);
		return $resultat;
	}
	
	/**
	 * Retirer les strucutures du fichier excel. 
	 *
	 * @return Un tableau 2D.
	 * @author Fan ZHAO
	 **/
	function retirerStructure()	{
		$resultat = array();
		$worksheet = $this->objExcel->getSheetByName(self::sheetname);
		$tempA = array();
		foreach ($worksheet->getRowIterator() as $row) {
			$index = $row ->getRowIndex();
			
			
			if ($index <= 3){
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
				$indexCol = 0;
				$temp = array();
				foreach ($cellIterator as $cell) {
					if (!is_null($cell)) {
						$val = $cell->getCalculatedValue();
						if ($indexCol > 0){
							array_push($temp,$val);
						}
						$indexCol ++;
					}

				}
			//	print_r($temp);
				array_push($tempA,$temp);
				if ($index == 1){
					$this->nomCol = $temp;
				}
				
			}
			else{

				for ($i = 0; $i < count($tempA[0]); $i++){
					$temp = array();
					$temp['Nom'] = $tempA[0][$i];
					$temp['Type'] = $tempA[1][$i];
					$temp['Importance'] = $tempA[2][$i];
					array_push($resultat, $temp);
				}
				break;
			}
		}
		return $resultat;
	}
}



?>