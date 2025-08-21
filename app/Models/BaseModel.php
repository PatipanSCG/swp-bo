<?php

// สร้างไฟล์ app/Models/BaseModel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * Default connection สำหรับ models ที่ไม่ใช่ auth
     */
    protected $connection = 'sqlsrv_secondary';
    
    /**
     * Override การสร้าง query builder เพื่อบังคับใช้ SQL Server
     */
    public function newQuery()
    {
        $query = parent::newQuery();
        
        // บังคับใช้ sqlsrv_secondary connection
        return $query->setConnection($this->getConnection());
    }
    
    /**
     * Override getConnection เพื่อให้แน่ใจว่าใช้ SQL Server
     */
    public function getConnection()
    {
        return static::resolveConnection('sqlsrv_secondary');
    }
}

// แล้วแก้ไข Models ทั้งหมดให้ extend จาก BaseModel แทน Model

// app/Models/Station.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends BaseModel
{
    use HasFactory;

    protected $table = 'stations';
    protected $primaryKey = 'StationID';
    
    // ... rest of the model code
}

// app/Models/Quotation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends BaseModel
{
    use HasFactory;

    protected $table = 'quotations';
    protected $primaryKey = 'QuotationID';
    
    // ... rest of the model code
}

// app/Models/Customer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends BaseModel
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'CustomerID';
    
    // ... rest of the model code
}

// app/Models/Province.php
namespace App\Models;

class Province extends BaseModel
{
    protected $table = 'sys_provinces';
    protected $primaryKey = 'Id';
    public $timestamps = false;
    
    // ... rest of the model code
}

// app/Models/District.php
namespace App\Models;

class District extends BaseModel
{
    protected $table = 'sys_districts';
    protected $primaryKey = 'Id';
    public $timestamps = false;
    
    // ... rest of the model code
}

// app/Models/Subdistrict.php
namespace App\Models;

class Subdistrict extends BaseModel
{
    protected $table = 'sys_subdistricts';
    protected $primaryKey = 'Id';
    public $timestamps = false;
    
    // ... rest of the model code
}