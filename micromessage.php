<?php
	 /**
	  * Wechat php implementation.
	  */
	  
	//define your token
	header("Content-type: textml; charset=gb2312"); 

	define("TOKEN", "AlienTech"); // Define TOKEN.
	define("HELP", "欢迎关注【AlienTech】"."\n"."使用帮助:"
		."\n【1】查询比特币价格，输入：btc"
		."\n【2】查询莱特币价格，输入：ltc"
		."\n【3】查询狗狗币价格，输入：dog"
		."\n【4】查询质数币价格，输入：xpm"
		."\n【5】查询比奥币价格，输入：bec"
		."\n【6】查询瑞波币价格，输入：xrp"
		."\n【7】查询招财币价格，输入：zcc"
		."\n【8】查询美卡币价格，输入：mec"
		."\n【9】查询阿侬币价格，输入：anc"
		."\n【10】查询点点币价格，输入：ppc"
		."\n【11】查询安全币价格，输入：src"
		."\n【12】查询悬赏币价格，输入：tag"
		."\n【13】查询比特股价格，输入：pts"
		."\n【14】查询世界币价格，输入：wdc"
		."\n【15】查询苹果币价格，输入：apc"
		."\n【16】查询数码币价格，输入：dgc"
		."\n【17】查询联合币价格，输入：unc"
		."\n【18】查询夸克币价格，输入：qrk"
		."\n【19】查询时代币价格，输入：tmc"
		."\n【20】翻译，如输入：翻译I love you."
		."\n【21】聊天机器人，如输入：小黄鸡我喜欢你。"
		."\n【22】帮助，如输入：help"
		."\n【23】关于，如输入：about"); // Define HELP.
	define("WELCOME", "AlienTech for Better Life.欢迎来到AlienTech的地盘，这里有最新的货币行情。");
	define("SORRY", "不好意思，我还在学习中，请不要生气！");
	define("ABOUT", "作者：\n齐士垚，\n西安电子科技大学，\n邮件：qishiyao2008@126.com\n回复您想要的功能，没准就会上线哦！");
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
				
				// Define the currencies array.
				$currencies = array("btc", "ltc", "dog", "xpm", "bec",
						"xrp", "zcc", "mec", "anc", "ppc",
						"src", "tag", "pts", "wdc", "apc",
						"dgc", "unc", "qrk", "tmc");
				$currencies_name = array(
								"btc" => "比特币", "ltc" => "莱特币", "dog" => "狗狗币",
								"xpm" => "质数币", "bec" => "比奥币", "xrp" => "瑞波币",
								"zcc" => "招财币", "mec" => "美卡币", "anc" => "阿侬币",
								"ppc" => "点点币", "src" => "安全币", "tag" => "悬赏币",
								"pts" => "比特股", "wdc" => "世界币", "apc" => "苹果币",
						        "dgc" => "数码币", "unc" => "联合币", "qrk" => "夸克币",
						        "tmc" => "时代币");
				
				if(in_array(strtolower($keyword), $currencies)) {
					$price = new prices();
					$data = $price->price($keyword);
					$data = json_decode($data);
					$contentStr = "最高价：".$data->ticker->high . "元"
						."\n最低价：".$data->ticker->low . "元"
						."\n最近一次成交价：".$data->ticker->last . "元"
						."\n成交量：".$data->ticker->vol
						."\n买一价：".$data->ticker->buy . "元"
						."\n卖一价：".$data->ticker->sell . "元";
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
