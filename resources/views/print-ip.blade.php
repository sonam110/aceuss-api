<style type="text/css">
    body {
        font-size: 13px;
    }
    table {
        width: 100%;
        line-height: inherit;
        text-align: left;
        border-collapse: collapse;
        margin-top: 15px;
        margin-bottom: 25px;
    }

    .table-striped table, .table-striped tr {
      border: 1px solid #e7e6e6;
    }

    table td {
        padding: 7px 10px;
        vertical-align: top;
    }

    /*table.header tr td:nth-child(2) {
        text-align: right;
    }*/

    h2 {
        text-align: center;
    }
    .main-title {
        font-size: 24px;
    }
    .sub-title {
        font-size: 20px;
    }
    .heading {
        font-size: 18px;
    }
    .sub-heading {
        font-size: 16px;
    }
    
    .text-center {
        text-align: center;
    }
    .small {
        font-size: 12px;
    }
    .mb-20 {
        margin-bottom: 20px;
    }
    .title {
        font-size: 14px;
        font-weight: 800;
    }
    .value {
        font-size: 14px;
    }
    .page-break {
        page-break-after: always;
    }
</style>
<body>
    <h2>
        <center><span class="main-title">Implementation Plan</span></center>
    </h2>
    <div>
    <center>------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    </center></div>
    

    <table class="header table table-striped">
        <tr>
            <td class="title"><strong>Category</strong></td>
            <td class="value">{{($ip->category) ? $ip->category->name : null}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Subcategory</strong></td>
            <td class="value">{{($ip->subcategory) ? $ip->subcategory->name : null}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Title</strong></td>
            <td class="value">{{$ip->title}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Limitations</strong></td>
            <td class="value">{{$ip->limitations}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Limitation Detail</strong></td>
            <td class="value">{{$ip->limitation_details}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Goal</strong></td>
            <td class="value">{{$ip->goal}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Limitations</strong></td>
            <td class="value">{{$ip->limitations}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Sub goal</strong></td>
            <td class="value">{{$ip->sub_goal_selected}}</td>
        </tr>
        <tr>
            <td class="value" colspan="2">{{$ip->sub_goal_details}}</td>
        </tr>
        <tr>
            <td class="value" colspan="2">{{$ip->sub_goal}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Overall goal</strong></td>
            <td class="value">{{$ip->overall_goal}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Overall goal details</strong></td>
            <td class="value">{{$ip->overall_goal_details}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Body functions</strong></td>
            <td class="value">{{$ip->body_functions}}</td>
        </tr>
        <tr>
            <td class="title"><strong>personal factors</strong></td>
            <td class="value">{{$ip->personal_factors}}</td>
        </tr>
        <tr>
            <td class="title"><strong>health conditions</strong></td>
            <td class="value">{{$ip->health_conditions}}</td>
        </tr>
        <tr>
            <td class="title"><strong>other factors</strong></td>
            <td class="value">{{$ip->other_factors}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Treatment</strong></td>
            <td class="value">{{$ip->treatment}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Working method</strong></td>
            <td class="value">{{$ip->working_method}}</td>
        </tr>        
        
        <tr>
            <td class="title"><strong>Start date </strong></td>
            <td class="value">{{$ip->start_date}}
            </td>
        </tr>
        <tr>
            <td class="title"><strong>End date </strong></td>
            <td class="value">{{$ip->end_date}}
            </td>
        </tr>
       
    </table>
      <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Patient Info</strong></td>
        </tr>
        @if($ip->patient)
        @if($ip->patient->is_secret==0)
        <tr>
            <td class="title"><strong>Name</strong></td>
            <td class="value">{{($ip->patient) ? $ip->patient->name : null}}</td>
            <td class="title"><strong>Personal Number</strong></td>
            <td class="value">{{($ip->patient) ? $ip->patient->personal_number : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Patient ID</strong></td>
            <td class="value">{{($ip->patient) ? $ip->patient->custom_unique_id : null}}</td>
            <td class="title"><strong>Email</strong></td>
            <td class="value">{{($ip->patient) ? $ip->patient->email : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Gender</strong></td>
            <td class="value">{{($ip->patient) ? $ip->patient->gender : null}}</td>
            <td class="title"><strong>Contact Number</strong></td>
            <td class="value">{{($ip->patient) ? $ip->patient->contact_number : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Full address</strong></td>
            <td class="value" colspan="3">
            </td>
                {{($ip->patient) ? $ip->patient->full_address : null}},
                {{($ip->patient) ? $ip->patient->city : null}},
                {{($ip->patient) ? $ip->patient->postal_area : null}},
                {{($ip->patient) ? $ip->patient->zipcode : null}}
        </tr>
        @else
        <tr>
            <td class="title"><strong>Patient ID</strong></td>
            <td class="value" colspan="3">{{($ip->patient) ? $ip->patient->custom_unique_id : null}}</td>
        </tr>
        @endif
        @else
        <tr>
            <td class="title" colspan="4"><strong>Information not found</strong></td>
        </tr>
        @endif
    </table>

    <div class="page-break"></div>
    <br>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="sub-title" colspan="2"><strong>Approved By</strong></td>
            </tr>
            @foreach($ip->requestForApprovals as $key => $person)
            @if($person->RequestedTo)
            <tr>
                <td><strong>{{$key+1}}: {{$person->RequestedTo->name}}</strong></td>
                <td><br><center><strong>__________________________</strong></center></td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</body>
<htmlpagefooter name="page-footer">
</htmlpagefooter>