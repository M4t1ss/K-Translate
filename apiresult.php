<?php
//Read configurations
include('include/config.php');
$config = MyConfig::read('include/settings.php');
//Google -		https://cloud.google.com/translate/
$GoogleTranslateKey	= $config['google_key'];
//Bing -		http://www.bing.com/dev/en-us/translator
$BingClientID		= $config['bing_id'];
$BingClientSecret	= $config['bing_se'];
//Hugo -		https://www.hugo.lv
$LetsMTusername 	= $config['hugo_em'];
$LetsMTpassword 	= $config['hugo_pw'];
$LetsMTSystemID		= "smt-1c08a5bb-95e8-4806-9a7f-3a9ad2114eca";
//Yandex - 		https://tech.yandex.com/translate/
$YandexApiKey 		= $config['yandex_key'];
include 'API/googleTranslate.php';
include 'API/bingTranslator.php';
include 'API/yandexTranslator.php';
include 'API/LetsMT.php';
$en_gram 	= $config['en_gram'];
$en_lm 		= $config['en_lm'];
$lv_gram 	= $config['lv_gram'];
$lv_lm 		= $config['lv_lm'];
$de_gram 	= $config['de_gram'];
$de_lm 		= $config['de_lm'];
$fr_gram 	= $config['fr_gram'];
$fr_lm 		= $config['fr_lm'];
//Get input data
if($_POST['sentence'] != '')
	$src = $_POST['sentence'];
switch($_POST['srclang']){
	case "English":
		$grammarFile = $en_gram;
		$from = "en";
		break;
	case "Latvian":
		$grammarFile = $lv_gram;
		$from = "lv";
		break;
	case "German":
		$grammarFile = $de_gram;
		$from = "de";
		break;
	case "French":
		$grammarFile = $fr_gram;
		$from = "fr";
		break;
}
switch($_POST['trglang']){
	case "English":
		$languageModelFile = $en_lm;
		$to = "en";
		break;
	case "Latvian":
		$languageModelFile = $lv_lm;
		$to = "lv";
		break;
	case "German":
		$languageModelFile = $de_lm;
		$to = "de";
		break;
	case "French":
		$languageModelFile = $fr_lm;
		$to = "fr";
		break;
}

//Parse input source sentence
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	$src = str_replace("\n", " ", $src);
	$src = str_replace("&", "", $src);
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

//Translate the chunks with each system
$mt1Chunks = $mt2Chunks = $mt3Chunks = $mt4Chunks = array();
foreach($finalChunks as $finalChunk){
	$finalChunk;
	if(isset($_POST['bing']) && $_POST['bing'] == 'on'){
		$mt1Chunks[] = translateWithBing($from,$to,$finalChunk);
		$mtNames[] = "Bing";
	}
	if(isset($_POST['google']) && $_POST['google'] == 'on'){
		$mt2Chunks[] = translateWithGoogle($from,$to,$finalChunk);
		$mtNames[] = "Google";
	}
	if(isset($_POST['hugo']) && $_POST['hugo'] == 'on'){
		$mt3Chunks[] = translateWithLetsMT($finalChunk);
		$mtNames[] = "Hugo";
	}
	if(isset($_POST['yandex']) && $_POST['yandex'] == 'on'){
		$mt4Chunks[] = translateWithYandex($from,$to,$finalChunk);
		$mtNames[] = "Yandex";
	}
}
//Some nice random color coding
$inputChunkCount = count($mt1Chunks);

$chunkVariants = array();
$chunkColors = array();
for ($i = 0; $i < $inputChunkCount; $i++){
	$chunkColors[] = 'rgba('.rand(1, 255).', '.rand(1, 255).', '.rand(1, 255).', 0.15)';
	if(count($mt1Chunks) == $inputChunkCount)
		$chunkVariants[$i][] = $mt1Chunks[$i];
	if(count($mt2Chunks) == $inputChunkCount)
		$chunkVariants[$i][] = $mt2Chunks[$i];
	if(count($mt3Chunks) == $inputChunkCount)
		$chunkVariants[$i][] = $mt3Chunks[$i];
	if(count($mt4Chunks) == $inputChunkCount)
		$chunkVariants[$i][] = $mt4Chunks[$i];
}


//

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
	$selectedMT[] = $mtNames[array_keys($perplexities, min($perplexities))[0]];
	if(min($perplexities) == max($perplexities)){
		$pplDiff[] = 1;
	}else{
		$pplDiff[] = (max($perplexities)-min($perplexities))/max($perplexities);
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
	echo "<li style='background-color: ".$chunkColors[$i]."' >Source: ".$selectedMT[$i]."</li>";
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

