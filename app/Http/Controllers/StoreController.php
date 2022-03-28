<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{

    public function view(Request $request)
    {
        $this->validate($request, [
            // Regular expression will match the following: "S00001", "S99999", "S100000"
            'store_code'     => 'required|string|min:6|exists:stores|regex:/S\d{5,}/',
        ]);

        try {
            $store = Store::where('store_code', $request->store_code)->first();

            return response()->json([
                'store' => $store->getFullJsonResponseArray(),
                'message' => 'Store Retrieved'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Store Retrieval Failed'], 500);
        }
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name'            => 'required|string|between:2,100',
            'user_main_store' => 'nullable|boolean',
        ]);

        try {
            $store = Store::create([
                'name' => $request->name,
            ]);

            $user = Auth::user();
            if (! $user->mainStore()->exists() || $request->user_main_store) {
                $user->stores()->updateExistingPivot($user, ['user_main_store' => false]);
                $user->stores()->attach($store->id, [
                    'user_main_store' => true,
                ]);
            } else {
                $user->stores()->attach($store->id);
            }

            return response()->json([
                'item' => $store->getFullJsonResponseArray(),
                'message' => 'Store Created'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Store Creation Failed'], 500);
        }
    }
}
