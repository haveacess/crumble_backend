<?php

namespace App\Console\Commands\Steam;

use App\Services\Steam\ImportService\ImportArtisanService;
use Closure;
use Illuminate\Console\Command;

class ImportAccountCookie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:import-account-cookie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import your steam account by cookie file';

    /**
     * Getting list of steps for import account
     *
     * @param ImportArtisanService $service
     * @return Closure[] List of Closure which need be
     * called step by step
     */
    private function getSteps(ImportArtisanService $service): array
    {
        return [
            $service->askUserAliases(),
            $service->askSendCookieFile(),
            $service->showFinalImportMessage(),
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Just skeleton of import using console.
        // Not contain main logic from import service.
        // Is just represent how it will be in artisan console

        $service = new ImportArtisanService();
        $service->setConsoleCommand($this);

        $this->withProgressBar($this->getSteps($service), function (Closure $step) use ($service) {
            $this->newLine();
            call_user_func($step);
        });
        $this->newLine();

        return self::SUCCESS;
    }
}
