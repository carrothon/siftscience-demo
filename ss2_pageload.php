<?php
include 'shieldsquare/ss2.php';
$shieldsquare_username = "fakeuser1"; // Enter the UserID of the user. This is optional.
$shieldsquare_calltype = 1;
$shieldsquare_pid = ""; // Please leave this as empty.
$shieldsquare_response=shieldsquare_ValidateRequest($shieldsquare_username, $shieldsquare_calltype, $shieldsquare_pid);

if($shieldsquare_response->responsecode == -1)
{
echo "Curl Error – ".$shieldsquare_response->reason;
echo "Allow the user request.";
}
?>
<html>
	<head>	
<script type="text/javascript">
 var __uzdbm_a = "<?php echo $shieldsquare_response->pid?>";
</script>
<div id="ss_098786_234239_238479_190541"></div>
<script async = "true" type="text/javascript" src="https://cdn.perfdrive.com/static/jscall_min.js"></script>
</head>
<body>
<div class="container">
	<h1>ShielSquare 产品测试页面</h1>
	<form action="createaccount.php">
		<div><input type="text" name="name" id=""></div>
		<div><input type="password" name="password" id=""></div>
		
		<div><input type="submit" value="Sumbit"></div>
	</form>
</div>
</body>
</html>
