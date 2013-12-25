<?php
	class Translation {
	
		/**
		 * Translate word between Chinese and English.
		 */
		public function youdaoDic($word){  
  
        $keyfrom = "AlienTech"; // key from.
        $apikey = "581127715"; // api key.
          
        // Youdao JSON format.
        $url_youdao = 'http://fanyi.youdao.com/fanyiapi.do?keyfrom='.$keyfrom.'&key='.$apikey.'&type=data&doctype=json&version=1.1&q='.$word;           
        $jsonStyle = file_get_contents($url_youdao); // Get response from specified url.  
        $result = json_decode($jsonStyle,true); // Convert JSON into PHP variable. 
        $errorCode = $result['errorCode']; // Error code.
        $trans = '';  // Translate result.
        if(isset($errorCode)){ // Error code.
            switch ($errorCode){
                case 0:
                    $trans = $result['translation']['0'];  
                    break;  
                case 20:  
                    $trans = '要翻译的文本过长';  
                    break;  
                case 30:  
                    $trans = '无法进行有效的翻译';  
                    break;  
                case 40:  
                    $trans = '不支持的语言类型';  
                    break;  
                case 50:  
                    $trans = '无效的key';  
                    break;  
                default:  
                    $trans = '出现异常';  
                    break;  
            }  
        }  
        return $trans; // Return translate result.
		}  
	}
?>
