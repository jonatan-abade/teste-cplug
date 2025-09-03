<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Inventory;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_sale()
    {
        $product = Product::factory()->create(['sale_price' => 100, 'cost_price' => 50]);
       
        Inventory::create(['product_id' => $product->id, 'quantity' => 10]);

        $saleData = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ];

        $response = $this->postJson('/api/sales', $saleData);

        $response->assertStatus(201)
                 ->assertJsonPath('sale.total_amount', 200)
                 ->assertJsonPath('sale.total_profit', 100);

        $this->assertDatabaseHas('sales', ['total_amount' => 200]);
        $this->assertDatabaseHas('sale_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_cannot_create_sale_with_insufficient_stock()
    {
        $product = Product::factory()->create();
        Inventory::create(['product_id' => $product->id, 'quantity' => 1]);

        $saleData = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5]
            ]
        ];

        $response = $this->postJson('/api/sales', $saleData);

        $response->assertStatus(422);
    }
}