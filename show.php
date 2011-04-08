<?php 
	require_once('xmlwriter.php');
	if ($_FILES["file"]["error"] > 0){
	  echo "Error: " . $_FILES["file"]["error"] . "<br />";
  	}
	
	echo "Stored in: " . $_FILES["file"]["tmp_name"];
  	$filename = $_FILES["file"]["tmp_name"];
	
	$traiter = new xmlTrait($filename);
	$var = $traiter->xmlToString();
?>

<html>
	<head>
	<script type="text/javascript" src="GoogleAPI.js"></script>
    <script type="text/javascript">

	  	google.load("visualization", "1", {packages:["corechart"]});
      	google.setOnLoadCallback(loadToDataTable);
      	function loadToDataTable(){
			var data = new google.visualization.DataTable();

		var xml = <?= json_encode($var); ?>;
		//document.write(xml);	
		//document.write("safdsaf");
	
		xmlDoc=document.implementation.createDocument("","",null);
		xmlDoc.async="false";
		parser = new DOMParser();
		xmlDoc = parser.parseFromString(xml,"text/xml");
		
		
		x = xmlDoc.getElementsByTagName("Configuration")[0].childNodes[0]
		y = xmlDoc.getElementsByTagName("Configuration")[0].childNodes[1];
		nx = x.childNodes[0].nodeValue;
		ny = y.childNodes[0].nodeValue;

		datas = xmlDoc.getElementsByTagName("Data");
//		document.write(datas.length);
		
		att = xmlDoc.getElementsByTagName("Attribute");

 		//	document.write(att.length);


		for (i=0; i<att.length; i++){
			//document.write(att[i].childNodes[0].childNodes[0].nodeValue);
			nom = att[i].childNodes[0].childNodes[0].nodeValue;
			if (nom.match(nx) != null){
				nxt = att[i].childNodes[1].childNodes[0].nodeValue;

			}
			if (nom.match(ny) != null){
				nyt = att[i].childNodes[1].childNodes[0].nodeValue;
			}
		}

		if (nxt.match("numeric") != null) nxt = "number";
		if (nyt.match("numeric") != null) nyt = "number";
		
		
		data.addColumn("string",nx);
		data.addColumn("number",ny);
		data.addRows(datas.length);
		for (i=0; i<datas.length; i++){
			data.setValue(i,0,datas[i].childNodes[0].childNodes[0].nodeValue);
			data.setValue(i,1,parseInt(datas[i].childNodes[1].childNodes[0].nodeValue));			
		}
	    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, {width: 400, height: 240, title: 'Company Performance'});
      
		
	}
	
	
	</script>
	</head>


<body>
	<div id="chart_div"></div>
</body>


</html>