<?php

$fomako_ip = '192.168.1.188';
$fomako_settings_url = 'http://'.$fomako_ip.'/ajaxcom?szCmd=%7B%22GetEnv%22:%7B%22StreamPublish%22:%7B%22nChannel%22:-1%7D%7D%7D';
$fomako_start_url = 'http://'.$fomako_ip.'/ajaxcom';
$fomako_stop_url = 'http://'.$fomako_ip.'/ajaxcom';

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// fomako_getstreamsettings
//-----------------------------------------------
// summary: gets the master and slave video publish settings on the Fomako camera.
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function fomako_getstreamsettings()
{
    global $fomako_settings_url;
    $response = fomako_raw_execute($fomako_settings_url, null, null, 5, 10);
    $obj = json_decode($response);
    return $obj;
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// fomako_startstream
//-----------------------------------------------
// summary: enables the master stream on the Fomako camera.
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function fomako_startstream()
{
    global $fomako_start_url;

    //get the stream settings
    $settings = fomako_getstreamsettings();

    //enable the master stream, disable the slave stream
    $settings->{'stValue'}[0]->{'stMaster'}->{'bEnable'} = 1;
    $settings->{'stValue'}[0]->{'stSlave'}->{'bEnable'} = 0;

    //create a parameter to update the Fomako stream publish settings
    $szCmd = array(
        'SetEnv' => array(
            'StreamPublish' => $settings->{'stValue'}
        )
    );

    //create the payload to send to the "ajaxcom" handler
    $payload = array("szCmd" => json_encode($szCmd));

    //send stream publish settings the Fomako camera
    $response = fomako_raw_execute($fomako_start_url, null, $payload, 5, 10);

    //output the response
    echo $response;
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// fomako_stopstream
//-----------------------------------------------
// summary: disables the master stream on the Fomako camera.
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function fomako_stopstream()
{
    global $fomako_stop_url;
    
    //get the stream settings
    $settings = fomako_getstreamsettings();
    
    //disable the master and slave streams
    $settings->{'stValue'}[0]->{'stMaster'}->{'bEnable'} = 0;
    $settings->{'stValue'}[0]->{'stSlave'}->{'bEnable'} = 0;

    //create a parameter to update the Fomako stream publish settings
    $szCmd = array(
        'SetEnv' => array(
            'StreamPublish' => $settings->{'stValue'}
        )
    );

    //create the payload to send to the "ajaxcom" handler
    $payload = array("szCmd" => json_encode($szCmd));

    //send stream publish settings the Fomako camera
    $response = fomako_raw_execute($fomako_stop_url, null, $payload, 5, 10);

    //output the response
    echo $response;
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// fomako_raw_execute
//-----------------------------------------------
// summary: executes an HTTP request using the specified URL.
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function fomako_raw_execute($url, $cookie = null, $payload=null, $conntimeout = 1, $totaltimeout = 3){
    //create a list of headers that we will send with the request
    $headers = array();

    //if a cookie value was passed in, then append it to the headers array
    if( !empty($cookie) ){
       array_push($headers, 'Cookie: ' . $cookie);
    }
   
    // create curl resource
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $conntimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $totaltimeout);

    //if a payload was specified
    if(!empty($payload)){
        //add the payload as a POST parameter
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }

    //execute the URL request and get the response
    $response = curl_exec($ch);

    //close the curl resource
    curl_close($ch);

    //return the response to the calling function
    return $response;
   }

   //fomako_startstream()
   fomako_stopstream()
?>