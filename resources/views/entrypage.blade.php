@extends('layout')

@section('content')
    <!-- TIMER PAGE -->
    <div class="timer-page">
        <div class="container">
            <span>Dear Member,</span>
            <p>Voting process will starts from 10:30 am, 30 Jun 2021. Requesting you to get back at 10:30am. Thank You</p>
            <div class="circle">
                <span>Voting Will Starts in</span>
                <span class="hours">01Hrs</span>
                <span class="minutes">30Min</span>
                <span class="seconds">20Sec</span>
            </div>
        </div>
    </div>

    <div class="wrapper position-relative" style="display: none;">
        <div class="wizard-content-1 clearfix">
            <div class="steps d-inline-block position-absolute clearfix">
                <ul class="tablist multisteps-form__progress"></ul>
            </div>
            <div class="step-inner-content clearfix position-relative">
                <form class="multisteps-form__form" action="thank-you.html" id="wizard" method="POST"></form>
            </div>
        </div>
    </div>
@endsection