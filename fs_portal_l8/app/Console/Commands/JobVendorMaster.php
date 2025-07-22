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


class JobVendorMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:vendormaster';

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
        $config = [
            'ashost' => env('SAP_HOST'),
            'sysnr' => env('SAP_SYSNR'),
            'client' => env('SAP_CLIENT'),
            'user' => env('SAP_USER'),
            'passwd' => env('SAP_PWD'),
            'trace' => SapConnection::TRACE_LEVEL_OFF,
        ];

        try {
            $startTime = Carbon::now(new DateTimeZone('Asia/Kolkata'))->toDateTimeString();

          $c = new SapConnection($config);  

          $f = $c->getFunction('ZPHPVE_VENDOR_DETAIL'); //Function name

          $result = $f->invoke();

          $this->storeVendorMasterData($result['VENDOR_DETAIL']) ;

    
          $endTime = Carbon::now(new DateTimeZone('Asia/Kolkata'))->toDateTimeString();
      
          $start = Carbon::parse($startTime);
          $end = Carbon::parse($endTime);
      
          $difference = $end->diffInSeconds($start);
      
          DB::select(
              'call USP_Job_Logs (?,?,?,?,?)',
              array("1", $startTime, "AUto", $difference, "Success")
            );
          } catch (\Throwable $th) {
      
            $currentTime = Carbon::now(new DateTimeZone('Asia/Kolkata'))->toDateTimeString();
             $endTime = Carbon::now(new DateTimeZone('Asia/Kolkata'))->toDateTimeString();
      
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);
        
            $difference = $end->diffInSeconds($start);
      
            DB::select(
              'call USP_Job_Logs (?,?,?,?,?)',
              array("1", $startTime, "Auto", $difference, "FAIL")
            );
          }

    }



    public function storeVendorMasterData($VendorData){
        foreach ($VendorData as $clean) {
            $call = array_map(function ($value) {
                return mb_convert_encoding(trim((string) $value), 'UTF-8', 'auto');
            }, $clean);
            DB::insert('insert into temp_v001_vendor (LIFNR, LAND1, NAME1, NAME2, NAME3, NAME4  TELF1, EMAIL, SORT1, SORT2, HOUSE_NUM1, STREET, STR_SUPPL1, STR_SUPPL2, 
                BUILDING, FLOOR, ROOMNUMBER, REGION, CITY1, CITY2, LOCCO, PFACH, PSTL2, TEXT1, TEXT2, TEXT3, TEXT4, TEXT5) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
                array(
                    trim($call['LIFNR']),
                    trim($call['LAND1']),
                    trim($call['NAME1']),
                    trim($call['NAME2']),
                    trim($call['NAME3']),
                    trim($call['NAME4']),
                    trim($call['TELF1']),
                    trim($call['EMAIL']),
                    trim($call['SORT1']),
                    trim($call['SORT2']),
                    trim($call['HOUSE_NUM1']),
                    trim($call['STREET']),
                    trim($call['STR_SUPPL1']),
                    trim($call['STR_SUPPL2']),
                    trim($call['BUILDING']),
                    trim($call['FLOOR']),
                    trim($call['ROOMNUMBER']),
                    trim($call['REGION']),
                    trim($call['CITY1']),
                    trim($call['CITY2']),
                    trim($call['LOCCO']),
                    trim($call['PFACH']),
                    trim($call['PSTL2']),
                    trim($call['TEXT1']),
                    trim($call['TEXT2']),
                    trim($call['TEXT3']),
                    trim($call['TEXT4']),
                    trim($call['TEXT5']),));
            }

        $DataCount = count($VendorData) ;
        $Count = DB::table('temp_v001_vendor')->count();
        if ($Count == $DataCount) {
            DB::select('Call USP_InsertingData(?)', array("VendorMasterData"));
        }


    }

}
