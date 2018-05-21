<?php
$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
$ticketUrl = 'http://' . $_SERVER['HTTP_HOST'] . $uri_parts[0];
if(!isset($_GET['ticket']) && empty($_GET['ticket'])){
    
    fetchNewTicket($ticketUrl);
} else {
   $ch = curl_init();
    $headers = array(
    'Accept: application/xml',
    'Content-Type: application/xml',

    );
    $url = "http://total-health.testing.edufra.me/cas/proxyValidate.xml?service=".$ticketUrl."&ticket=" .$_GET['ticket'];
    curl_setopt($ch, CURLOPT_URL,$url );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $result = curl_exec($ch);

        if (strpos($result, '@') !== false) {
            // split response into valid username
            $result = explode('@',$result,2);
            $result = $result[1]; 
            var_dump($result);
        }
        else{
            fetchNewTicket($ticketUrl);
        }
}

function fetchNewTicket($ticketUrl)
{
    header("Location:http://total-health.testing.edufra.me/cas/login?service=" .$ticketUrl);
}
?>