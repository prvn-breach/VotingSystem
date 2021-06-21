@extends('layout')

@section('content')
<div class="declaration-area">
    <div class="container">
        <div class="row">
            <h2 class="font-weight-bold" style="font-family: 'Playfair Display',serif;">Hello Member</h2>
        </div>
        <div class="row">
            <p>
                It is hereby informed to all the members that the THCAA addressed a letter to the Hon'ble ChIef
                Justice requesting to permit us to conduct our association elections in the last week of April, but
                due to spike of Covid 19 , Hon'ble ChIef Justice requested us to defer the elections vide it's
                communication Dt
            </p>
        </div>
        <div class="row">
            <p>
                Since we are continuing on the decision of General Body Dt 16.03.2020, we thought it appropriate to
                conduct elections through online. Though we have some difficulties in online elections some of the
                members are requesting to conduct elections either physical or virtual. In view of the decision of
                the Hon'ble ChIef Justice to defer the elections for time being, we thought it appropriate to seek
                General Body decision for conducting of elections through online system or to defer the elections.
                Hence we hereby request all the members to participate in General Body to decide whether elections
                are to be conducted through online or defer for time being..Thank you..THCAA
            </p>
        </div>
        <div class="row">
            <form action="{{ URL::to('/submit-declaration').'/'.request()->route()->parameters['enc_vtr_card_no'] }}" class="m-2" method="POST">
                {{ csrf_field() }} 
                
                <input type="checkbox" name="accept" id="accept_t_c" onchange="change()" class="mr-2">I have read and accepting above said conditions
                
                <div class="g-recaptcha mt-3" data-callback="onReturnCallback" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                @if($errors->has('g-recaptcha-response'))
                    <span class="invalid-feedback" style="display:block;">
                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                    </span>
                @endif

                <button type="submit" id="declaration-submit-btn" disabled class="mt-3 font-weight-bold">GO<i class="fas fa-arrow-right ml-2"></i></button>
            </form>
        </div>
    </div>
</div>


<script>

var captcha = null;

var onReturnCallback = (response) => {
    captcha = response;
    change();
};

var change = () => {
    var accept_t_c = document.querySelector('#accept_t_c').checked;
    if (accept_t_c && captcha) {
    $('#declaration-submit-btn').prop('disabled', false);
    } else {
        $('#declaration-submit-btn').prop('disabled', true);
    }
}
</script>

@endsection