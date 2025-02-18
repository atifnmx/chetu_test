<?php

namespace App\Console\Commands;

use App\Models\ErrorLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogError extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:error';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store the the error log in custom table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logs = Log::getLogger();
        dd($logs);
        //$logs = Log::channel('single')->getLogger()->getHandlers()[0]->getLogs();

        //Customize this table name to your liking
        $tableName = 'error_logs';

        foreach ($logs as $log) {
            // $data = [
            //     'message' => json_encode($log['message']),
            //     'level' => $log['level_name'],
            //     'created_at' => $log['datetime']->format('Y-m-d H:i:s'),
            //     'updated_at' => $log['datetime']->format('Y-m-d H:i:s'),
            // ];
            $error = new ErrorLog();
            $error->message = $log['message'];
            $error->level = $log['level_name'];
            $error->save();
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::statement('TRUNCATE TABLE ' . $tableName);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return Command::SUCCESS;
        $this->info('Successfully logged errors');
    }
}
