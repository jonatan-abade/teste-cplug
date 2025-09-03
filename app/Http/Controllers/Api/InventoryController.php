<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory = Inventory::firstOrNew(['product_id' => $validated['product_id']]);
        $inventory->quantity = ($inventory->quantity ?? 0) + $validated['quantity'];
        $inventory->last_updated = now();
        $inventory->save();

        return response()->json(['message' => 'Estoque atualizado com sucesso!', 'inventory' => $inventory], 200);
    }

    public function index()
    {
        $inventory = Inventory::with('product')->get();

        $totalValue = $inventory->sum(function ($item) {
            return $item->quantity * $item->product->cost_price;
        });

        $projectedProfit = $inventory->sum(function ($item) {
            return $item->quantity * ($item->product->sale_price - $item->product->cost_price);
        });

        return response()->json([
            'total_inventory_value' => $totalValue,
            'projected_profit' => $projectedProfit,
            'items' => $inventory,
        ]);
    }
}
