<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            ->where('user_id', auth()->id())
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
            try {
                // Fetch the package
                $package = Package::findOrFail($request->input('package_id'));

                // Check for existing active subscription
                $existingSubscription = Subscription::where('user_id', auth()->id())
                    ->where('status', 'active')
                    ->first();

                if ($existingSubscription) {
                    throw new \Exception('You already have an active subscription');
                }

                // Validate payment amount
                $paymentAmount = $request->input('amount');
                if ($paymentAmount < $package->price) {
                    throw new \Exception('Payment amount is insufficient for the selected package');
                }

                // Create subscription
                $subscription = Subscription::create([
                    'user_id' => auth()->id(),
                    'package_id' => $package->id,
                    'zone_id' => $request->input('zone_id'),
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addMonth()->toDateString(),
                    'status' => 'pending',
                    'notes' => $request->input('notes')
                ]);

                // Create payment
                $payment = Payment::create([
                    'subscription_id' => $subscription->id,
                    'amount' => $paymentAmount,
                    'currency' => 'XAF',
                    'transaction_id' => $this->generateUniqueTransactionId(),
                    'payment_method' => $request->input('payment_method'),
                    'status' => $request->input('status', 'pending'),
                    'payment_date' => now(),
                    'payment_details' => json_encode($request->input('payment_details', []))
                ]);

                // Update subscription status based on payment
                if ($payment->status === 'completed') {
                    $subscription->update(['status' => 'active']);
                } else {
                    $subscription->update(['status' => 'cancelled']);
                    throw new \Exception('Payment failed, subscription could not be activated');
                }

                // Load relationships for the response
                $subscription->load('package', 'zone', 'payments');

                return response()->success(new SubscriptionResource($subscription), 'Subscription created and payment processed successfully', 200);

            } catch (\Exception $e) {
                // Rollback the transaction in case of any error
                DB::rollBack();

                // Log the error for debugging
                Log::error('Subscription creation failed: ' . $e->getMessage());

                return response()->errors([], $e->getMessage(),400);
            }
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
        $this->authorize('update', $subscription);

        return DB::transaction(function () use ($request, $subscription) {
            try {
                $validatedData = $request->validated();

                if (isset($validatedData['status'])) {
                    switch ($validatedData['status']) {
                        case 'cancelled':
                            $validatedData['end_date'] = now();
                            break;
                        case 'expired':
                            $validatedData['end_date'] = $validatedData['end_date'] ?? now();
                            break;
                    }
                }

                $subscription->update($validatedData);

                $subscription->refresh();

                return response()->success(new SubscriptionResource($subscription->load('package', 'zone', 'payments')), __('Subscription updated successfully'),200);
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Subscription update failed: ' . $e->getMessage());

                return response()->errors([], __('Failed to update subscription: ') . $e->getMessage(),400);
            }
        });
    }

    /**
     * Cancel an active subscription.
     */
    public function cancel(Request $request, Subscription $subscription)
    {
        $this->authorize('cancel', $subscription);

        $validatedData = $request->validate([
            'reason' => ['required','string','max:500'],
        ]);

        if ($subscription->status !== 'active') {
            return response()->errors([],__('Impossible d\'annuler un abonnement qui n\'est pas actif'), 400);
        }

        $subscription->update([
            'status' => 'cancelled',
            'end_date' => now()->toDateString(),
            'notes' => $validatedData['reason'],
        ]);

        // Créer un enregistrement de paiement ou journal si nécessaire
        // $this->createCancellationLog($subscription);

        return response()->success(new SubscriptionResource($subscription), __('Abonnement annulé avec succès'),200);
    }

    /**
     * Renew an expired subscription.
     */
    public function renew(SubscriptionRequest $request, Subscription $subscription)
    {
        return DB::transaction(function () use ($request, $subscription) {
            $this->authorize('renew', $subscription);

            if (!$this->isEligibleForRenewal($subscription)) {
                return response()->errors(
                    [], 
                    __('Cet abonnement ne peut pas être renouvelé'), 
                    400
                );
            }

            // Récupérer le package existant
            $package = $subscription->package;

            // Vérifier le montant du paiement
            $paymentAmount = $request->input('amount');
            if ($paymentAmount < $package->price) {
                return response()->errors(
                    [], 
                    __('Le montant du paiement est insuffisant'), 
                    400
                );
            }

            $newSubscription = Subscription::create([
                'user_id' => $subscription->user_id,
                'package_id' => $package->id,
                'zone_id' => $subscription->zone_id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'status' => 'pending', // Initialement en attente
                'notes' => 'Renouvellement de l\'abonnement précédent'
            ]);

            $payment = Payment::create([
                'subscription_id' => $newSubscription->id,
                'amount' => $paymentAmount,
                'currency' => 'XAF',
                'transaction_id' => $this->generateUniqueTransactionId(),
                'payment_method' => $request->input('payment_method'),
                'status' => $request->input('status', 'pending'),
                'payment_date' => now(),
                'payment_details' => json_encode($request->input('payment_details', []))
            ]);

            // Mettre à jour le statut de l'abonnement en fonction du paiement
            if ($payment->status === 'completed') {
                $newSubscription->update(['status' => 'active']);
                
                // Mettre à jour l'ancien abonnement
                $subscription->update([
                    'status' => 'renewed',
                    'end_date' => now()->toDateString()
                ]);
            } else {
                $newSubscription->update(['status' => 'cancelled']);
                
                return response()->errors([], __('Le paiement a échoué, le renouvellement n\'a pas pu être effectué'),400);
            }

            return response()->success(new SubscriptionResource($newSubscription->load('package', 'zone', 'payments')), __('Abonnement renouvelé avec succès'),200);
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

    /**
     * Vérifier l'éligibilité au renouvellement.
     */
    protected function isEligibleForRenewal(Subscription $subscription): bool
    {
        return in_array($subscription->status, ['expired', 'cancelled']) 
            && $subscription->end_date < now();
    }
}
