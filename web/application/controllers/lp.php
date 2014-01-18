<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LP extends CI_Controller {
	private $s;
	private $p;
	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->s=$this->input->get_post('s');
		$this->p=$this->input->get_post('p');
	}
	public function rt(){

	}
	public function ch(){
		$ptid=$this->input->get_post('subid');
		$data=array('ptid'=>$ptid,'s'=>$this->s,'p'=>$this->p);

		$this->load->view('lp/'.$this->s.'/index.php',$data);
	}
	public function track(){
		$ptid=$this->input->get_post('ptid');
		$msisdn=$this->input->get_post('msisdn');
		$this->load->view('lp/'.$this->s.'/thankyou.php');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */