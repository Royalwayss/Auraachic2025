<!-- <p>Please wait...</p> -->
<style>
/*ajax loading div*/
.PleaseWaitDiv{background: #ffffff;color: #666666;position: fixed;height: 100%;width: 100%;z-index: 5000;top: 0;left: 0;float: left;text-align: center;opacity: .80;}
.PleaseWaitDiv p{margin: 0;}
.PleaseWaitDiv b{position: absolute; top: 50%; left: 0; width: 100%; text-align: center; display: inline-block; float: left; -webkit-transform: translateY(-50%); -moz-transform: translateY(-50%); -ms-transform: translateY(-50%); -o-transform: translateY(-50%); transform: translateY(-50%);}
/*ajax loading div*/
</style>
<div class="PleaseWaitDiv" style="display:none;">
        <b><p style="color: #000;">Please wait... Don't refresh the page or don't press the back button.. We are processsing your transaction...</p></b>
    </div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    $('.PleaseWaitDiv').show();
    function demoSuccessHandler(transaction) {
        // You can write success code here. If you want to store some data in database.
        $.ajax({
            method: 'post',
            url: "{!!route('dopayment')!!}",
            data: {
                "_token": "{{ csrf_token() }}",
                "razorpay_payment_id": transaction.razorpay_payment_id,
                "data" :transaction
            },
            complete: function (r) {
                $('.PleaseWaitDiv').hide();
                if(r.status){
                    window.location.href = "{{url('/thanks')}}";
                }else{
                    window.location.href = "{{url('/')}}";
                }
            }
        })
    }
</script>
<script>
    var options = {
        key: "{{ $credentials['RAZORPAY_KEY'] }}",
        name: 'Auraachic',
        description: 'Auraachic Order Payment',
        order_id: "<?php echo $orderid;?>",
        capture:1,
        handler: demoSuccessHandler,
        prefill: {
            "name": "{{Auth::user()->name}}",
            "email": "{{Auth::user()->email}}",
            "contact": "{{Auth::user()->mobile}}"
        },
        modal: {
            "ondismiss": function(){
                window.location.href = "{{url('/cancel')}}";
            }
        }
    }
</script>
<script>
    window.r = new Razorpay(options);
    r.open();
</script>