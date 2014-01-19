<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LP extends CI_Controller {
	private $s;
	private $p;
	private $pp;

	private $th_sub;
	private $TRACK_KEY_LEN=30;
	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->helper('date');
		$this->load->helper('string');
		$this->s=$this->input->get_post('s');
		$this->p=$this->input->get_post('p');
		$this->pp=$this->input->get_post('pp');
		$this->th_sub = $this->load->database(DB_TH_SUB, TRUE);




	}
	public function rt(){
		$partner_params='uc='.$this->input->get_post('uc');
		if($this->_check_parnert_params($partner_params)){
			$this->_common_partner_landingpage($partner_params);
		}
	}
	public function ch(){
		$partner_params='txId='.$this->input->get_post('txId');
		if($this->_check_parnert_params($partner_params)){
			$this->_common_partner_landingpage($partner_params);
		}
	}

	public function track(){
		$track_id=$this->input->get_post('track_id');
		$msisdn=$this->input->get_post('msisdn');
		$query_track_entry_pending=$this->th_sub->get_where('track_entry_pending',array('track_id'=>$track_id));
		$track_entry_pending=null;
		if($query_track_entry_pending!=null&&$query_track_entry_pending->num_rows() > 0){
			$track_entry_pending = $query_track_entry_pending->row();
		}
		if($track_entry_pending!=null){
			$this->th_sub->update('track_entry_pending',array('msisdn'=>$msisdn),array('track_id'=>$track_id));
			$this->load->view('lp/'.$track_entry_pending->service_id.'/thankyou.php');
		}else{
			#show error page not found trackid
		}

	}

	private function _common_partner_landingpage($partner_params){

		$service=null;
		$shortcode=null;
		$keyword=null;
		$service_id=$this->s;
		if($service_id!=null){
			$query_service=$this->th_sub->get_where('service',array('service_id'=>$service_id));
			if($query_service!=null&&$query_service->num_rows() > 0){
				$service = $query_service->row();
			}else{
				$shortcode=$this->input->get_post('shortcode');
				$keyword=$this->input->get_post('keyword');
				$query_service=$this->th_sub->get_where('service',array('shortcode'=>$shortcode,'keyword'=>$keyword));
				if($query_service!=null&&$query_service->num_rows() > 0){
					$service = $query_service->row();
				}
			}
		}
		if($service!=null){
			$service_id=$service->service_id;
			$shortcode=$service->shortcode;
			$keyword=$service->keyword;

			$track_id_prefix=mdate(DB_FORMAT_TIMESTAMP,time());
			$track_id=$track_id_prefix.random_string('alnum', $this->TRACK_KEY_LEN-strlen($track_id_prefix));
			$data_track_entry_pending =array(
					'track_id'=>$track_id,
					'parent_id'=>$this->pp,
					'pid'=>$this->p,
					'service_id'=>$service_id,
					'partner_params'=>$partner_params,
					'sys_time'=>mdate(DB_FORMAT_DATETIME, time())
			);
			$this->th_sub->insert('track_entry_pending',$data_track_entry_pending);

			$data=array('track_id'=>$track_id);
			$this->load->view('lp/'.$service_id.'/index.php',$data);

		}else{
			#show error not found service
		}
	}
	private function _check_parnert_params($partner_params){
		$query_track_entry_pending=$this->th_sub->get_where('track_entry_pending',array('partner_params'=>$partner_params));
		$track_entry_pending=null;
		if($query_track_entry_pending!=null&&$query_track_entry_pending->num_rows() > 0){
			$track_entry_pending = $query_track_entry_pending->row();
		}
		if($track_entry_pending!=null){
			$this->load->view('lp/'.$track_entry_pending->service_id.'/thankyou.php');
			return false;
		}else{
			return true;
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */