TYPE=VIEW
query=select `t`.`THREAD_ID` AS `thread_id`,if((`t`.`NAME` = \'thread/sql/one_connection\'),concat(`t`.`PROCESSLIST_USER`,\'@\',`t`.`PROCESSLIST_HOST`),replace(`t`.`NAME`,\'thread/\',\'\')) AS `user`,sum(`mt`.`CURRENT_COUNT_USED`) AS `current_count_used`,sum(`mt`.`CURRENT_NUMBER_OF_BYTES_USED`) AS `current_allocated`,ifnull((sum(`mt`.`CURRENT_NUMBER_OF_BYTES_USED`) / nullif(sum(`mt`.`CURRENT_COUNT_USED`),0)),0) AS `current_avg_alloc`,max(`mt`.`CURRENT_NUMBER_OF_BYTES_USED`) AS `current_max_alloc`,sum(`mt`.`SUM_NUMBER_OF_BYTES_ALLOC`) AS `total_allocated` from (`performance_schema`.`memory_summary_by_thread_by_event_name` `mt` join `performance_schema`.`threads` `t` on((`mt`.`THREAD_ID` = `t`.`THREAD_ID`))) group by `t`.`THREAD_ID`,if((`t`.`NAME` = \'thread/sql/one_connection\'),concat(`t`.`PROCESSLIST_USER`,\'@\',`t`.`PROCESSLIST_HOST`),replace(`t`.`NAME`,\'thread/\',\'\')) order by sum(`mt`.`CURRENT_NUMBER_OF_BYTES_USED`) desc
md5=cc53b9c3a372316d91714f5733e30048
updatable=0
algorithm=1
definer_user=mysql.sys
definer_host=localhost
suid=0
with_check_option=0
timestamp=2023-11-22 04:48:47
create-version=1
source=SELECT t.thread_id, IF(t.name = \'thread/sql/one_connection\',  CONCAT(t.processlist_user, \'@\', t.processlist_host),  REPLACE(t.name, \'thread/\', \'\')) user, SUM(mt.current_count_used) AS current_count_used, SUM(mt.current_number_of_bytes_used) AS current_allocated, IFNULL(SUM(mt.current_number_of_bytes_used) / NULLIF(SUM(current_count_used), 0), 0) AS current_avg_alloc, MAX(mt.current_number_of_bytes_used) AS current_max_alloc, SUM(mt.sum_number_of_bytes_alloc) AS total_allocated FROM performance_schema.memory_summary_by_thread_by_event_name AS mt JOIN performance_schema.threads AS t USING (thread_id) GROUP BY thread_id, IF(t.name = \'thread/sql/one_connection\',  CONCAT(t.processlist_user, \'@\', t.processlist_host),  REPLACE(t.name, \'thread/\', \'\')) ORDER BY SUM(mt.current_number_of_bytes_used) DESC
client_cs_name=utf8
connection_cl_name=utf8_general_ci
view_body_utf8=select `t`.`THREAD_ID` AS `thread_id`,if((`t`.`NAME` = \'thread/sql/one_connection\'),concat(`t`.`PROCESSLIST_USER`,\'@\',`t`.`PROCESSLIST_HOST`),replace(`t`.`NAME`,\'thread/\',\'\')) AS `user`,sum(`mt`.`CURRENT_COUNT_USED`) AS `current_count_used`,sum(`mt`.`CURRENT_NUMBER_OF_BYTES_USED`) AS `current_allocated`,ifnull((sum(`mt`.`CURRENT_NUMBER_OF_BYTES_USED`) / nullif(sum(`mt`.`CURRENT_COUNT_USED`),0)),0) AS `current_avg_alloc`,max(`mt`.`CURRENT_NUMBER_OF_BYTES_USED`) AS `current_max_alloc`,sum(`mt`.`SUM_NUMBER_OF_BYTES_ALLOC`) AS `total_allocated` from (`performance_schema`.`memory_summary_by_thread_by_event_name` `mt` join `performance_schema`.`threads` `t` on((`mt`.`THREAD_ID` = `t`.`THREAD_ID`))) group by `t`.`THREAD_ID`,if((`t`.`NAME` = \'thread/sql/one_connection\'),concat(`t`.`PROCESSLIST_USER`,\'@\',`t`.`PROCESSLIST_HOST`),replace(`t`.`NAME`,\'thread/\',\'\')) order by sum(`mt`.`CURRENT_NUMBER_OF_BYTES_USED`) desc
