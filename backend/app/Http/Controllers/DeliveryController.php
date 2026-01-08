<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\DeliveryHistory;
use App\Models\ProofOfDelivery;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Get all assigned deliveries for the current user (driver).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Assuming we want today's deliveries or all active ones
        // Just returning all assigned/active for simplicity as per requirement "Assigned Deliveries List"
        $deliveries = Delivery::where('driver_id', $user->id)
            ->whereIn('status', ['assigned', 'on_way']) // active statuses
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($deliveries);
    }

    /**
     * Get delivery history.
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        
        $deliveries = Delivery::where('driver_id', $user->id)
            ->whereIn('status', ['delivered', 'failed'])
            ->with(['order', 'proofOfDelivery', 'history'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($deliveries);
    }

    /**
     * Get single delivery details.
     */
    public function show($id)
    {
        $delivery = Delivery::with(['order', 'locations', 'proofOfDelivery', 'history'])
            ->where('driver_id', auth()->id())
            ->findOrFail($id);

        return response()->json($delivery);
    }

    /**
     * Update delivery status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:assigned,on_way,delivered,failed',
        ]);

        $delivery = Delivery::where('driver_id', auth()->id())->findOrFail($id);
        
        $oldStatus = $delivery->status;
        $newStatus = $request->status;
        
        if ($oldStatus === $newStatus) {
            return response()->json($delivery);
        }

        DB::transaction(function () use ($delivery, $newStatus) {
            $delivery->status = $newStatus;
            
            if ($newStatus === 'on_way' && !$delivery->start_time) {
                $delivery->start_time = now();
            }
            if (in_array($newStatus, ['delivered', 'failed']) && !$delivery->end_time) {
                $delivery->end_time = now();
            }
            
            $delivery->save();

            // Log history
            DeliveryHistory::create([
                'delivery_id' => $delivery->id,
                'status' => $newStatus,
            ]);
            
            // TODO: Trigger Notification here
        });

        return response()->json($delivery->fresh('history'));
    }

    /**
     * Submit proof of delivery.
     */
    public function submitProof(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:qr,signature,photo',
            'data' => 'required', // Base64 or URL
        ]);

        $delivery = Delivery::where('driver_id', auth()->id())->findOrFail($id);

        $proof = ProofOfDelivery::create([
            'delivery_id' => $delivery->id,
            'type' => $request->type,
            'data' => $request->data,
        ]);

        // Auto update status to delivered if proof submitted?
        // Let's assume the driver manually sets status to delivered, or we do it here.
        // The requirement says "Status Workflow: On the way -> In delivery -> Delivered"
        // Usually proof is part of marking it delivered.
        
        return response()->json($proof);
    }
}
