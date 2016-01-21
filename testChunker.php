<html>
<head>
	<title>Chunker</title>
	<link type="text/css" href="style.css" rel="stylesheet">
</head>
<body>
	<b>Input:</b><br/>
	<form action="testChunker.php">
		<textarea name="parsedText"><?php if(isset($_GET["parsedText"])) echo $_GET["parsedText"] ?></textarea>
		<br/>
		<input type="submit" value="Chunk!"/>
	</form>
<?php
if(isset($_GET['parsedText']) && $_GET['parsedText'] != ""){
	include('chunkParseTree.php');

	$parsed = $_GET["parsedText"];
	$parsed = str_replace("\n", "", $parsed);
	$parsed = substr($parsed, 2);
	$parsed = substr($parsed, 0, -2);
	$parsed = str_replace("((", "( (", $parsed);
	$parsed = str_replace("((", "( (", $parsed);
	$parsed = str_replace("))", ") )", $parsed);
	$parsed = str_replace("))", ") )", $parsed);

	$tokens = explode(" ", $parsed);

	// var_dump($tokens);



	foreach($tokens as $token){
		if(strcmp(substr($token, 0, 1), "(") == 0){
			//nāk jauna frāze, jātaisa jauna lapa
			$tokenCategory = trim(substr($token, 1));
			if(!isset($rootNode)){
				$rootNode = new Node($tokenCategory);
				$currentNode = $rootNode;
				$currentNode->level = 0;
			}else{
				$newNode = new Node($tokenCategory);
				$newNode->setParent($currentNode);
				$newNode->level = $currentNode->level + 1;
				if(!$rootNode->hasChildren())
					$rootNode->addChild($newNode);
				else
					$currentNode->addChild($newNode);
				$currentNode = $newNode;
			}

		}elseif(strcmp(substr($token, -1, 1), ")") == 0){
			//frāze beidzas
			//ja bija vārds, jāpievieno pie aktuālās lapas, ja nē, jāiet pie vecāka
			$tokenWord = substr($token, 0, -1);
			if(strlen($tokenWord) > 0){
				$currentNode->setWord($tokenWord);
			}
			if($currentNode->getParent() != null)
				$currentNode = $currentNode->getParent();

		}
	}

	// Izdrukā visu koku
	// var_dump($rootNode);

	// Izdrukā teikumu secīgi
	// echo $rootNode->traverse('inorder', '');
	// echo "<br/><br/>";
	
	// Izdrukā teikumu pretējā secībā
	// echo $rootNode->traverse('revorder', '');
	// echo "<br/><br/>";
	
	$wordCount = str_word_count($rootNode->traverse('inorder', ''));
	$chunkSize = ceil($wordCount/4);
	
	$finalChunks = array();
	$rootNode->getChunksToSize($rootNode, $chunkSize, $finalChunks);
	while(count($finalChunks) > 10){
		$finalChunks = array();
		$rootNode->clearInnerChunks($rootNode);
		$chunkSize = $chunkSize * 1.5;
		$rootNode->getChunksToSize($rootNode, $chunkSize, $finalChunks);
	}
	
	$finalChunks = array_reverse($finalChunks);
	
	// Izdrukā gabalu izmēru
	// var_dump($chunkSize);
	
	// Izdrukā teikuma gabalus
	echo "<b>Chunks:</b><br/><div class='finalChunks'><ul>";
	foreach($finalChunks as $finalChunk){
		echo "<li>".$finalChunk."</li>";
	}
	echo "</ul></div>";
	echo "<br style='clear:both;'/><br style='clear:both;'/>";
	
	echo "<b>Tree:</b><br/>";
	// Uzzīmē skaistu kociņu :)
	$rootNode->printTree($rootNode);
}

?>
<br/>
<br/>
</body>
</html>



<?php
