<?php
/**
 * Log Default Meta Class.
 * 
 * To be instatiated in LogConfig.php of consuming app in static function values(): 
 * 
 * ```
 * 	public static function values() {
 * 		$values = new \stdClass();
 *      // ... setLogDefaultFor
 *		$values->posts = new \stdClass();
 * 		$values->posts->meta = new LogDefaultMeta(
 *				Array("posts", "id"),	// int
 *				null, // int
 *				null, // int
 *				Array("posts", "N_TITEL"), // string 80
 *				Array("posts", "N_TXT")	// string 255
 *			);
 *      return $values;
 *  }
 * ```
 */
namespace myStagedoor\Log\Default;

Class Meta {
	public int|string|array|null $Meta1;
	public int|string|array|null $Meta2;
	public int|string|array|null $Meta3;
	public int|string|array|null $Meta4;
	public int|string|array|null $Meta5;

	function __construct(int|string|array|null $A=null, int|string|array|null $B=null, int|string|array|null $C=null, int|string|array|null $D=null, int|string|array|null $E=null) {
		$this->Meta1 = $A;
		$this->Meta2 = $B;
		$this->Meta3 = $C;
		$this->Meta4 = $D;
		$this->Meta5 = $E;
	}
}