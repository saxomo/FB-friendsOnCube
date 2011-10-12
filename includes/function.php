<?php 
function cache($script,$file,$expire){
	if (file_exists($file) && filemtime($file) > (time() - $expire))
	{
		include($file);
	}
	else{
		ob_start(); echo $script;
		$fp = fopen($file,"w"); 
	fputs($fp, ob_get_contents());
	fclose($fp);
	}
}

class sfFacebookPhoto {

    private $useragent = 'Loximi sfFacebookPhoto PHP5 (curl)';
    private $curl = null;
    private $response_meta_info = array();
    private $header = array(
            "Accept-Encoding: gzip,deflate",
            "Accept-Charset: utf-8;q=0.7,*;q=0.7",
            "Connection: close"
        );

    public function __construct() {
        $this->curl = curl_init();
        register_shutdown_function(array($this, 'shutdown'));
    }

    /**
     * Get the real url for picture to use after
     */
    public function getRealUrl($photoLink) {            
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->curl, CURLOPT_URL, $photoLink);
        //this assumes your code is into a class method, and uses $this->readHeader as the callback //function
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, array(&$this,'readHeader'));
        $response = curl_exec($this->curl);
        if(!curl_errno($this->curl)) {
            $info = curl_getinfo($this->curl);
           // var_dump($info);
             if($info["http_code"] == 302) {
                 $headers = $this->getHeaders();
                 if(isset($headers['fileUrl'])) {
                     return $headers['fileUrl'];
                 }
             }
        }
        return false;
    }


    /**
     * Download facebook user photo
     * 
     */
    public function download($fileName) {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->curl, CURLOPT_URL, $fileName);
        $response = curl_exec($this->curl);
        $return = false;
        if(!curl_errno($this->curl)) {
            $parts = explode('.',$fileName);
            $ext = array_pop($parts);
            $return = sfConfig::get('sf_upload_dir') . '/tmp/' . uniqid('fbphoto') . '.' . $ext;
            file_put_contents($return, $response);
        }
        return $return;
    }

    /**
 * CURL callback function for reading and processing headers
 * Override this for your needs
 *
 * @param object $ch
 * @param string $header
 * @return integer
 */

private function readHeader($ch, $header) {
        //extracting example data: filename from header field Content-Disposition
        $filename = $this->extractCustomHeader('Location: ', '\n', $header);
        if ($filename) {
            $this->response_meta_info['fileUrl'] = trim($filename);
        }
        return strlen($header);
}

    private function extractCustomHeader($start,$end,$header) {
    $pattern = '/'. $start .'(.*?)'. $end .'/';
    if (preg_match($pattern, $header, $result)) {
        return $result[1];
    } else {
        return false;
    }
}

    public function getHeaders() {
        return $this->response_meta_info;
}

    /**
     * Cleanup resources
     */
    public function shutdown() {
        if($this->curl) {
            curl_close($this->curl);
        }
    }


}
 
function save_image($img,$fullpath){
 
    $ch = curl_init ($img); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,0); 
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $rawdata=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($fullpath)){
        unlink($fullpath);
    }
    $fp = fopen($fullpath,'x');
    fwrite($fp, $rawdata);
    fclose($fp);
}
function getImg($path,$url){
	set_time_limit(0); // set no time limit to download large file
	ini_set('display_errors',true);//Just in case we get some errors, let us know....
	$fp = fopen ($path, 'w+');//where the file will be saved
	$ch = curl_init($url);//Here is the file we are downloading
	curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
	
}function get_data($url){
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
	return $data;
}
//&amp;#353; za š &amp;#269; za č in &amp;#382; za ž. 
function clean($data){
	$data = preg_replace( "/\\\u010d/" ,'&#269;',$data) ; //č
	$data = preg_replace( "/\\\u010c/" ,'&#268;',$data) ;//Č
    $data = preg_replace( "/\\\u0161/" ,'&#353;',$data) ;//š
	$data = preg_replace( "/\\\u0160/" ,'&#352;',$data) ;//Š
    $data = preg_replace( "/\\\u017e/" ,'&#382;',$data) ;//ž
    $data = preg_replace( "/\\\u017e/" ,'&#382;',$data) ;//Ž
    $data = preg_replace( "/\\\u0107/" ,'&#263;',$data) ;//ć
    $data = preg_replace( "/\\\u0106/" ,'&#262;',$data) ; //Ć
	$pattern = '/\{\"data\":\[/';
	$replacement = '';
	$data= preg_replace($pattern, $replacement, $data);
	$pattern = '/\]\}/';
	return preg_replace($pattern, $replacement, $data);
	}
 function saveFile($myFile,$text){
	$fh = fopen($myFile, 'w') or die("can't open file");	
	fputs($fh, $text);	
	fclose($fh);
	}
 function isin($a,$b){
	 $in=false;
	 for($i=0;$i<(count($b)-1);$i++){
		 if(strcmp($a,$b[$i])==0)$in=true;	
	 } return $in;
}
?>
