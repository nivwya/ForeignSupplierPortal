<?php
//changes made by niveditha
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'v001_vendor';
    protected $primaryKey = 'LIFNR';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'LIFNR', 'LAND1', 'NAME1', 'NAME2', 'NAME3', 'NAME4',
        'TELF1', 'EMAIL', 'SORT1', 'SORT2', 'HOUSE_NUM1',
        'STREET', 'STR_SUPPL1', 'STR_SUPPL2', 'BUILDING', 'FLOOR',
        'ROOMNUMBER', 'REGION', 'CITY1', 'CITY2', 'LOCCO', 'PFACH',
        'PSTL2', 'TEXT1', 'TEXT2', 'TEXT3', 'TEXT4', 'TEXT5'
    ];

    public function poMasters()
    {
        return $this->hasMany(PoMaster::class, 'vendor_account_no', 'LIFNR');
    }

    public function grnDeliveries()
    {
        return $this->hasMany(GrnDelivery::class, 'vendor_acc_no', 'LIFNR');
    }
}

//changes mend