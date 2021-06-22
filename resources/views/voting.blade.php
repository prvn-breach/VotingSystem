@extends('layout')

@section('content')
<div class="voting-page">
    <div class="wrapper position-relative">
        <div class="wizard-content-1 clearfix">
            <div class="steps d-inline-block position-absolute clearfix">
                <ul class="tablist multisteps-form__progress">
                    @foreach($election_posts as $index => $post)
                    <li class="multisteps-form__progress-btn @if($index==0) js-active current @endif"></li>
                    @endforeach
                </ul>
            </div>
            <div class="step-inner-content clearfix position-relative">
                <form class="multisteps-form__form" onsubmit="return confirm('Once you submit, you can not change your votes. Do you want to submit?');" action="{{ URL::to('/submitVotes').'/'.request()->route()->parameters['enc_vtr_card_no'] }}" id="wizard" method="POST">
                    {{ csrf_field() }} 
                    <div class="form-area position-relative">
                        @foreach($election_posts as $index => $post)
                        <div id="step{{ $post['elec_post_id'] }}" class="multisteps-form__panel @if($index==0) js-active @endif" data-animation="fadeIn">
                            <div class="wizard-forms clearfix position-relative">
                                <div class="quiz-title text-center">
                                    <h2>{{ $post['elec_post_name'] }}</h2>
                                    @if($post['no_of_partcipnt_to_choose'] == 1)
                                    <p>Please select the person you want to vote as {{ $post['elec_post_name'] }}</p>
                                    @else
                                    <p>Please select any {{ $post['no_of_partcipnt_to_choose'] }} persons you want to vote as {{ $post['elec_post_name'] }}</p>
                                    @endif
                                </div>
                                <div class="quiz-option-selector clearfix">
                                    <ul class="post{{ $post['elec_post_id'] }}">
                                        @foreach($post['participants'] as $participant)
                                        <li>
                                            <label class="start-quiz-item">
                                                <input 
                                                    type="checkbox" 
                                                    name="participent_ids[]" 
                                                    value="{{ $participant['elec_post_id'] }}:{{ $participant['elec_participnt_id'] }}" 
                                                    class="exp-option-box checkbox{{ $post['elec_post_id'] }}"
                                                >
                                                <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">{{ $participant['voter']['asoci_vtr_name'] }}</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="bottom-vector">
                                    <img src="assets/img/bq1.png" alt="">
                                </div>
                                <div class="actions clearfix">
                                    <ul>
                                        <li class="@if($index == 0) d-none @endif"><span class="js-btn-prev" title="PREV">Previous Question</span></li>
                                        @if($index<=count($election_posts)-2)
                                        <li><span class="js-btn-next" title="NEXT">Next Question</span></li>
                                        <li><button class="js-btn-submit" type="submit"><span>End Poll</span></button></li>
                                        @else
                                        <li><button class="js-btn-submit" type="submit"><span>SUBMIT</span></button></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var election_posts = @json($election_posts);
election_posts.forEach(post => {
    var checks = document.querySelectorAll(".checkbox"+post['elec_post_id']);
    var max = post['no_of_partcipnt_to_choose'];
    for (var i = 0; i < checks.length; i++) {
        checks[i].onclick = selectiveCheck;
    }
    function selectiveCheck (event) {
        var checkedChecks = document.querySelectorAll(".checkbox"+post['elec_post_id']+":checked");
        if (checkedChecks.length >= max + 1) {
            return false;
        }
    }
});
</script>
@endsection