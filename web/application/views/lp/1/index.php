<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="samsung,galaxy,note3,เน�เธ—เธฃเธจเธฑเธ�เธ—เน�,เธ�เธฑเธกเธ�เธธเธ�,เน�เธ�เน�เธ•3,เธกเธทเธญเธ–เธทเธญ">
<title>:เธฅเธธเน�เธ�!!Galaxy Note3:</title>
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script>
function validate(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}
function track(){
	 valid=true;
	 if($('#msisdn').val().length==10){
		 if(!$('#msisdn').val().match('^0')){
          valid=false;
		 }else{
			 $('#msisdn').val('66'+$('#msisdn').val().substring(1));
		 }
	 }else if($('#msisdn').val().length==11){
		 if(!$('#msisdn').val().match('^66')){
			 valid=false;
		 }
	 }else{
		 valid=false;
	 }

	
	 if(!valid){
		    alert("Invalid Phone Number:"+$('#msisdn').val());
		    $('#msisdn').focus();
		    return false;		 
	 }
	 return true;	
}
</script>
<style type="text/css">
.content {
	background-repeat: no-repeat;
	width:800px;
	height:600px;
	margin-left:auto;
	margin-right:auto;
	position:relative;
	text-align:center;
	border: 0px;
}
</style>
</head>

<body bgcolor="#000000">
	<form id="aform" action="<?php echo base_url();?>lp/track" onsubmit="return track();">
		<input type="hidden" id="track_id" name="track_id" value="<?php echo $track_id?>" />
		<div class="content">
			<img src="<?php echo base_url();?>images/lp/1/note3lp21.jpg"
				alt="samsung,galaxy,note3,เน�เธ—เธฃเธจเธฑเธ�เธ—เน�,เธ�เธฑเธกเธ�เธธเธ�,เน�เธ�เน�เธ•3,เธกเธทเธญเธ–เธทเธญ" width="800"
				height="600"
				style="position: absolute; top: 0px; left: 0px; z-index: 1000;" /><img
				src="<?php echo base_url();?>images/lp/1/text.png"
				alt="samsung,galaxy,note3,เน�เธ—เธฃเธจเธฑเธ�เธ—เน�,เธ�เธฑเธกเธ�เธธเธ�,เน�เธ�เน�เธ•3,เธกเธทเธญเธ–เธทเธญ" width="330"
				height="210" border=0px
				style="position: absolute; top: 328px; left: 103px; z-index: 1000;" />
			<input id="msisdn" name="msisdn" type="text"
				onkeypress="validate(event)" maxlength="11"
				style="font-size: 24px; text-align: center; position: absolute; width: 244px; height: 24px; top: 402px; left: 109px; z-index: 2000; border: 0px;" />
			<img src="<?php echo base_url();?>images/lp/1/box.png"
				alt="samsung,galaxy,note3,เน�เธ—เธฃเธจเธฑเธ�เธ—เน�,เธ�เธฑเธกเธ�เธธเธ�,เน�เธ�เน�เธ•3,เธกเธทเธญเธ–เธทเธญ" width="260"
				height="42" border=0px
				style="position: absolute; top: 396px; left: 103px; z-index: 1000;" />
			<img src="<?php echo base_url();?>images/lp/1/sub.png" onclick="$('#aform').submit()"
				alt="samsung,galaxy,note3,เน�เธ—เธฃเธจเธฑเธ�เธ—เน�,เธ�เธฑเธกเธ�เธธเธ�,เน�เธ�เน�เธ•3,เธกเธทเธญเธ–เธทเธญ" width="54"
				height="55" border=0px
				style="position: absolute; top: 388px; left: 371px; z-index: 1000; cursor: pointer;" />

		</div>
	</form>
</body>
</html>