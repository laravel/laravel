<?php 
$apiEssearch = new ApiEsSearch();
App::instance('apiEsSearch',$apiEssearch);

$apicheckPermission = new ApiCheckPermission();
App::instance('apicheckPermission',$apicheckPermission);
 ?>