<?php

namespace App\Services\Steam\ImportService;

use Closure;
use Illuminate\Console\Command;

class ImportArtisanService {

    private string $userAlias;
    private Command $consoleCommand;

    public function __construct()
    {

    }

    public function setConsoleCommand(Command $command) {
        $this->consoleCommand = $command;
    }

    /**
     * Getting list of users alias
     * available for import
     *
     * @return string[]
     */
    public function getUsersAlias(): array
    {
        return ['user1', 'user2']; // get users from .env
    }

    /**
     * Get Import url for open in browser
     * for current process id
     *
     * @return string
     */
    public function getImportUrl(): string
    {
        return "http://127.0.0.1:8111/backend/import/steam/{$this->userAlias}";
    }

    /**
     * Ask User about accounts
     *
     * @return Closure
     */
    public function askUserAliases(): Closure
    {
        return function () {
            $userAlias = $this->consoleCommand->choice(
                'Select User for import: ', $this->getUsersAlias()
            );
            $this->userAlias = $userAlias;
        };
    }

    /**
     * Ask User of send cookie files
     *
     * @return Closure
     */
    public function askSendCookieFile(): Closure
    {
        return function() {
            $this->consoleCommand->alert($this->userAlias);

            $this->consoleCommand->info(
                'Follow this url and import file using your browser: ' . PHP_EOL .
                $this->getImportUrl() . PHP_EOL
            );

            $this->consoleCommand->info('Waiting while you import your files');

            $maxTry = 1;
            // while(true)
            while ($maxTry) {

                sleep(5);

                $maxTry--;
            }
            $json = "{name: Jhon}"; // test json

            // send in service
        };
    }

    /**
     * Show final import message for user
     *
     * @return Closure
     */
    public function showFinalImportMessage(): Closure
    {
        return function () {
            $this->consoleCommand
                ->newLine()
                ->alert("User {$this->userAlias} has been successfully imported!");
        };
    }
}
