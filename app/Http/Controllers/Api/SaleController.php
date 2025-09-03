<?php

namespace App\Http\Controllers\Api;

use App\Events\SaleFinalized;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => Rule::forEach(function ($value, $attribute, $data) {
                $index = explode('.', $attribute)[1];
                $productId = $data["items.$index.product_id"] ?? null;
                $max = $productId ? Inventory::where('product_id', $productId)->value('quantity') ?? 0 : 0;
                return ['required', 'integer', 'min:1', "max:$max"];
            }),
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $totalCost = 0;

            foreach ($validated['items'] as $itemData) {
                $product = Product::with('inventory')->find($itemData['product_id']);

                if (!$product->inventory || $product->inventory->quantity < $itemData['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Estoque insuficiente para o produto {$product->name} (SKU: {$product->sku})",
                    ]);
                }

                $totalAmount += $product->sale_price * $itemData['quantity'];
                $totalCost += $product->cost_price * $itemData['quantity'];
            }

            $sale = Sale::create([
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'total_profit' => $totalAmount - $totalCost,
                'status' => 'finalized'
            ]);

            foreach ($validated['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);
                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $product->sale_price,
                    'unit_cost' => $product->cost_price,
                ]);
            }

            DB::commit();
            event(new SaleFinalized($sale));

            return response()->json(['message' => 'Venda registrada com sucesso!', 'sale' => $sale->load('items')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao registrar a venda.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $sale = Sale::with('items.product')->find($id);

        if (!$sale) {
            return response()->json(['message' => 'Venda não encontrada'], 404);
        }

        return response()->json($sale);
    }
}
