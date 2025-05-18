<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Modules\Core\Services\ConfigurationService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

class AppConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:config';

    /**
     * The console command description.
     */
    protected $description = 'Create The Configuration for the Project.';


    protected $configService;
    /**
     * Create a new command instance.
     */
    public function __construct(ConfigurationService $configService)
    {
        $this->configService = $configService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info("Starting app configuration");
            // $this->configService->createRoles();
            // $this->configService->createIndustries();
            $this->configService->createCountries();
            $this->configService->createCounties();
            $this->info("App configuration completed");
        } catch (Throwable $th) {
            logError($th);
        }
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
