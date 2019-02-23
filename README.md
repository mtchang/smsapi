## 改寫 EVERY8D 簡訊發送的SAMPLE CODE

* 到 EVERY8D 申請簡訊系統測試帳號 https://tw.every8d.com/
*抓取程式碼
```
git clone https://github.com/mtchang/smsapi.git
```

* 修改程式碼的帳密及發送簡訊的目標手機號碼
```php
$userid="帳號";	
$password="密碼";	
//接收人之手機號碼。格式為: +886912345678或09123456789。多筆 0912345678,0922333444
$mobile = "09xxxxxxxx";	
```

* 使用 PHP 執行程式
```bash
$ php ./call_8dsms.php 
﻿array(2) {
  ["CREDIT"]=>
  string(2) "79"
  ["MESSAGE"]=>
  string(22) "目前剩餘 79 點數"
}
array(5) {
  ["CREDIT"]=>
  string(5) "78.00"
  ["SENDED"]=>
  string(1) "1"
  ["COST"]=>
  string(1) "1"
  ["UNSEND"]=>
  string(1) "0"
  ["BATCH_ID"]=>
  string(36) "89cbd1e4-4666-4be0-9d00-3169313a60a0"
}
```

* 其餘不足的地方，請看官方網站文件說明。http://tw.every8d.com/api20/doc/EVERY8D%20HTTP%20API%E6%96%87%E4%BB%B6-v2%201-https.pdf


