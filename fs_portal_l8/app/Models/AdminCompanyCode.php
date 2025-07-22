<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//changes made by niveditha
class AdminCompanyCode extends Model
{
    use HasFactory;

    protected $table = 'admin_company_code';
    public $timestamps = false;

    protected $fillable = [
        'admin_email',
        'company_code',
        'emp_id',
        'po',
        'mobile',
        'status',
        'role',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'admin_email', 'email');
    }
}
//changes end
