<?php
$apiKey="REMOVED_SECRET";
$url="https://openrouter.ai/api/v1/chat/completions";
$data=["model"=>"openrouter/free","messages"=>[["role"=>"user","content"=>"hello"]]];
$ch=curl_init($url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));
curl_setopt($ch,CURLOPT_HTTPHEADER,[
    "Content-Type: application/json",
    "Authorization: Bearer ".$apiKey
]);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
$response=curl_exec($ch);
echo "HTTP Code: ".curl_getinfo($ch,CURLINFO_HTTP_CODE)."\nResponse: $response\n";
?>
