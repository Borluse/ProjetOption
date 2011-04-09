<?php
  require_once('Analyseur.php');
 
  class xmlTrait
  {
    /**
	 * $filename reprente le nom du fichier excel.
	 *
	 * @var $filename
	 **/
	var $filename;
    /**
	 * L'objet de la class Analyseur. Cet objet se charge de retirer les informations.
	 *
	 * @var $objetXLS
	 **/
	var $objetXLS;
    var $structures;
    var $data;
    function __construct($file)
	{
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
          $variable1 = $doc->createElement("x");
          $variable1->appendChild(
          $doc->createTextNode($elementData[$var1])
          );
          $donnee->appendChild( $variable1 );
          $variable2 = $doc->createElement("y");
          $variable2->appendChild(
          $doc->createTextNode($elementData[$var2])
          );
          $donnee->appendChild($variable2);
          $d->appendChild( $donnee );
        }
        //parti configuration:choisir les deux attributs qui ont le plus grand valeur
         $c = $doc->createElement("Configuration"); 
         $xml->appendChild($c);
          
         $x = $doc->createElement("x");
         $x->appendChild(
         $doc->createTextNode($this->structures[$indexMax]["Nom"]));  
        
        $c->appendChild($x);
        
        $y = $doc->createElement("y");
        $y->appendChild(
          $doc->createTextNode($this->structures[$indexMaxsec]["Nom"])
        );
        $c->appendChild( $y );
        return $doc->saveHTML();
        //echo 'doc',$doc->saveHTML();
        $doc->save("order.xml");
    }
    
  /******************************************************************
            1.read a file xml
            communicate with database to realise the correspondance between attributs of visualisation
            and the parameter of xml
            2.write the part of configuration of this xml file
  **********************************************************************/
    function correspondance($filename,$typeVisualisation)
    {
      //communquer avec la base de donnee
    
    $mysql_server_name="localhost"; //数据库服务器名称
    $mysql_username="root"; // 连接数据库用户名
    $mysql_password=""; // 连接数据库密码
    $mysql_database="visualisation"; // 数据库的名字
    // 连接到数据库
    $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
    if (!$conn)
    {
      die('Could not connect: ' . mysql_error());
    }
    $db_selected = mysql_select_db('visualisation', $conn);
    if (!$db_selected) {
	die ('Can\'t use foo : ' . mysql_error());
    }
    // 从表中提取信息的sql语句
    $strsql="select id_visualisation from visualisation where nom_visualisation ='$typeVisualisation'";
    //$strsql= "select * from visualisation"; 
    // 执行sql查询
    $result=mysql_query($strsql, $conn);
    if (!$result) {
    echo 'Could not run query: ' . mysql_error();
    exit;
    }
    $id_visualisation = mysql_fetch_row($result);
    print_r($id_visualisation);
    $sql_element = "select id_element from element_visualisation where id_visualisation = $id_visualisation[0]";
    //echo $sql_element;
    $result = mysql_query($sql_element, $conn);
    $id_element = mysql_fetch_row($result);
    print_r( $id_element);
    $sql_attriuts = "select * from attribut where id_element = $id_element[0] ORDER BY importance DESC";
    //echo $sql_attriuts;
    $result = mysql_query($sql_attriuts, $conn);
    mysql_data_seek($result, 0);
    // 循环取出记录
    $list_importances = array();
    while ($row=mysql_fetch_row($result))
    {
      $list_importances[] = $row[4];     
    }
    //print_r($list_importances);
    // 释放资源
    mysql_free_result($result);
    // 关闭连接
    mysql_close();  
      
     $nombre_attributs = count($list_importances);
     //echo $nombre_attributs;
     
      $doc = new DOMDocument();
      $doc->load("test.xml");
      //echo 'correspondant1';
      
      $attributs = $doc->getElementsByTagName( "Attribute" );
      
      $list_attributs = array();
      $list_attributs_importance = array();
      
      for($i = 0 ; $i < $nombre_attributs ; $i++)
      {//initialisation a 0
	$list_attributs_importance[] = 0;
	
      }
      $list = array();
      $listIndex = array();
      foreach( $attributs as $attribut )
      {
	
	$noms = $attribut ->getElementsByTagName( "Nom" );
	$nom = $noms->item(0)->nodeValue;
	//echo 'correspondant2';echo 'doc';
	
	$types = $attribut->getElementsByTagName( "Type" );
	$type = $types->item(0)->nodeValue;
	//echo 'correspondant3';
	$importances = $attribut->getElementsByTagName( "Importance");
	$importance = $importances->item(0)->nodeValue;
	//echo 'correspondant4';
	
	//echo "$nom - $type - $importance";
	$list[$nom] = $importance;
	$list[$listIndex] = $nom;
	//burble order le plus grand est le premier
  	/*for($i = 0; $i<$nombre_attributs;$i++)
	{
	  $value = $list[$listIndex[$i]];
	  
	  if($value > $list_attributs_importance[$i])
	  {
	    $temp = $list_attributs_importance[$i+1];
	    for($j = $i ; $j < $nombre_attributs - 1; $j++)
	    {
	      $list_attributs_importance[$j] = $temps;
	      //$temp = $list_attributs_importance[$j+1];
	      $list_attributs_importance[$j+1] = $list_attributs_importance[$j];
	    }
	  }
	*/
	} //fin pour
    
   
   
   
   uasort($list,"my_sort");
   //print_r($list);
   $idx = 0;
   $delete = array();
   $conf =  $doc->getElementsByTagName('Configuration');  
   $r = $doc->getElementsByTagName('variable');  
	foreach($r as $Node) {  
		$delete[] = $Node;  
		}  
        foreach($delete as $val) {  
                $val->parentNode->removeChild($val);  
              }
   foreach ($list as $key => $value){
     
      if ($idx < $nombre_attributs)
      {
	foreach ($conf as $c) {
	  $element = $doc->createElement('variable');
	  $c->appendChild($element);
	  $text = $doc->createTextNode($value);
	  $element->appendChild($text);
	  $a = $doc->createAttribute('nom');
	  $element->appendChild($a);
	  $nomAttribut = $doc->createTextNode($key);
	  $a->appendChild($nomAttribut);
	  //$c->appendChild($text);
	  
	  //echo $c->nodeValue . "\n";
	}	
	//echo '***********************<br></br>';
	//echo $key;
	//echo $value;
	/*
	foreach($confs as $conf)
	{
	  
	  $element = $doc->createElement($key);
	  $element->appendChild($doc->createTextNode($value));
	  $conf->item(0)->appendChild($element);
	}*/
      }else{
	break; 
      }
      $idx++;
      
   }
   //echo $doc->saveHTML();
   $doc->save("test.xml");
   
 
}
}//fin class
  function my_sort($a, $b)
  {
    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
  }
?>