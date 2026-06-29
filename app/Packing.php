<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Packing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'packing_name',
        'packing_date',
        'created_by',
        'business_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'packing_date' => 'date',
    ];

    /**
     * Relationship with Transaction model
     */
 

    /**
     * Relationship with User model (who created the packing)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with Business model
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * Relationship with DetailPacking model
     */
    public function details()
    {
        return $this->hasMany(DetailPacking::class, 'packing_id');
    }
}