<?php

//echo "http://api.kickofflabs.com/v1/".$instance['landing_page_id']."/subscribe";

class KickoffLabsAPI {

	//	landing page id
	private $landing_page_id;
	private $send_ar;
	public $error;

	function __construct($landing_page_id, $send_ar) {
		$this->landing_page_id = $landing_page_id;
		$this->send_ar = $send_ar;
	}

	//	subscribe email using the php curl
	function Subscribe($email) {
		$url = "http://api.kickofflabs.com/v1/".$this->landing_page_id."/subscribe";
		$ip=$_SERVER['REMOTE_ADDR'];
		$data = "email=".urlencode($email)."&ip=".$ip;
		$count = 2;
		if(!$this->send_ar) {
			$data .="&skip_ar=1";
			$count++;
		}
		//echo $data;
		
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,$count);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		
		//execute post and grab json string
		if( !$response = curl_exec($ch)) {
			//	error?!
			$this->error = curl_error($ch);
			return false;
		}
		else {
			$result = json_decode($response);
		}
		
		//close connection & return social_url
		curl_close($ch);
		return $result->social_url;
		
	}

}

?>