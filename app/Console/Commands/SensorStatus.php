<?php

namespace App\Console\Commands;
use App\Models\Sensor;
use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SensorStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If a sensor is offline for an hour sets status to offline';

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
        $sensors = Sensor::all();
        $notifications = Notification::all();
        foreach($sensors as $sensor)
        {
            //Logic for online sensors that have not updated in 10 mins
            if (Carbon::now()->diffInMinutes($sensor->updated_at) > 10 && $sensor->status == "Online")
            {
                //Logic for Critical Alert
                if($sensor->reading >= 500)
                {
                    $sensor->status = "Offline";
                    $sensor->alert = "Critical";
                    $sensor->save();
                    $message = "is offline and last displayed a high reading !";
                    $notificationExists = false;
                    
                    foreach($notifications as $notification)
                    {
                        if ($notification->message == $sensor->name.' '.$message && $notification->alert == $sensor->alert)
                        {
                            $notificationExists = true;
                            $notification->touch();
                        }
                    }

                    if($notificationExists == false)
                    {
                        $newNotification = Notification::create(['alert' => $sensor->alert,'message' => $sensor->name.' '.$message]);
                    }
                }
                //Logic for Error Alert
                else{
                    $sensor->status = "Offline";
                    $sensor->alert ="Error";
                    $sensor->save();
                    $message = "is offline !";
                    $notificationExists = false;

                    foreach($notifications as $notification)
                    {
                        if ($notification->message == $sensor->name.' '.$message && $notification->alert == $sensor->alert)
                        {
                            $notificationExists = true;
                            $notification->touch();
                        }
                    }

                    if($notificationExists == false)
                    {
                        $newNotification = Notification::create(['alert' => $sensor->alert,'message' => $sensor->name.' '.$message]);
                    }
                }
            }
            //Logic for offline sensors that have not updated in 10 mins
            else if (Carbon::now()->diffInMinutes($sensor->updated_at) > 10 && $sensor->status == "Offline")
            {
                //Logic for Critical Alert
                if($sensor->reading >= 500)
                {
                    if($sensor->alert != "Critical")
                    {
                        $sensor->alert = "Critical";
                        $sensor->save();
                    }
                    $message = "is offline and last displayed a high reading !";
                    $notificationExists = false;
                    
                    foreach($notifications as $notification)
                    {
                        if ($notification->message == $sensor->name.' '.$message && $notification->alert == $sensor->alert)
                        {
                            $notificationExists = true;
                            $notification->touch();
                        }
                    }

                    if($notificationExists == false)
                    {
                        $newNotification = Notification::create(['alert' => $sensor->alert,'message' => $sensor->name.' '.$message]);
                    }
                }
                //Logic for Error Alert
                else{
                    if($sensor->alert != "Error")
                    {
                        $sensor->alert ="Error";
                        $sensor->save();
                    }
    
                    $message = "is offline !";
                    $notificationExists = false;

                    foreach($notifications as $notification)
                    {
                        if ($notification->message == $sensor->name.' '.$message && $notification->alert == 'Error')
                        {
                            $notificationExists = true;
                            $notification->touch();
                        }
                    }

                    if($notificationExists == false)
                    {
                        $newNotification = Notification::create(['alert' => 'Error','message' => $sensor->name.' '.$message]);
                    }
                }
            }
            //Logic for online sensors that have been updating
            else if(Carbon::now()->diffInMinutes($sensor->updated_at) < 1 && $sensor->status == "Online"){
                //Logic for warning
                if($sensor->reading >= 500){
                    if($sensor->alert != "Warning"){
                        $sensor->alert = "Warning";
                        $sensor->save();
                    }
                    $message = "is displaying a high reading !";
                    $notificationExists = false;
                    
                    foreach($notifications as $notification)
                    {
                        if ($notification->message == $sensor->name.' '.$message && $notification->alert == $sensor->alert)
                        {
                            $notificationExists = true;
                            $notification->touch();
                        }
                    }

                    if($notificationExists == false)
                    {
                        $newNotification = Notification::create(['alert' => $sensor->alert,'message' => $sensor->name.' '.$message]);
                    }
                }
                else{
                    if($sensor->alert != "Normal"){
                        $sensor->alert = "Normal";
                        $sensor->save();
                    }
                    
                }
            }
        }
    }
}