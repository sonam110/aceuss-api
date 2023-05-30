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
    @foreach($ips as $key => $ip)
    <h2>
        <center><span class="main-title">Implementation Plan - {{$key+1}}</span></center>
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
    @if($ip->patient)
    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Patient Info</strong></td>
        </tr>
        
        @if($ip->patient->is_secret==0)
        <tr>
            <td class="title"><strong>Name</strong></td>
            <td class="value">{{($ip->patient) ? aceussDecrypt($ip->patient->name) : null}}</td>
            <td class="title"><strong>Personal Number</strong></td>
            <td class="value">{{($ip->patient) ? aceussDecrypt($ip->patient->personal_number) : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Patient ID</strong></td>
            <td class="value">{{($ip->patient) ? $ip->patient->custom_unique_id : null}}</td>
            <td class="title"><strong>Email</strong></td>
            <td class="value">{{($ip->patient) ? aceussDecrypt($ip->patient->email) : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Gender</strong></td>
            <td class="value">{{($ip->patient) ? $ip->patient->gender : null}}</td>
            <td class="title"><strong>Contact Number</strong></td>
            <td class="value">{{($ip->patient) ? aceussDecrypt($ip->patient->contact_number) : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Full address</strong></td>
            <td class="value" colspan="3">
            
                {{($ip->patient) ? aceussDecrypt($ip->patient->full_address) : null}},
                {{($ip->patient) ? $ip->patient->city : null}},
                {{($ip->patient) ? $ip->patient->postal_area : null}},
                {{($ip->patient) ? $ip->patient->zipcode : null}}
            </td>
        </tr>
        @else
        <tr>
            <td class="title"><strong>Patient ID</strong></td>
            <td class="value" colspan="3">{{($ip->patient) ? $ip->patient->custom_unique_id : null}}</td>
        </tr>
        @endif
    </table>

    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Relatives & Caretakers</strong></td>
        </tr>
        @foreach($ip->persons as $pKey => $person)
        @if($person->user)
        <tr>
            <td colspan="4" class="sub-title"><strong>Person # {{$pKey + 1}}</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Full Name</strong></td>
            <td class="value">{{aceussDecrypt($person->user->name)}}</td>
            <td class="title"><strong>Personal Number</strong></td>
            <td class="value">{{aceussDecrypt($person->user->personal_number)}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Email</strong></td>
            <td class="value">{{aceussDecrypt($person->user->email)}}</td>
            <td class="title"><strong>Phone </strong></td>
            <td class="value">{{aceussDecrypt($person->user->contact_number)}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Address </strong></td>
            <td class="value">{{aceussDecrypt($person->user->full_address)}}</td>
            <td class="title"><strong>Person Type </strong></td>
            <td class="value">
                @if($person->user->is_family_member==1)
                    Family Member<br>
                @endif
                @if($person->user->is_caretaker==1)
                    Caretaker<br>
                @endif
                @if($person->user->is_contact_person==1)
                    Contact Person<br>
                @endif
                @if($person->user->is_guardian==1)
                    Guardian<br>
                @endif
                @if($person->user->is_family_member==1)
                    Family Member<br>
                @endif
                @if($person->user->is_other==1)
                    {{$person->is_other_name}}
                @endif
            </td>
        </tr>

        <tr>
            <td class="title"><strong>Presented</strong></td>
            <td class="value">{{($person->is_presented=='1') ? 'Yes': 'No'}}</td>
            <td class="title"><strong>Participated </strong></td>
            <td class="value">{{($person->is_participated=='1') ? 'Yes': 'No'}}</td>
        </tr>

        @if($person->is_participated=='1')
        <tr>
            <td class="title"><strong>How Helped</strong></td>
            <td class="value" colspan="2">{{ $person->how_helped }}</td>
        </tr>
        @endif
        @endif

        @endforeach
    </table>

    <table class="header table table-striped">
        <tr>
            <td colspan="2" class="sub-title"><strong>Disability Details</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Description</strong></td>
            <td class="value">{{$ip->patient->disease_description}}</td>
        </tr>
        @if($ip->patient->PatientInformation)
        <tr>
            <td class="title"><strong>Additional Information</strong></td>
            <td class="value">{{$ip->patient->PatientInformation->aids}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Special Information</strong></td>
            <td class="value">{{$ip->patient->PatientInformation->special_information}}</td>
        </tr>
        @endif
    </table>

    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Patient Types</strong></td>
        </tr>
        @if(!empty($ip->patient->patient_type_id) && is_array($ip->patient->patient_type_id))
         @foreach($ip->patient->patient_type_id as $pkey => $ptype)
             <tr>
                <td colspan="4" class="title"><strong>
                    @php
                    $getPType = App\Models\EmployeeType::find($ptype);
                    @endphp
                    {{$getPType->designation}}
                </strong></td>
            </tr>
            @if($ptype==2 && $ip->patient->PatientInformation)
            <tr>
                <td class="title"><strong>Institute Name</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->institute_name}}</td>
                <td class="title"><strong>Contact Person</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->institute_contact_person}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Institute's Phone No.</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->institute_contact_number}}</td>
                <td class="title"><strong>Weekdays</strong></td>
                <td class="value">
                    @if(is_array(json_decode($ip->patient->PatientInformation->institute_week_days, true)))
                    @foreach(json_decode($ip->patient->PatientInformation->institute_week_days, true) as $wkey =>  $dayNum)
                        {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                    @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td class="title"><strong>Time From</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->classes_from}}</td>
                <td class="title"><strong>Time To</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->classes_to}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Institute's Address</strong></td>
                <td class="value" colspan="3">{{$ip->patient->PatientInformation->institute_full_address}}</td>
            </tr>
            @endif

            @if($ptype==3 && $ip->patient->PatientInformation)
            <tr>
                <td class="title"><strong>Company Name</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->company_name}}</td>
                <td class="title"><strong>Contact Person</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->company_contact_person}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Company's Phone No.</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->company_contact_number}}</td>
                <td class="title"><strong>weekdays</strong></td>
                <td class="value">
                    @if(is_array(json_decode($ip->patient->PatientInformation->company_week_days, true)))
                    @foreach(json_decode($ip->patient->PatientInformation->company_week_days, true) as $wkey =>  $dayNum)
                        {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                    @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td class="title"><strong>Working From</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->from_timing}}</td>
                <td class="title"><strong>Work To</strong></td>
                <td class="value">{{$ip->patient->PatientInformation->to_timing}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Company's Address</strong></td>
                <td class="value" colspan="3">{{$ip->patient->PatientInformation->company_full_address}}</td>
            </tr>
            @endif
         @endforeach
        @endif
    </table>

    @if($ip->patient->PatientInformation)
    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Other Activities</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Activity Type</strong></td>
            <td class="value">{{$ip->patient->PatientInformation->another_activity}}</td>
            <td class="title"><strong>Full Name</strong></td>
            <td class="value">{{$ip->patient->PatientInformation->another_activity_name}}</td>
        </tr>
        <!-- <tr>
            <td class="title"><strong>Contact Person</strong></td>
            <td class="value">{{$ip->patient->PatientInformation->another_activity_contact_person}}</td>
            <td class="title"><strong>Phone</strong></td>
            <td class="value">{{$ip->patient->PatientInformation->activitys_contact_number}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Address</strong></td>
            <td class="value" colspan="3">{{$ip->patient->PatientInformation->activitys_full_address}}</td>
        </tr> -->
        <tr>
            <td class="title"><strong>Days</strong></td>
            <td class="value">
                @if(is_array(json_decode($ip->patient->PatientInformation->week_days, true)))
                @foreach(json_decode($ip->patient->PatientInformation->week_days, true) as $wkey =>  $dayNum)
                    {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                @endforeach
                @endif
            </td>
            <td class="title"><strong>Start Time</strong></td>
            <td class="value">{{$ip->patient->PatientInformation->another_activity_start_time}}</td>
        </tr>
        <tr>
            <td class="title"><strong>End Time</strong></td>
            <td class="value" colspan="3">{{$ip->patient->PatientInformation->another_activity_end_time}}</td>
        </tr>
    </table>
    @endif

    @if($ip->patient->agencyHours)
    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Assigned Hours By Agency</strong></td>
        </tr>
        @foreach($ip->patient->agencyHours as $hkey => $hour)
        <tr>
            <td class="title"><strong>#{{$hkey+1}}: Issuer</strong></td>
            <td class="value">{{$hour->name}}</td>
            <td class="title"><strong>Hours</strong></td>
            <td class="value">{{$hour->assigned_hours}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Start Date</strong></td>
            <td class="value">{{$hour->start_date}}</td>
            <td class="title"><strong>End Date </strong></td>
            <td class="value">{{$hour->end_date}}</td>
        </tr>
        @endforeach
    </table>
    @endif
    @endif

    <div class="page-break"></div>
    <br>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="sub-title" colspan="2"><strong>Approved By</strong></td>
            </tr>
            @foreach($ip->requestForApprovals as $key => $person)
            @if($person->user)
            <tr>
                <td><strong>{{$key+1}}: {{aceussDecrypt(@$person->user->name)}}</strong></td>
                <td><br>
                    <center>
                        <strong>
                            @if($person->approval_type==2)
                                <u>SIGNED VIA MOBILE BANKID</u>
                            @else
                                __________________________
                            @endif 
                        </strong>
                    </center>
                </td>
            </tr>
            @endif
            @endforeach
            @if($ip->patient)
            <tr>
                <td><strong>{{$ip->requestForApprovals->count()+1}}: {{aceussDecrypt($ip->patient->name)}}</strong></td>
                <td><br><center><strong>__________________________</strong></center></td>
            </tr>
            @endif
        </tbody>
    </table>
    @endforeach
    
</body>
<htmlpagefooter name="page-footer">
</htmlpagefooter>