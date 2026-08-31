<?php

namespace Illuminate\Foundation\Console;

use Dotenv\Parser\Lines;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;

#[AsCommand(name: 'env:encrypt')]
class EnvironmentEncryptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:encrypt
                    {--key= : The encryption key}
                    {--cipher= : The encryption cipher}
                    {--env= : The environment to be encrypted}
                    {--readable : Encrypt each variable individually with readable, plain-text variable names}
                    {--prune : Delete the original environment file}
                    {--force : Overwrite the existing encrypted environment file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt an environment file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $cipher = $this->option('cipher') ?: 'AES-256-CBC';

        $key = $this->option('key');

        if (! $key && $this->input->isInteractive()) {
            $ask = select(
                label: 'What encryption key would you like to use?',
                options: [
                    'generate' => 'Generate a random encryption key',
                    'ask' => 'Provide an encryption key',
                ],
                default: 'generate'
            );

            if ($ask == 'ask') {
                $key = password('What is the encryption key?');
            }
        }

        $keyPassed = $key !== null;

        $environmentFile = $this->option('env')
            ? Str::finish($this->laravel->environmentPath(), DIRECTORY_SEPARATOR).'.env.'.$this->option('env')
            : $this->laravel->environmentFilePath();

        $encryptedFile = $environmentFile.'.encrypted';

        if (! $keyPassed) {
            $key = Encrypter::generateKey($cipher);
        }

        if (! $this->files->exists($environmentFile)) {
            $this->fail('Environment file not found.');
        }

        if ($this->files->exists($encryptedFile) && ! $this->option('force')) {
            $this->fail('Encrypted environment file already exists.');
        }

        try {
            $encrypter = new Encrypter($this->parseKey($key), $cipher);

            $contents = $this->files->get($environmentFile);

            $encrypted = $this->option('readable')
                ? $this->encryptReadableFormat($contents, $encrypter)
                : $encrypter->encrypt($contents);

            $this->files->put($encryptedFile, $encrypted);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        if ($this->option('prune')) {
            $this->files->delete($environmentFile);
        }

        $this->components->info('Environment successfully encrypted.');

        $this->components->twoColumnDetail('Key', $keyPassed ? $key : 'base64:'.base64_encode($key));
        $this->components->twoColumnDetail('Cipher', $cipher);
        $this->components->twoColumnDetail('Encrypted file', $encryptedFile);

        $this->newLine();
    }

    /**
     * Encrypt the environment file in readable format.
     *
     * @param  string  $contents
     * @param  \Illuminate\Encryption\Encrypter  $encrypter
     * @return string
     */
    protected function encryptReadableFormat(string $contents, Encrypter $encrypter): string
    {
        $result = '';

        foreach (Lines::process(preg_split('/\r\n|\r|\n/', $contents)) as $entry) {
            $pos = strpos($entry, '=');

            if ($pos === false) {
                continue;
            }

            $name = substr($entry, 0, $pos);
            $value = substr($entry, $pos + 1);

            $result .= $name.'='.$encrypter->encryptString($value)."\n";
        }

        return $result;
    }

    /**
     * Parse the encryption key.
     *
     * @param  string  $key
     * @return string
     */
    protected function parseKey(string $key)
    {
        if (Str::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }
}
