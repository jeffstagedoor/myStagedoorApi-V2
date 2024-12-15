<?php
/********
* Customizing LOG Entries
*/
// require_once("Constants.php");
namespace myStagedoor\Log;
use myStagedoor\Config\Constants;

Class Config extends \myStagedoor\Log\Default\Config {

	public static function values() {
		$values = new \stdClass();
		$values->workgroup = new \stdClass();
		$values->workgroup->for = new \myStagedoor\Log\Default\TargetUsers(
				NULL, Constants::USER_ADMIN, 		//A
				NULL, Constants::WORKGROUPS_ADMIN,	// B = workgroup
				NULL, NULL,		// C = production
				NULL, NULL 		// D = audition
			);
		$values->workgroup->meta = new \myStagedoor\Log\Default\Meta(
				Array("workgroup", "id"),	// int
				null, // int
				null, // int
				Array("workgroup", "label"), // string 80
				Array("workgroup", "description")	// string 255
			);

			
		$values->artistgroups2event = new \stdClass();
		$values->artistgroups2event->for = new \myStagedoor\Log\Default\TargetUsers(
				NULL, Constants::USER_ADMIN, 		//A
				NULL, NULL,	// B = workgroup
				NULL, NULL,		// C = production
				NULL, NULL 		// D = audition
			);
		$values->artistgroups2event->meta = new \myStagedoor\Log\Default\Meta(
				Array("artistgroups2event", "isNeeded"),	// int
				Array("artistgroups2event", "event"), // int
				Array("artistgroups2event", "artistgroup"), // int
				Array("artistgroups2event", "id"), // string 80
				NULL	// string 255
			);

		$values->artists2event = new \stdClass();
		$values->artists2event->for = new \myStagedoor\Log\Default\TargetUsers(
				NULL, Constants::USER_ADMIN, 		//A
				NULL, NULL,	// B = workgroup
				NULL, NULL,		// C = production
				NULL, NULL 		// D = audition
			);
		$values->artists2event->meta = new \myStagedoor\Log\Default\Meta(
				Array("artists2event", "role"),	// int
				Array("artists2event", "artist"), // int
				Array("artists2event", "event"), // int
				Array("artists2event", "id"), // string 80
				NULL	// string 255
			);

		return $values;
	}
}