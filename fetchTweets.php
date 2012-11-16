<?php
/* Configurable Definitions */
define('HASHTAG','hpmprinter'); //def 32
define('NARROW', true); // true/false
define('NARROW_RIGHT_ALIGN', true); // applies if using narrow


/* Definitions */
define('CHAR_WIDTH_58',28); //def 32
define('CHAR_WIDTH_80',43); //def 43

if (NARROW){
    define('CHAR_WIDTH',CHAR_WIDTH_58);
} else {
    define('CHAR_WIDTH',CHAR_WIDTH_80);
}

$textFilesArray = scandir('textfiles', 1);

if (is_null($textFilesArray)){
    $url = fopen('http://search.twitter.com/search.json?q=%23'.HASHTAG, 'r');
} else {
    $url = fopen('http://search.twitter.com/search.json?since_id='.$textFilesArray[0].'&q=%23'.HASHTAG, 'r');
}
$tweetArray = json_decode(stream_get_contents($url));
fclose($url);
$results = $tweetArray->results;



$reverseResults = array_reverse($results);
foreach($reverseResults as $result){
    $id = $result->id_str;
    $file = 'textfiles/'.$id.'.txt';
    
    if(!file_exists($file)){
        $outFile = fopen($file, 'w');
        
        $timeTemp = explode('+',$result->created_at);
        $formattedTime = trim($timeTemp[0]);
        
        if (NARROW){
            $formattedTime_t = explode(',' , $formattedTime);
            $formattedTime = $formattedTime_t[1];
            
        }
        
        $message =  str_pad($formattedTime, CHAR_WIDTH, '*', STR_PAD_BOTH)."\n"
		    .'From: @'.$result->from_user."\n"
                    .'Message: '
                    .trim($result->text)."\n"
                    .str_pad("", CHAR_WIDTH, '*');
        
        $message = wordwrap($message, CHAR_WIDTH);
        
        if (NARROW && NARROW_RIGHT_ALIGN) {
            $lines = explode("\n", $message);
            $message = '';
            foreach($lines as $line){
                $message = $message . str_pad(trim($line), CHAR_WIDTH_80, ' ', STR_PAD_LEFT)."\n";
            }
        }
        //Tidy up characters and encoding for printer
        $message = (htmlspecialchars_decode(utf8_encode($message)));
        echo $message ."\n";
        fwrite($outFile, $message);
        fclose($outFile);
        exec('./async '. $file . ' 2');
    } 
    
} // End of foreach

?>
