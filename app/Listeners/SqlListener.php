<?php

namespace App\Listeners;

use App\Models\Systems;
use Illuminate\Database\Events\QueryExecuted;

class SqlListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        $sql = str_replace("?", "'%s'", $event->sql);
        foreach($event->bindings as $key =>$binding){
            if($binding instanceof \DateTime){
                $event->bindings[$key] = $binding->format('Y-m-d H:i:s');
            }
        }
        $log = vsprintf($sql, $event->bindings);

        $log = '[' . date('Y-m-d H:i:s') . '] ' . $log . ' time:' .$event->time . 'ms' . "\r\n";
//        Log::info($log);
        Systems::$sqlArray[] = $log;
    }
}
