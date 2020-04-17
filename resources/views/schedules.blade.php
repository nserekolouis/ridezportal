@extends('layout')

@section('content')

<!--<script src="https://bitbucket.org/pellepim/jstimezonedetect/downloads/jstz-1.0.4.min.js"></script>
<script src="http://momentjs.com/downloads/moment.min.js"></script>
<script src="http://momentjs.com/downloads/moment-timezone-with-data.min.js"></script> -->
<!--
<div class="col-md-6 col-sm-12">

    <div class="box box-danger">

        <form method="get" action="{{ URL::Route('/sortreq') }}">
            <div class="box-header">
                <h3 class="box-title">Sort</h3>
            </div>
            <div class="box-body row">

                <div class="col-md-6 col-sm-12">

                    <select class="form-control" id="sortdrop" name="type">
                        <option value="reqid" <?php
if (isset($_GET['type']) && $_GET['type'] == 'reqid') {
    echo 'selected="selected"';
}
?>  id="reqid">Request ID</option>
                        <option value="owner" <?php
if (isset($_GET['type']) && $_GET['type'] == 'owner') {
    echo 'selected="selected"';
}
?>  id="owner">{{ trans('customize.User')}} Name</option>
                        <option value="walker" <?php
if (isset($_GET['type']) && $_GET['type'] == 'walker') {
    echo 'selected="selected"';
}
?>  id="walker">{{ trans('customize.Provider')}}</option>
                        <option value="payment" <?php
if (isset($_GET['type']) && $_GET['type'] == 'payment') {
    echo 'selected="selected"';
}
?>  id="payment">Payment Mode</option>
                    </select>

                    <br>
                </div>
                <div class="col-md-6 col-sm-12">
                    <select class="form-control" id="sortdroporder" name="valu">
                        <option value="asc" <?php
if (isset($_GET['type']) && $_GET['valu'] == 'asc') {
    echo 'selected="selected"';
}
?>  id="asc">Ascending</option>
                        <option value="desc" <?php
if (isset($_GET['type']) && $_GET['valu'] == 'desc') {
    echo 'selected="selected"';
}
?>  id="desc">Descending</option>
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

        <form method="get" action="{{ URL::Route('/searchreq') }}">
            <div class="box-header">
                <h3 class="box-title">Filter</h3>
            </div>
            <div class="box-body row">

                <div class="col-md-6 col-sm-12">

                    <select class="form-control" id="searchdrop" name="type">
                        <option value="reqid" id="reqid">Request ID</option>
                        <option value="owner" id="owner">{{ trans('customize.User')}} Name</option>
                        <option value="walker" id="walker">{{ trans('customize.Provider')}}</option>
                        <option value="payment" id="payment">Payment Mode</option>
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
-->


<div class="box box-info tbl-box">
    <div align="left" id="paglink"><?php echo $schedules->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>
    <?php /* echo date("d M Y g:iA",time()); */ ?>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <!--<th>Request ID</th>-->
                <th>{{ trans('customize.Schedules') }} Date</th>
                <th>{{ trans('customize.Schedules') }} Time</th>
                <th>{{ trans('customize.User')}} Name</th>
                <th>{{ trans('customize.User')}} Time-Zone</th>
                <th>Source Address</th>
                <th>Destination Address</th>
                <th>Promotional Code</th>
                <th>Payment Mode</th>
                <!--<th>Request ID</th>
                <th>{{ trans('customize.User')}} Name</th>
                <th>{{ trans('customize.Provider')}}</th>
                <th>Date/Time</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Payment Status</th>
                <th>Action</th>-->
            </tr>
            <?php $i = 0; ?>

            <?php foreach ($schedules as $schedule) { ?>
                <tr>
                    <!--<td><?= $schedule->id ?></td>-->
                    <td><?php echo date("d M Y", strtotime($schedule->server_start_time)); ?></td>
                    <td><?php echo date("g:iA", strtotime($schedule->server_start_time)); ?></td>
                    <td><?php echo $schedule->owner_first_name . " " . $schedule->owner_last_name; ?> </td>
                    <td><?= $schedule->time_zone ?></td>
                    <td><?= $schedule->src_address ?></td>
                    <td><?= $schedule->dest_address ?></td>
                    <td>
                        <?php
                        if ($schedule->promo_code == "" || $schedule->promo_code == NULL) {
                            echo "<span class='badge bg-red'>" . Config::get('app.blank_fiend_val') . "</span>";
                        } else {
                            echo $schedule->promo_code;
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($schedule->payment_mode == 0) {
                            echo "<span class='badge bg-orange'>Stored Cards</span>";
                        } elseif ($schedule->payment_mode == 1) {
                            echo "<span class='badge bg-blue'>Pay by Cash</span>";
                        } elseif ($schedule->payment_mode == 2) {
                            echo "<span class='badge bg-purple'>Paypal</span>";
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div align="left" id="paglink"><?php echo $schedules->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>




</div>

<!--
  <script>
  $(function() {
    $( "#start-date" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#end-date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#end-date" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#start-date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>
-->

<script type="text/javascript">
</script>
@stop