<?php

namespace App\Jobs;

use App\Jobs\Serializers\ExampleUserSerializer;

class ExampleJob extends BaseJob
{
    protected const QUEUE = 'examples';

    private ExampleUserSerializer $userSerializer;

    public function __construct(ExampleUserSerializer $userSerializer)
    {
        parent::__construct();

        $this->userSerializer = $userSerializer;
    }

    public function handle(): void
    {
        // Handle the job.
        // The serializer helps to avoid the problem of entities authomatic serialization.
        // If you need info from the entity's related entities, create serializers and fill them in the main serializer constructor.
    }
}
