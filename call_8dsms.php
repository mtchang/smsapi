<?php
// ---------------------------------------------------------
// EVERY8D SMS SAMPLE CODE
// 帳號申請網站 https://tw.every8d.com/
// 多了檢查 UTF8 編碼及長度的限制
// 參考文件：http://tw.every8d.com/api20/doc/EVERY8D%20HTTP%20API%E6%96%87%E4%BB%B6-v2%201-https.pdf
// Update: 2019.02.23 by mtchang.tw@gmail.com
// ---------------------------------------------------------
mb_internal_encoding('UTF-8');

$userid="帳號";	
$password="密碼";	
$subject = "簡訊主旨註記用途";	
//簡訊內容 
$content = "繁简日한Thêmभारत".time();;	
//接收人之手機號碼。格式為: +886912345678或09123456789。多筆 0912345678,0922333444
$mobile = "手機";	
// $method = 'sendsms'; // 送出簡訊
// $method = 'getcredit'; // 取得點數餘額

// 呼叫簡訊服務, 使用者ID, 密碼, 功能
$r = sms_api($userid, $password, $method = 'getcredit');
var_dump($r);

// 呼叫簡訊服務, 使用者ID, 密碼, 功能 , 主旨, 內容 , 手機號碼
$r = sms_api($userid, $password, $method = 'sendsms', $subject ,$content ,$mobile );
var_dump($r);
// ---------------------------------------------------------




// ---------------------------------------------------------
/*
* 函式：呼叫發送簡訊服務
sms_api($userid, $password, $method='', $subject='',$content='',$mobile='', $sendtime = '', $retrytime='14', $msg_lenmax=70)

* 輸入欄位
$userid string
$password string 
$method string  	getcredit=取得餘額(預設)  sendsms = 送出簡訊
$subject string		簡訊主旨，主旨不會隨著簡訊內容發送出去。用以註記本次發送之用途。可傳入空字串。
$content string		簡訊內容
$mobile string 		接收人之手機號碼。格式為: +886912345678或09123456789。多筆接收人時，請以半形逗點隔開( , ，如0912345678,0922333444。國際簡訊(非+886 開頭)則需以三倍點數計價之。	
$sendtime = ''   簡訊預定發送時間。-立即發送：請傳入空字串。-預約發送：請傳入預計發送時間，若傳送時間小於系統接單時間，將不予傳送。格式為YYYYMMDDhhmnss；例如:預約2009/01/31 15:30:00發送，則傳入20090131153000。若傳遞時間已逾現在之時間，將立即發送。
$retrytime='14'  簡訊有效時間
$msg_lenmax=70   預設簡訊長度, 如果超過會被阻止. 直到修正長度上線。

* 回傳值範例
- 查詢餘額
array(2) {
  ["CREDIT"]=>
  string(2) "81"
  ["MESSAGE"]=>
  string(20) "目前剩餘81點數"
}

- 送出簡訊
array(5) {
  ["CREDIT"]=>
  string(5) "81.00"
  ["SENDED"]=>
  string(1) "1"
  ["COST"]=>
  string(1) "1"
  ["UNSEND"]=>
  string(1) "0"
  ["BATCH_ID"]=>
  string(36) "3049ac57-e0b1-4b35-a9c7-92e70ab7d8d8"
}

- 發生錯誤
array(2) {
  ["CREDIT"]=>
  string(4) "-300"
  ["MESSAGE"]=>
  string(31) " 帳號密碼不得為空值。"
}
array(2) {	
  ["CREDIT"]=>
  string(3) "-98"
  ["MESSAGE"]=>
  string(25) "UTF8字元或編碼錯誤"
}
array(2) {
  ["CREDIT"]=>
  string(3) "-97"
  ["MESSAGE"]=>
  string(25) "簡訊長度超過 70 字"
}


*/
function sms_api($userid, $password, $method='', $subject='',$content='',$mobile='', $sendtime = '', $retrytime='14', $msg_lenmax=70)
{
	$url = '';
	// 未加密的URL
	//$smshost = "http://api.every8d.com";
	// 加密的 URL
	$smshost = "https://oms.every8d.com";	
	$url_sendsms = $smshost."/API21/HTTP/sendSMS.ashx";
	$url_getcredit = $smshost."/API21/HTTP/getCredit.ashx";
	
	// 檢查字元及簡訊內容
	$result = NULL;
	$content_utf8 = '';
	$content_utf8_len = 0;
	$content_utf8 = utf8_clean($content);	
	$content_utf8_len = mb_strlen($content_utf8);
	
	if($content_utf8 != $content) {
		$result = '-98, UTF8字元或編碼錯誤';
	}elseif($content_utf8_len >= $msg_lenmax) {
		$result = "-97, 簡訊長度超過 $msg_lenmax 字";
	}
	
	// 當沒有錯誤的時後, 才進入發簡訊
	if(is_null($result) ){
		// 簡訊送出
		$uid = $userid;
		$pwd = $password;
		$sb =  urlEncode($subject);
		$msg  = urlEncode($content);
		$dest = $mobile;
		$st = $sendtime;
		//$retrytime = '';	
		
		if($method=='sendsms') {
		  $url = $url_sendsms;
		}else{
		  $method='getcredit';
  		  $url = $url_getcredit;
		}	
		
		$postdata = "UID=$uid&PWD=$pwd&SB=$sb&MSG=$msg&DEST=$dest&ST=$st&RETRYTIME=$retrytime";
		$result = curl($url, $postdata);		
	}
	
	$sms_result = array();
	$sms_result = explode(",", $result);
	// 整理成為 JSON 方式回傳. 當 ret_result['CREDIT'] 大於 0 為成功, 小於 0 為失敗
	if(isset($sms_result[0]) AND $sms_result[0] >= 0 AND $method=='sendsms'){
		$ret_result['CREDIT'] 	= $sms_result[0];
		$ret_result['SENDED'] 	= $sms_result[1];
		$ret_result['COST'] 	= $sms_result[2];
		$ret_result['UNSEND'] 	= $sms_result[3];
		$ret_result['BATCH_ID'] = $sms_result[4];
	}elseif(isset($sms_result[0]) AND $sms_result[0] >= 0 AND $method=='getcredit'){
		$ret_result['CREDIT'] 	= $sms_result[0];
		$ret_result['MESSAGE'] 	= "目前剩餘 ".$sms_result[0]." 點數";
	}else{
		$ret_result['CREDIT'] 	= $sms_result[0];
		$ret_result['MESSAGE'] 	= trim($sms_result[1]);
	}
	
	return($ret_result);
}


// 清理不合法的 UTF8 字元
function utf8_clean($string, $control = true)
{
    $string = iconv('UTF-8', 'UTF-8//IGNORE', $string);

    if ($control === true)
    {
            return preg_replace('~\p{C}+~u', '', $string);
    }

    return preg_replace(array('~\r\n?~', '~[^\P{C}\t\n]+~u'), array("\n", ''), $string);
}


// CURL送出post data , timeout 3秒
function curl($url, $postdata) 
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"$url");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"$postdata");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT,3000);
	$server_output = curl_exec($ch);
	curl_close ($ch);
	
	return($server_output);
}
// ---------------------------------





?>
