<?php
require_once 'class.ilExcerciseState.php';
class ilExcerciseStates {

	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilExcerciseState[]
	 */
	public static function getData(array $arr_usr_ids = array()) {
		global $ilDB;


		$result = $ilDB->query(self::getSQL($arr_usr_ids));
		$exerc_data = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$exerc_state = new ilExcerciseState();
			$exerc_state->setUsrId($row['exc_usr_id']);
			$exerc_state->setExcObjId($row['exc_obj_id']);
			$exerc_state->setExcObjTitle($row['exc_obj_title']);
			$exerc_state->setExcRefId($row['exc_ref_id']);
			$exerc_state->setPassed($row['exc_passed']);
			$exerc_state->setTotal($row['exc_total']);
			$exerc_state->setPassedPercentage($row['exc_passed_percentage']);

			$exerc_data[$row['exc_usr_id']] = $exerc_state;
		}

		return $exerc_data;
	}


	/**
	 * @param array $arr_usr_ids
	 *
	 * @return string
	 */
	protected static function getSQL(array $arr_usr_ids = array()) {
		global $ilDB;

		$select = "SELECT 
					exerc.usr_id as exc_usr_id,
					exc_obj.title as exc_obj_title,
					exc_ass.exc_id as exc_obj_id,
					exc_ref.ref_id as  exc_ref_id,
					COUNT(CASE WHEN exerc.status = 'passed' THEN exerc.status END) as exc_passed,
					COUNT(exerc.status) as exc_total,
					COALESCE(round(( COUNT(CASE WHEN exerc.status = 'passed' THEN exerc.status END)/COUNT(exerc.status) * 100 ),0),0) as exc_passed_percentage
					FROM 
					exc_mem_ass_status as exerc
					inner join exc_assignment as exc_ass on exc_ass.id = exerc.ass_id
					inner join object_data as exc_obj on exc_obj.obj_id = exc_ass.exc_id
					inner join object_reference as exc_ref on exc_ref.obj_id = exc_ass.exc_id
					where  ".$ilDB->in('exerc.usr_id', $arr_usr_ids, false, 'integer')."
					group by 
					exerc.usr_id,
					exc_ass.exc_id,
					exc_ref.ref_id,
					exc_obj.title";

		return $select;
	}
}
?>