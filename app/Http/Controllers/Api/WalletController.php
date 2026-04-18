<?php
// ══════════════════════════════════════════════════════
// app/Http/Controllers/Api/WalletController.php
// ══════════════════════════════════════════════════════

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    // GET /api/wallet
    public function show(Request $request)
    {
        $w = $this->wallet($request->user()->id);
        return response()->json(['data' => ['id' => $w->id, 'user_id' => $w->user_id, 'balance' => $w->balance]]);
    }

    // GET /api/wallet/transactions
    public function transactions(Request $request)
    {
        $w    = $this->wallet($request->user()->id);
        $txns = $w->transactions()->paginate(30);
        return response()->json([
            'data' => collect($txns->items())->map(fn($t) => [
                'id' => $t->id, 'type' => $t->type, 'amount' => $t->amount,
                'balance_before' => $t->balance_before, 'balance_after' => $t->balance_after,
                'description' => $t->description, 'status' => $t->status,
                'created_at' => $t->created_at->toDateTimeString(),
            ]),
        ]);
    }

    // POST /api/wallet/topup  { amount, provider }
    public function topup(Request $request)
    {
        $request->validate([
            'amount'   => 'required|numeric|min:1|max:50000',
            'provider' => 'required|in:gopay,ovo,dana,shopeepay',
        ]);

        $w = $this->wallet($request->user()->id);
        DB::transaction(function () use ($w, $request) {
            $w->credit($request->amount, 'topup',
                "Top up via {$request->provider}", $request->provider);
        });

        return response()->json([
            'message' => 'Top up berhasil!',
            'data'    => ['id' => $w->id, 'user_id' => $w->user_id, 'balance' => $w->refresh()->balance],
        ]);
    }

    private function wallet(int $userId): Wallet
    {
        return Wallet::firstOrCreate(['user_id' => $userId], ['balance' => 0]);
    }
}
