<?php
// ══════════════════════════════════════════
// app/Models/Wallet.php
// ══════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];
    protected $casts = ['balance' => 'decimal:2'];

    public function user() { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(WalletTransaction::class)->latest(); }

    public function credit(float $amount, string $type, string $desc, ?string $provider = null): WalletTransaction
    {
        $before = $this->balance;
        $this->increment('balance', $amount);
        $this->refresh();
        return $this->transactions()->create([
            'user_id' => $this->user_id, 'type' => $type, 'amount' => $amount,
            'balance_before' => $before, 'balance_after' => $this->balance,
            'description' => $desc, 'status' => 'success', 'provider' => $provider,
        ]);
    }

    public function debit(float $amount, string $desc): WalletTransaction
    {
        if ($this->balance < $amount) throw new \Exception('Saldo E-Wallet tidak cukup.');
        $before = $this->balance;
        $this->decrement('balance', $amount);
        $this->refresh();
        return $this->transactions()->create([
            'user_id' => $this->user_id, 'type' => 'payment', 'amount' => $amount,
            'balance_before' => $before, 'balance_after' => $this->balance,
            'description' => $desc, 'status' => 'success',
        ]);
    }
}