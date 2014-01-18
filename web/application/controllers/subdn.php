<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class subdn extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	private $msisdn;
	private $telco_id;
	private $sh;
	private $kw;
	private $SUB_KEY_LEN=20;
	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->helper('date');
		$this->load->helper('string');
	}
	public function f2u(){
		$ip_allow=array('203.146.102.205','203.146.102.206','202.176.88.73');
		$ip = $_SERVER['REMOTE_ADDR'];
		if(in_array($ip, $ip_allow)){
			/*
			 $xmldn_str= '<?xml version="1.0" encoding="utf-8" ?>';
			$xmldn_str .= '<XML>';
			$xmldn_str .= '<COMMAND>REGISTER</COMMAND>';
			$xmldn_str .= '<USERNAME>username</USERNAME>';
			$xmldn_str .= '<PASSWORD>password</PASSWORD>';
			$xmldn_str .= '<MESSAGEID>'.random_string('alnum', 30).'</MESSAGEID>';
			$xmldn_str .= '<PARTNERREFID></PARTNERREFID>';
			$xmldn_str .= '<TELCOID>1</TELCOID>';
			$xmldn_str .= '<SERVICENUMBER>4761986</SERVICENUMBER>';
			$xmldn_str .= '<SERVICEPACKAGEID>289</SERVICEPACKAGEID>';
			$xmldn_str .= '<CHANNEL>1</CHANNEL>';
			$xmldn_str .= '<MSISDN>6691275929</MSISDN>';
			$xmldn_str .= '<MESSAGE>&#x0054;&#x0065;&#x0073;&#x0074;</MESSAGE>';
			$xmldn_str .= '<TIMESTAMP>2009-12-05 22:00:12</TIMESTAMP>';
			$xmldn_str .= '<STATUSCODE>1003</STATUSCODE>';
			$xmldn_str .= '<STATUSTEXT>Register success</STATUSTEXT>';
			$xmldn_str .= '</XML>';
			*/
			#Parse XML


			$xmldn_str =file_get_contents('php://input');

			log_message('info','IP:'.$ip);
			log_message('info','xmldn_str:'.$xmldn_str);

			$xml_res = simplexml_load_string($xmldn_str);
			$json_response = json_encode($xml_res);
			$data_response = json_decode($json_response,TRUE);

			#Verify parameter
			if($data_response['COMMAND']==null||
			$data_response['MESSAGEID']==null||
			$data_response['TELCOID']==null||
			$data_response['SERVICENUMBER']==null||
			$data_response['SERVICEPACKAGEID']==null||
			$data_response['CHANNEL']==null||
			$data_response['MSISDN']==null||
			$data_response['STATUSCODE']==null||
			$data_response['STATUSTEXT']==null
			){

				log_message('info','COMMAND:'.$data_response['COMMAND']);
				log_message('info','MESSAGEID:'.$data_response['MESSAGEID']);
				log_message('info','TELCOID:'.$data_response['TELCOID']);
				log_message('info','SERVICENUMBER:'.$data_response['SERVICENUMBER']);
				log_message('info','SERVICEPACKAGEID:'.$data_response['SERVICEPACKAGEID']);
				log_message('info','CHANNEL:'.$data_response['CHANNEL']);
				log_message('info','MSISDN:'.$data_response['MSISDN']);
				log_message('info','STATUSCODE:'.$data_response['STATUSCODE']);
				log_message('info','STATUSTEXT:'.$data_response['STATUSTEXT']);

				$xml_return = '<?xml version="1.0" encoding="utf-8" ?>';
				$xml_return .= '<XML>';
				$xml_return .= '<STATUSCODE>9999</STATUSCODE>';
				$xml_return .= '<STATUSTEXT>INVALID PARAMETER</STATUSTEXT>';
				$xml_return .= '<MESSAGEID></MESSAGEID>';
				$xml_return .= '<PARTNERREFID></PARTNERREFID>';
				$xml_return .= '</XML>';
				// 				$this->output->set_content_type('text/xml');
				// 				$this->output->set_output($xml_return);
				$this->_outputxml($xml_return);
			}else{
				#connect DB
				$th_sub = $this->load->database(DB_TH_SUB, TRUE);
					
				$partner_gateway=null;
				$query_partner_gateway = $th_sub->get_where('partner_gateway', array('partner_gw_id' => PARTNER_GATEWAY_F2U));
				if($query_partner_gateway!=null&&$query_partner_gateway->num_rows() > 0){
					$partner_gateway=$query_partner_gateway->row();
				}
				if($partner_gateway!=null){
					#find service
					$service=$this->_getservice(PARTNER_GATEWAY_F2U, $data_response['SERVICEPACKAGEID'], $th_sub);
					$shortcode=null;
					$keyword=null;
					$service_id=null;
					if($service!=null){
						$service_id= $service->service_id;
						$shortcode=$service->shortcode;
						$keyword=$service->keyword;
					}

					#find telco
					$partner_gw_telco_map=$this->_getpartnertelco(PARTNER_GATEWAY_F2U, $data_response['TELCOID'], $th_sub);
					$telco_id=null;
					if($partner_gw_telco_map!=null){
						$telco_id=$partner_gw_telco_map->telco_id;
					}

					$sub_dn_id_prefix=mdate(DB_FORMAT_TIMESTAMP,time());
					$sub_dn_id=$sub_dn_id_prefix.random_string('alnum', $this->SUB_KEY_LEN-strlen($sub_dn_id_prefix));
					$data_subscriber_dn=array('sub_dn_id'=>$sub_dn_id,
							'partner_gw_id'=>PARTNER_GATEWAY_F2U,
							'partner_gw_txid'=>$data_response['MESSAGEID'],
							'cmd'=>$data_response['COMMAND'],
							'shortcode'=>$shortcode,
							'keyword'=>$keyword,
							'msisdn'=>$data_response['MSISDN'],
							'telco_id'=>$telco_id,
							'response_data'=>$xmldn_str,
							'sys_time'=>mdate(DB_FORMAT_DATETIME, time()),
							'response_time'=>$data_response['TIMESTAMP']
					);
					$th_sub->insert('subscriber_dn',$data_subscriber_dn);

					#find account
					$query_account=$th_sub->get_where('account',array('msisdn'=>$data_response['MSISDN'],'service_id'=>$service_id));
					$account=null;
					if($query_account!=null&&$query_account->num_rows() > 0){
						$account=$query_account->row();
					}

					if($data_response['COMMAND']=='REGISTER'){
						#insert to account
						if($account==null){
							$data_account =array('msisdn'=>$data_response['MSISDN'],
									'service_id'=>$service_id,
									'telco_id'=>$telco_id,
									'register_time'=>mdate(DB_FORMAT_DATETIME, time())
							);
							$th_sub->insert('account',$data_account);

							#trig to advertise partner
						}else{
						}
					}else if($data_response['COMMAND']=='UNREGISTER'){
						#delete account and insert ot quit log
						if($account!=null){
							#insert quit_log
							$data_quit_log =array('msisdn'=>$account->msisdn,
							'service_id'=>$account->service_id,
							'telco_id'=>$account->telco_id,
							'register_time'=>$account->register_time,
							'quit_time'=>mdate(DB_FORMAT_DATETIME, time())
							);


							$th_sub->insert('quit_log',$data_quit_log);
							$th_sub->delete('account', array('msisdn'=>$account->msisdn,
									'service_id'=>$account->service_id));
						}
					}

					$xml_return = '<?xml version="1.0" encoding="utf-8" ?>';
					$xml_return .= '<XML>';
					$xml_return .= '<STATUSCODE>1000</STATUSCODE>';
					$xml_return .= '<STATUSTEXT>Transaction success</STATUSTEXT>';
					$xml_return .= '<MESSAGEID>'.$data_subscriber_dn['partner_gw_txid'].'</MESSAGEID>';
					$xml_return .= '<PARTNERREFID>'.$data_subscriber_dn['sub_dn_id'].'</PARTNERREFID>';
					$xml_return .= '</XML>';
					// 					$this->output->set_content_type('text/xml');
					// 					$this->output->set_output($xml_return);
					$this->_outputxml($xml_return);


				}else{
					//partner gateway not config
				}
			}
		}else{
			$xml_return = '<?xml version="1.0" encoding="utf-8" ?>';
			$xml_return .= '<XML>';
			$xml_return .= '<STATUSCODE>9999</STATUSCODE>';
			$xml_return .= '<STATUSTEXT>IP Not allow:'.$ip.'</STATUSTEXT>';
			$xml_return .= '<MESSAGEID></MESSAGEID>';
			$xml_return .= '<PARTNERREFID></PARTNERREFID>';
			$xml_return .= '</XML>';
			// 			$this->output->set_content_type('text/xml');
			// 			$this->output->set_output($xml_return);
			$this->_outputxml($xml_return);
		}
	}

	private function _getservice($partner_gw_id,$partner_service_id,$db){
		#find service
		$sql_service="select * from service where service_id in(select service_id from partner_gw_service_map where partner_gw_id=".$partner_gw_id." and partner_service_id='".$partner_service_id."')";
		$service=null;
		$query_service=$db->query($sql_service);
		if($query_service!=null&&$query_service->num_rows() > 0){
			$service=$query_service->row();
		}
		return $service;
	}
	private function _getpartnertelco($partner_gw_id,$partner_telco_id,$db){
		#find service
		$sql_partner_gw_telco_map="select * from partner_gw_telco_map where partner_gw_id=".$partner_gw_id." and partner_telco_id='".$partner_telco_id."'";
		$partner_gw_telco_map=null;
		$query_partner_gw_telco_map=$db->query($sql_partner_gw_telco_map);
		$telco_id=null;
		if($query_partner_gw_telco_map!=null&&$query_partner_gw_telco_map->num_rows() > 0){
			$partner_gw_telco_map=$query_partner_gw_telco_map->row();
			$telco_id=$partner_gw_telco_map->telco_id;
		}
		return $partner_gw_telco_map;
	}
	private function _shootpixel($msisdn,$shortcode,$keyword,$db){
		#trick to publisher
		$sql_track_entry_pending="select * from track_entry_pending where msisdn='".$msisdn."' and shortcode='".$shortcode."' and keyword='".$keyword."' and  and order by sys_time desc limit 1 ";
		$query_track = $db->query($sql_track_entry_pending);
		$track=null;
		if($query_track!=null&&$query_track->num_rows() > 0){
			$track=$query_track->row();
		}
		if($track!=null){
			#track to partner
			$query_campaign_promote=$db->get_where('campaign_promote',array('pid'=>$track->pid));
			$campaign_promote =null;
			if($query_campaign_promote!=null&&$query_campaign_promote->num_rows() > 0){
				$campaign_promote=$query_campaign_promote->row();
			}
			if($campaign_promote!=null){
				#check is shoot or not
				switch ($campaign_promote->publisher_id){
					case PARTNER_ADV_RINGTONEPARTER:
						break;
					case PARTNER_ADV_CHINESEAN:
						break;
				}
			}
		}else{
			#find on tracking maybe not track to publisher
		}
	}
	private function _outputxml($xml_return){
		header('Content-Type: text/xml; charset=utf-8');
		header("Content-length: " . strlen($xml_return)); // tells file size
		//Set no caching
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		echo $xml_return;
	}
	private function _istrack($campaign_promote){

	}


		public function t(){
			$this->load->library('curl');
// 			$msisdn=$this->input->get_post('msisdn');
// 			$cmd=$this->input->get_post('cmd');
// 			$xmldn_str= "";
// 			$xmldn_str .= "<XML>";
// 			$xmldn_str .= "<COMMAND>".$cmd."</COMMAND>";
// 			$xmldn_str .= "<USERNAME>username</USERNAME>";
// 			$xmldn_str .= "<PASSWORD>password</PASSWORD>";
// 			$xmldn_str .= "<MESSAGEID>".random_string("alnum", 30)."</MESSAGEID>";
// 			$xmldn_str .= "<PARTNERREFID></PARTNERREFID>";
// 			$xmldn_str .= "<TELCOID>1</TELCOID>";
// 			$xmldn_str .= "<SERVICENUMBER>4761986</SERVICENUMBER>";
// 			$xmldn_str .= "<SERVICEPACKAGEID>289</SERVICEPACKAGEID>";
// 			$xmldn_str .= "<CHANNEL>1</CHANNEL>";
// 			$xmldn_str .= "<MSISDN>".$msisdn."</MSISDN>";
// 			$xmldn_str .= "<MESSAGE>&#x0054;&#x0065;&#x0073;&#x0074;</MESSAGE>";
// 			$xmldn_str .= "<TIMESTAMP>2009-12-05 22:00:12</TIMESTAMP>";
// 			$xmldn_str .= "<STATUSCODE>1003</STATUSCODE>";
// 			$xmldn_str .= "<STATUSTEXT>Register success</STATUSTEXT>";
// 			$xmldn_str .= "</XML>";

			$objHttpResult =$this->curl->simple_post('http://clubfunfun.com/sub/subdn/f2u');

			log_message('info','$objHttpResult:'.$objHttpResult);
		}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
?>