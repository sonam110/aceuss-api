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
            <td class="title"><strong>Goal</strong></td>
            <td class="value">{{$ip->goal}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Sub goal</strong></td>
            <td class="value">{{$ip->sub_goal}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Sub goal Detail</strong></td>
            <td class="value">{{$ip->sub_goal_details}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Limitations</strong></td>
            <td class="value">{{$ip->limitations}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Health Condition</strong></td>
            <td class="value">{{$ip->health_conditions}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Treatment</strong></td>
            <td class="value">{{$ip->treatment}}</td>
        </tr>
        <tr>
            <td class="title"><strong>Working Method</strong></td>
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
            <td colspan="4" class="sub-title"><strong>User Info</strong></td>
        </tr>
        <tr>
            <td class="title"><strong>Name</strong></td>
            <td class="value">{{($person) ? $person->name : null}}</td>
            <td class="title"><strong>Email</strong></td>
            <td class="value">{{($person) ? $person->email : null}}</td>
        </tr>

        <tr>
            <td class="title"><strong>Contact Number</strong></td>
            <td class="value">{{($person) ? $person->contact_number : null}}</td>
        </tr>

        <tr>
            
            <td class="title"><strong>Full address</strong></td>
            <td class="value" colspan="3">
                {{($person) ? $person->full_address : null}},
                {{($person) ? $person->city : null}},
                {{($person) ? $person->postal_area : null}},
                {{($person) ? $person->zipcode : null}}
            </td>
        </tr>
    </table>

    <hr>
    <br>
    <table class="table table-striped">
        <tbody>
            <tr>
                @if($bankid_verified=='yes')
                    <td><center><strong>Signed By</strong></center></td>
                @endif
                <td><center><strong>Date & Time</strong></center></td>
            </tr>
            <tr>
                @if($bankid_verified=='yes')
                    <td><center><strong>{{($person) ? $person->name : null}}</strong></center></td>
                @endif
                <td><center><strong>{{date('Y-m-d H:i:s')}}</strong></center></td>
            </tr>
        </tbody>
    </table>
</body>
<htmlpagefooter name="page-footer">
</htmlpagefooter>