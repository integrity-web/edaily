<?php

class Task_Model extends CI_Model 
{

	public function addtaskdb($user_id, $task, $hours_taken, $in_scope,$out_scope,$extra_work,$remarks) {
		$this->db->insert("task", 
			array(
				"user_id" => $user_id,
				"task_name" => $task,
				"hours_taken" => $hours_taken, 
				"in_scope" => $in_scope,
				"out_scope" => $out_scope,
				"extra_work" => $extra_work, 
				"date_of_task" => date("Y-m-d H:i:s"),
				"remarks" => $remarks
			)
		);
		return $this->db->insert_id();
	}

		public function get_task($id) 
	{
			$this->db->select('*');
		   $this->db->from('task');
		   $this->db->where(array('user_id'=>$id));
		   return $this->db->get();
	}

		public function edit_task($uid,$id){
				$this->db->select('*');
			   $this->db->from('task');
			   $this->db->where(array('user_id'=>$uid,'task_id'=>$id));
			   return $this->db->get();

		}
	public function updatetaskdb($user_id, $task_id,$task, $hours_taken, $in_scope,$out_scope,$extra_work,$remarks) {
			 $this->db->where("user_id", $user_id)
				->where("task_id", $task_id)
				->update("task", array("task_name" => $task,"hours_taken" => $hours_taken,"in_scope" => $in_scope,"out_scope" => $out_scope,"extra_work" => $extra_work,"remarks" => $remarks));
		}

}

?>
