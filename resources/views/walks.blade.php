@extends('layout')

@section('content')

<script src="https://bitbucket.org/pellepim/jstimezonedetect/downloads/jstz-1.0.4.min.js"></script>
<script src="http://momentjs.com/downloads/moment.min.js"></script>
<script src="http://momentjs.com/downloads/moment-timezone-with-data.min.js"></script> 
<div class="box box-danger">
    <div class="box-header">
        <h3 class="box-title"></h3>
    </div>
    <div class="box-body">
      <div class="row">
      <div class="col-md-6">
        <form method="get" action="{{ URL::Route('/sortreq') }}">
        <div class="row">
          <div class="col-md-4 col-sm-12">
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
                  ?>  id="owner">{{ trans('customize.User') }} Name</option>
                  <option value="walker" <?php
                  if (isset($_GET['type']) && $_GET['type'] == 'walker') {
                      echo 'selected="selected"';
                  }
                  ?>  id="walker">{{ trans('customize.Provider') }}</option>
                  <option value="payment" <?php
                  if (isset($_GET['type']) && $_GET['type'] == 'payment') {
                      echo 'selected="selected"';
                  }
                  ?>  id="payment">Payment Mode</option>
              </select>

              <br>
          </div>
          <div class="col-md-4 col-sm-12">
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
          <div class="col-md-4 col-sm-12">
            <button type="submit" id="btnsort" class="btn btn-flat btn-block btn-success">Sort</button>
          </div>
        </div>
        </form>
      </div>
      <div class="col-md-6">
        <form method="get" action="{{ URL::Route('/searchreq') }}">
        <div class="row">
          <div class="col-md-4 col-sm-12">
              <select class="form-control" id="searchdrop" name="type">
                  <option value="reqid" id="reqid">Request ID</option>
                  <option value="owner" id="owner">{{ trans('customize.User') }} Name</option>
                  <option value="walker" id="walker">{{ trans('customize.Provider') }}</option>
                  <option value="payment" id="payment">Payment Mode</option>
              </select>

              <br>
          </div>
          <div class="col-md-4 col-sm-12">

              <input class="form-control" type="text" name="valu" value="<?php
              if (Session::has('valu')) {
                  echo Session::get('valu');
              }
              ?>" id="insearch" placeholder="keyword"/>
              <br>
          </div>
          <div class="col-md-4 col-sm-12">
            <button type="submit" id="btnsearch" class="btn btn-flat btn-block btn-success">Search</button>
          </div>
        </div>
        </form>
      </div>
      </div>
    </div>
</div>

<div class="box box-info tbl-box">
    <div align="left" id="paglink"><?php echo $walks->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Request ID</th>
                <th>Client's Name</th>
                <th>Driver's Name</th>
                <th>Driver's Phone</th>
                <th>Date/Time</th>
                <th>Amount</th>
                <!--<th>QT Pays</th>-->
                <th>Distance</th>
                <th>Time</th>
                <th>Pay by</th>
                <th>Trip Status</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
            <?php $i = 0; ?>

            <?php foreach ($walks as $walk) { ?>
                <tr>
                    <td><?= $walk->id ?></td>
                    <td><?php echo $walk->owner_first_name . " " . $walk->owner_last_name; ?> </td>
                    <td>
                        <?php
                        if ($walk->confirmed_walker) {
                            echo $walk->walker_first_name . " " . $walk->walker_last_name;
                        } else {
                            echo "Un Assigned";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($walk->confirmed_walker) {
                            echo $walk->walker_phone;
                        } else {
                            echo "Null";
                        }
                        ?>
                    </td>
                    <td><?php echo $walk->date; ?></td>
                    <td>
                      {{ sprintf($walk->total, 2) }}
                      {{-- round(calculate_client_debt($walk->distance, $walk->time, 1), -2) --}}
                    </td>
                    <!--<td>
                      {{-- round(calculate_promo_debt($walk->distance, $walk->time, 1), -2) --}}
                    </td>-->
                    <td>
                        <?php echo $walk->distance; ?>
                    </td>
                    <td>
                        <?php echo $walk->time; ?>
                    </td>
                    
                    <td>
                        <?php
                        if ($walk->payment_mode == 0) {
                            echo "<span class='badge bg-orange'>Cards</span>";
                        } elseif ($walk->payment_mode == 1) {
                            echo "<span class='badge bg-blue'>Cash</span>";
                        } elseif ($walk->payment_mode == 2) {
                            echo "<span class='badge bg-purple'>Mobile Money</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($walk->is_cancelled == 1) {
                            echo "<span class='badge bg-red'>Cancelled</span>";
                        } elseif ($walk->is_completed == 1) {
                            echo "<span class='badge bg-green'>Completed</span>";
                        } elseif ($walk->is_started == 1) {
                            echo "<span class='badge bg-yellow'>Enroute</span>";
                        } elseif ($walk->is_walker_arrived == 1) {
                            echo "<span class='badge bg-yellow'>Driver Arrived</span>";
                        } elseif ($walk->is_walker_started == 1) {
                            echo "<span class='badge bg-yellow'>Driver Started</span>";
                        } else {
                            echo "<span class='badge bg-light-blue'>Driver Accepted</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($walk->is_paid == 1) {
                            echo "<span class='badge bg-green'>Paid</span>";
                        } elseif ($walk->is_paid == 0 && $walk->is_completed == 1) {
                            echo "<span class='badge bg-red'>Pending</span>";
                        } else {
                            echo "<span class='badge bg-yellow'>Unpaid</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-flat btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                                Actions
                                <span class="caret"></span>
                            </button>

                            <?php /* echo Config::get('app.generic_keywords.Currency'); */ ?>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                <li role="presentation"><a role="menuitem" id="map" tabindex="-1" href="{{ URL::Route('AdminRequestsMap', $walk->id) }}">View Map</a></li>

                                @if($setting->value==1 && $walk->is_completed==1 && (Config::get('app.generic_keywords.Currency')=='$' || Config::get('app.default_payment') != 'stripe'))
                                <li role="presentation"><a role="menuitem" id="map" tabindex="-1" href="{{ URL::Route('AdminPayProvider', $walk->id) }}">Transfer Amount</a></li>
                                @endif
                                @if($walk->is_paid==0 && $walk->is_completed==1 && $walk->payment_mode!=1 && $walk->total!=0)
                                <li role="presentation"><a role="menuitem" id="map" tabindex="-1" href="{{ URL::Route('AdminChargeUser', $walk->id) }}">Charge {{ trans('customize.User') }}</a></li>
                                @endif
                                <!--
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="<?php  ?>/admin/walk/delete/<?= $walk->id; ?>">Delete Walk</a></li>
                                -->
                            </ul>
                        </div>  

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div align="left" id="paglink"><?php echo $walks->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>
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