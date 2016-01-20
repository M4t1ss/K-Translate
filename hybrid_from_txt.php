<?php
if(!isset($argv[1]) || $argv[1]==""){
	echo "Please provide the language model!\n";
}

$languageModel 	= $argv[1];
$ing = fopen("/home/matiss/EXP_JAN_2016/data/legal/translated/chunks/test_short.en.chunks.google.txt", "r") or die("Can't create output file!"); 					//Google output
$inb = fopen("/home/matiss/EXP_JAN_2016/data/legal/translated/chunks/test_short.en.chunks.bing.txt", "r") or die("Can't create output file!"); 					//Bing output
$inh = fopen("/home/matiss/EXP_JAN_2016/data/legal/translated/chunks/test_short.en.chunks.hugo.txt", "r") or die("Can't create output file!"); 					//Hugo output
$iny = fopen("/home/matiss/EXP_JAN_2016/data/legal/translated/chunks/test_short.en.chunks.yandex.txt", "r") or die("Can't create output file!"); 					//Yandex output
$outh = fopen("/home/matiss/EXP_JAN_2016/data/legal/translated/full/DGT6gram/test_short.hybrid.full.txt", "a") or die("Can't create output file!"); 			//Hybrid output
$outCount = fopen("/home/matiss/EXP_JAN_2016/data/legal/translated/full/DGT6gram/test_short.hybrid.full.count.txt", "a") or die("Can't create output file!"); 	//Hybrid count


$totalChunks 	= 0;
$equalChunks 	= 0;
$googleChunks 	= 0;
$bingChunks 	= 0;
$hugoChunks 	= 0;
$yandexChunks 	= 0;

//process input file by line
if ($ing && $inb && $inh) {
    while (($sentenceOne = fgets($ing)) !== false && ($sentenceTwo = fgets($inb)) !== false && ($sentenceThree = fgets($inh)) !== false  && ($sentenceFour = fgets($iny)) !== false ) {
		
		unset($sentences);
		unset($perplexities);
		
		if($sentenceOne == "\n" && $sentenceTwo == "\n" && $sentenceThree == "\n"){
			$outputString = "\n";
		}else{
			//Use the language model ONLY if the translations differ
			if(strcmp($sentenceOne, $sentenceTwo) != 0 || strcmp($sentenceOne, $sentenceThree) != 0 || strcmp($sentenceOne, $sentenceFour) != 0){
				$sentences[] = str_replace(array("\r", "\n"), '', $sentenceOne);
				$sentences[] = str_replace(array("\r", "\n"), '', $sentenceTwo);
				$sentences[] = str_replace(array("\r", "\n"), '', $sentenceThree);
				$sentences[] = str_replace(array("\r", "\n"), '', $sentenceFour);
				
				//Get the perplexities of the translations
				$perplexities[] = shell_exec('/home/matiss/EXP_JAN_2016/HybridTXTin/exp.sh '.$languageModel.' "'.$sentenceOne.'"');
				$perplexities[] = shell_exec('/home/matiss/EXP_JAN_2016/HybridTXTin/exp.sh '.$languageModel.' "'.$sentenceTwo.'"');
				$perplexities[] = shell_exec('/home/matiss/EXP_JAN_2016/HybridTXTin/exp.sh '.$languageModel.' "'.$sentenceThree.'"');
				$perplexities[] = shell_exec('/home/matiss/EXP_JAN_2016/HybridTXTin/exp.sh '.$languageModel.' "'.$sentenceFour.'"');
				
				$outputString = $sentences[array_keys($perplexities, min($perplexities))[0]];
			}else{
				$outputString = $sentenceOne;
			}
			$outputString = trim($outputString)." ";
			
			//Count chunks
			$totalChunks++;
			$googleSentence = str_replace(array("\r", "\n"), '', $sentenceOne);
			$bingSentence = str_replace(array("\r", "\n"), '', $sentenceTwo);
			$hugoSentence = str_replace(array("\r", "\n"), '', $sentenceThree);
			$yandexSentence = str_replace(array("\r", "\n"), '', $sentenceFour);
			$googleSentence = trim($googleSentence)." ";	
			$bingSentence = trim($bingSentence)." ";	
			$hugoSentence = trim($hugoSentence)." ";	
			$yandexSentence = trim($yandexSentence)." ";	
			if(strcmp($sentenceOne, $sentenceTwo) == 0 && strcmp($sentenceOne, $sentenceThree) == 0 && strcmp($sentenceOne, $sentenceFour) == 0){
				$equalChunks++;
			}elseif ($outputString == $hugoSentence){
				$hugoChunks++;
			}elseif($outputString == $bingSentence){
				$bingChunks++;
			}elseif($outputString == $googleSentence){
				$googleChunks++;
			}elseif($outputString == $yandexSentence){
				$yandexChunks++;
			}
		}
		fwrite($outh, $outputString);
	}
	
	//Write chunk counts
	fwrite($outCount, "Total chunk count: ".$totalChunks."\n");
	fwrite($outCount, "Equal chunk count: ".$equalChunks."\n");
	fwrite($outCount, "Google chunk count: ".$googleChunks."\n");
	fwrite($outCount, "Bing chunk count: ".$bingChunks."\n");
	fwrite($outCount, "Hugo chunk count: ".$hugoChunks."\n");
	fwrite($outCount, "Yandex chunk count: ".$yandexChunks."\n");
	
	fclose($ing);
	fclose($inb);
	fclose($inh);
	fclose($iny);
	fclose($outh);
	fclose($outCount);
}
