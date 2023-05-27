<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title></title>
<style>
@import url('https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap');
</style>
<style type="text/css">
  @font-face {
      font-family: 'Roboto Condensed', sans-serif;
      src: url(https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap);
  }
  body{
    font-family: 'Roboto Condensed', sans-serif;
    color: #000;
  }
  .table table {
    border-collapse: collapse;
    width: 100%;
    color: #000;
  }

  .table th, .table td {
    text-align: left;
    padding: 8px;
    border-top: 1px solid #428b9f;
    color: #000;
  }

  .table tr:nth-child(even) {
    background-color: #2d4046;
  }
  .ii a[href], a {
      color: #000!important;
  }
</style>
</head>
<body>
<table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#fff">
  <tr>
    <td>
        <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#4dbce7" style="padding-bottom: 30px;padding: 1px; font-family: 'Roboto Condensed', sans-serif;">
          <tr>
            <td>
                <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#fff" style="padding: 15px 15px 0px 15px; font-family: 'Roboto Condensed', sans-serif;">
                  <tr>
                    <td>
                      <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#4dbce7" style="padding: 15px; font-family: 'Roboto Condensed', sans-serif;">
                        <tr>
                          <td>
                            <div>
                              <center style="color: #fff; font-size: 18px;text-decoration: underline; font-family: 'Roboto Condensed', sans-serif;">

                                <!-- Head section -->
                                {{$data['content']['subject']}}
                              </center>
                            </div>
                          </td>
                        </tr>
                      </table>

                      <table cellspacing="0" border="0" cellpadding="0" width="100%" style="padding: 15px 0px; font-family: 'Roboto Condensed', sans-serif;">
                        <tr>
                          <td colspan="2">
                            <div style="border: 3px solid #fff; width: 225px; padding: 10px; background: #4dbce7; display: inline-block; font-family: 'Roboto Condensed', sans-serif;">
                              <center style="color: #fff; font-size: 16px; font-family: 'Roboto Condensed', sans-serif;"><?php echo date('d/m/Y, H:i A') ?></center>
                            </div>

                            @if(!empty($data['company']))
                            <div style="float: right; background: #fff; padding: 10px;">
                              <img src="{{ $data['company']['company_logo']}}" width="50px" height="50px">
                            </div>
                            @endif
                          </td>
                        </tr>
                      </table>

                      <table cellspacing="0" border="0" cellpadding="0" width="100%" style="padding: 15px 15px 0px 15px; font-family: 'Roboto Condensed', sans-serif;">
                      
                        <tr>
                          <td colspan="2" style="color: #000; font-family: 'Roboto Condensed', sans-serif;">
                            
                            <!-- Body section -->
                            {{$data['content']['body']}}

                            <br><br>
                          </td>
                        </tr>

                        @php
                          $getUser = \App\Models\User::where('id',$data['id'])->first();
                        @endphp
                        <tr>
                          <td colspan="2" width="100%" style="color: #000; font-family: 'Roboto Condensed', sans-serif;">
                          <br>
                            <div style="color: #f79646; font-family: 'Roboto Condensed', sans-serif;">
                              <table cellspacing="0" border="0" cellpadding="0" width="100%" style="padding: 15px 0px 0px 0px; font-family: 'Roboto Condensed', sans-serif; color: #000;">
                                <tr>
                                  <td>Name</td>
                                  <td>{{ aceussDecrypt($getUser->name) }}</td>
                                </tr>
                          

                                <tr>
                                  <td>Email</td>
                                  <td>{{ aceussDecrypt($getUser->email) }}</td>
                                </tr>

                                <tr>
                                  <td>Password</td>
                                  <td>{{ $data['password'] }}</td>
                                </tr>

                                <tr>
                                  <td>Contact No</td>
                                  <td>{{ aceussDecrypt($getUser->contact_number) }}</td>
                                </tr>
                                
                                <tr>
                                  <td>City</td>
                                  <td>{{ $getUser->city }}</td>
                                </tr>
                                
                                <tr>
                                  <td>Address</td>
                                  <td>{{ aceussDecrypt($getUser->full_address) }}</td>
                                </tr>
                                <tr>
                                  <td>zipcode</td>
                                  <td>{{ $getUser->zipcode }}</td>
                                </tr>

                              </table>
                            </div><br><hr><br>
                            
                          </td>
                        </tr>
                
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#262626" style="padding: 5px; color: #030303; background: #fff; text-align: center;border: 1px solid #4dbce7; font-family: 'Roboto Condensed', sans-serif;">
                        <tr>
                          <td style="font-family: 'Roboto Condensed', sans-serif;">
                            @if(!empty($data['company']))
                              {{aceussDecrypt($data['company']['company_name'])}}
                              <br>
                              {{$data['company']['company_address']}}
                              <br>
                              {{$data['company']['company_contact']}}
                              <br>
                            @endif
                          </td>
                        </tr>
                      </table>
                      <br>
                    </td>
                  </tr>
                </table>
            </td>
          </tr>
        </table>
    </td>
  </tr>
</table>

</body>
</html>