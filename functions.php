<?php
//function for getting instagram id drom url.
function insta_id($link)
{
	$parsed = parse_url($link);
	$link_path = $parsed['path'];
	//checking valid link or not by checking host
if ($parsed['host'] == "www.instagram.com" || $parsed['host'] == "instagram.com")
    {
	$id_ = str_replace("/p/", "", $link_path);
	$id = str_replace("/", "", $id_);
	}
else 
    {
    $id = "null";
    }
    return $id;
}
//for getting media parameters from id.
function media_info($i_id)
{
	//checking empty or not. if empty status sets to 'error'.
	if ($i_id == "null")
	{
		$details = array('status' => "error");
	}
    else
    {	
 
    //if not empty. Then making curl request.
	$ch = curl_init ();
    $url = "https://www.instagram.com/p/{$i_id}";
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36");
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
	
    $file_contents = curl_exec ($ch);
	
	//checking for curl error.
    if (curl_errno ($ch)) {
        echo curl_error ($ch);
        curl_close ($ch);
        exit ();
        }
    curl_close ($ch); 

	//using dom to parse document. 
    $html = $file_contents;
    $doc = new DOMDocument();
    @$doc->loadHTML($html); 
    $doc->preserveWhiteSpace = false; 
    $hTwo= $doc->getElementsByTagName('script'); 
    $content = $hTwo->item(1)->nodeValue; 
    $id2 = str_replace("window._sharedData = ", "", $content);
    $id = str_replace(";", "", $id2);
    $final = json_decode($id,true);
    //found valid tags using inspect element in chrome.
	$is_video = $final['entry_data']['PostPage']['0']['graphql']['shortcode_media']['is_video'];
	//defining media type. To make classification easy. Was present in the json data from instagram.
	if ($is_video)
	{
		$media = $final['entry_data']['PostPage']['0']['graphql']['shortcode_media']['video_url'];//medial url for video
		$type = "video";
	}
    else
    {
        $media = $final['entry_data']['PostPage']['0']['graphql']['shortcode_media']['display_url'];//media url for image.
		$type = "image"; 		
    }
	if ($media != null)
	{
		//Final array with details
	$details = array('status' => "ok",'insta_id' => $i_id, 'type' => $type, 'media_url' => $media);
	}
	else
	{
	$details = array('status' => "error");	
	}
	}
	return $details;
}	
?>
