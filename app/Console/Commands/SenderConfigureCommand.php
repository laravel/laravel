<?php

namespace App\Console\Commands;

use App\Dommain\ListErrorAdapter;
use App\Rules\FilePathRule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SenderConfigureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sender:configure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a list configurations to send message';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Create new configuration');

        /** @var ListErrorAdapter $listErrorAdapter */
        $listErrorAdapter = new ListErrorAdapter();
        $validator = null;

        $fileName = null;
        do {
            $fileName = $this->ask('Configuration\'s name?');

            $validator = Validator::make([
                'file\'s name' => $fileName
                ],
                [
                    'file\'s name' => [
                        'required',
                        new FilePathRule,
                    ],
                ]);

            if ($validator->fails()) {
                $listErrorAdapter->sourcer($validator->getMessageBag()->getMessages());
                $errors = $listErrorAdapter->convert();

                foreach ($errors as $error) {
                    $this->error($error);
                }
            }
        }while($validator->fails());

        $this->info('Process was finished correctly!!');
    }
}
