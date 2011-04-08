<?php
	function getIdChart($nameV){
		$conn = mysql_connect("localhost","root","root");
		mysql_select_db("visualisation");
		$sql = "select * from visualisation where nom_visualisation='".$nameV."'";
		$return;
		$result = mysql_query($sql);
		while ($row =  mysql_fetch_object($result)){
			$return = $row->id_visualisation;
		}
		mysql_close();
		return $return;
	}
	function getUniqueCode($length = "")
	{	
		$code = md5(uniqid(rand(), true));
		if ($length != "") return substr($code, 0, $length);
		else return $code;
	}
?>
<?php

 	$mode = $_REQUEST['mode'];
	switch($mode){
		case '1' :{
 
			require_once('xmlwriter.php');
			if ($_FILES["file"]["error"] > 0){
	  			echo "Error: " . $_FILES["file"]["error"] . "<br />";
  			}
	    	
//			echo "Stored in: " . $_FILES["file"]["tmp_name"];
			
  			$filename = getUniqueCode(5);
  			while (file_exists($filename)){
				$filename = getUniqueCode(5);
			}
			copy($_FILES["file"]["tmp_name"], $filename);
			unlink($_FILES["file"]["tmp_name"]);
			$traiter = new xmlTrait($filename);
			$var = $traiter->xmlToString();
			$traiter->saveToXMLFile();
    		unlink($filename);
			
			$filename = $filename.".xml";
			echo $filename;
			break;
		}
		case '2' :{
			$nameOfChart = $_GET['typeChart'];
			$xmlName = $_GET['filename'];
			$idchart = getIdChart($nameOfChart);
			echo readfile("327ef.xml");
			break;
		}

			//google.load("visualization", "1", {packages:["corechart"]});
			//google.setOnLoadCallback(loadToDataTable);
			
			//function loadToDataTable(){
				// var data = new google.visualization.DataTable();
				//var xml = <?= json_encode($var); 
				/*
				var httpRequest = null;
	    		if (!httpRequest) {
			         httpRequest = CreateHTTPRequestObject ();   // defined in ajax.js
			    }
			    if (httpRequest) {          
	    	       // The requested file must be in the same domain that the page is served from.
	    	        var url = "327ef.xml";
	    	        httpRequest.open ("GET", url, true);    // async
	    	        httpRequest.send (null);
					
	    	    }
	    	   
				var xmlDoc = ParseHTTPResponse (httpRequest);  
				if (!xmlDoc){
					alert("err");
				}
			
				document.write(httpRequest.responseText);
				document.write("hello");
				 document.write(xmlDoc.getElementsByTagName("Configuration"));
//				tagConfiguration = xmlDoc.getElementsByTagName("Configuration")[0];
				i=0;
				
		/*		while (tagConfiguration.childNodes[i] != null){
					i++
				}*/
//				document.write(tagConfiguration.childNodes[0].childNodes[0].nodeValue);
		/*		
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
			data.addColumn("number ",ny);
			data.addRows(datas.length);
			for (i=0; i<datas.length; i++){
				data.setValue(i,0,datas[i].childNodes[0].childNodes[0].nodeValue);
				data.setValue(i,1,parseInt(datas[i].childNodes[1].childNodes[0].nodeValue));			
			}
		    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	        chart.draw(data, {width: 400, height: 240, title: 'Company Performance'});*/
	   // }
	


	}

?>