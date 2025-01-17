<?php

function getSelectOptions($selType, $selValue)
{
	if ($selType == 'SHOW_TOP')
	{
		$options = array('5'=>'Top 5',
			'10'=>'Top 10', '20'=>'Top 20',
			'50'=>'Top 50', '0'=>'All');
	}
	else if ($selType == 'REQ_SHOW_SORTBY')
	{
		$options = array(
			'vhname'=>'Virtual Host Name',
			'client'=>'Client IP',
			'req_time'=>'Request Time (Desc)',
			'extproc_time'=>'ExtApp Process Time (Desc)',
			'in'=>'Req Body Size (Desc)',
			'out'=>'Resp Bytes Out (Desc)');
	}

	return DUtil::genOptions($options, $selValue);

}

$req_show_top = DUtil::getGoodVal(DUtil::grab_input("request","req_show_top"));
$req_show_sort = DUtil::getGoodVal(DUtil::grab_input("request","req_show_sort","string"));
$vhname_filter = DUtil::getGoodVal(DUtil::grab_input("request","vhname_filter","string"));
$ip_filter = DUtil::getGoodVal(DUtil::grab_input("request","ip_filter","string"));
$url_filter = DUtil::getGoodVal(DUtil::grab_input("request","url_filter","string"));
$reqtime_filter = DUtil::getGoodVal(DUtil::grab_input("request","reqtime_filter","string"));
$extproctime_filter = DUtil::getGoodVal(DUtil::grab_input("request","extproctime_filter","string"));
$reqbodysize_filter = DUtil::getGoodVal(DUtil::grab_input("request","reqbodysize_filter","string"));
$outsize_filter = DUtil::getGoodVal(DUtil::grab_input("request","outsize_filter","string"));
$extapp_more = DUtil::getGoodVal(DUtil::grab_input("request","extapp_more","string"));

// setting defaults

if ($req_show_top == '') {
	$req_show_top = '10';
}

if ($req_show_sort == '') {
	$req_show_sort = 'req_time';
}

$show_more = '';
if ($extapp_more == 'on') {
	$show_more = 'checked';
}

$probe = new ReqProbe();
$filters = array('SHOW_TOP' => $req_show_top,
	'SHOW_SORT' => $req_show_sort,
	'VHNAME' => $vhname_filter,
	'IP' => $ip_filter,
	'URL' => $url_filter,
	'REQ_TIME' => $reqtime_filter,
	'PROC_TIME' => $extproctime_filter,
	'IN'=> $reqbodysize_filter,
	'OUT' => $outsize_filter);

$total_count = 0;
$filtered_count = 0;
$reqlist = $probe->retrieve($filters, $total_count, $filtered_count);
$cur_time = gmdate("D M j H:i:s T");
$server_info = "at $cur_time for server {$service->serv['name']}";


?>
<script type="text/javascript">
  // <![CDATA[
  function clearForm() {
  var elements, i, elm;
		elements = document.rptform.elements;
		for( i=0, elm; elm=elements[i++]; ) {
			if (elm.type == "text") {
				elm.value ='';
			}
			document.rptform.req_show_top.selectedIndex = 1;
			document.rptform.req_show_sort.selectedIndex = 2;
		}
	}

  // ]]>
</script>
<form name="rptform" method="get"><input type="hidden" name="vl" value="4">

<div class="bottom_bar">
<span  class="h2_font">Real-Time Statistics: Requests Snapshot</span> <? echo $server_info;?>
<span style="float:right"><input name="refresh" value="Refresh" type="submit"></span>
</div>

