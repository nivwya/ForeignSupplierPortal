<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Log;
use Carbon\Carbon; 
use DateTimeZone;
use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;

class SAPController extends Controller {

 
    public function getJobs(Request $request)
    {
        $data = DB::table('v010_jobs as j')
            ->leftJoin('v001_jobs as l', 'j.job_id', '=', 'l.job_id')
            ->select(
                'j.job_id',
                'j.job_name',
                'j.job_symbol',
                'j.execution_cycle',
                DB::raw('(SELECT status FROM v001_jobs WHERE job_id = j.job_id ORDER BY execute_at DESC LIMIT 1) as last_status'),
                DB::raw('(SELECT status FROM v001_jobs WHERE job_id = j.job_id ORDER BY execute_at DESC LIMIT 1) as type'),
                DB::raw('(SELECT execute_at FROM v001_jobs WHERE job_id = j.job_id ORDER BY execute_at DESC LIMIT 1) as last_run_time')
            )
            ->groupBy('j.job_id', 'j.job_name', 'j.job_symbol', 'j.execution_cycle')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Vendor Master DAta start
    public function VendorProfile(Request $request)
    {
    ini_set('memory_limit', '1024M');   // Increase memory
    set_time_limit(0); 
        
        $startTime = Carbon::now(new DateTimeZone('Asia/Kuwait'))->toDateTimeString();

        $config = [
        'ashost' => '192.168.100.59',
        'sysnr' => '00',
        'client' => '500',
        'user' => 'ser_oncall',
        'passwd' => 'Welcome@123',
            // 'ashost' => env('SAP_HOST'),
            // 'sysnr' => env('SAP_SYSNR'),
            // 'client' => env('SAP_CLIENT'),
            // 'user' => env('SAP_USER'),
            // 'passwd' => env('SAP_PWD'),
            'trace' => SapConnection::TRACE_LEVEL_OFF,
        ];

        
        try {
          Log::info('Attempting SAP connection...', ['config' => $config]);
          $c = new SapConnection($config);  

          Log::info('SAP connection established successfully.');

          $f = $c->getFunction('ZPHPVE_VENDOR_DETAIL');

          Log::info('Function ZPHPVE_VENDOR_DETAIL found in SAP.');

            $result = $f->invoke();

          Log::info('Function invoked successfully, processing data.');
          
          $this->storeVendorMasterData($result['VENDOR_DETAIL']) ;

          $endTime = Carbon::now(new DateTimeZone('Asia/Kuwait'))->toDateTimeString();
       
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        $difference = $end->diffInSeconds($start);

            DB::select(
              'call USP_Job_Logs (?,?,?,?,?)',
              array("1", $startTime, "Manual", $difference, "Success")
            );
          $Dataout = array(["Result" => $result, "Config" =>  $config]);
          Log::info('Vendor profile processed and logged successfully.', ['duration_seconds' => $difference]);

          return $Dataout;

          } catch (\Throwable $th) {

            $endTime = Carbon::now(new DateTimeZone('Asia/Kuwait'))->toDateTimeString();
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);
            $difference = $end->diffInSeconds($start);
            DB::select(
              'call USP_Job_Logs (?,?,?,?,?)',
              array("1", $startTime, "Manual", $difference, "FAIL")
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


    public function storeVendorMasterData($VendorData){
        foreach ($VendorData as $call) {

            // Clean all fields using array_map + custom function
            $cleanedData = array_map(function ($value) {
                return mb_convert_encoding(trim((string) $value), 'UTF-8', 'auto');
            }, $call);
            
            DB::insert('insert into temp_v001_vendor (LIFNR, LAND1, NAME1, NAME2,NAME3, NAME4, TELF1, EMAIL, SORT1, SORT2, HOUSE_NUM1, STREET, STR_SUPPL1, STR_SUPPL2, 
                BUILDING, FLOOR, ROOMNUMBER, REGION, CITY1, CITY2, LOCCO, PFACH, PSTL2, TEXT1, TEXT2, TEXT3, TEXT4, TEXT5) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
                array(
                    trim($cleanedData['LIFNR']),
                    trim($cleanedData['LAND1']),
                    trim($cleanedData['NAME1']),
                    trim($cleanedData['NAME2']),
                    trim($cleanedData['NAME3']),
                    trim($cleanedData['NAME4']),
                    trim($cleanedData['TELF1']),
                    trim($cleanedData['EMAIL']),
                    trim($cleanedData['SORT1']),
                    trim($cleanedData['SORT2']),
                    trim($cleanedData['HOUSE_NUM1']),
                    trim($cleanedData['STREET']),
                    trim($cleanedData['STR_SUPPL1']),
                    trim($cleanedData['STR_SUPPL2']),
                    trim($cleanedData['BUILDING']),
                    trim($cleanedData['FLOOR']),
                    trim($cleanedData['ROOMNUMBER']),
                    trim($cleanedData['REGION']),
                    trim($cleanedData['CITY1']),
                    trim($cleanedData['CITY2']),
                    trim($cleanedData['LOCCO']),
                    trim($cleanedData['PFACH']),
                    trim($cleanedData['PSTL2']),
                    trim($cleanedData['TEXT1']),
                    trim($cleanedData['TEXT2']),
                    trim($cleanedData['TEXT3']),
                    trim($cleanedData['TEXT4']),
                    trim($cleanedData['TEXT5']),
                )
            );
        }

        $DataCount = count($VendorData) ;
        $Count = DB::table('temp_v001_vendor')->count();
        if ($Count == $DataCount) {
            DB::select('Call USP_InsertingData(?)', array("VendorMasterData"));
        }

    }

    // Vendor Master Data Ends

    // PO MASTER AND DEliveyr API
    public function POMasterData(Request $request)
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
                array("2", $startTime, "Manual", $difference, "Success")
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
              array("2", $startTime, "Manual", $difference, "FAIL")
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


    // PO MASTER AND DEliveyr API ENDS 

}

