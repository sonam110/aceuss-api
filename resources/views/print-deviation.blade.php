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
        <center><span class="main-title">Deviation</span></center>
    </h2>
    <div>
    <center>------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    </center></div>

    @if($deviation->Patient)
    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Patient Info</strong></td>
        </tr>
        
        @if($deviation->Patient->is_secret==0)
        <tr>
            <td class="title"><strong>Name</strong></td>
            <td class="value">{{($deviation->Patient) ? $deviation->Patient->name : null}}</td>
            <td class="title"><strong>Personal Number</strong></td>
            <td class="value">{{($deviation->Patient) ? $deviation->Patient->personal_number : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Patient ID</strong></td>
            <td class="value">{{($deviation->Patient) ? $deviation->Patient->custom_unique_id : null}}</td>
            <td class="title"><strong>Email</strong></td>
            <td class="value">{{($deviation->Patient) ? $deviation->Patient->email : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Gender</strong></td>
            <td class="value">{{($deviation->Patient) ? $deviation->Patient->gender : null}}</td>
            <td class="title"><strong>Contact Number</strong></td>
            <td class="value">{{($deviation->Patient) ? $deviation->Patient->contact_number : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Full address</strong></td>
            <td class="value" colspan="3">
            </td>
                {{($deviation->Patient) ? $deviation->Patient->full_address : null}},
                {{($deviation->Patient) ? $deviation->Patient->city : null}},
                {{($deviation->Patient) ? $deviation->Patient->postal_area : null}},
                {{($deviation->Patient) ? $deviation->Patient->zipcode : null}}
        </tr>
        @else
        <tr>
            <td class="title"><strong>Patient ID</strong></td>
            <td class="value" colspan="3">{{($deviation->Patient) ? $deviation->Patient->custom_unique_id : null}}</td>
        </tr>
        @endif
    </table>

    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Relatives & Caretakers</strong></td>
        </tr>
        @foreach($deviation->Patient->persons as $pKey => $person)
        <tr>
            <td colspan="4" class="sub-title"><strong>Person # {{$pKey + 1}}</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Full Name</strong></td>
            <td class="value">{{$person->name}}</td>
            <td class="title"><strong>Personal Number</strong></td>
            <td class="value">{{$person->personal_number}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Email</strong></td>
            <td class="value">{{$person->email}}</td>
            <td class="title"><strong>Phone </strong></td>
            <td class="value">{{$person->contact_number}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Address </strong></td>
            <td class="value">{{$person->full_address}}</td>
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
            <td class="value">{{$deviation->Patient->disease_description}}</td>
        </tr>
        @if($deviation->Patient->PatientInformation)
        <tr>
            <td class="title"><strong>Additional Information</strong></td>
            <td class="value">{{$deviation->Patient->PatientInformation->aids}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Special Information</strong></td>
            <td class="value">{{$deviation->Patient->PatientInformation->special_information}}</td>
        </tr>
        @endif
    </table>

    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Patient Types</strong></td>
        </tr>
        @if(!empty($deviation->Patient->patient_type_id) && is_array($deviation->Patient->patient_type_id))
         @foreach($deviation->Patient->patient_type_id as $pkey => $ptype)
             <tr>
                <td colspan="4" class="title"><strong>
                    @php
                    $getPType = App\Models\EmployeeType::find($ptype);
                    @endphp
                    {{$getPType->designation}}
                </strong></td>
            </tr>
            @if($ptype==2 && $deviation->Patient->PatientInformation)
            <tr>
                <td class="title"><strong>Institute Name</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->institute_name}}</td>
                <td class="title"><strong>Contact Person</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->institute_contact_person}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Institute's Phone No.</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->institute_contact_number}}</td>
                <td class="title"><strong>Weekdays</strong></td>
                <td class="value">
                    @if(is_array(json_decode($deviation->Patient->PatientInformation->institute_week_days, true)))
                    @foreach(json_decode($deviation->Patient->PatientInformation->institute_week_days, true) as $wkey =>  $dayNum)
                        {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                    @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td class="title"><strong>Time From</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->classes_from}}</td>
                <td class="title"><strong>Time To</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->classes_to}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Institute's Address</strong></td>
                <td class="value" colspan="3">{{$deviation->Patient->PatientInformation->institute_full_address}}</td>
            </tr>
            @endif

            @if($ptype==3 && $deviation->Patient->PatientInformation)
            <tr>
                <td class="title"><strong>Company Name</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->company_name}}</td>
                <td class="title"><strong>Contact Person</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->company_contact_person}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Company's Phone No.</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->company_contact_number}}</td>
                <td class="title"><strong>weekdays</strong></td>
                <td class="value">
                    @if(is_array(json_decode($deviation->Patient->PatientInformation->company_week_days, true)))
                    @foreach(json_decode($deviation->Patient->PatientInformation->company_week_days, true) as $wkey =>  $dayNum)
                        {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                    @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td class="title"><strong>Working From</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->from_timing}}</td>
                <td class="title"><strong>Work To</strong></td>
                <td class="value">{{$deviation->Patient->PatientInformation->to_timing}}</td>
            </tr>
            <tr>
                <td class="title"><strong>Company's Address</strong></td>
                <td class="value" colspan="3">{{$deviation->Patient->PatientInformation->company_full_address}}</td>
            </tr>
            @endif
         @endforeach
        @endif
    </table>

    @if($deviation->Patient->PatientInformation)
    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Other Activities</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Activity Type</strong></td>
            <td class="value">{{$deviation->Patient->PatientInformation->another_activity}}</td>
            <td class="title"><strong>Full Name</strong></td>
            <td class="value">{{$deviation->Patient->PatientInformation->another_activity_name}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Contact Person</strong></td>
            <td class="value">{{$deviation->Patient->PatientInformation->another_activity_contact_person}}</td>
            <td class="title"><strong>Phone</strong></td>
            <td class="value">{{$deviation->Patient->PatientInformation->activitys_contact_number}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Address</strong></td>
            <td class="value" colspan="3">{{$deviation->Patient->PatientInformation->activitys_full_address}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Days</strong></td>
            <td class="value">
                @if(is_array(json_decode($deviation->Patient->PatientInformation->week_days, true)))
                @foreach(json_decode($deviation->Patient->PatientInformation->week_days, true) as $wkey =>  $dayNum)
                    {{($wkey>0) ? ', ': ''}}{{strftime('%A', strtotime($dayNum." days"))}}
                @endforeach
                @endif
            </td>
            <td class="title"><strong>Start Time</strong></td>
            <td class="value">{{$deviation->Patient->PatientInformation->another_activity_start_time}}</td>
        </tr>
        <tr>
            <td class="title"><strong>End Time</strong></td>
            <td class="value" colspan="3">{{$deviation->Patient->PatientInformation->another_activity_end_time}}</td>
        </tr>
    </table>
    @endif

    @if($deviation->Patient->weeklyHours)
    <table class="header table table-striped">
        <tr>
            <td colspan="4" class="sub-title"><strong>Assigned Hours By Agency</strong></td>
        </tr>
        @foreach($deviation->Patient->weeklyHours as $hkey => $hour)
        <tr>
            <td class="title"><strong>#{{$hkey+1}}: Issuer</strong></td>
            <td class="value">{{$hour->name}}</td>
            <td class="title"><strong>Hours</strong></td>
            <td class="value">{{$hour->weekly_hours_allocated}}</td>
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

    <table class="header table table-striped">
        <tr>
            <td colspan="2" class="sub-title"><strong>Deviation Info</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Category </strong></td>
            <td class="value">{{$deviation->category_id }}</td>
        </tr>
        <tr>
            <td class="title"><strong>Category </strong></td>
            <td class="value">{{($deviation->Category) ? $deviation->Category->name : '-' }}</td>
        </tr>
        <tr>
            <td class="title"><strong>Sub Category </strong></td>
            <td class="value">{{($deviation->Subcategory) ? $deviation->Subcategory->name : '-' }}</td>
        </tr>
        <tr>
            <td class="title"><strong>Date time </strong></td>
            <td class="value">{{$deviation->date_time }}</td>
        </tr>
        <tr>
            <td class="title"><strong>description </strong></td>
            <td class="value">{{$deviation->description }}</td>
        </tr>
        <tr>
            <td class="title"><strong>Immediate action </strong></td>
            <td class="value">{{$deviation->immediate_action }}</td>
        </tr>
        <tr>
            <td class="title"><strong>probable cause of the incident </strong></td>
            <td class="value">{{$deviation->probable_cause_of_the_incident }}</td>
        </tr>
        <tr>
            <td class="title"><strong>suggestion to prevent event again </strong></td>
            <td class="value">{{$deviation->suggestion_to_prevent_event_again }}</td>
        </tr>
        <tr>
            <td class="title"><strong>critical range </strong></td>
            <td class="value">{{$deviation->critical_range }}</td>
        </tr>
        <tr>
            <td class="title"><strong>related factor </strong></td>
            <td class="value">{{$deviation->related_factor }}</td>
        </tr>
        <tr>
            <td class="title"><strong>follow up</strong></td>
            <td class="value">{{$deviation->follow_up }}</td>
        </tr>
        <tr>
            <td class="title"><strong>further investigation</strong></td>
            <td class="value">{{$deviation->further_investigation }}</td>
        </tr>
        <tr>
            <td class="title"><strong>Signed By</strong></td>
            <td class="value">{{($deviation->is_signed) ? $deviation->Employee->name : 'Not signed yet' }}</td>
        </tr>
    </table>

</body>
<htmlpagefooter name="page-footer">
</htmlpagefooter>