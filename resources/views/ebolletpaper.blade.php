<style>
    table, tr, td, th {
        border: 1px solid;
        text-align: center;
    }
    
    th {
        height: 35px;
    }

    thead tr th:nth-child(1) {
        width: 130px;
    }

    thead tr th:nth-child(2) {
        width: 135px;
    }
</style>
<div style="display: flex; justify-content: center;">
    <div style="width: 900px; text-align: center; page-break-after: always;">
        <h1>E-Ballot Paper - THCAA Election 2021</h1>
        <h2 style="border: 2px solid;margin-bottom:2;">{{ $post['elec_post_name'] }}</h2>
            <table style="width: 100%;" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th>Candidate ID</th>
                        <th>Candidate Image</th>
                        <th>Candidate Name</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($post['participants'] as $participant)
                    <tr>
                        <td>{{ $participant['elec_participnt_id'] }}</td>
                        <td><img height='100' style="margin: 3" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt=""></td>
                        <td>{{ $participant['voter']['asoci_vtr_name'] }}</td>
                        <td>
                            @if(in_array($participant['elec_participnt_id'], $voted_participants))
                            <img height='60' src="https://lh3.googleusercontent.com/proxy/TM8g8TnjC2IblGYr1aaxpsFqZaqyjh3JWkACAaxyhmgUt1TahzJnOolV145prFuAxt4QDz6yNgBM2jUtqOFzcxIf-0A_GmU" alt="">
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

