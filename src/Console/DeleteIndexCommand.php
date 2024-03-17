<?php

namespace Laravel\Scout\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Scout\EngineManager;

class DeleteIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:delete-index {name : The name of the index}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an index';

    /**
     * Execute the console command.
     *
     * @param  \Laravel\Scout\EngineManager  $manager
     * @return void
     */
    public function handle(EngineManager $manager)
    {
        try {
            foreach ($this->indexNames($this->argument('name')) as $name) {
                $manager->engine()->deleteIndex($name);

                $this->info('Index "' . $name . '" deleted.');
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Get the fully-qualified index name for the given index.
     *
     * @param  string  $name
     * @return array
     */
    protected function indexNames($name)
    {
        if (class_exists($name)) {
            $searchableAs = (new $name)->searchableAs();

            return is_array($searchableAs) ? $searchableAs : [$searchableAs];
        }

        $prefix = config('scout.prefix');

        return ! Str::startsWith($name, $prefix) ? [$prefix.$name] : [$name];
    }
}
