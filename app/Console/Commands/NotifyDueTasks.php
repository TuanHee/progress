<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDueSoon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyDueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:duetasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $tasks = Task::where('completed', false)
                    ->whereDate('due_at', Carbon::tomorrow())
                    ->get();

        $tasks->map(function($task) {
            $notify_users = $task->performers()
                        ->get()
                        ->pluck('user')
                        ->all();

            Notification::send($notify_users, new TaskDueSoon($task));
        });
    }
}
