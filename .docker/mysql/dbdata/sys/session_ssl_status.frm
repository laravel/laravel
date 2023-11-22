TYPE=VIEW
query=select `sslver`.`THREAD_ID` AS `thread_id`,`sslver`.`VARIABLE_VALUE` AS `ssl_version`,`sslcip`.`VARIABLE_VALUE` AS `ssl_cipher`,`sslreuse`.`VARIABLE_VALUE` AS `ssl_sessions_reused` from ((`performance_schema`.`status_by_thread` `sslver` left join `performance_schema`.`status_by_thread` `sslcip` on(((`sslcip`.`THREAD_ID` = `sslver`.`THREAD_ID`) and (`sslcip`.`VARIABLE_NAME` = \'Ssl_cipher\')))) left join `performance_schema`.`status_by_thread` `sslreuse` on(((`sslreuse`.`THREAD_ID` = `sslver`.`THREAD_ID`) and (`sslreuse`.`VARIABLE_NAME` = \'Ssl_sessions_reused\')))) where (`sslver`.`VARIABLE_NAME` = \'Ssl_version\')
md5=85a4a938aeb0d850e448a6821ca91f12
updatable=0
algorithm=2
definer_user=mysql.sys
definer_host=localhost
suid=0
with_check_option=0
timestamp=2023-11-22 04:48:50
create-version=1
source=SELECT sslver.thread_id,  sslver.variable_value ssl_version,  sslcip.variable_value ssl_cipher, sslreuse.variable_value ssl_sessions_reused FROM performance_schema.status_by_thread sslver  LEFT JOIN performance_schema.status_by_thread sslcip  ON (sslcip.thread_id=sslver.thread_id and sslcip.variable_name=\'Ssl_cipher\') LEFT JOIN performance_schema.status_by_thread sslreuse  ON (sslreuse.thread_id=sslver.thread_id and sslreuse.variable_name=\'Ssl_sessions_reused\')  WHERE sslver.variable_name=\'Ssl_version\'
client_cs_name=utf8
connection_cl_name=utf8_general_ci
view_body_utf8=select `sslver`.`THREAD_ID` AS `thread_id`,`sslver`.`VARIABLE_VALUE` AS `ssl_version`,`sslcip`.`VARIABLE_VALUE` AS `ssl_cipher`,`sslreuse`.`VARIABLE_VALUE` AS `ssl_sessions_reused` from ((`performance_schema`.`status_by_thread` `sslver` left join `performance_schema`.`status_by_thread` `sslcip` on(((`sslcip`.`THREAD_ID` = `sslver`.`THREAD_ID`) and (`sslcip`.`VARIABLE_NAME` = \'Ssl_cipher\')))) left join `performance_schema`.`status_by_thread` `sslreuse` on(((`sslreuse`.`THREAD_ID` = `sslver`.`THREAD_ID`) and (`sslreuse`.`VARIABLE_NAME` = \'Ssl_sessions_reused\')))) where (`sslver`.`VARIABLE_NAME` = \'Ssl_version\')
