<?php

namespace App\Console\Commands;

use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class JobPOMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:pomaster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
     public function handle()
    {
        $startTime = Carbon::now(new DateTimeZone('Asia/Kuwait'))->toDateTimeString();
        $config = [
            'ashost' => env('SAP_HOST'),
            'sysnr' => env('SAP_SYSNR'),
            'client' => env('SAP_CLIENT'),
            'user' => env('SAP_USER'),
            'passwd' => env('SAP_PWD'),
            'trace' => SapConnection::TRACE_LEVEL_OFF,
        ];

        Log::info('config',$config);
        
        try {
            Log::info('Attempting SAP connection...', ['config' => $config]);
            $c= new SAPConnection($config);
            $f=$c->getFunction('ZPHPVE_PO_DETAILS');
            Log::info('Connection established successfully',$f);
            $result = $f->invoke();
            Log::info('Function invoked successfully, processing data.');
                    
            // $this->storeVendorMasterData($result['VENDOR_DETAIL']) ;

            $endTime = Carbon::now(new DateTimeZone('Asia/Kuwait'))->toDateTimeString();
        
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);

            $difference = $end->diffInSeconds($start);

                DB::select(
                'call USP_Job_Logs (?,?,?,?,?)',
                array("2", $startTime, "Auto", $difference, "Success")
                );
            $Dataout = array(["Result" => $result, "Config" =>  $config]);
            Log::info('Vendor profile processed and logged successfully.', ['duration_seconds' => $difference]);

            return $Dataout;

        }
        catch (\Throwable $th) {

            $endTime = Carbon::now(new DateTimeZone('Asia/Kuwait'))->toDateTimeString();
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);
            $difference = $end->diffInSeconds($start);
            DB::select(
              'call USP_Job_Logs (?,?,?,?,?)',
              array("2", $startTime, "Auto", $difference, "FAIL")
            );

             // Log the error with the message and full trace
            Log::error('SAP connection or function call failed', [
                'error_message' => $th->getMessage(),
                'stack_trace' => $th->getTraceAsString(),
                'duration_seconds' => $difference,
            ]);
            return $th;


          }
    }
}