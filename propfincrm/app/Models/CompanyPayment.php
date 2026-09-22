<?php

namespace App\Models;

use App\Models\Concerns\UsesCentralConnection;
use Illuminate\Database\Eloquent\Model;

class CompanyPayment extends Model
{
    use UsesCentralConnection;

    protected $fillable = [
        'company_id',
        'subscription_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'status',
        'notes',
        'recorded_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription()
    {
        return $this->belongsTo(CompanySubscription::class, 'subscription_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function formattedAmount(): string
    {
        return ($this->currency === 'INR' ? '₹' : ($this->currency . ' ')) . number_format($this->amount, 2);
    }

    public static function paymentMethods(): array
    {
        return [
            'bank_transfer' => 'Bank Transfer (NEFT/RTGS/IMPS)',
            'upi' => 'UPI / QR Code',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'card' => 'Debit / Credit Card',
            'stripe' => 'Stripe',
            'razorpay' => 'Razorpay',
            'other' => 'Other / Offline',
        ];
    }
}
