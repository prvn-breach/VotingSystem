<style>
    table, tr, td, th {
        border: 1px solid;
        text-align: center;
    }

    tbody {
        font-size: 30px;
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
<meta http-equiv="refresh" content="2">
<div style="display: flex; justify-content: center;">
@foreach($posts as $post)
    <div style="width: 100%; text-align: center;">
        <h1>E-Ballot Paper - THCAA Election 2021</h1>
        <h2 style="border: 2px solid;margin-bottom:2;">{{ $post['elec_post_name'] }}</h2>
            <table style="width: 100%;" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th>Candidate ID</th>
                        <th>Candidate Image</th>
                        <th>Candidate Name</th>
                        <th>Votes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($post['participants'] as $index => $participant)
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td><img height='100' style="margin: 3" src="https://media.wired.com/photos/5dd3081844aad10009406a30/1:1/w_2400,c_limit/Biz-Sundar-h_20.93146994.jpg" alt=""></td>
                        <td>{{ $participant['voter']['asoci_vtr_name'] }}</td>
                        @if (array_key_exists($participant['elec_participnt_id'], $votes))
                        <td>{{ $votes[$participant['elec_participnt_id']] }}</td>
                        @else
                        <td>0</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
@endforeach
</div>

