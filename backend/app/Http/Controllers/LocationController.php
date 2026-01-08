<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\DeliveryLocation;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $delivery = Delivery::where('driver_id', auth()->id())
            ->where('id', $request->delivery_id)
            ->firstOrFail();

        $location = DeliveryLocation::create([
            'delivery_id' => $delivery->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json($location);
    }
}
