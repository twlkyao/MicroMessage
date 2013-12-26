<?php
	class Simsimi {
		
		public function simsimi($keyword){

			$key="849eac65-9355-4457-ade1-151d69dd2646"; // Simsimi key.
			$url_simsimi="http://sandbox.api.simsimi.com/request.p?key=".$key."&lc=ch&ft=0.0&text=".$keyword; // Simsimi url.
			
			$json = file_get_contents($url_simsimi); // Get the content from url.
			$result=json_decode($json, true); // Encode the content to PHP array.
			$response=$result['response']; // Get the response.
			if(!empty($response)){ // Response is not null.
				return $response;
			} else { // Response is null, return a random string.
				$ran=rand(1,5);
				switch($ran){
					case 1:
						return "小鸡鸡今天累了，明天再陪你聊天吧。";
						break;
					case 2:
						return "小鸡鸡睡觉喽~~";
						break;
					case 3:
						return "呼呼~~呼呼~~";
						break;
					case 4:
						return "你话好多啊，不跟你聊了。";
						break;
					case 5:
						return "小黄鸡要去洗澡了，不陪你聊了。";
						break;
					default:
						return "AlienTech for Better Life.欢迎来到AlienTech的地盘，这里有最新的科技资讯。";
						break;
				}
			}
		}
	}
?>

