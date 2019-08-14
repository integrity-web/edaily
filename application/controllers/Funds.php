<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Funds extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("funds_model");
		$this->load->model("task_model");

		if(!$this->user->loggedin) $this->template->error(lang("error_1"));
	}

	public function index() 
	{
		$this->template->loadData("activeLink", 
			array("funds" => array("general" => 1)));
		if(!$this->settings->info->payment_enabled) {
			$this->template->error(lang("error_60"));
		}

		if(!empty($this->settings->info->stripe_secret_key) && !empty($this->settings->info->stripe_publish_key)) {
			// Stripe
			require_once(APPPATH . 'third_party/stripe/init.php');

			$stripe = array(
			  "secret_key"      => $this->settings->info->stripe_secret_key,
			  "publishable_key" => $this->settings->info->stripe_publish_key
			);

			\Stripe\Stripe::setApiKey($stripe['secret_key']);
		} else {
			$stripe = null;
		}

		$this->template->loadContent("funds/index.php", array(
			"stripe" => $stripe
			)
		);
	}

	public function payment_log() 
	{
		$this->template->loadContent("funds/payment_log.php", array(
			)
		);
	}

	public function payment_logs_page() 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("users.joined", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 2 => array(
				 	"payment_logs.amount" => 0
				 ),
				 3 => array(
				 	"payment_logs.timestamp" => 0
				 ),
				 4 => array(
				 	"payment_logs.processor" => 0
				 )
			)
		);

		$this->datatables->set_total_rows(
			$this->user_model
				->get_total_payment_logs_count($this->user->info->ID)
		);
		$logs = $this->user_model->get_payment_logs($this->user->info->ID, $this->datatables);

		foreach($logs->result() as $r) {
			$this->datatables->data[] = array(
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp)),
				$r->email,
				number_format($r->amount, 2),
				date($this->settings->info->date_format, $r->timestamp),
				$r->processor
			);
		}
		echo json_encode($this->datatables->process());
	}

	public function plans() 
	{
		// print_r($this->user->info->ID);
		$this->template->loadData("activeLink", 
			array("funds" => array("plans" => 1)));
		if(!$this->settings->info->payment_enabled) {
			$this->template->error(lang("error_60"));
		}

		$plans = $this->funds_model->get_plans();
		$this->template->loadContent("funds/plans.php", array(
			"plans" => $plans
			)
		);
	}

	public function add_task(){
		if($_POST){
			// print_r($_POST);exit;
			$this->task_model
				->addtaskdb($this->user->info->ID,$_POST['task_name'],$_POST['hours'],$_POST['in_scope'],$_POST['out_scope'],$_POST['extra_work'],$_POST['remarks']);	
				$this->session->set_flashdata("globalmsg", lang("success_1001"));
		}
		$this->template->loadContent("funds/add_task.php", array(
			)
		);
	}

	public function view_previous_task(){
		$task_see = $this->task_model->get_task($this->user->info->ID);
		if($task_see->num_rows() == 0) $this->template->error(lang("error_2159"));
		$total_task = $task_see->result_array();
		$this->template->loadContent("funds/view_previous_task.php", array(
			"old_task" => $total_task
			)
		);
		// print_r($total_task);
		// exit;

	}

	public function edit_task($id){
		if($id == 'update_task'){
			// print_r($_POST);
			$this->update_task($_POST);
			exit;
		}
		$task_see = $this->task_model->edit_task($this->user->info->ID,$id);
		if($task_see->num_rows() == 0) $this->template->error(lang("error_2160"));
		$total_task = $task_see->result_array();
		$this->template->loadContent("funds/edit_previous_task.php", array(
			"old_task" => $total_task
			)
		);
		// print_r($total_task);
		// exit;

	}

	public function update_task(){
		if($_POST){
			// print_r($_POST);exit;
			$this->task_model
				->updatetaskdb($this->user->info->ID,$_POST['task_id'],$_POST['task_name'],$_POST['hours'],$_POST['in_scope'],$_POST['out_scope'],$_POST['extra_work'],$_POST['remarks']);	
				// echo '123';
				$this->session->set_flashdata("globalmsg", lang("success_1002"));

		}
		redirect(site_url("funds/view_previous_task"));

	}

	public function buy_plan($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		// print_r($id);
		$plan = $this->funds_model->get_plan($id);
		if($plan->num_rows() == 0) $this->template->error(lang("error_61"));
		$plan = $plan->row();

		// Check user has dolla
		if($this->user->info->points < $plan->cost) {
			$this->template->error(lang("error_62"));
		}

		if($this->user->info->premium_time == -1) {
			$this->template->error(lang("error_63"));
		}

		if($plan->days > 0) {
			$premium_time = $this->user->info->premium_time;
			$time_added = (24*3600) * $plan->days;

			// Check to see if user currently has time.
			if($premium_time > time()) {
				// If plan does not equal current one, then we reset 
				// the timer 
				if($this->user->info->premium_planid != $plan->ID) {
					$premium_time = time() + $time_added;
				} else {
					$premium_time = $premium_time + $time_added;
				}
			} else {
				$premium_time = time() + $time_added;
			}
		} else {
			// Unlimited Time modifier
			$premium_time = -1;
		}

		$this->user->info->points = $this->user->info->points - $plan->cost;

		$this->user_model->update_user($this->user->info->ID, array(
			"premium_time" => $premium_time,
			"points" => $this->user->info->points,
			"premium_planid" => $plan->ID
			)
		);

		$this->funds_model->update_plan($id, array(
			"sales" => $plan->sales + 1
			)
		);

		$this->user_model->add_log(array(
			"userid" => $this->user->info->ID,
			"IP" => $_SERVER['REMOTE_ADDR'],
			"user_agent" => $_SERVER['HTTP_USER_AGENT'],
			"timestamp" => time(),
			"message" => lang("ctn_442") . $plan->name
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_28"));
		redirect(site_url("funds/plans"));
	}

}

?>