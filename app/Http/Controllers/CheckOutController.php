<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

class CheckOutController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        DB::beginTransaction();
        try {
            $productId = $request->input('product_id');
            $product = Product::findOrFail($productId);
            $this->setStripEnv();

            $orderReferenceId = Str::uuid()->toString();
            $session = Session::create([
                'client_reference_id' => $orderReferenceId,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'inr',
                        'unit_amount' => $product->price * 100, // Stripe expects amount in cents
                        'product_data' => [
                            'name' => $product->name,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                "billing_address_collection" => "required",
                "shipping_address_collection" => [
                    "allowed_countries" => ["IN"],
                ],
                'mode' => 'payment',
                'success_url' => route('checkout.success') . "?session_id=" . $orderReferenceId,
                'cancel_url' => route('checkout.cancel') . "?session_id=" . $orderReferenceId,
            ]);
            OrderDetail::create([
                "order_reference_id" => $orderReferenceId,
                "session_id" => $session->id]);

            DB::commit();

            return response()->json(['sessionId' => $session->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function success(Request $request)
    {
        $this->setStripEnv();
        $orderReferenceId = $request->input('session_id');
        $orderDetail = OrderDetail::where("order_reference_id", $orderReferenceId)->first();
        $session = Session::retrieve($orderDetail->session_id);

        $paymentIntent = PaymentIntent::retrieve($session->payment_intent);
        // Extract order details
        $productName = $paymentIntent->description;
        $amountReceived = number_format($paymentIntent->amount_received / 100, 2);
        $customerEmail = $paymentIntent->customer_email;
        // Extract additional order details as needed

        // Determine payment status
        $paymentStatus = $paymentIntent->status;
        $shippingAddress = $paymentIntent->shipping->address;

        // Return view with order details and payment status
        return view('checkout.success', [
            'productName' => $productName,
            'amountReceived' => $amountReceived,
            'customerEmail' => $customerEmail,
            'paymentStatus' => $paymentStatus,
            'shippingAddress' => $shippingAddress
        ]);

    }

    public function setStripEnv()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }
}
