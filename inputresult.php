<?php
include('include/config.php');

//Get input data
if($_GET['mt1'] != ''){
	$mt1Chunks = explode("\n", $_GET['mt1']);
}
if($_GET['mt2'] != ''){
	$mt2Chunks = explode("\n", $_GET['mt2']);
}
if($_GET['mt3'] != ''){
	$mt3Chunks = explode("\n", $_GET['mt3']);
}
if($_GET['mt4'] != ''){
	$mt4Chunks = explode("\n", $_GET['mt4']);
}
if($_GET['src'] != '')
	$src = $_GET['src'];

$config = MyConfig::read('include/settings.php');
$en_gram 	= $config['en_gram'];
$en_lm 		= $config['en_lm'];
$lv_gram 	= $config['lv_gram'];
$lv_lm 		= $config['lv_lm'];
$de_gram 	= $config['de_gram'];
$de_lm 		= $config['de_lm'];
$fr_gram 	= $config['fr_gram'];
$fr_lm 		= $config['fr_lm'];
switch($_GET['srclang']){
	case "English":
		$grammarFile = $en_gram;
		break;
	case "Latvian":
		$grammarFile = $lv_gram;
		break;
	case "German":
		$grammarFile = $de_gram;
		break;
	case "French":
		$grammarFile = $fr_gram;
		break;
}
switch($_GET['trglang']){
	case "English":
		$languageModelFile = $en_lm;
		break;
	case "Latvian":
		$languageModelFile = $lv_lm;
		break;
	case "German":
		$languageModelFile = $de_lm;
		break;
	case "French":
		$languageModelFile = $fr_lm;
		break;
}

$inputChunkCount = count(explode("\n", $_GET['mt1']));

$chunkVariants = array();
$chunkColors = array();
for ($i = 0; $i < $inputChunkCount; $i++){
	$chunkColors[] = 'rgba('.rand(1, 255).', '.rand(1, 255).', '.rand(1, 255).', 0.15)';
	$chunkVariants[$i][] = $mt1Chunks[$i];
	$chunkVariants[$i][] = $mt2Chunks[$i];
	if(isset($mt3Chunks) && is_array($mt3Chunks))
		$chunkVariants[$i][] = $mt3Chunks[$i];
	if(isset($mt4Chunks) && is_array($mt4Chunks))
		$chunkVariants[$i][] = $mt4Chunks[$i];
}



//Parse input source sentence
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	$src = str_replace("\n", " ", $src);
	$output = shell_exec('exp.bat "'.$src.'" "'.$grammarFile.'"');
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

	// Print the chunks
	echo "<b>Chunks:</b><br/><div class='finalChunks'><ul>";
	foreach($finalChunks as $finalChunk){
		echo "<li>".$finalChunk."</li>";
	}
	echo "</ul></div>";
	echo "<br style='clear:both;'/><br style='clear:both;'/>";

	echo "<b>Tree:</b><br/>";
	// Draw a pretty tree :)
	$rootNode->printTree($rootNode);
}



	// Choose output chunks
	foreach($chunkVariants as $chunkVariant){
		foreach($chunkVariant as $trChunk){
			
			// Query KenLM
			$outputQ = shell_exec('query.bat "'.$trChunk.'" "'.$languageModelFile.'"');

			$boomQ = explode("\n", $outputQ);
			$perplexQ = $boomQ[5];
			$perplexQ = str_replace("Perplexity including OOVs:	", "", $perplexQ);
			$perplexQ = intval($perplexQ);
			
			$sentences[] = $trChunk;
			$perplexities[] = $perplexQ;
			
		}
		$selectedMT[] = array_keys($perplexities, min($perplexities))[0];
		if(min($perplexities) == max($perplexities)){
			$pplDiff[] = 1;
		}else{
			$pplDiff[] = (max($perplexities)-min($perplexities))/min($perplexities);
		}
		$best[] = $sentences[array_keys($perplexities, min($perplexities))[0]];
		
		unset($sentences);
		unset($perplexities);
	}

	echo "<br style='clear:both;'/><br style='clear:both;'/>";
	echo "<b>Combined translation:</b><br/>";
	echo "<div class='finalChunks'><ul>";
	$i = 0;
	foreach($best as $bestTr){
		echo "<li style='background-color: ".$chunkColors[$i]."' >".$bestTr."</li>";
		$i++;
	}
	echo "</ul></div>";
	echo "<br style='clear:both;'>";
	echo "<div class='finalChunks srcConf'><ul>";
	$i = 0;
	foreach($best as $bestTr){
		echo "<li style='background-color: ".$chunkColors[$i]."' >Source: MT".($selectedMT[$i]+1)."</li>";
		$i++;
	}
	echo "</ul></div>";
	echo "<br style='clear:both;'>";
	echo "<div class='finalChunks srcConf'><ul>";
	$i = 0;
	foreach($best as $bestTr){
		echo "<li style='background-color: ".$chunkColors[$i]."' >Confidence: ".(round($pplDiff[$i], 2)*100)."%</li>";
		$i++;
	}
	echo "</ul></div>";
	echo "<br style='clear:both;'>";
	echo "<br style='clear:both;'>";
	echo "<br style='clear:both;'>";
?>
