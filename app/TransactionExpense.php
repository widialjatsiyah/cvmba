<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionExpense extends Model
{
    protected $table = 'transaction_expenses';
    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_sub_category_id');
    }
}