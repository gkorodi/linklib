<?php
require_once('_includes.php');

function get_web_page( $url )
{
	$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

	$options = array(
		CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
		CURLOPT_POST           =>false,        //set to GET
		CURLOPT_USERAGENT      => $user_agent, //set user agent
		CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
		CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
		CURLOPT_RETURNTRANSFER => true,     // return web page
		CURLOPT_HEADER         => false,    // don't return headers
		CURLOPT_FOLLOWLOCATION => true,     // follow redirects
		CURLOPT_ENCODING       => "",       // handle all encodings
		CURLOPT_AUTOREFERER    => true,     // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
		CURLOPT_TIMEOUT        => 120,      // timeout on response
		CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	);

	$ch      = curl_init( $url );
	curl_setopt_array( $ch, $options );
	$content = curl_exec( $ch );
	$err     = curl_errno( $ch );
	$errmsg  = curl_error( $ch );
	$header  = curl_getinfo( $ch );
	curl_close( $ch );

	$header['errno']   = $err;
	$header['errmsg']  = $errmsg;
	$header['content'] = $content;
	return $header;
}

function curlGET($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_URL, $url);
	$resp['content'] = curl_exec($ch);
	$resp['errno'] = curl_errno( $ch );
	$resp['errmsg'] = curl_error( $ch );
	$resp['info'] = curl_getinfo( $ch );
	curl_close($ch);
	return $resp;
}

function findMetaTags($content) {
	global $debugs;
	$debugs[] = "findMetaTags() Starting. content-length:".strlen($content);

	libxml_use_internal_errors(true);
	$metaTags = Array();
	$doc = new DOMDocument;

	if (!isset($content) || empty($content)) {
		return $metaTags;
	}

	if (!$doc->loadHTML($content)) {
		foreach (libxml_get_errors() as $error) {
			// handle errors here
			$debugs[] = "findMetaTags() Error:".print_r($error, true);
		}
		libxml_clear_errors();
	} else {
		$tags = $doc->getElementsByTagName('meta');
		$debugs[] = "findMetaTags() There are ".count($tags)." tags";
		$debugs[] = "findMetaTags() tags:".print_r($tags, true);
		foreach ($tags as $tag) {

			if ($tag->hasAttributes())  {
				$attrs = Array();
				foreach ($tag->attributes as $attr)
				{
					//if ($attr->nodeName === 'content') { $debugs[] = $attr->nodeValue; } else { continue; }
					$attrs[$attr->nodeName] = $attr->nodeValue;
				}

				$fieldName = 'nothing';
				if (isset($attrs['name'])) {
					$fieldName = $attrs['name'];
				} else {
					if (isset($attrs['property'])) {
						$fieldName = $attrs['property'];
					} else {
						if (isset($attrs['itemprop'])) {
							$fieldName = $attrs['itemprop'];
						} else {
							$fieldName = 'unknown_'.print_r($attrs, true);
						}
					}
				}
				//$debugs[] = print_r($attrs, true);
				$metaTags[str_replace('-','_', str_replace(':','_', $fieldName))] = (isset($attrs['content'])?$attrs['content']:'n/a');
			} else {
				$debugs[] = "findMetaTags() No attributes ".$tag->nodeName;
			}
		}
	}
	$debugs[] = "findMetaTags() Finished.";
	return $metaTags;
}


