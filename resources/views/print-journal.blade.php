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
    @foreach($journals as $key => $journal)
    <h2>
        <center><span class="main-title">Journal - {{$key+1}} <br><small style="font-size: 10px;"></small></span></center>
    </h2>
    <div>
    <center>------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    </center></div>
    
    <table class="header table table-striped">
        <tr>
            <td class="title"><strong>Category</strong></td>
            <td class="value">{{($journal->category) ? $journal->category->name : null}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Subcategory</strong></td>
            <td class="value">{{($journal->subcategory) ? $journal->subcategory->name : null}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Date / Time </strong></td>
            <td class="value">{{$journal->date}} {{$journal->time}}</td>
        </tr>
        @foreach($journal->journalLogs as $key => $log)
        <tr>
            <td class="title"><strong>Description</strong></td>
            <td class="value">
                {{$log->description}}
                <br>
                {{$log->description_created_at}}
            </td>
        </tr>
        @endforeach
        <tr>
            <td class="title"><strong>Description</strong></td>
            <td class="value">
                {{$journal->description}}
                <br>
                {{$journal->edit_date}}
            </td>
        </tr>
       
    </table>
        @if($journal->journalActions->count()>0)
        <table class="header table table-striped">
            <tr>
                <td colspan="4" class="sub-title"><strong>Journal Actions</strong></td>
            </tr>
            @foreach($journal->journalActions as $jkey => $action)
            <tr>
                <td><strong>{{$jkey+1}}: Comment action</strong></td>
                <td colspan="3">{{$action->comment_action}}</td>
            </tr>
            <tr>
                <td><strong>Comment result</strong></td>
                <td colspan="3">{{$action->comment_result}}</td>
            </tr>
            <tr>
                <td><strong>Signed: </strong></td>
                <td>{{ ($action->is_signed==1) ? $action->signed_date : 'Not signed yet'}}</td>
                <td><strong>Signed By:</strong></td>
                <td>@if($action->is_signed==1) {{ aceussDecrypt($action->signedBy->name) }} @endif</td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            @endforeach
        </table>
        @endif
    @endforeach

    @if(@$journal->Patient)
        <table class="header table table-striped">
            <tr>
                <td colspan="4" class="sub-title"><strong>Patient Info</strong></td>
            </tr>
            
            @if($journal->Patient->is_secret==0)
            <tr>
                <td class="title" width="25%"><strong>Name</strong></td>
                <td class="value" width="25%">{{($journal->Patient) ? aceussDecrypt($journal->Patient->name) : null}}</td>
                <td class="title" width="25%"><strong>Personal Number</strong></td>
                <td class="value" width="25%">{{($journal->Patient) ? aceussDecrypt($journal->Patient->personal_number) : null}}</td>
            </tr>

            <tr>
                <td class="title"><strong>Patient ID</strong></td>
                <td class="value">{{($journal->Patient) ? $journal->Patient->custom_unique_id : null}}</td>
                <td class="title"><strong>Email</strong></td>
                <td class="value">{{($journal->Patient) ? aceussDecrypt($journal->Patient->email) : null}}</td>
            </tr>

            <tr>
                <td class="title"><strong>Gender</strong></td>
                <td class="value">{{($journal->Patient) ? $journal->Patient->gender : null}}</td>
                <td class="title"><strong>Contact Number</strong></td>
                <td class="value">{{($journal->Patient) ? aceussDecrypt($journal->Patient->contact_number) : null}}</td>
            </tr>

            <tr>
                <td class="title"><strong>Full address</strong></td>
                <td class="value" colspan="3">
                
                    {{($journal->Patient) ? aceussDecrypt($journal->Patient->full_address) : null}},
                    {{($journal->Patient) ? $journal->Patient->city : null}},
                    {{($journal->Patient) ? $journal->Patient->postal_area : null}},
                    {{($journal->Patient) ? $journal->Patient->zipcode : null}}
                </td>
            </tr>
            @else
            <tr>
                <td class="title"><strong>Patient ID</strong></td>
                <td class="value" colspan="3">{{($journal->Patient) ? $journal->Patient->custom_unique_id : null}}</td>
            </tr>
            @endif
        </table>

        <table class="header table table-striped">
            <tr>
                <td colspan="4" class="sub-title"><strong>Relatives & Caretakers</strong></td>
            </tr>
            @foreach($journal->Patient->persons as $pKey => $person)
            <tr>
                <td colspan="4" class="sub-title"><strong>Person # {{$pKey + 1}}</strong></td>
            </tr>
            <tr>
                <td class="title" width="25%"><strong>Full Name</strong></td>
                <td class="value" width="25%">{{aceussDecrypt($person->name)}}</td>
                <td class="title" width="25%"><strong>Personal Number</strong></td>
                <td class="value" width="25%">{{aceussDecrypt($person->personal_number)}}</td>
            </tr>

            <tr>
                <td class="title"><strong>Email</strong></td>
                <td class="value">{{aceussDecrypt($person->email)}}</td>
                <td class="title"><strong>Phone </strong></td>
                <td class="value">{{aceussDecrypt($person->contact_number)}}</td>
            </tr>

            <tr>
                <td class="title"><strong>Address </strong></td>
                <td class="value">{{aceussDecrypt($person->full_address)}}</td>
                <td class="title"><strong>Person Type </strong></td>
                <td class="value">
                    @if($person->is_family_member==1)
                        Family Member<br>
                    @endif
                    @if($person->is_caretaker==1)
                        Caretaker<br>
                    @endif
                    @if($person->is_contact_person==1)
                        Contact Person<br>
                    @endif
                    @if($person->is_guardian==1)
                        Guardian<br>
                    @endif
                    @if($person->is_family_member==1)
                        Family Member<br>
                    @endif
                    @if($person->is_other==1)
                        {{$person->is_other_name}}
                    @endif
                </td>
            </tr>
            @endforeach
        </table>

        <table class="header table table-striped">
            <tr>
                <td colspan="2" class="sub-title"><strong>Disability Details</strong></td>
            </tr>
            <tr>
                <td class="title"><strong>Description</strong></td>
                <td class="value">{{$journal->Patient->disease_description}}</td>
            </tr>
            @if($journal->Patient->PatientInformation)
            <tr>
                <td class="title"><strong>Additional Information</strong></td>
                <td class="value">{{$journal->Patient->PatientInformation->aids}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Special Information</strong></td>
                <td class="value">{{$journal->Patient->PatientInformation->special_information}}</td>
            </tr>
            @endif
        </table>

        <table class="header table table-striped">
            <tr>
                <td colspan="4" class="sub-title"><strong>Patient Types</strong></td>
            </tr>
            @if(!empty($journal->Patient->patient_type_id) && is_array($journal->Patient->patient_type_id))
             @foreach($journal->Patient->patient_type_id as $pkey => $ptype)
                 <tr>
                    <td colspan="4" class="title"><strong>
                        @php
                        $getPType = App\Models\EmployeeType::find($ptype);
                        @endphp
                        {{$getPType->designation}}
                    </strong></td>
                </tr>
                @if($ptype==2 && $journal->Patient->PatientInformation)
                <tr>
                    <td class="title" width="25%"><strong>Institute Name</strong></td>
                    <td class="value" width="25%">{{$journal->Patient->PatientInformation->institute_name}}</td>
                    <td class="title" width="25%"><strong>Contact Person</strong></td>
                    <td class="value" width="25%">{{$journal->Patient->PatientInformation->institute_contact_person}}</td>
                </tr>
                <tr>
                    <td class="title"><strong>Institute's Phone No.</strong></td>
                    <td class="value">{{$journal->Patient->PatientInformation->institute_contact_number}}</td>
                    <td class="title"><strong>Weekdays</strong></td>
                    <td class="value">
                        @if(is_array(json_decode($journal->Patient->PatientInformation->institute_week_days, true)))
                        @foreach(json_decode($journal->Patient->PatientInformation->institute_week_days, true) as $wkey =>  $dayNum)
                            {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                        @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="title"><strong>Time From</strong></td>
                    <td class="value">{{$journal->Patient->PatientInformation->classes_from}}</td>
                    <td class="title"><strong>Time To</strong></td>
                    <td class="value">{{$journal->Patient->PatientInformation->classes_to}}</td>
                </tr>
                <tr>
                    <td class="title"><strong>Institute's Address</strong></td>
                    <td class="value" colspan="3">{{$journal->Patient->PatientInformation->institute_full_address}}</td>
                </tr>
                @endif

                @if($ptype==3 && $journal->Patient->PatientInformation)
                <tr>
                    <td class="title"><strong>Company Name</strong></td>
                    <td class="value">{{$journal->Patient->PatientInformation->company_name}}</td>
                    <td class="title"><strong>Contact Person</strong></td>
                    <td class="value">{{$journal->Patient->PatientInformation->company_contact_person}}</td>
                </tr>
                <tr>
                    <td class="title"><strong>Company's Phone No.</strong></td>
                    <td class="value">{{$journal->Patient->PatientInformation->company_contact_number}}</td>
                    <td class="title"><strong>weekdays</strong></td>
                    <td class="value">
                        @if(is_array(json_decode($journal->Patient->PatientInformation->company_week_days, true)))
                        @foreach(json_decode($journal->Patient->PatientInformation->company_week_days, true) as $wkey =>  $dayNum)
                            {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                        @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="title"><strong>Working From</strong></td>
                    <td class="value">{{$journal->Patient->PatientInformation->from_timing}}</td>
                    <td class="title"><strong>Work To</strong></td>
                    <td class="value">{{$journal->Patient->PatientInformation->to_timing}}</td>
                </tr>
                <tr>
                    <td class="title"><strong>Company's Address</strong></td>
                    <td class="value" colspan="3">{{$journal->Patient->PatientInformation->company_full_address}}</td>
                </tr>
                @endif
             @endforeach
            @endif
        </table>

        @if($journal->Patient->PatientInformation)
        <table class="header table table-striped">
            <tr>
                <td colspan="4" class="sub-title"><strong>Other Activities</strong></td>
            </tr>
            <tr>
                <td class="title" width="25%"><strong>Activity Type</strong></td>
                <td class="value" width="25%">{{$journal->Patient->PatientInformation->another_activity}}</td>
                <td class="title" width="25%"><strong>Full Name</strong></td>
                <td class="value" width="25%">{{$journal->Patient->PatientInformation->another_activity_name}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Contact Person</strong></td>
                <td class="value">{{$journal->Patient->PatientInformation->another_activity_contact_person}}</td>
                <td class="title"><strong>Phone</strong></td>
                <td class="value">{{$journal->Patient->PatientInformation->activitys_contact_number}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Address</strong></td>
                <td class="value" colspan="3">{{$journal->Patient->PatientInformation->activitys_full_address}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Days</strong></td>
                <td class="value">
                    @if(is_array(json_decode($journal->Patient->PatientInformation->week_days, true)))
                    @foreach(json_decode($journal->Patient->PatientInformation->week_days, true) as $wkey =>  $dayNum)
                        {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                    @endforeach
                    @endif
                </td>
                <td class="title"><strong>Start Time</strong></td>
                <td class="value">{{$journal->Patient->PatientInformation->another_activity_start_time}}</td>
            </tr>
            <tr>
                <td class="title"><strong>End Time</strong></td>
                <td class="value" colspan="3">{{$journal->Patient->PatientInformation->another_activity_end_time}}</td>
            </tr>
        </table>
        @endif

        @if($journal->Patient->agencyHours)
        <table class="header table table-striped">
            <tr>
                <td colspan="4" class="sub-title"><strong>Assigned Hours By Agency</strong></td>
            </tr>
            @foreach($journal->Patient->agencyHours as $hkey => $hour)
            <tr>
                <td class="title" width="25%"><strong>#{{$hkey+1}}: Issuer</strong></td>
                <td class="value" width="25%">{{$hour->name}}</td>
                <td class="title" width="25%"><strong>Hours</strong></td>
                <td class="value" width="25%">{{$hour->assigned_hours}}</td>
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

    <hr>
    <table class="header table table-striped">
        <tr>
            <td class="title"><strong>Printed By: {{aceussDecrypt(auth()->user()->name)}}</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Reason: {{$print_reason}}</strong></td>
        </tr>
    </table>
</body>
<htmlpagefooter name="page-footer">
</htmlpagefooter>