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
                    <form action="{{ URL::to('/submitOtp2').'/'.request()->route()->parameters['enc_vtr_card_no'] }}" method="POST" class="contact-inner-page">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="otp" placeholder="Enter OTP" required="">
                                @if($errors->has('otp'))
                                    <span class="invalid-feedback" style="display:block;">
                                        <strong>{{ $errors->first('otp') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="readon upper">Verify<i class="fas fa-arrow-right ml-2"></i></button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <p class="desc"> Have not received Otp</p>
                    <a href="javascript:void(0)"><button type="button"
                            class="readon upper">Resend Otp<i class="fas fa-arrow-right ml-2"></i></button></a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection