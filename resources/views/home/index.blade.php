<!-- products.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="row">
        @foreach ($products as $product)
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">Price: <i class="indian-rupee-symbol"></i> {{ $product->price }}</p>
                        <div class="d-flex justify-content-end align-items-end">
                            <div class="btn-group">
                                <a
                                    data-productId="{{ $product->id }}"
                                    data-productName = "{{ $product->name }}
                                    href="{{ route('product.checkout', $product->id) }}"
                                    class="btn btn-sm btn-outline-primary checkOutBtn">Buy Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('page-script')
        <script src="https://js.stripe.com/v3/"></script>
    @endpush

    @push('js')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            $('.checkOutBtn').on('click', function() {
                var productId = $(this).attr('data-productId'); // Assuming you have an input field with the product ID
                var productName = $(this).attr('data-productName'); // Assuming you have an input field with the product name

                $.post('{{ route('product.checkout') }}', { product_id: productId, product_name: productName }, function(data) {
                    initStripe(data.sessionId);
                });
            });

            function initStripe(sessionId) {
                // Initialize Stripe with your publishable API key
                var stripe = Stripe('pk_test_51PB5Q2SEZ1daSwZaVpvR0lVoxuJLr9DPh15uGj1DVeUC6oITA9cmvM8Bd8VDuxPYlUIHJih03rXM7pciSdzb3Dgv008bgeapCz');

                // Use stripe.redirectToCheckout here
                stripe.redirectToCheckout({ sessionId: sessionId })
                    .then(function (result) {
                        // Handle result (optional)
                    })
                    .catch(function (error) {
                        // Handle errors (optional)
                        console.error('Error:', error);
                    });
            }
        </script>
    @endpush
@endsection
