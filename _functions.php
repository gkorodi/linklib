<?php
require_once('_includes.php');

$resp['status'] = 'error';
$resp['message'] = 'No method has been specified';
$debugs = $_REQUEST;

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

function getContentByLinkId($linkId) {
	global $debugs;
	$resp = query('SELECT * FROM tobecurated WHERE id = '.$linkId.' LIMIT 1');
	$content = '';
	try {
		$content = file_get_contents($resp['rows'][0][1]);
	} catch (Exception $ex) {
		$debugs[] = 'getContentByLinkId() ERROR reading link content:'.$ex->getMessage();
	}
	return $content;
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

if (isset($_REQUEST['method'])) {
	array_push($debugs, '_functions.php Method '.$_REQUEST['method'].' has been specified.');


	if ($_REQUEST['method']==='deletelink') {
		$link = new Link($_REQUEST['id']);
		if ($link->delete()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Deleted link, with id <b>'.$_REQUEST['id'].'</b>';
			array_push($debugs, $link->debugs);
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Could not delete link, with id <b>'.$_REQUEST['id'].'</b><br />'.
				'<small>'.$link->getErrorListFormatted().'</small>';
		}
	} else if ($_REQUEST['method']==='curatelink') {
		$debugs[] = '/curatelink starting '.json_encode($_REQUEST);
		$tagList = findMetaTags(getContentByLinkId($_REQUEST['id']));
		$debugs[] = '/curatelink found '.count($tagList).' tags';

		$timestamp = '';
    $tags = 'curate';
		$skippTags = Array('parsely_link','twitter_site','twitter_image','twitter_image_src');
    if (gettype($tagList)==='string') { $debugs[] = print_r($tagList, true);}
		if (isset($tagList) && !empty($tagList) && gettype($tagList)==='array') {
      foreach($tagList AS $tag=>$tagValue) {
			     if (in_array($tag, $skippTags)) { continue; $debugs[] = '/curatelink skipp '.$tag.' tag'; }

           switch($tag) {
             case 'REVISION_DATE':
             case 'analyticsAttributes.articleDate':
             case 'og_article_published_time':
             case 'article_published_time':
              $timestamp = date("Y-m-d H:i:s",strtotime($tagValue));
              break;
             case 'keywords':
              $tags = $tagValue;
              break;
             default:
           }
			     $debugs[] = 'tag:'.$tag.' = '.(gettype($tagValue)==='string'?$tagValue:gettype($tagValue));
		  }
    }
		$tags = str_replace("'",",", $tags);
		$sqlQueryString = "UPDATE tobecurated SET tags = '".$tags."' ".
      (!empty($timestamp)?', timestamp = "'.$timestamp.'"':'').
      " WHERE id = ".$_REQUEST['id'];
		$debugs[] = '/curatelink SQL:'.$sqlQueryString;

		// Create connection
		$conn = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			$resp['status'] = 'error';
			$resp['message'] = "Error connecting to the datasource: " . $conn->connect_error;
		} else {
			if ($conn->query($sqlQueryString) === TRUE) {
			    $resp['status'] = 'ok';
					$resp['message'] = "Link ".$_REQUEST['id']." has been updated with tags `".$tags."`.";
			} else {
				$resp['status'] = 'error';
				$resp['message'] = "Error updating record: " . $conn->error.' as '.$sqlQueryString;
			}
		}
		$conn->close();

  } else if ($_REQUEST['method']==='delcuratelink') {

    $sql = "DELETE FROM tobecurated WHERE id = ".$_REQUEST['id'];
    $resultset = query($sql);
    $debugs[] = "Resultset:".print_r($resultset, true);
    if (isset($resultset['status']) && $resultset['status'] === 'ok') {
      $resp['status'] = 'ok'; $resp['message'] = 'Deleted link '.$_REQUEST['id'];
    } else {
      $resp['status'] = 'error'; $resp['message'] = 'Could not delete link '.$_REQUEST['id'];
    }

  } else if ($_REQUEST['method']==='savcuratelink') {

    $sql = "INSERT INTO `links` (`link`, `title`, `last_updated`, `tags`)
    	SELECT `link`, `title`, `timestamp`,`tags` FROM tobecurated WHERE id = ".$_REQUEST['id'];
    $resultset = query($sql);
    $debugs[] = print_r($resultset, true);
    if (isset($resultset['status']) && $resultset['status'] === 'ok') {

      $resultset = Array();
      $sql = "DELETE FROM tobecurated WHERE id = ".$_REQUEST['id'];
      $resultset = query($sql);
      $debugs[] = print_r($resultset, true);
      if (isset($resultset['status']) && $resultset['status'] === 'ok') {
        $resp['status'] = 'ok'; $resp['message'] = 'Moved link from `tobecurated` table to `links` table.';
      } else {
        $resp['status'] = 'error'; $resp['message'] = 'Could not delete link from `tobecurated` table.';
      }
    } else {
      $resp['status'] = 'error'; $resp['message'] = 'Could not insert link details to `links` table.';
    }

	} else if ($_REQUEST['method']==='delfile') {
		$resp['status'] = 'error';
		if (file_exists($_REQUEST['filename'])) {
			array_push($debugs, "File exists");
			try {
				$s = unlink($_REQUEST['filename']);
				$resp['message'] = ($s?
					"File '".$_REQUEST['filename']."' has been deleted.":
					"File '".$_REQUEST['filename']."' could not be deleted."
				);
				$resp['status'] = 'ok';
			} catch (Exception $e) {
				$resp['message'] = "Could not delete file!!!!";
				array_push($debugs, print_r($e, true));
			}
		} else {
			array_push($debugs, "File does not exists.");
		}

} else if ($_REQUEST['method']==='getfilecount') {
		$resp['status'] = 'ok';
		$resp['value'] = count(glob('data/*.json'));

	} else if ($_REQUEST['method'] === 'savefeeditem') {
		$debugs[] = 'saving new feed item.';

		$link = new Link();
		$link->title = $_REQUEST['title'];
		$link->link = $_REQUEST['link'];
		$link->last_updated = $_REQUEST['last_updated'];

		if ($link->addLink()) {
			$resp['status'] = 'ok';
			$resp['message'] = "Imported details.";
			foreach($link->debugs AS $msg) { $debugs[] = "${msg}"; }
		} else {
			$resp['status'] = 'error';
			$resp['message'] = "Could not import feed item.";
		}


	} else if ($_REQUEST['method']==='savefile') {
		$resp['status'] = 'error';
		$inputFileName = FEED_DIR.'/'.$_REQUEST['filename'].'.json';

		$debugs[] = 'savefile() Processing file:'.$inputFileName;

		if (file_exists($inputFileName)) {
			$debugs[] = "savefile() File exists";

			try {
				$raw = file_get_contents($inputFileName);
				$newLink = new Link();
				$obj = json_decode($raw);
				$newLink->link = $obj->link.'';
				$newLink->title = $obj->title;
				$newLink->status = getLinkStatus($newLink->link);
				$newLink->last_updated = $obj->last_updated.''; //date('c', strtotime(str_replace(' at ',' ', $obj->published)));
				$newLink->tags = $obj->tags;

				$debugs[] = 'savefile() link         :'.$newLink->link;
				$debugs[] = 'savefile() title        :'.$newLink->title;
				$debugs[] = 'savefile() status       :'.$newLink->status;
				$debugs[] = 'savefile() last_updated :'.$newLink->last_updated;
				$debugs[] = 'savefile() tags         :'.$newLink->tags;

				if ($newLink->addLink()) {
					$resp['status'] = 'ok';
					$resp['message'] = "Imported file for curation. id:". $newLink->id;
					foreach($newLink->debugs AS $msg) { $debugs[] = $msg; }
					if (unlink($inputFileName)) {
						$debugs[] = "savefile() deleted file ${inputFileName}";
					} else {
						$debugs[] = "savefile() ERROR: could not delete file ${inputFileName}";
					}
				} else {
					$resp['message'] = "Could not import file for curation!!!!";
				}

			} catch (Exception $e) {
				$resp['message'] = "Could not import file!!!!";
				$debugs[] = "savefile() ".print_r($e, true);
			}
		} else {
			$debugs[] = "savefile() File does not exists.";
		}

	} else if ($_REQUEST['method']==='curate_del') {

		$status = false;
		$logmessages = Array();

		$mysqli = new mysqli(DB_HOST.(defined(DB_PORT)?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($logmessages, "Failed to connect to MySQL: ".
			    "(" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);
			$queryStr = "DELETE FROM import1 WHERE link = '".$_REQUEST['linkUrl']."'";
			if ($mysqli->query($queryStr) === TRUE) {
				$status = true;
				array_push($logmessages, "Link ".$_REQUEST['linkUrl']." has been successfully deleted.");
			} else {
				array_push($logmessages, "Could not execute delete statement [".$queryStr."]: ".
					"(" . $mysqli->errno . ") " .$mysqli->error);
			}
			$mysqli->close();
		}
		$resp['status'] = ($status?'ok':'error');
		$resp['message'] = implode("\n",$logmessages);

	} else if ($_REQUEST['method']==='curate_tag') {

		$status = false;
		$logmessages = Array();

		$mysqli = new mysqli(DB_HOST.(defined(DB_PORT)?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
		    array_push($logmessages, "Failed to connect to MySQL: ".
			    "(" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		} else {
			$mysqli->autocommit(true);
			$queryStr = "UPDATE import1 SET tags = '".$_REQUEST['tags']."', ".
					"status = ".getLinkStatus($_REQUEST['linkUrl']).
						" WHERE link = '".$_REQUEST['linkUrl']."'";
			if ($mysqli->query($queryStr) === TRUE) {
				$status = true;
				array_push($logmessages, "Link ".$_REQUEST['linkUrl']." has been tagged.");
			} else {
				array_push($logmessages, "Could not update 'tags' column [".$queryStr."]: ".
					"(" . $mysqli->errno . ") " .$mysqli->error);
			}
			$mysqli->close();
		}
		$resp['status'] = ($status?'ok':'error');
		$resp['message'] = implode("\n",$logmessages);

	} else if ($_REQUEST['method']==='getheader') {
			$link = new Link($_REQUEST['id']);
			$resp['status'] = 'ok';
			$resp['message'] = 'Meta tags parsing';

			$RESTresponse = curlGET(trim($link->link));
			//$debugs[] = print_r($RESTresponse, true);

			$debugs[] = 'About to parse '.strlen($RESTresponse['content'])." bytes.";
			$resp['meta'] = findMetaTags($RESTresponse['content']);
			//$resp['info'] = $RESTresponse['info'];
			$resp['status'] = 'ok';
			$resp['message'] = 'URL Headers for '.$link->link;


	} else if ($_REQUEST['method']==='curate_done') {
		$newLink = new Link();

		$newLink->link = $_REQUEST['link'];
		$newLink->title = $_REQUEST['title'];
		$newLink->status = getLinkStatus($_REQUEST['link']);
		$newLink->last_updated = $_REQUEST['published'];
		$newLink->tags = $_REQUEST['tags'];

		if ($newLink->addLink()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'New link has been created, with id '.$newLink->id;
		} else {
			$resp['status'] = 'error';
			$resp['message'] = $newLink->message;
		}
	} else if ($_REQUEST['method']==='repairlink') {
		array_push($debugs, 'repairlink() id:'.$_REQUEST['id']);

		$link = new Link($_REQUEST['id']);
		$resp['message'] = 'Repair options for link '.$link->id;

		$resp['id'] = $link->id;
		$resp['title'] = $link->title;
		$resp['link'] = $link->link;
		$resp['state'] = $link->status;
		$resp['last_updated'] = $link->last_updated;
		$resp['tags'] = $link->tags;

		$ch = curl_init($link->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);

		$content = curl_exec($ch);
		$resp['info'] = curl_getinfo($ch);
		curl_close($ch);
		if ($resp['info']['http_code'] == '200') {
			$resp['status'] = 'ok';
		}
		$resp['meta'] = findMetaTags($content);

	} else if ($_REQUEST['method']==='repair') {
		$resp['status'] = 'ok';

		$link = new Link($_REQUEST['id']);
		$resp['message'] = 'Repair options for link '.$link->id;

		$resp['id'] = $_REQUEST['id'];
		$resp['title'] = $link->title;
		$resp['link'] = $link->link;
		$resp['last_updated'] = date('Y-m-d H:i:s');
		$resp['tags'] = '';

		$ch = curl_init($link->link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);

		$content = curl_exec($ch);
		$resp['info'] = curl_getinfo($ch);
		curl_close($ch);

		if ($resp['info']['http_code']!=200) {
			$resp['status'] = 'error';
			$resp['message'] = 'Could not get the expected webpage!'.print_r($resp['info'], true);
		} else {

			$startpos = strpos($content, '>', strpos($content, '<title'));
			$endpos = strpos($content, '</title', $startpos);
			$resp['title'] = substr($content, $startpos+1, $endpos-$startpos-1);

			$editorLink = '<div class="form-group">'.
			'<label for="link">Link</label>'.
			'<input type="text" class="form-control" id="link" name="link" placeholder="URL for the link " value="'.$resp['link'].'">'.
			'</div>';

			$editorTitle = '<div class="form-group">'.
			'<label for="title">Title</label>'.
			'<input type="title" class="form-control" id="title" name="title" '.
				'placeholder="Enter the title of the link" value="'.$resp['title'].'">'.
			'</div>';

			$editorTags = '<div class="form-group">'.
			'<label for="tags">Tags</label>'.
			'<input type="text" class="form-control" id="tags" name="tags" placeholder="Comma separated list of tags." value="'.$resp['tags'].'">'.
			'</div>';

			$editorDate = '<div class="form-group">'.
			'<label for="last_updated">Last Updated</label>'.
			'<input type="text" class="form-control" id="last_updated" name="last_updated" placeholder="Last time updated" value="'.$resp['last_updated'].'">'.
			'</div>';

			$resp['body'] = '<form method="POST" action="linkedit.php?id='.$_REQUEST['id'].'">'.
				'<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />'.
				$editorTitle.
				$editorLink.
				$editorTags.
				$editorDate.
				'<button type="submit" class="btn btn-default">Submit</button>'.
			'</form>';
		}

	} else if ($_REQUEST['method']==='getRandomTotal') {
		$sql="SELECT count(*)  FROM links WHERE tags IS NULL OR last_updated IS NULL OR tags = '' OR last_updated = ''";
		$raw_rs = query($sql);
		$resp['status'] = 'ok';
		$resp['count'] = 123;

	} else if ($_REQUEST['method']==='getRandomList') {
		//$sql = 'SELECT count(*) AS rowcount FROM links WHERE status != 200';
		//$query_response = query($sql);

		$resp['status'] = 'ok';
		$extra_criteria = (isset($_REQUEST['status'])?'AND status='.$_REQUEST['status']:'');
		if (isset($_REQUEST['notags'])) {
			$extra_criteria .= ' AND tags IS NULL ';
		}
		$sql='SELECT * FROM links WHERE '.'status != 200 '.$extra_criteria.' LIMIT 100';

		$resp['linklist'] = query($sql);

	} else if ($_REQUEST['method']==='getAllTags') {
		$categories['empty'] = 0;
		$categories['NULL'] = 0;
		$r = query("SELECT tags FROM links GROUP BY tags");
		foreach ($r['rows'] AS $row) {
			if ($row[0]===null) { $categories['NULL']++; continue;}
			if ($row[0]==='') { $categories['empty']++; continue;}
			$cats = explode(',', $row[0]);
			foreach($cats AS $category) {
				$c = trim($category);
				if (isset($categories[$c])) {
					$categories[$c]++;
				} else {
					$categories[$c] = 1;
				}
			}
		}
		arsort($categories);
		$resp['alltags'] = $categories;

	} else if ($_REQUEST['method']==='getLinkTags') {
		$resp['status'] = 'ok';
		$resp['linktaglist'] = query('SELECT tags FROM links WHERE id = '.$_REQUEST['id']);

	} else if ($_REQUEST['method']==='getSearchResults') {
		$resp['status'] = 'ok';
		$sql="SELECT * FROM links WHERE UCASE(title) LIKE '%".$_REQUEST['q']."%' ORDER BY title DESC";
		$resp['searchresults'] = query($sql);


	} else if ($_REQUEST['method']==='getRecentPosts') {
		$r = query("SELECT * FROM links ORDER BY last_updated LIMIT 10");
		$resp['status'] = 'ok';
		$resp['message'] = 'recent posts are not available.';

	} else if ($_REQUEST['method']==='getStatusList') {
		$resp['status'] = 'ok';
		$query_response = query('SELECT status, count(*) FROM links WHERE status != 200 GROUP BY status ORDER BY count(*) DESC');
		$resp['status_list'] = $query_response['rows'];

	} else if ($_REQUEST['method']==='getStats') {
		$resp['status'] = 'ok';

		$categories['empty'] = 0;
		$categories['NULL'] = 0;
		$r = query("SELECT tags FROM links GROUP BY tags");
		foreach ($r['rows'] AS $row) {
			if ($row[0]===null) { $categories['NULL']++; continue;}
			if ($row[0]==='') { $categories['empty']++; continue;}
			$cats = explode(',', $row[0]);
			foreach($cats AS $category) {
				$c = trim($category);
				if (isset($categories[$c])) {
					$categories[$c]++;
				} else {
					$categories[$c] = 1;
				}
			}
		}
		arsort($categories);
		$resp['categories'] = $categories;

		$r = query("SELECT tags, count(*) AS counter FROM links GROUP BY tags ORDER BY counter DESC LIMIT 20");
		$resp['category_combinations'] = $r['rows'];


	} else if ($_REQUEST['method']==='getHostList') {
		$resp['status'] = 'ok';
		$query_response = query('SELECT link FROM links WHERE status != 200');

		$hostList = Array();
		foreach($query_response['rows'] AS $row) {
			$lst = split('/',  $row[0]);
			if (isset($lst[2])) {
				$hostList[$lst[2]]++;
			} else {
				$hostList[$lst[2]] = 1;
			}
		}
		asort($hostList);

		$resp['hostlist'] = $hostList;
	} else if ($_REQUEST['method']==='testurl') {

		$link = new Link($_REQUEST['id']);
		array_push($debugs, 'Link #'.$link->id.' and ['.$link->link.']');

		if ($link->test()) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Link is valid.';
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Invalid link.';
		}
		foreach($link->debugs AS $msg) { array_push($debugs, $msg);}

	} else if ($_REQUEST['method']==='testlink') {
		// ***** Test a possible new link ******

		$ch = curl_init($_REQUEST['link']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);

		$content = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		if ($info['http_code'] == 200) {
			$resp['status'] = 'ok';
			$resp['message'] = 'Link is valid.';
		} else {
			$resp['status'] = 'error';
			$resp['message'] = 'Invalid link.';
		}


		$tags = findMetaTags($content);
		$skipTags = explode(',', 'viewport,');
		foreach($tags AS $k => $v) {
			if (in_array($k, $skipTags)) { continue;}

			switch($k) {
				case 'keywords':
					$resp['keywords'] = $v;
					break;
				case 'article_published_time':
					$resp['timestamp'] = $v;
					break;
				default:
					array_push($debugs, print_r('tag:'.$k.' = '.(gettype($v)==='string'?$v:'type:'.gettype($v)), true));
			}
		}
		//array_push($debugs, 'metatags:'.print_r($metatags, true));

		// ***** End of new link test ******

	} else if ($_REQUEST['method']==='insertlink') {
		array_push($debugs, 'Processing new link request.');

		if (!isset($_REQUEST['link'])) {
			$debugs << 'Could not find _link_ attribute in request.';

			$resp['status'] = 'error';
			$resp['message'] = 'link attribute is minimally mandatory.';
		} else {
			$newlink = new Link();
			$debugs << 'Created new Link object.';

			$newlink->link = $_REQUEST['link'];
			$newlink->title = (isset($_REQUEST['title'])?$_REQUEST['title']:'');
			$newlink->tags = (isset($_REQUEST['tags'])?$_REQUEST['tags']:'');
			$newlink->status = (isset($_REQUEST['status'])?$_REQUEST['status']:-1);
			$newlink->last_updated = (isset($_REQUEST['last_updated'])?$_REQUEST['last_updated']:-1);
			$debugs << 'Updated fields of object.';

			if ($newlink->save()) {
				$debugs << implode(PHP_EOL, $newlink->debugs);
				$debugs << 'Object have been saved to persistent store.';

				$resp['status'] = 'ok';
				$resp['message'] = 'Saved link. id:'.$newlink->id;
			} else {
				$debugs << $newlink->debugs;
				$debugs << 'Object could not be saved';

				$resp['status'] = 'error';
				$resp['message'] = 'Could not save link.';
			}
		}
	} else if ($_REQUEST['method']==='updateTag') {

		$sql = "UPDATE links SET tags = '".$_REQUEST['value']."' WHERE id = ".$_REQUEST['id'];
		$resp = query($sql);

	} else if ($_REQUEST['method']==='updatelink') {
		$link = new Link($_REQUEST['id']);

		array_push($debugs,'updatelink() id:'.$link->id);

		if (isset($_REQUEST['column'])) {
			$col = $_REQUEST['column'];
			array_push($debugs,'updatelink() column:'.$col.' new value:`'.$_REQUEST['value'].'`');

			if ($col == 'link') {
				$link->link = $_REQUEST['value'];
			}

			if ($col == 'title') {
				$link->title = $_REQUEST['value'];
			}

			if ($col == 'status') {
				$link->status = $_REQUEST['value'];
			}

			if ($col == 'tags') {
				$link->tags = $_REQUEST['value'];
			}

			if ($col == 'last_updated') {
				$link->last_updated = $_REQUEST['value'];
			}

			if ($link->update()) {
				$resp['status'] = 'ok';
				$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b> and column <b>'.$col.'</b>';
				array_push($debugs,'Updated link, with id *'.$_REQUEST['id'].'* and column *'.$col.'*');
			} else {
				$resp['status'] = 'error';
				$resp['message'] = 'Could not update row with id:'.$link->id.' for column '.$col;
			}
			array_push($debugs, $link->debugs);

		} else {
			if ($link->updateByMap($_REQUEST)) {
				$resp['status'] = 'ok';
				$resp['message'] = 'Updated link, with id <b>'.$_REQUEST['id'].'</b>';
				array_push($debugs, $link->debugs);
			} else {
				$resp['status'] = 'error';
				$resp['message'] = 'Could not update link, with id <b>'.$_REQUEST['id'].'</b>';
				array_push($debugs, $link->debugs);
				if (count($link->errors) > 0) { array_push($debugs, $link->errors); }
			}
		}
	} else {
		$resp['status'] = 'error';
		$resp['message'] = 'Unknown method <b>'.$_REQUEST['method'].'</b>';
	}

} else {

}

$resp['debugs'] = $debugs; //implode("<br />", $debugs);

header("Content-type: application/json");
echo json_encode($resp);
?>
