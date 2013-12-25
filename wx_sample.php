<?php
	 /**
	  * wechat php test
	  */

	//define your token
	header("Content-type: textml; charset=gb2312"); 

	define("TOKEN", "AlienTech"); // Define TOKEN.
	define("HELP", "欢迎关注【AlienTech】"."\n"."使用帮助:【1】查Ripple价格，如输入：xrp"
		."\n【2】查LTC价格，如输入：ltc"); // Define HELP.
	define("ABOUT", "AlienTech for Better Life.欢迎来到AlienTech的地盘，这里有最新的科技资讯。"); // Define ABOUT.
	define("WELCOME", "AlienTech for Better Life.欢迎来到AlienTech的地盘，这里有最新的科技资讯。");
	
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
			if(!empty( $keyword )) {
				$msgType = "text";
				if(strtolower($keyword) == "xrp") {
					$contentStr = "Ripple";
				} else if(strtolower($keyword) == "ltc") {
					$contentStr = "LTC";
				}
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			} else {
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
