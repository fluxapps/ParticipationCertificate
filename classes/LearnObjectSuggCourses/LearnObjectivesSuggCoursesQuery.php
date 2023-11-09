<?php

class LearnObjectivesSuggCoursesQuery {

	const DEFAULT_TMP_TABLE_NAME = 'tmp_learn_objectives_sugg_courses';

	public function getSQL(): string
    {
		return "SELECT sugg.user_id, real_sugg_crs.target_obj_id, sugg.objective_id from crs_objectives
				/* suggested courses -> course links */
				inner join crs_objective_lm as objective_crsr on objective_crsr.objective_id = crs_objectives.objective_id and objective_crsr.type = 'crsr'
				inner join alo_suggestion as sugg on sugg.objective_id = objective_crsr.objective_id
				/* suggested courses */
				inner join container_reference as real_sugg_crs on real_sugg_crs.obj_id = objective_crsr.obj_id";
	}

	public function createTemporaryTable(string $table_name =  self::DEFAULT_TMP_TABLE_NAME): void
    {
		global $DIC;
		$ilDB = $DIC->database();


		$sql = "CREATE Temporary Table IF NOT Exists $table_name  (INDEX usc (user_id, target_obj_id, objective_id)) (".$this->getSQL().")";
		//echo $sql."; ";
		$ilDB->query($sql);
	}
}