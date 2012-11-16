<?php


$textFilesArray = scandir('textfiles', 1);

if (is_null($textFilesArray)){
    $url = fopen('http://search.twitter.com/search.json?q=%23hpmprinter', 'r');
} else {
    $url = fopen('http://search.twitter.com/search.json?since_id='.$textFilesArray[0].'&q=%23hpmprinter', 'r');
}
$tweetArray = json_decode(stream_get_contents($url));
fclose($url);
$results = $tweetArray->results;

define('CHAR_WIDTH',43);

$reverseResults = array_reverse($results);
foreach($reverseResults as $result){
    $id = $result->id_str;
    $file = 'textfiles/'.$id.'.txt';
    
    if(!file_exists($file)){
        $outFile = fopen($file, 'w');
        
        $timeTemp = explode('+',$result->created_at);
        $formattedTime = trim($timeTemp[0]);
        
        $message =  str_pad($formattedTime, CHAR_WIDTH, '*', STR_PAD_BOTH)."\n"
		    .'From: @'.$result->from_user."\n"
                    .'Message: '
                    .trim($result->text)."\n"
                    .str_pad("", CHAR_WIDTH, '*');
        
        $message = wordwrap($message, CHAR_WIDTH);
        
        echo ($message);
        fwrite($outFile, $message);
        fclose($outFile);
        exec('./async '. $file);
    }
}

?>
