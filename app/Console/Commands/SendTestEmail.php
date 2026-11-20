<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    protected $signature = 'mail:test {email : Address that should receive the test message}';
    protected $description = 'Send a simple test email using the current mail settings';

    public function handle(): int
    {
        Mail::raw('This is a test email from the Laravel POS project.', function ($message): void {
            $message->to($this->argument('email'))->subject('Laravel POS email test');
        });

        $this->info('Test email sent using the configured mail driver.');
        return self::SUCCESS;
    }
}
