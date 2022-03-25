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
</style>
<body>
	<h2>
		<center><span class="main-title">IP Followups</span></center>
	</h2>
	<div>
	<center>------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	</center></div>

    <table class="header table table-striped">
        <tr>
            <td colspan="2" class="sub-title"><strong>IP Info</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Category</strong></td>
            <td class="value">{{($ipfollowupInfo->category) ? $ipfollowupInfo->category->name : null}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Subcategory</strong></td>
            <td class="value">{{($ipfollowupInfo->subcategory) ? $ipfollowupInfo->subcategory->name : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>What happened</strong></td>
            <td class="value">{{$ipfollowupInfo->what_happened}}</td>
        </tr>
        <tr>
            <td class="title"><strong>How it happened</strong></td>
            <td class="value">{{$ipfollowupInfo->how_it_happened}}</td>
        </tr>
        <tr>
            <td class="title"><strong>When it started</strong></td>
            <td class="value">{{$ipfollowupInfo->when_it_started}}</td>
        </tr>
        <tr>
            <td class="title"><strong>What to do</strong></td>
            <td class="value">{{$ipfollowupInfo->what_to_do}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Goal</strong></td>
            <td class="value">{{$ipfollowupInfo->goal}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Sub goal</strong></td>
            <td class="value">{{$ipfollowupInfo->sub_goal}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Start date / Time</strong></td>
            <td class="value">{{$ipfollowupInfo->plan_start_date}}
            {{(!empty($ipfollowupInfo->plan_start_time) ? date('H:i:s', strtotime($ipfollowupInfo->plan_start_time)) : '')}} </td>
        </tr>
        <tr>
            <td class="title"><strong>Activity message</strong></td>
            <td class="value">{{$ipfollowupInfo->activity_message}}</td>
        </tr>
    </table>

	<table class="header table table-striped">
		<tr>
            <td colspan="4" class="sub-title"><strong>Patient Info</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Name</strong></td>
            <td class="value">{{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->name : null}}</td>
            <td class="title"><strong>Email</strong></td>
            <td class="value">{{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->email : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Contact Number</strong></td>
            <td class="value">{{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->contact_number : null}}</td>
            <td class="title"><strong>Gender</strong></td>
            <td class="value">{{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->gender : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Personal Number</strong></td>
            <td class="value">{{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->personal_number : null}}</td>
            <td class="title"><strong>Joining date</strong></td>
            <td class="value">{{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->joining_date : null}}</td>
	    </tr>
        <tr>
            
            <td class="title"><strong>Full address</strong></td>
            <td class="value" colspan="3">
            	{{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->full_address : null}},
	            {{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->city : null}},
	            {{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->postal_area : null}},
	            {{($ipfollowupInfo->patient) ? $ipfollowupInfo->patient->zipcode : null}}
	        </td>
        </tr>
    </table>

    @foreach($ipfollowupInfo->ipFollowUps as $key => $followup)
	<table class="table table-striped">
		<tbody>
            <tr>
                <td colspan="4" class="sub-title"><strong>#{{$key + 1}}: Follow up</strong></td>
            </tr>
			<tr>
				<td><strong>Title</strong></td>
				<td colspan="3">{{$followup->title}}</td>
			</tr>
			
			<tr>
				<td><strong>Description</strong></td>
				<td colspan="3">{{$followup->description}}</td>
			</tr>

            <tr>
                <td><strong>Start date / time</strong></td>
                <td>{{$followup->start_date}} {{(!empty($followup->start_time) ? date('H:i:s', strtotime($followup->start_time)) : '')}}</td>
                <td><strong>End date / time</strong></td>
                <td>{{$followup->end_date}} {{(!empty($followup->end_time) ? date('H:i:s', strtotime($followup->end_time)) : '')}}</td>
            </tr>

            <tr>
                <td><strong>Remark</strong></td>
                <td colspan="3">{{$followup->remark}}</td>
            </tr>
		</tbody>
	</table>
    @endforeach

    <hr>
    <br>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td><center><strong>Signed By</strong></center></td>
                <td><center><strong>Date & Time</strong></center></td>
            </tr>
            <tr>
                <td><center><strong>Ashok</strong></center></td>
                <td><center><strong>{{date('Y-m-d H:i:s')}}</strong></center></td>
            </tr>
        </tbody>
    </table>
</body>
<htmlpagefooter name="page-footer">
</htmlpagefooter>