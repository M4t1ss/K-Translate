<?php
//Get input data
if($_GET['src'] != '')
	$src = $_GET['src'];
if($_GET['srclang'] != '')
	$srclang = $_GET['srclang'];
if($_GET['trglang'] != '')
	$trglang = $_GET['trglang'];

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

}
?>
<form action="?">
	<div style="float: left; margin-left:5px;">
		Source language:<br/>
		<select class="form-control" name="srclang"/>
			<?php if($srclang == "English") echo "<option SELECTED>English</option>";?>
			<?php if($srclang == "German") echo "<option SELECTED>German</option>"; ?>
			<?php if($srclang == "French") echo "<option SELECTED>French</option>"; ?>
		</select>
	</div>
	<div style="float: left; margin-left:5px;">
		Target language:<br/>
		<select class="form-control" name="trglang" />
			<?php if($trglang == "Latvian") echo "<option SELECTED>Latvian</option>"; ?>
		</select>
	</div>
	<br style="clear: both;"/><br/>
	<div class="mt">
		Source sentence:<br/>
		<textarea class="form-control" name="src" placeholder="Required" readonly><?php
		foreach($finalChunks as $finalChunk){
			echo $finalChunk."\n";
		}
		?></textarea><br/>
	</div>
	<br style="clear: both;"/>
<?php

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
	echo "<br style='clear:both;'/><br style='clear:both;'/>";
?>
	<div class="mt">
		MT 1:<br/>
		<textarea class="form-control" name="mt1" placeholder="Required"></textarea><br/>
	</div>

	<div class="mt">
		MT 2:<br/>
		<textarea class="form-control" name="mt2" placeholder="Required"></textarea><br/>
	</div>
	<br style="clear: both;"/>

	<div class="mt">
		MT 3:<br/>
		<textarea class="form-control" name="mt3" placeholder="Optional"></textarea><br/>
	</div>

	<div class="mt">
		MT 4:<br/>
		<textarea class="form-control" name="mt4" placeholder="Optional"></textarea><br/>
	</div>

	<br style="clear: both;"/>
	<input type="hidden" name="id" value="inputresult"/>
	<input style="margin-left:5px;" type="submit" class="btn btn-sm btn-default" value="Combine!"/>
</form>
