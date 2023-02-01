@extends('layouts.app')
@section('content')
    <p style="text-align: center">Laravel Integration Payment Gateway</p>
    <div style="width: 640px; margin: 0 auto;">
        <table style="width: 100%; margin: 15px auto; text-align: left;">
            <tr>
                <th>Sl</th>
                <th>Product</th>
                <th style="text-align: center">Quantity</th>
                <th style="text-align: right">Price</th>
                <th style="text-align: center">Action</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Blank Canvas</td>
                <td style="text-align: center">1</td>
                <td style="text-align: right">100 Taka</td>
                <td style="text-align: center">
                    <a href="">Delete</a>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Wild Gears</td>
                <td style="text-align: center">1</td>
                <td style="text-align: right">50 Taka</td>
                <td style="text-align: center">
                    <a href="">Delete</a>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>LightPacker</td>
                <td style="text-align: center">1</td>
                <td style="text-align: right">30 Taka</td>
                <td style="text-align: center">
                    <a href="">Delete</a>
                </td>
            </tr>
        </table>
        <table style="width: 320px; text-align: left; margin: 15px 0 15px auto;">
            <tr>
                <td>Subtotal</td>
                <td style="text-align: right">180 Taka</td>
            </tr>
            <tr>
                <td>Shipping</td>
                <td style="text-align: right">20 Taka</td>
            </tr>
            <tr>
                <td>Total</td>
                <td style="text-align: right"><span id="total_amount">200</span> Taka</td>
            </tr>
        </table>
        <form action="{{ url('/pay') }}" method="POST">
            <input type="hidden" value="{{ csrf_token() }}" name="_token"/>
            <button type="submit">Pay Now</button>
            <button type="button"
                    id="sslczPayBtn"
                    token="if you have any token validation"
                    postdata="your javascript arrays or objects which requires in backend"
                    order="If you already have the transaction generated for current order"
                    endpoint="{{ url('/pay-via-ajax') }}"> Pay Now
            </button>
        </form>
        <button type="button" id="bKash_button">Pay with bKash</button>
    </div>
    {{-- sslcommerz --}}
    @push('scripts')
        <!-- If you want to use the popup integration, -->
        <script>
            (function (window, document) {
                var loader = function () {
                    var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
                    // script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR LIVE
                    script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR SANDBOX
                    tag.parentNode.insertBefore(script, tag);
                };

                window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
            })(window, document);
        </script>
    @endpush

    {{-- bkash --}}
    @push('scripts')
        <script>
            var accessToken = '';
            $(document).ready(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{!! route('token') !!}",
                    type: 'POST',
                    contentType: 'application/json',
                    success: function (data) {
                        console.log('got data from token  ..');
                        console.log(JSON.stringify(data));
                        accessToken = JSON.stringify(data);
                    },
                    error: function () {
                        console.log('error');
                    }
                });
                var paymentConfig = {
                    createCheckoutURL: "{!! route('createpayment') !!}",
                    executeCheckoutURL: "{!! route('executepayment') !!}"
                };
                var paymentRequest;
                paymentRequest = {amount: $('#total_amount').text(), intent: 'sale'};
                console.log(JSON.stringify(paymentRequest));
                bKash.init({
                    paymentMode: 'checkout',
                    paymentRequest: paymentRequest,
                    createRequest: function (request) {
                        console.log('=> createRequest (request) :: ');
                        console.log(request);
                        $.ajax({
                            url: paymentConfig.createCheckoutURL + "?amount=" + paymentRequest.amount,
                            type: 'GET',
                            contentType: 'application/json',
                            success: function (data) {
                                console.log('got data from create  ..');
                                console.log('data ::=>');
                                console.log(data);
                                var obj = JSON.parse(data);
                                if (data && obj.paymentID != null) {
                                    paymentID = obj.paymentID;
                                    bKash.create().onSuccess(obj);
                                } else {
                                    console.log('error');
                                    bKash.create().onError();
                                }
                            },
                            error: function () {
                                console.log('error');
                                bKash.create().onError();
                            }
                        });
                    },
                    executeRequestOnAuthorization: function () {
                        console.log('=> executeRequestOnAuthorization');
                        $.ajax({
                            url: paymentConfig.executeCheckoutURL + "?paymentID=" + paymentID,
                            type: 'GET',
                            contentType: 'application/json',
                            success: function (data) {
                                console.log('got data from execute  ..');
                                console.log('data ::=>');
                                console.log(JSON.stringify(data));
                                data = JSON.parse(data);
                                if (data && data.paymentID != null) {
                                    alert('[SUCCESS] data : ' + JSON.stringify(data));
                                } else {
                                    bKash.execute().onError();
                                }
                            },
                            error: function () {
                                bKash.execute().onError();
                            }
                        });
                    }
                });
                console.log("Right after init ");
            });

            function callReconfigure(val) {
                bKash.reconfigure(val);
            }

            function clickPayButton() {
                $("#bKash_button").trigger('click');
            }
        </script>
    @endpush
@endsection
