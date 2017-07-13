<?php

class To_Twitter{
/**
@date: 12.03.10
@author: schmiddi, schmiddim@gmx.at, twitter:derZwitschernde ;)
@desc:Nachrichten an Twitter senden. Die Nachricht wird im Konstruktor 
uebergeben und dann kann wahlweise per Mail(twittermail) oder per Curl 
die Mail abgesetzt werden
public function __construct($message)		constructor with the messagen (>180 will be cut)
public function tweet_via_curl($usr, $pass)	tweets via curl
public function is_twitter_reachable()		are you able to connect to twitter?
public function tweet_via_email($to)		if not, use the mail function (twittermail account required)
*/

	public $err_arr;	//array with all the errors
	public $msg;

	public function __construct($message){
		$this->err_arr =array();
		if (strlen($message)>180) {
			$this->add_error('construct: Warning more than 180 chars - will be cut');
			$this->msg= substr($message,0,179);
			
		}else {	$this->msg=$message;}
	}//function


	public function tweet_via_curl($usr, $pass){
		if (empty($usr)||empty($pass)) {
			$this->add_error('tweet_via_curl: empty login');
			return;
		}//fi

		$message = $this->msg;
		// The twitter API address
		$url = 'http://twitter.com/statuses/update.xml';

		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, "$url");
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_POST, 1);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$message");
		curl_setopt($curl_handle, CURLOPT_USERPWD, "$usr:$pass");
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);


	if(preg_match('/<error>Could not authenticate you./',$buffer))
			$this->add_error('tweet_via_curl: coult not authenticate you');
		
	}//function


	public function tweet_via_email($to){
		mail($to, "no subject", $this->msg,"From: twitteruser nobody@nohost.com");
		
	}#function

	public function is_twitter_reachable(){
	/** is your server able to talk to twitter? **/
		$hostname='twitter.com';		
		$ip = gethostbyname('twitter.com');
		if ($hostname == $ip) {
			$this->add_error("unable to reach twitter");
			
			return false;
		}else {
			
			return true;
		}//fi

	}//function

	/**************************Error Handling **/
	private function add_error($msg) {
		array_push($this->err_arr,$msg);
	}#function

	public function get_errors() {
		foreach($this->err_arr as $error)
			echo $error."<p>";
	}//function
	/**************************Error Handling **/

}//class

/***********TESTING*******/
#echo "bit.ly";
#$tw = new To_Twitter("via bit.ly");

#$tw->tweet_via_curl("derZwitschernde","");

#$tw->is_twitter_reachable();
#$tw->get_errors();
?>
