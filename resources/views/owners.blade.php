@extends('layout')

@section('content')
@if(Session::has('msg'))
<div class="alert alert-success"><b><?php
        echo Session::get('msg');
        Session::put('msg', NULL);
        ?></b></div>
@endif
<div class="col-md-6 col-sm-12">

    <div class="box box-danger">

        <form method="get" action="{{ URL::Route('/sortur') }}">
            <div class="box-header">
                <h3 class="box-title">Sort</h3>
            </div>
            <div class="box-body row">

                <div class="col-md-6 col-sm-12">


                    <select id="sortdrop" class="form-control" name="type">
                        <option value="userid" <?php
                        if (isset($_GET['type']) && $_GET['type'] == 'userid') {
                            echo 'selected="selected"';
                        }
                        ?> id="provid">{{ trans('customize.User')}} ID</option>
                        <option value="username" <?php
                        if (isset($_GET['type']) && $_GET['type'] == 'username') {
                            echo 'selected="selected"';
                        }
                        ?> id="pvname">{{ trans('customize.User')}} Name</option>
                        <option value="useremail" <?php
                        if (isset($_GET['type']) && $_GET['type'] == 'useremail') {
                            echo 'selected="selected"';
                        }
                        ?> id="pvemail">{{ trans('customize.User')}} Email</option>
                    </select>

                    <br>
                </div>
                <div class="col-md-6 col-sm-12">

                    <select id="sortdroporder" class="form-control" name="valu">
                        <option value="asc" <?php
                        if (isset($_GET['valu']) && $_GET['valu'] == 'asc') {
                            echo 'selected="selected"';
                        }
                        ?> id="asc">Ascending</option>
                        <option value="desc" <?php
                        if (isset($_GET['valu']) && $_GET['valu'] == 'desc') {
                            echo 'selected="selected"';
                        }
                        ?> id="desc">Descending</option>
                    </select>

                    <br>
                </div>

            </div>

            <div class="box-footer">


                <button type="submit" id="btnsort" class="btn btn-flat btn-block btn-success">Sort</button>


            </div>
        </form>

    </div>
</div>


<div class="col-md-6 col-sm-12">

    <div class="box box-danger">

        <form method="get" action="{{ URL::Route('/searchur') }}">
            <div class="box-header">
                <h3 class="box-title">Filter</h3>
            </div>
            <div class="box-body row">

                <div class="col-md-6 col-sm-12">

                    <select class="form-control" id="searchdrop" name="type">
                        <option value="userid" id="userid">{{ trans('customize.User')}} ID</option>
                        <option value="username" id="username">{{ trans('customize.User')}} Name</option>
                        <option value="useremail" id="useremail">{{ trans('customize.User')}} Email</option>
                        <option value="useraddress" id="useraddress">{{ trans('customize.User') }} Address</option>
                    </select>


                    <br>
                </div>
                <div class="col-md-6 col-sm-12">


                    <input class="form-control" type="text" name="valu" value="<?php
                    if (Session::has('valu')) {
                        echo Session::get('valu');
                    }
                    ?>" id="insearch" placeholder="keyword"/>
                    <br>
                </div>

            </div>

            <div class="box-footer">

                <button type="submit" id="btnsearch" class="btn btn-flat btn-block btn-success">Search</button>


            </div>
        </form>

    </div>
</div>



<div class="box box-info tbl-box">
    <div align="left" id="paglink"><?php echo $owners->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>State</th>
                <th>Registered</th>
                <th>Refferal Code</th>
                <th>Debt</th>
                <th>Referred By</th>
                <th>Actions</th>

            </tr>

            <?php foreach ($owners as $owner) { ?>
                <tr>
                    <td><?= $owner->id ?></td>
                    <td><?php echo $owner->first_name . " " . $owner->last_name; ?> </td>
                    <td><?= $owner->email ?></td>
                    <td><?= $owner->phone ?></td>
                    <td>
                        <?php
                        if ($owner->address) {
                            echo $owner->address;
                        } else {
                            echo "<span class='badge bg-red'>" . Config::get('app.blank_fiend_val') . "</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($owner->state) {
                            echo $owner->state;
                        } else {
                            echo "<span class='badge bg-red'>" . Config::get('app.blank_fiend_val') . "</span>";
                        }
                        ?>
                    </td>
                    <td width="140px">
                        <?= $owner->created_at ?>
                    </td>
                    <?php
                    $referred_code = Ledger::where('owner_id', '=', $owner->id)->first();
                    ?>
                    <td><?php if($referred_code){
                                  echo $referred_code->referral_code;
                              }else{
                                  echo "None";
                              }          
                    ?></td>
                    <td><?= sprintf($owner->debt, 2) ?></td>
    <?php
    $refer = Owner::where('id', $owner->referred_by)->first();
    if ($refer) {
        $referred = $refer->first_name . " " . $refer->last_name;
    } else {
        $referred = "None";
    }
    ?>
                    <td><?php echo $referred; ?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-flat btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                                Actions
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                <li role="presentation"><a role="menuitem" tabindex="-1" id="edit" href="{{ URL::Route('AdminUserEdit', $owner->id) }}">Edit {{ trans('customize.User') }}</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" id="history" href="{{ URL::Route('AdminUserHistory',$owner->id) }}">View History</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" id="coupon" href="{{ URL::Route('AdminUserReferral', $owner->id) }}">Coupon Details</a></li>
                            <?php
                            $check = Requests::where('owner_id', '=', $owner->id)->where('is_cancelled', '=', 0)->where('is_started','=',1)->where('is_completed','=',1)->get()->count(); //print_r($check);
                            if ($check == 0){
                                $is_available = 1;
                            }else{
                                $is_available = 0;
                            }

                            ?>
                                    <!--<li role="presentation"><a role="menuitem" tabindex="-1" id="add_req" href="{{ URL::Route('AdminAddRequest', $owner->id) }}">Add Request</a></li-->
                                
                               <li role="presentation"><a role="menuitem" tabindex="-1" id="delete" class="delete" <?php if ($is_available == 1) { ?> href="{{ URL::Route('AdminDeleteUser', $owner->id) }} <?php } else { ?> href='' <?php } ?>">Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
<?php } ?>
        </tbody>
    </table>

    <div align="left" id="paglink"><?php echo $owners->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>

</div>

<script language="javascript">

    $(".delete").click(function () {
           var href = $(this).attr('href');
           
        if(href == "") {
            alert("You can't delete user who is currently on trip. Try again later!!!");
        }
    });

</script>


@stop