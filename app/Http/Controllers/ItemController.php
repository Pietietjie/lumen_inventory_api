<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
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
            return response()->json([
                'item' => $item->only([
                    'name',
                    'ean13_bar_code',
                    'default_price',
                    'default_markup',
                    'default_markup_price',
                    'display_default_markup_price',
                    'default_sell_price',
                    'display_default_sell_price',
                ]),
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
                'item' => $item->only([
                    'name',
                    'ean13_bar_code',
                    'default_price',
                    'default_markup',
                    'default_markup_price',
                    'display_default_markup_price',
                    'default_sell_price',
                    'display_default_sell_price',
                ]),
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
            return response()->json(['message' => 'Item Restored'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Item Restore Failed'], 500);
        }
    }
}
