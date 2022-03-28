<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Store;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{

    public function add(Request $request)
    {
        $this->validate($request, [
            'amount'         => 'required|integer|min:1',
            'store_code'     => 'nullable|string|min:6|exists:stores|regex:/S\d{5,}/',
            'ean13_bar_code' => [
                'required',
                'string',
                'digits:13',
                'numeric',
                Rule::exists('items', 'ean13_bar_code')->where(
                    fn ($query) => $query->where('deleted_at', null)
                ),
            ],
        ]);

        try {
            $store = ! empty($request->store_code)
                ? Store::where('store_code', $request->store_code)->first()
                : Auth::user()->mainStore()->first();
    
            $inventory = $store->inventories()
                ->whereHas('item', fn ($query) => $query->where('ean13_bar_code', $request->ean13_bar_code))
                ->with('item')
                ->first();

            if (empty($inventory)) {
                $inventory = new Inventory(['item_quantity' => $request->amount]);
                $inventory->item_id = Item::where('ean13_bar_code', $request->ean13_bar_code)->first()->id;
                $inventory->store_id = $store->id;
            } else {
                $inventory->item_quantity += $request->amount;
            }
            $inventory->save();

            return response()->json([
                'inventory' => $inventory->getFullJsonResponseArray(),
                'item' => $inventory->item->getSimplifiedJsonResponseArray(),
                'store' => $store->getFullJsonResponseArray(),
                'message' => 'Inventory Added'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Inventory Addition Failed'], 500);
        }
    }

    public function subtract(Request $request)
    {
        $this->validate($request, [
            'amount'         => 'required|integer|min:1',
            'store_code'     => 'nullable|string|min:6|exists:stores|regex:/S\d{5,}/',
            'ean13_bar_code' => [
                'required',
                'string',
                'digits:13',
                'numeric',
                Rule::exists('items', 'ean13_bar_code')->where(
                    fn ($query) => $query->where('deleted_at', null)
                ),
            ],
        ]);

        try {
            $store = ! empty($request->store_code)
                ? Store::where('store_code', $request->store_code)->first()
                : Auth::user()->mainStore()->first();
    
            $inventory = $store->inventories()
                ->whereHas('item', fn ($query) => $query->where('ean13_bar_code', $request->ean13_bar_code))
                ->with('item')
                ->first();

            if (empty($inventory)) {
                $inventory = new Inventory(['item_quantity' => 0]);
                $inventory->item_id = Item::where('ean13_bar_code', $request->ean13_bar_code)->first()->id;
                $inventory->store_id = $store->id;
                $inventory->save();
            }

            if ($request->amount > $inventory->item_quantity) {
                return response()->json(['message' => 'Cannot Subtract more than the Existing Inventory'], 400);
            }

            $inventory->item_quantity -= $request->amount;
            $inventory->save();

            return response()->json([
                'inventory' => $inventory->getFullJsonResponseArray(),
                'item' => $inventory->item->getSimplifiedJsonResponseArray(),
                'store' => $store->getFullJsonResponseArray(),
                'message' => 'Inventory Subtracted'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Inventory Subtraction Failed'], 500);
        }
    }

    public function view(Request $request)
    {
        $this->validate($request, [
            'store_code'     => 'nullable|string|min:6|exists:stores|regex:/S\d{5,}/',
            'ean13_bar_code' => [
                'required',
                'string',
                'digits:13',
                'numeric',
                Rule::exists('items', 'ean13_bar_code')->where(
                    fn ($query) => $query->where('deleted_at', null)
                ),
            ],
        ]);

        try {
            $store = ! empty($request->store_code)
                ? Store::where('store_code', $request->store_code)->first()
                : Auth::user()->mainStore()->first();
    
            $inventory = $store->inventories()
                ->whereHas('item', fn ($query) => $query->where('ean13_bar_code', $request->ean13_bar_code))
                ->with('item')
                ->first();

            if (empty($inventory)) {
                $inventory = new Inventory(['item_quantity' => 0]);
                $inventory->item_id = Item::where('ean13_bar_code', $request->ean13_bar_code)->first()->id;
                $inventory->store_id = $store->id;
                $inventory->save();
            }

            return response()->json([
                'inventory' => $inventory->getFullJsonResponseArray(),
                'item' => $inventory->item->getSimplifiedJsonResponseArray(),
                'store' => $store->getFullJsonResponseArray(),
                'message' => 'Inventory Retrieved'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Inventory Retrieval Failed'], 500);
        }
    }

}
