<?php
// Load all the stuff
require_once( __DIR__ . '/vendor/autoload.php' );
require_once( __DIR__ . '/user-config.php' );

// Log in to a wiki
$api = new \Mediawiki\Api\MediawikiApi( 'https://en.wikipedia.org/w/api.php' );
$api->login( new \Mediawiki\Api\ApiUser( 'Lcawte', MW_ACCOUNT_PASS ) );
$services = new \Mediawiki\Api\MediawikiFactory( $api );

/*
// Get a page
$page = $services->newPageGetter()->getFromPageId( 19065069 );
$content = $page->getRevisions()->getLatest()->getContent()->getData();
*/

$params = [
	'pageid' => 19065069,
	'prop' => 'wikitext',
	'section' => 0,
	'contentmodel' => 'wikitext',
	'utf8' => 1,
	'formatversion' => 2,
];
$request = new \Mediawiki\Api\SimpleRequest( 'parse', $params );
$response = $api->getRequest( $request );
$wikitext = $response['parse'];
var_dump( $wikitext );
processWikitext( $wikitext['wikitext'] );

function processWikitext( $wikitext ) {
	$lines = explode( "\n", $wikitext );
	$infobox = selectInfobox( $lines );
	$infobox = processInfobox( $infobox );
	printf( "Clean Infobox data" );
	var_dump( $infobox );
}

function selectInfobox( $content ) {
	$infobox = array();
	//$ibOpen = 0;
	//$ibClose = 0;
	$ibLine = 0;
	$ibActive = false;

	foreach( $content as $line=>$value ) {
		if( strpos( strtolower( $value ), '{{infobox officeholder' ) !== false ) {
			printf( "start @ " . $line );
			$ibActive = true;
			//$ibOpen++;
			$ibLine++;
			$infobox[$ibLine] = $value;
		} else if( $ibActive == true /*&& $ibOpen !== $ibClose*/ ) {
			// If we're in the infobox, but we've not closed it yet...
			$ibLine++;
			$infobox[$ibLine] = $value;
			if( $value === "}}") {
				$ibActive = false;
			}
		}
	}

	return $infobox;
}

function processInfobox( array $box ) {
	$completeBox = array();
	foreach( $box as $key=>$value ) {
		$tmpArray = explode( '=', $value );
		$tmpKey = preg_replace("/[^A-Za-z0-9 ]/", '', $tmpArray[0]);
		$completeBox[$tmpKey] = $tmpArray[1];
	}
	return $completeBox;
}
?>