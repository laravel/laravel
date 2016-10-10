<?php

class IlluminateQueueClosure {

	/**
	 * Fire the Closure based queue job.
	 *
	 * @param  \Illuminate\Queue\Jobs\Job  $job
	 * @param  array  $data
	 * @return void
	 */
	public function fire($job, $data)
	{
		$closure = unserialize($data['closure']);

		$closure($job);
	}

}
