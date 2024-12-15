<?php
/**
 * Log\Default\TargetUsers Class.
 * 
 * To be instatiated in LogConfig.php of consuming app in static function values(): 
 * 
 * ```
 * 	public static function values() {
 * 		$values = new \stdClass();
 *      // ... setLogDefaultMeta
 *		$values->posts = new \stdClass();
 *		$values->posts->for = new Log\Default\TargetUsers(
 *				NULL, Constants::USER_ADMIN, 		//A
 *				Array("post","id"), Constants::WORKGROUPS_ADMIN,	// B = workgroup
 *				NULL, NULL,		// C = production
 *				NULL, NULL 		// D = audition
 *			);
 *      return $values;
 *  }
 * ```
 */
namespace myStagedoor\Log\Default;

Class TargetUsers {
	public int|null $account;
	public $accountRights;
	public int|null $workgroup;
	public int|null $workgroupRights;
	public int|null $production;
	public int|null $productionRights;
	public int|null $audition;
	public int|null $auditionRights;

	function __construct(int|null $account, int|null $accountRights, int|null $workgroup, int|null $workgroupRights, int|null $production, int|null $productionRights, int|null $audition, int|null $auditionRights) {
		$this->account = $account;
		$this->accountRights = $accountRights;
		$this->workgroup = $workgroup;
		$this->workgroupRights = $workgroupRights;
		$this->production = $production;
		$this->productionRights = $productionRights;
		$this->audition = $audition;
		$this->auditionRights = $auditionRights;
	}
}