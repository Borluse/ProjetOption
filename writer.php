<?php
  $structures = array();
  $i = 0;
  /*
  $s = array();
  $s = retirerStructure();
  for($i = 0; $i < $s.count ; $i++)
  {
    $structures [] = $s[$i];
  }
    */
   $structures [] = array(
  'Nom' => 'Petal width',
  'Type' => 'numeric',
  'Importance' => "99"
  );
  
   $structures [] = array(
  'Nom' => 'Petal width',
  'Type' => 'numeric',
  'Importance' => "99"
  );

  $doc = new DOMDocument();
  $doc->formatOutput = true;
  //partie structure
  $r = $doc->createElement( "Structure" );
  $doc->appendChild( $r );
  
  foreach( $structures as $structure )
  {
  $a = $doc->createElement( "Attribute" );
  $nom = $doc->createElement( "Nom" );
  $nom->appendChild(
  $doc->createTextNode( $structure['Nom'] )
  );
  $a->appendChild( $nom );
  
  $type = $doc->createElement( "Type" );
  $type->appendChild(
  $doc->createTextNode( $structure['Type'] )
  );
  $a->appendChild( $type );
  
  $importance = $doc->createElement( "Importance" );
  $importance->appendChild(
  $doc->createTextNode( $structure['Importance'] )
  );
  $a->appendChild( $importance );
  
  $r->appendChild( $a );
  }
  
  echo $doc->save("order.xml");
  ?>