<div class="stat_filter">
<?
	$buf = '<div><span style="margin-right:50px"><b>Filters: </b>(* Will accept regular expression)</span>';
	$buf .= '<span style="margin-right:50px">Display: <select class="th-clr" name="req_show_top">'
		. getSelectOptions('SHOW_TOP', $req_show_top)
		. '</select></span>'."\n";
	$buf .= '<span style="margin-right:50px">Sort by: <select class="th-clr" name="req_show_sort">'
		. getSelectOptions('REQ_SHOW_SORTBY', $req_show_sort)
		. '</select></span>'."\n";
	$buf .= '<span style="float:right"><input name="apply" value="Apply" type="submit">
			<input name="clear" value="Clear" type="button" onclick="javascript:clearForm()"></span>
			</div>';
	$buf .= '<div><span>VHost Name*: <input type="text" name="vhname_filter" value="'
		. $vhname_filter . '" size="30"></span>' ."\n";
	$buf .= '<span>Client IP*: <input type="text" name="ip_filter" value="'
		. $ip_filter . '" size="10"></span>' ."\n";
	$buf .= '<span>Request URL*: <input type="text" name="url_filter" value="'
		. $url_filter . '" size="68"></span></div>' ."\n";

	$buf .= '<div><span>Req Time &gt; <input type="text" name="reqtime_filter" value="'
		. $reqtime_filter . '" size=5> secs</span>' ."\n";
	$buf .= '<span>ExtApp Proc Time &gt; <input type="text" name="extproctime_filter" value="'
		. $extproctime_filter . '" size=5> secs</span>' ."\n";
	$buf .= '<span>ExtApp Detail <input type="checkbox" name="extapp_more" '
		. $show_more . '></span>' ."\n";

	$buf .= '<span>Req Body Size &gt; <input type="text" name="reqbodysize_filter" value="'
		. $reqbodysize_filter . '" size=7> KB</span>' ."\n";
	$buf .= '<span>Resp Output Bytes &gt; <input type="text" name="outsize_filter" value="'
		. $outsize_filter . '" size=7> KB</span>' ."</div>\n";
	echo $buf;

?>
</div>
<div>
	<table class="xtbl_value highlight-line" width="100%" border="0" cellpadding="2" cellspacing="1">
	<tr>
		<td title="client IP"><b>Client</b></td>
		<td title="keep alive Requests served through current connection"><b>Ka</b></td>
		<td title="mode ('RE' Reading Request Header,
	'RB' Reading Request Body,
	'EA' External Authentication,
	'TH' Throttling,
	'PR' Processing,
	'RD' Redirect,
	'WR' Sending Reply,
	'AP' AIO Pending,
	'AC' AIO Complete,
	'CP' Complete,
	'SD' Shutting down connection,
	'CL' Closing connection)"><b>M</b></td>
		<td title="seconds used since request coming in"><b>R</b></td>
		<td nowrap title="bytes read from request / bytes of full request body"><b>In/Total</b></td>
		<td nowrap title="bytes written to reply / bytes of full response (-1 means CHUNKED, -2 means UNKNOWN)"><b>Out/Total</b></td>
		<td title="Virtal Host Name"><b>VHost</b></td>
		<td title="Handler name: ExtApp name or 'static'"><b>Handler</b></td>
		<td title="seconds used for processing request by ExtApp, empty for static contents"><b>P</b></td>
<? if ($show_more != '') {?>
		<td><b>ExtApp Socket</b></td>
		<td title="ExtApp PID"><b>pid</b></td>
		<td title="Requests processed through current ExtApp socket connection"><b>RP</b></td>
<?	}?>
		<td><b>Request</b></td>
	</tr>

	<?
		$buf = '';
		foreach( $reqlist as $line ) {
			$d = explode("\t", $line);
            for ($i = FLD_VHost ; $i <= FLD_Url ; $i++) { // fill up empty ones
                if (!isset($d[$i]))
                    $d[$i] = '';
            }

			$buf.= '<tr><td>' . $d[FLD_IP]
				. '</td><td>' . $d[FLD_KAReqServed]
				. '</td><td>' . $d[FLD_Mode]
				. '</td><td>' . $d[FLD_ReqTime]
				. '</td><td>' . $d[FLD_InBytesCurrent] . '/' . $d[FLD_InBytesTotal]
				. '</td><td>' . $d[FLD_OutBytesCurrent] . '/' . $d[FLD_OutBytesTotal]
				. '</td><td>' . $d[FLD_VHost]	//vhost
				. '</td><td>' . $d[FLD_Handler]  //handler
				. '</td><td>' . $d[FLD_ExtappProcessTime];  // P

			if ($show_more != '') {
				$buf .= '</td><td>' . $d[FLD_ExtappSocket]  // socket
				. '</td><td>' . $d[FLD_ExtappPid]  //pid
				. '</td><td>' . $d[FLD_ExtappConnReqServed];  //RP
			}
			$url = trim($d[FLD_Url], '"');
			if (strlen($url) > 50) {
				$buf .= '</td><td title=\'' . wordwrap($url,60,"\n",true) . '\'>' . substr($url, 0, 50) . ' ..."';
			}
			else {
				$buf .= '</td><td>' . $url;
			}
			$buf .= "</td></tr>\n";
		}
		echo $buf;

	?>
	</table>
</div>


</form>
