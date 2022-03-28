<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{

    public function view(Request $request)
    {
        $this->validate($request, [
            'ean13_bar_code' => [
                'required',
                'string',
                'digits:13',
                'numeric',
                Rule::unique('items', 'ean13_bar_code')->where(
                    fn ($query) => $query->where('deleted_at', null)
                ),
            ],
            // Regular expression will match the following: "S00001", "S99999", "S100000"
            'store_code'     => 'nullable|string|min:6|exists:stores|regex:/S\d{5,}/',
        ]);

        try {
            $item = Item::where('ean13_bar_code', $request->ean13_bar_code)->first();

            $store = ! empty($request->store_code)
                ? Store::where('store_code', $request->store_code)->first()
                : Auth::user()->mainStore()->first();

            if (! empty($store)) {
                $inventory = new Inventory;
                $inventory->store_id      = $store->id;
                $inventory->item_id       = $item->id;
                $inventory->item_quantity = $request->amount ?? 0;
                $inventory->save();
            }

            return response()->json([
                'item'    => $item->getFullJsonResponseArray(),
                'message' => 'Item Retrieved'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Item Retrieval Failed'], 500);
        }
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name'           => 'required|string|between:2,100',
            'ean13_bar_code' => [
                'required',
                'string',
                'digits:13',
                'numeric',
                Rule::unique('items', 'ean13_bar_code')->where(
                    fn ($query) => $query->where('deleted_at', null)
                ),
            ],
            'default_price'  => 'required|numeric|min:0',
            'default_markup' => 'required|numeric|min:0',
            'amount'         => 'nullable|numeric|min:0',
            // Regular expression will match the following: "S00001", "S99999", "S100000"
            'store_code'     => 'nullable|string|min:6|exists:stores|regex:/S\d{5,}/',
        ]);

        try {
            $trashedItem = Item::onlyTrashed()->where('ean13_bar_code', $request->ean13_bar_code)->first();
            if (! empty($trashedItem)) {
                $trashedItem->restore();
                $item = $trashedItem;
                $item->update([
                    'name' => $request->name,
                    'default_price' => $request->default_price,
                    'default_markup' => $request->default_markup,
                ]);
            } else {
                $item = new Item([
                    'name' => $request->name,
                    'default_price' => $request->default_price,
                    'default_markup' => $request->default_markup,
                ]);
                $item->ean13_bar_code = $request->ean13_bar_code;
                $item->save();
            }

            $store = ! empty($request->store_code)
                ? Store::where('store_code', $request->store_code)->first()
                : Auth::user()->mainStore()->first();

            if (! empty($store)) {
                $inventory = new Inventory;
                $inventory->store_id      = $store->id;
                $inventory->item_id       = $item->id;
                $inventory->item_quantity = $request->amount ?? 0;
                $inventory->save();
            }

            return response()->json([
                'item'    => $item->getFullJsonResponseArray(),
                'message' => 'Item Added'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Item Addition Failed'], 500);
        }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'ean13_bar_code' => [
                'required',
                'digits:13',
                'numeric',
                Rule::exists('items', 'ean13_bar_code')->where(
                    fn ($query) => $query->where('deleted_at', null)
                ),
            ],
            'name'           => 'nullable|string|between:2,100',
            'default_price'  => 'nullable|numeric|min:0',
            'default_markup' => 'nullable|numeric|min:0',
        ]);

        try {
            $item = Item::where('ean13_bar_code', $request->ean13_bar_code)->first();
            $item->update([
                'name'           => $request->name ?? $item->name,
                'default_price'  => $request->default_price ?? $item->default_price,
                'default_markup' => $request->default_markup ?? $item->default_markup,
            ]);

            return response()->json([
                'item'    => $item->getFullJsonResponseArray(),
                'message' => 'Item Updated'
            ], 202);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Item Update Failed'], 500);
        }
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'ean13_bar_code' => [
                'required',
                'digits:13',
                'numeric',
                Rule::exists('items', 'ean13_bar_code')->where(
                    fn ($query) => $query->where('deleted_at', null)
                ),
            ],
        ]);

        try {
            $item = Item::where('ean13_bar_code', $request->ean13_bar_code)->first();
            $item->inventories()->delete();
            $item->delete();
            return response()->json(['message' => 'Item Deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Item Delete Failed'], 500);
        }
    }

    public function restore(Request $request)
    {
        $this->validate($request, [
            'ean13_bar_code' => [
                'required',
                'digits:13',
                'numeric',
                Rule::exists('items', 'ean13_bar_code')->where(
                    fn ($query) => $query->where('deleted_at', '<>', null)
                ),
            ],
        ]);

        try {
            $item = Item::onlyTrashed()->where('ean13_bar_code', $request->ean13_bar_code)->first();
            $item->restore();
            $item->inventories()->restore();
            return response()->json([
                'item'    => $item->getFullJsonResponseArray(),
                'message' => 'Item Restored'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Item Restore Failed'], 500);
        }
    }
}
