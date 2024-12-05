<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\SubscriptionResource;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::with(['package', 'zone'])
            // ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);
        
        return response()->success(SubscriptionResource::collection($subscriptions), __('Subscription charged successfully'), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubscriptionRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $package = Package::findOrFail($request->input('package_id'));
    
            // Vérification de souscription active existante
            $existingSubscription = Subscription::where('user_id', auth()->id())
                ->where('status', 'active')
                ->first();
    
            if ($existingSubscription) {
                return response()->errors([], __('You already have an active subscription'), 400);
            }
    
            // Validation du montant du paiement
            $paymentAmount = $request->input('amount');
            if ($paymentAmount < $package->price) {
                return response()->errors([], __('Payment amount is insufficient for the subscription package'), 400);
            }
    
            // Création de la souscription
            $subscription = Subscription::create([
                'user_id' => auth()->id(),
                'package_id' => $package->id,
                'zone_id' => $request->input('zone_id'),
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(), // Durée par défaut : 1 mois
                'status' => 'pending', // Initialement en attente jusqu'à la réussite du paiement
                'notes' => $request->input('notes')
            ]);
    
            // Création du paiement
            $payment = Payment::create([
                'subscription_id' => $subscription->id,
                'amount' => $paymentAmount,
                'currency' => 'XAF',
                'transaction_id' => $this->generateUniqueTransactionId(),
                'payment_method' => $request->input('payment_method'),
                'status' => $request->input('status', 'pending'),
                'payment_date' => now(),
                'payment_details' => $request->input('payment_details')
            ]);
    
            // Mise à jour de la souscription si le paiement est réussi
            if ($payment->status === 'completed') {
                $subscription->update([
                    'status' => 'active'
                ]);
            } else {
                // Si le paiement échoue, la souscription reste en attente ou est annulée
                $subscription->update(['status' => 'cancelled']);
                return response()->errors([], __('Payment failed, subscription could not be activated'), 400);
            }
    
            return response()->success(
                new SubscriptionResource($subscription->load('package', 'zone', 'payments')),
                __('Subscription created and payment completed successfully'),
                200
            );
        });
    }

    /**
     * Current active subscription.
     */
    public function currentSubscription()
    {
        $subscription = Subscription::with(['package', 'zone'])
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();
        
        return response()->errors($subscription ? new SubscriptionResource($subscription) : null, __('Subscription charged successfully'), 400);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubscriptionRequest $request, Subscription $subscription)
    {
        // Ensure the subscription belongs to the authenticated user
        $this->authorize('update', $subscription);

        $subscription->update($request->validated());
        
        return response()->success(new SubscriptionResource($subscription->load('package', 'zone')), __('Subscription updated successfully'), 200);
    }

    /**
     * Cancel an active subscription.
     */
    public function cancel(Subscription $subscription)
    {
        $this->authorize('cancel', $subscription);

        $subscription->update([
            'status' => 'cancelled',
            'end_date' => now()->toDateString()
        ]);

        return response()->success(new SubscriptionResource($subscription), __('Subscription cancelled successfully'), 200);
    }

    /**
     * Renew an expired subscription.
     */
    public function renew(Subscription $subscription)
    {
        return DB::transaction(function () use ($subscription) {

            $this->authorize('renew', $subscription);

            // Create a new subscription based on the previous one
            $newSubscription = Subscription::create([
                'user_id' => $subscription->user_id,
                'package_id' => $subscription->package_id,
                'zone_id' => $subscription->zone_id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'status' => 'active',
                'notes' => 'Renewal of previous subscription'
            ]);

            return response()->success(new SubscriptionResource($newSubscription->load('package', 'zone')), __('Subscription renewed successfully'), 200);
        });
    }

    /**
     * Get subscription history for the authenticated user.
     */
    public function history()
    {
        $subscriptions = Subscription::with(['package', 'zone'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();
        
        return response()->success(SubscriptionResource::collection($subscriptions), __('Your Subscription charged successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // Générer un identifiant de transaction unique
    private static function generateUniqueTransactionId()
    {
        return 'PAY-' . Str::upper(Str::random(10)) . '-' . now()->timestamp;
    }
}
