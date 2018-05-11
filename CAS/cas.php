<?php
if(!isset($_GET['ticket']) && empty($_GET['ticket'])){

    header("Location:http://total-health.testing.edufra.me/cas/login?service=http://".$_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
} else {
    //echo "Ticket = ".$_GET['ticket'];
   //header('Content-type: application/xml');
 //  header("Location:http://total-health.testing.edufra.me/cas/proxyValidate?ticket=".$_GET['ticket']."&service=http://localhost/Cas/cas.php");

   $ch = curl_init();
    $headers = array(
    'Accept: application/xml',
    'Content-Type: application/xml',

    );
    curl_setopt($ch, CURLOPT_URL, "http://total-health.testing.edufra.me/cas/proxyValidate?ticket=".$_GET['ticket']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $result = curl_exec($ch);
    var_dump($result);

    

}
?>