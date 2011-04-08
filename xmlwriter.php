<?php
  require 'Analyseur.php';
  class xmlTrait{
    /**
	 * $filename représente le nom du fichier excel.
	 *
	 * @var $filename
	 **/
	var $filename;
    /**
	 * L'objet de la class Analyseur. Cet objet se charge de retirer les informations.
	 *
	 * @var $objetXLS
	 **/
	
	
	var $filenameXML;
	
	var $objetXLS;
    var $structures;
    var $data;
    var $doc;
	function __construct($file) {
		$this->filename = $file;
        $this->objetXLS = new Analyseur($this->filename);
        $this->structures = $this->objetXLS->retirerStructure();
        $this->data = $this->objetXLS->retirerDonnee();
	}
    function xmlToString()
    {
        //$this->filename = 'test.xls';
        $i = 0;
        //parti configuration:choisir les deux attributs qui ont le plus grand valeur
        $max = $this->structures[0]['Importance'];
        $maxsec = $max;
        $indexMax = 0;
        $indexMaxsec = 0;
        for($i = 1; $i < count($this->structures); $i++)
        {
          $valeur = $this->structures[$i]['Importance'];
          if($max < $valeur)
          {
              $maxsec = $max;
              $indexMaxsec = $indexMax;
              $max = $valeur;
              $indexMax = $i;
          }
          elseif($valeur > $maxsec && $valeur < $max)
          {
              $maxsec = $valeur;
              $indexMaxsec = $i;
          }
        }
        $var1 = $this->structures[$indexMax]["Nom"];
        $var2 = $this->structures[$indexMaxsec]["Nom"];
        //echo $var1;
        //echo $var2;
        $doc = new DOMDocument();
        $doc->formatOutput = true;
        //partie structure
        $xml = $doc->createElement("XML");
        $doc->appendChild($xml);
          
        $s = $doc->createElement( "Structure" );
        $xml->appendChild( $s );
        
        foreach( $this->structures as $structure )
        {
            $a = $doc->createElement( "Attribute" );
            $nom = $doc->createElement( "Nom" );
            $nom->appendChild(
            $doc->createTextNode( $structure['Nom'] )
        );
        $a->appendChild( $nom );
        
        $type = $doc->createElement( "Type" );
        $type->appendChild(
        $doc->createTextNode( $structure['Type'] ));
        $a->appendChild( $type );
        
        $importance = $doc->createElement( "Importance" );
        $importance->appendChild(
        $doc->createTextNode( $structure['Importance'] ));
        
        $a->appendChild( $importance );
        $s->appendChild( $a );
        }


        //parti data
        $d = $doc->createElement("Donnee");
        $xml->appendChild($d);
        foreach($this->data as $elementData)
        {
	        $donnee = $doc->createElement( "Data" );
				
			foreach ($elementData as $i=>$value){
				$var = $doc->createElement("attribute");
				$var->setAttribute("Nom",$i);
				$var->appendChild($doc->createTextNode($value));
				$donnee->appendChild($var);
			}
			
            $d->appendChild( $donnee );
        }
        
        $c = $doc->createElement("Configuration"); 
		$xml->appendChild($c);
		$this->doc = $doc;
        return $doc->saveHTML();
    }
    function saveToXMLFile(){
    	$this->filenameXML = $this->filename.".xml";
    	//echo $this->filenameXML;
    	$this->doc->save($this->filenameXML);
    }
}

?>