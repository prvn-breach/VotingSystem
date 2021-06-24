@extends('layout')

@section('content')
<div class="otp-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="contact-wrap">
                    <div class="sec-title mb-3">
                        <h2 class="title mb-3" style="font-family: 'Playfair Display',serif;">Enter OTP</h2>
                        <div class="desc otp-content">
                            We have sent OTP to your mobile number registered with association. Please enter here
                            and do not share with anyone.
                        </div>
                    </div>
                    <form action="{{ URL::to('/submitOtp1').'/'.request()->route()->parameters['candidate_voter_card'] }}" method="POST" class="contact-inner-page">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="username" id="username">
                                <input type="hidden" name="password" id="password">
                                <input type="hidden" name="sec_key" id="sec_key">
                                <input type="text" name="otp" id="otp" placeholder="Enter OTP" required="">
                                <div class="d-flex">
                                    @if($errors->has('otp'))
                                        <span class="invalid-feedback" style="display:block;">
                                            <strong>{{ $errors->first('otp') }}</strong>
                                        </span>
                                    @endif
                                    <span style="@if($errors->has('otp')) width:300px; @endif" id="expiry_time"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="readon upper">Verify<i class="fas fa-arrow-right ml-2"></i></button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <!-- <div class="@if(!$errors->has('otp')) d-none @endif resend-btn"> -->
                        <p class="desc"> Have not received Otp</p>
                        <form action="{{ URL::to('/resendOtp1') }}" method="post">
                            {{ csrf_field() }} 
                            <input type="hidden" name="candidate_voter_card" value="{{ request()->route()->parameters['candidate_voter_card'] }}">
                            <button type="submit" class="readon upper">Resend Otp<i class="fas fa-arrow-right ml-2"></i></button>
                        </form>
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var interval = null;
clearInterval(interval);
var deadlineDate = new Date('{{ $otpExpiryDate }}').getTime();
interval = setInterval(() => {
    var currentDate = new Date().getTime();
    var distance = deadlineDate - currentDate;
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    // if (minutes < 1) {
    //     $('.resend-btn').removeClass('d-none');
    // }
    if (distance > 1) { 
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        if (minutes < 10) {
            minutes = '0'+minutes;
        }
        if (seconds < 10) {
            seconds = '0'+seconds;
        }
        document.getElementById('expiry_time').innerHTML = 'Otp expiry in '+minutes+':'+seconds;
    } else {
        $('#expiry_time').hide();
        clearInterval(interval);
    }
}, 1000);
</script>

@endsection