<?php 

namespace Fleetbase\VendorsForSuppliers\Models;

use Fleetbase\Models\Model;
use Fleetbase\Traits\HasUuid;
use Fleetbase\Traits\HasApiModelBehavior;
use Fleetbase\Traits\Searchable;

class VendorsForSupplier extends Model
{
    use HasUuid, HasApiModelBehavior, Searchable;

    /**
     * The database table used by the model.
     */
    protected $table = 'vendors_for_suppliers';

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Searchable columns.
     */
    protected $searchableColumns = ['name', 'email', 'phone'];

    /**
     * Get the company that owns the vendor.
     */
    public function company()
    {
        return $this->belongsTo(\Fleetbase\Models\Company::class, 'company_uuid', 'uuid');
    }

    /**
     * Get the QR code information.
     */
    public function qrCode()
    {
        return $this->hasOne(VendorQrCode::class, 'vendor_uuid', 'uuid');
    }

    /**
     * Scope to filter active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the full QR code URL.
     */
    public function getQrCodeUrlAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return url($value);
        }
        return $value;
    }
}
