<?php
class ilLearnObjectSuggReachedPercentage {

	/**
	 * @var int
	 */
	protected $usr_id;
	/**
	 *
	 * @var int
	 */
	protected $average_percentage;


	/**
	 * @return int
	 */
	public function getUsrId() {
		return $this->usr_id;
	}


	/**
	 * @param int $usr_id
	 */
	public function setUsrId($usr_id) {
		$this->usr_id = $usr_id;
	}


	/**
	 * @return int
	 */
	public function getAveragePercentage() {
		return $this->average_percentage;
	}


	/**
	 * @param int $average_percentage
	 */
	public function setAveragePercentage($average_percentage) {
		$this->average_percentage = $average_percentage;
	}
}
?>