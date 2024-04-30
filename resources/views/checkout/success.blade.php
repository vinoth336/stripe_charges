<!-- success.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Payment Success</div>
                    <div class="card-body">
                        <p>Thank you for your purchase!</p>
                        <p>Product Name: {{ $productName }}</p>
                        <p>Amount Received: <i class="indian-rupee-symbol"></i> {{ $amountReceived }}</p>
                        <p>Shipping Address: <br>{{ $shippingAddress->line1 }} <br> {{ $shippingAddress->line2 }} <br> {{ $shippingAddress->city }} <br> {{ $shippingAddress->state }}
                        <br>Pincode - {{ $shippingAddress->postal_code }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
