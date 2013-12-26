<?php
	 /**
	  * wechat php test
	  */

	//define your token
	header("Content-type: textml; charset=gb2312"); 

	define("TOKEN", "AlienTech"); // Define TOKEN.
	define("HELP", "欢迎关注【AlienTech】"."\n"."使用帮助:\n【1】查Ripple价格，如输入：xrp"
		."\n【2】查LTC价格，如输入：ltc"
		."\n【3】查BTC价格，如输入：btc"
		."\n【4】翻译，如输入：翻译I love you."
		."\n【5】聊天机器人，如输入：小黄鸡我喜欢你。"
		."\n【6】帮助，如输入：help"
		."\n【7】关于，如输入：about"); // Define HELP.
	define("WELCOME", "AlienTech for Better Life.欢迎来到AlienTech的地盘，这里有最新的科技资讯。");
	define("SORRY", "不好意思，我还在学习中，请不要生气！");
	define("ABOUT", "作者：\n齐士垚，\n西安电子科技大学，\nqishiyao2008@126.com\n回复您想要的功能，没准就会上线哦！");
	include_once("prices.php"); // Include the prices.php once.
	
	$wechatObj = new wechatCallbackapiTest();

	//$wechatObj->valid(); // Validate the signature.

	$wechatObj->responseMsg();	// Call the responseMsg() function to response message.

	/**
	 * Wechat Callback API class
	 */ 
	class wechatCallbackapiTest {
		/*
		 * Validate the signature.
		 */
		/**
		public function valid()
		{
			$echoStr = $_GET["echostr"];

			//valid signature , option
			if($this->checkSignature()){
				echo $echoStr;
				exit;
			}
		}
		*/
		
		/**
		 * Response according to different MsgType.
		 */
		public function responseMsg() {
			//get post data, May be due to the different environments
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

			//extract post data
			if (!empty($postStr)){ // Post data is not null.
				
				$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				$RX_TYPE = trim($postObj->MsgType); // Get the message type.
				switch($RX_TYPE) { // Construct response message according to different message type.
					case "text": // text.
						$resultStr = $this->handleText($postObj);
						break;
					case "event": // event.
						$resultStr = $this->handleEvent($postObj);
						break;
					default:
						$resultStr = "Unknown msg type!".$RX_TYPE;
						break;
				}
				echo $resultStr; // Echo the response message.
			} else { // Post data is null.
				echo "";
				exit;
			}
		}
		
		/**
		 * Handle the text message.
		 */
		public function handleText($object) {
			$fromUsername = $object->FromUserName; // The message source ID.将对象$postObj中的消息发送者赋值给$toUsername变量
			$toUsername = $object->ToUserName; // The public account ID.将对象$postObj中的公众账号的ID赋值给$toUsername变量
			$keyword = trim($object->Content); // Get the event.
			$time = time(); // Get current time.
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";    // The message format.         
			if(!empty( $keyword )) { // The keyword is not null.
				$msgType = "text";
				
				$str_trans = mb_substr($keyword, 0, 2, "UTF-8"); // Translate indicator.
				$str_valid = mb_substr($keyword, 0, -2, "UTF-8"); // Translate content.
				$str_word = mb_substr($keyword, 2, 220, "UTF-8"); // Translate keyword.
				
				$str_simis = mb_substr($keyword, 0, 3, "UTF-8"); // Simsimi indicator.
				$str_simis_valid = mb_substr($keyword, 0, -3, "UTF-8"); // Simsimi content.
				$str_simis_word = mb_substr($keyword, 3, 30, "UTF-8"); // Simsimi keyword.
				
				if(strtolower(trim($keyword)) == "xrp") { // Trim the space and convert to lower case.
					$contentStr = "Ripple暂不提供，敬请期待。";
				} else if(strtolower(trim($keyword)) == "ltc") { // Trim the space and convert to lower case.
					//$contentStr = "LTC";
					$price = new prices();
					$data = $price->price("ltc");
					$contentStr = "最高价".$data->ticker->high."\n"
						."买一价：".$data->ticker->buy."\n"
						."卖一价：".$data->ticker->buy."\n"
						."最近一次成交价：".$data->ticker->last."\n"
						."成交量：".$data->ticker->vol;
				} else if(strtolower(trim($keyword)) == "btc") {
					//$contentStr = "BTC"
					$price = new prices();
					$data = $price->price("btc");
					
					$contentStr = "最高价".$data->ticker->high."\n"
						."买一价：".$data->ticker->buy."\n"
						."卖一价：".$data->ticker->buy."\n"
						."最近一次成交价：".$data->ticker->last."\n"
						."成交量：".$data->ticker->vol;
				} else if($str_trans == "翻译" && $str_valid != null) {
					include_once("translate.php");
					$translate = new Translation();
					$data = $translate->youdaoDic($str_word);
					$contentStr = $data;
				} else if($str_simis == "小黄鸡" && $str_simis_valid != null) {
					include_once("simsimi.php");
					$sim = new Simsimi();
					$data = $sim->simsimi($str_simis_word);
					$contentStr = $data;
				}else if(strtolower(trim($keyword)) == "help") { // Trim the space and convert to lower case.
					$contentStr = HELP;
				} else if(strtolower(trim($keyword)) == "about") {
					$contentStr = ABOUT;
				}else {
					$contentStr = SORRY;
				}
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr); // Format the response string.
				echo $resultStr; // Echo the result.
			} else { // The keyword is null.
				echo "Input something...";
			}
		}
		
		/**
		 * Handle the Event.
		 */
		public function handleEvent($object) {
			$contentStr = "";
			switch($object->Event) {
				case "subscribe":
				$contentStr = HELP;
				break;
				default:
				$contentStr = "Unknown Event:".$object->Event;
				break;
			}
			$resultStr = $this->responseText($object, $contentStr);
			return $resultStr;
		}	
			
		/**
		 * Response Text message.
		 */ 
		public function responseText($object, $content, $flag = 0) {
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>%d</FuncFlag>
						</xml>";
			$resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
			return $resultStr;
		}
		
		/**
		 * Check the signature of the WeChat server.
		 */ 
		private function checkSignature()
		{
			$signature = $_GET["signature"];
			$timestamp = $_GET["timestamp"];
			$nonce = $_GET["nonce"];	
					
			$token = TOKEN;
			$tmpArr = array($token, $timestamp, $nonce);
			sort($tmpArr);
			$tmpStr = implode( $tmpArr );
			$tmpStr = sha1( $tmpStr );
			
			if( $tmpStr == $signature ){
				return true;
			}else{
				return false;
			}
		}
	}
?>
