<?php
// Load all the stuff
require_once( __DIR__ . '/vendor/autoload.php' );

$guzzleClient = new GuzzleHttp\Client(['base_uri' => 'https://mwph-api.toolforge.org/templates/']);

// @ToDo: Change this and have a way to actually feed page names in...
$page = "Boris Johnson";

$result = getTemplateData( $page, $guzzleClient );

function getTemplateData( $page, $guzzle ) {
	$uglyBox = NULL;
	$box = array();

	$response = $guzzle->request( 'GET', $page );
	//var_dump( $response->getBody()->getContents() );
	$data = json_decode( $response->getBody()->getContents(), true );

	foreach( $data as $ib ) {
		if( trim( $ib['name'] ) === "Infobox officeholder" ) {
			$uglyBox = $ib['params'];
			break;
		}
	}

	// Tidy up the box a little
	if( is_array( $uglyBox ) ) {
		foreach( $uglyBox as $param=>$value ) {
			$value = trim( $value );
			if( !empty( $param ) ) {
				$box[$param] = $value;
			} else {
				$param = null;
			}
		}
	}

	return $box;
}