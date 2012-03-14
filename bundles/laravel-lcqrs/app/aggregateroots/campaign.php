<?php namespace App\AggregateRoots;

use LCQRS\AggregateRoot;
use App\Events\Campaign\CampaignCreated;
use App\Events\Campaign\CampaignUpdated;

class Campaign extends AggregateRoot {

	public function __construct($attributes)
	{
		$this->create($attributes);
	}

	public function create_draft($attributes)
	{
		$this->apply_event(new CampaignDraftCreated($attributes));
		return $this;
	}

	public function update($attributes)
	{
		$this->apply_event(new CampaignUpdated($this->uuid, $attributes));
		return $this;
	}

	protected function apply_created_event(CampaignCreated $event)
	{
		$this->attributes = $event->attributes;
	}

	protected function apply_updated_event(CampaignUpdated $event)
	{
		$this->attributes = array_merge($this->attributes, $event->attributes);
	}

}