<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPacking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'packing_id',
        'transaction_id',
        'notes'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'quantity_packed' => 'decimal:2',
    ];

    /**
     * Relationship with Packing model
     */
    public function packing()
    {
        return $this->belongsTo(Packing::class, 'packing_id');
    }

    /**
     * Relationship with TransactionSellLine model
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}