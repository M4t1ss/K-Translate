﻿<?php
//Get input data
$sentences = array();
if($_GET['mt1'] != '')
	$sentences[] = $_GET['mt1'];
if($_GET['mt2'] != '')
	$sentences[] = $_GET['mt2'];
if($_GET['mt3'] != '')
	$sentences[] = $_GET['mt3'];
if($_GET['mt4'] != '')
	$sentences[] = $_GET['mt4'];
if($_GET['src'] != '')
	$src = $_GET['src'];

//Parse input source sentence
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	$output = shell_exec('exp.bat "'.$src.'"');
	$boom = explode("\n", $output);
	$parsed = $boom[4];
} else {
    echo 'This is a server not using Windows!';
}

//Chunk input data
if(isset($parsed) && $parsed != ""){
	include('chunkParseTree.php');
	$parsed = str_replace("\n", "", $parsed);
	$parsed = substr($parsed, 2);
	$parsed = substr($parsed, 0, -2);
	$parsed = str_replace("((", "( (", $parsed);
	$parsed = str_replace("((", "( (", $parsed);
	$parsed = str_replace("))", ") )", $parsed);
	$parsed = str_replace("))", ") )", $parsed);

	$tokens = explode(" ", $parsed);
	
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
			//frāze beidzas - ja bija vārds, jāpievieno pie aktuālās lapas, ja nē, jāiet pie vecāka
			$tokenWord = substr($token, 0, -1);
			if(strlen($tokenWord) > 0){
				$currentNode->setWord($tokenWord);
			}
			if($currentNode->getParent() != null)
				$currentNode = $currentNode->getParent();
		}
	}
	
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

//Choose output chunks
	
	echo "<b>Combined translation:</b><br/>";
	echo "<div class='finalChunks'><ul>";
	echo "<li>Preces tiek nogādātas ātri un efektīvi</li>";
	echo "<li>no rūpnīcas pie lietotājiem, bieži vien arī citās valstīs.</li>";
	echo "</ul></div>";
?>