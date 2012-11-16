<?php

$url = fopen('http://search.twitter.com/search.json?q=%23hpmprinter', 'r');
$tweetArray = json_decode(stream_get_contents($url));
fclose($url);
$results = $tweetArray->results;

foreach($results as $result){
    $id = $result->id_str;
    if(!file_exists($id.'.txt')){
        $outFile = fopen($id.'.txt', 'w');
        fwrite($outFile, 'From: @'.$result->from_user.'\n'
                .'Time: '.$result->created_at.'\n'
                .'Message: '
                .$result->text
                );
        fclose($outFile);
    }
}

?>
