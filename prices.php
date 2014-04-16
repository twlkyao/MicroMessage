<?php
	/**
	define("BTCADDRESS", "http://api.btc38.com/v1/ticker.php?c=btc"); // Define BTCADDRESS.	
    define("LTCADDRESS", "http://api.btc38.com/v1/ticker.php?c=ltc"); // Define LTCADDRESS.
	define("DOGADDRESS", "http://api.btc38.com/v1/ticker.php?c=dog"); // Define DOGADDRESS.
	define("XPMADDRESS", "http://api.btc38.com/v1/ticker.php?c=xpm"); // Define XPMADDRESS.
	define("PECADDRESS", "http://api.btc38.com/v1/ticker.php?c=bec"); // Define BECADDRESS.
	define("XRPADDRESS", "http://api.btc38.com/v1/ticker.php?c=xrp"); // Define XRPADDRESS.
	*/
	
	class prices {
		
		/**
		 * Get the price according to different kind of currency.
		 */
		public function price($kind){
			$kind = strtolower($kind); // Convert the string to lower case.
			$base_address = "http://api.btc38.com/v1/ticker.php?c="; // Define the base address.
			$address = $base_address . $kind;
			$json = file_get_contents($address);
			return $json;
		}
	}
?>
