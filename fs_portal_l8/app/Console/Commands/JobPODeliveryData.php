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


class JobPODeliveryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:pomasterdelivery';

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

        
        try {
            Log::info('Attempting SAP connection...', ['config' => $config]);

            $c= new SapConnection($config);

            $f=$c->getFunction('ZPHPVE_PO_DETAILS');


            Log::info('Connection established successfully',$f);

            $result = $f->invoke();

            Log::info('Function invoked successfully, processing data.');

             if (!empty($result['DATA'])) {
                $data = $result['DATA'];

                // Store data in temporary tables
                $this->storePOMasterData($data);
                $this->storePODeliveryData($data);
            }
                    
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


    public function storePOMasterData($data){
         
        foreach ($data as $call) {
            $cleanedData = array_map(function ($value) {
                return mb_convert_encoding(trim((string) $value), 'UTF-8', 'auto');
            }, $call);
            DB::insert('insert into temp_v002_POMasterData (EBELN, EBELP, LIFNR, ZTERM, TEXT1, LOEKZ, BEDAT, BUKRS, BUTXT, EKORG, EKOTX, EKGRP, EKNAM, WERKS, 
                PLANT_NAME1, LGORT, LGOBE, AEDAT, VERKF, TELF1, FRGKE, TXZ01, MATNR, NETPR, PEINH, NETWR, BRTWR, MENGE,
                MEINS, WAERS, ADD_TEXT1, ADD_TEXT2, ADD_TEXT3, ADD_TEXT4, ADD_TEXT5, CREATED_ON, CREATED_AT, CREATED_BY) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
                array(
                    trim($cleanedData['EBELN']),
                    trim($cleanedData['EBELP']),
                    trim($cleanedData['LIFNR']),
                    trim($cleanedData['ZTERM']),
                    trim($cleanedData['TEXT1']),
                    trim($cleanedData['LOEKZ']),
                    trim($cleanedData['BEDAT']),
                    trim($cleanedData['BUKRS']),
                    trim($cleanedData['BUTXT']),
                    trim($cleanedData['EKORG']),
                    trim($cleanedData['EKOTX']),
                    trim($cleanedData['EKGRP']),
                    trim($cleanedData['EKNAM']),
                    trim($cleanedData['WERKS']),
                    trim($cleanedData['PLANT_NAME1']),
                    trim($cleanedData['LGORT']),
                    trim($cleanedData['LGOBE']),
                    trim($cleanedData['AEDAT']),
                    trim($cleanedData['VERKF']),
                    trim($cleanedData['TELF1']),
                    trim($cleanedData['FRGKE']),
                    trim($cleanedData['TXZ01']),
                    trim($cleanedData['MATNR']),
                    trim($cleanedData['NETPR']),
                    trim($cleanedData['PEINH']),
                    trim($cleanedData['NETWR']),
                    trim($cleanedData['BRTWR']),
                    trim($cleanedData['MENGE']),
                    trim($cleanedData['MEINS']), 
                    trim($cleanedData['WAERS']), 
                    trim($cleanedData['ADD_TEXT1']), 
                    trim($cleanedData['ADD_TEXT2']), 
                    trim($cleanedData['ADD_TEXT3']), 
                    trim($cleanedData['ADD_TEXT4']), 
                    trim($cleanedData['ADD_TEXT5']), 
                    trim($cleanedData['CREATED_ON']), 
                    trim($cleanedData['CREATED_AT']), 
                    trim($cleanedData['CREATED_BY']),

                )
            );
        }

        $DataCount = count($data) ;
        $Count = DB::table('temp_v002_POMasterData')->count();
        if ($Count == $DataCount) {
            DB::select('Call USP_InsertingData(?)', array("POMasterData"));
        }

        //  DB::select('Call USP_InsertingData(?)', array("POMasterData"));

    }

     public function storePODeliveryData($data)
    {
        foreach ($data as $call) {
             $cleanedData = array_map(function ($value) {
                return mb_convert_encoding(trim((string) $value), 'UTF-8', 'auto');
            }, $call);
            DB::insert('insert into temp_v003_PODelivery (EBELN, EBELP, ETENR, EINDT, SLFDT, MENGE, AMENG,
             WEMNG, WAMNG, UZEIT, BEDAT, CHARG, MEINS,  ADD_TEXT1, ADD_TEXT2, ADD_TEXT3, ADD_TEXT4, ADD_TEXT5) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
                array(
                    trim($cleanedData['EBELN']),
                    trim($cleanedData['EBELP']),
                    trim($cleanedData['ETENR']),
                    trim($cleanedData['EINDT']),
                    trim($cleanedData['SLFDT']),
                    trim($cleanedData['MENGE']),
                    trim($cleanedData['AMENG']),
                    trim($cleanedData['WEMNG']),
                    trim($cleanedData['WAMNG']),
                    trim($cleanedData['UZEIT']),
                    trim($cleanedData['BEDAT']),
                    trim($cleanedData['CHARG']),
                    trim($cleanedData['MEINS']),
                    trim($cleanedData['ADD_TEXT1']), 
                    trim($cleanedData['ADD_TEXT2']), 
                    trim($cleanedData['ADD_TEXT3']), 
                    trim($cleanedData['ADD_TEXT4']), 
                    trim($cleanedData['ADD_TEXT5']), 

                )
            );
        }
        $DataCount = count($data) ;
        $Count = DB::table('temp_v003_PODelivery')->count();
        if ($Count == $DataCount) {
            DB::select('Call USP_InsertingData(?)', array("PODeliveryData"));
        }

        //  DB::select('Call USP_InsertingData(?)', array("PODeliveryData"));

    }
 
}
