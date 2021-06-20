@extends('layout')

@section('content')
<div class="voting-page">
    <div class="quiz-top-area text-center">
        <h1>Remaining Polling Time</h1>
        <div class="quiz-countdown text-center ul-li">
            <ul>
                <li class="days">
                    <span class="count-down-number"></span>
                    <span class="count-unit">Days</span>
                </li>
                <li class="hours">
                    <span class="count-down-number"></span>
                    <span class="count-unit">Hours</span>
                </li>
                <li class="minutes">
                    <span class="count-down-number"></span>
                    <span class="count-unit">Min</span>
                </li>
                <li class="seconds">
                    <span class="count-down-number"></span>
                    <span class="count-unit">Sec</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper position-relative">
        <div class="wizard-content-1 clearfix">
            <div class="steps d-inline-block position-absolute clearfix">
                <ul class="tablist multisteps-form__progress">
                    <li class="multisteps-form__progress-btn js-active current"></li>
                    <li class="multisteps-form__progress-btn "></li>
                </ul>
            </div>
            <div class="step-inner-content clearfix position-relative">
                <form class="multisteps-form__form" action="thank-you.html" id="wizard" method="POST">
                    <div class="form-area position-relative">
                        <div class="multisteps-form__panel  js-active" data-animation="fadeIn">
                            <div class="wizard-forms clearfix position-relative">
                                <div class="quiz-title text-center">
                                    <h2>President</h2>
                                    <p>Please select the person you want to vote as president</p>
                                </div>
                                <div class="quiz-option-selector clearfix">
                                    <ul>
                                        <li>
                                            <label class="start-quiz-item">
                                                <input type="radio" name="quiz" value="Email Markering"
                                                    class="exp-option-box">
                                                <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">Person 1</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="quiz" value="Font Developer"
                                                    class="exp-option-box">
                                                <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">Person 2</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="quiz" value="Digital Marketing"
                                                    class="exp-option-box">
                                                    <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">Person 3</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="quiz" value="SEO" class="exp-option-box">
                                                <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">Person 4</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="bottom-vector">
                                    <img src="assets/img/bq1.png" alt="">
                                </div>
                                <div class="actions clearfix">
                                    <ul>
                                        <li class="d-none"><span class="js-btn-next" title="PREV">Previous
                                                Question</span></li>
                                        <li><span class="js-btn-next" title="NEXT">Next Question</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- step 1 -->
                        <div class="multisteps-form__panel" data-animation="fadeIn">
                            <div class="wizard-forms clearfix position-relative">
                                <div class="quiz-title text-center">
                                    <h2>Vice Precident</h2>
                                    <p>Please select the person you want to vote as vice president</p>
                                </div>
                                <div class="quiz-option-selector clearfix">
                                    <ul>
                                        <li>
                                            <label class="start-quiz-item">
                                                <input type="radio" name="quiz" value="Email Markering"
                                                    class="exp-option-box">
                                                <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">Person 1</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="quiz" value="Font Developer"
                                                    class="exp-option-box">
                                                <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">Person 2</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="quiz" value="Digital Marketing"
                                                    class="exp-option-box">
                                                    <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">Person 3</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="quiz" value="SEO" class="exp-option-box">
                                                <img class="exp-number text-uppercase" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt="">
                                                <span class="exp-label">Person 4</span>
                                                <span class="checkmark-border"></span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="bottom-vector">
                                    <img src="assets/img/bq1.png" alt="">
                                </div>
                                <div class="actions clearfix">
                                    <ul>
                                        <li><span class="js-btn-prev" title="PREV">Previous Question</span></li>
                                        <li><button class="js-btn-submit" type="submit"><span>SUBMIT</span></button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection