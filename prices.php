<?php
    define("LTCADDRESS", "https://www.okcoin.com/api/ticker.do?symbol=ltc_cny"); // Define LTCADDRESS.
    define("BTCADDRESS", "https://www.okcoin.com/api/ticker.do"); // Define BTCADDRESS.

	/**
	 * $price = new prices();
	 * $data = $price->price("ltc");
	 * echo $data->ticker->high;
	 */
	
	class prices {
		
		/**
		 * Get the price according to different kind of currency.
		 */
		public function price($kind){
			switch($kind) {
				case "ltc":
					$json = file_get_contents(LTCADDRESS); 
					break;
				case "btc":
					$json = file_get_contents(BTCADDRESS);
					break;
				default:
					$json = null;
					break;
			} 
			return json_decode($json); // Convert the JSON string to PHP variable.
		}
	}
?>
