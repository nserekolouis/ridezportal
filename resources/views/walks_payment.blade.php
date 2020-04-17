@extends('layout')
@section('content')

<div class="page-content">

    <div class="box box-info tbl-box">
        <div class="portlet-body flip-scroll">


            <table class="table table-bordered table-striped table-condensed flip-content">
                <thead class="flip-content">
                    <tr>
                        <th>Id</th>
                        <th>Trips</th>
                        <th>Total</th>
                        <th>Week Ending On</th>
                        <th>Download</th>

                    </tr>
                </thead>
                <tbody>

                    <?php
                    $i = 0;
                    $start;
                    $end;
                    foreach ($walks as $walk) {

                        if ($i == 0) {
                            $start = 0;
                            $end = $walk->id;
                            $startdate = 0;
                            $enddate = $walk->created_at;
                        }
                        if ($i != 0) {

                            $start = $end - 1;
                            $end = $walk->id;

                            $startdate = strtotime($enddate);
                            $startdate = strtotime("+1 day", $startdate);
                            $startdate = date('Y-m-d H:m:s', $startdate);
                            $enddate = $walk->created_at;
                        }

                        if ($i == 1) {
                            $end = $walk->id;
                            $enddate = $walk->created_at;
                        }
                        ?>
                        <tr>

                            <td><?= $walk->id ?> </td>
                            <td><?= $walk->trips ?> </td>
                            <td><?= Config::get('app.currency') . sprintf2(($walk->total - $walk->pay_to_provider + $walk->take_from_provider), 2) ?></td>
                            <td><?php
                                //echo $walk->created_at;
                                $formate = 'Y-m-d H:i:s';
                                $displaydate = Config::get('app.appdate');

                                if (date('N', strtotime($walk->created_at)) == 1) {
                                    $dateweek = strtotime(date($formate, strtotime($walk->created_at)) . " -6 days");
                                    echo $weekend = date($displaydate, $dateweek);
//                            echo "<br>";
//                          echo   date('l', $dateweek);
                                }


                                if (date('N', strtotime($walk->created_at)) == 2) {
                                    $dateweek = strtotime(date($formate, strtotime($walk->created_at)) . " +5 days");
                                    echo $weekend = date($displaydate, $dateweek);
//                            echo "<br>";
//                          echo   date('l', $dateweek);
                                } else if (date('N', strtotime($walk->created_at)) == 3) {
                                    $dateweek = strtotime(date($formate, strtotime($walk->created_at)) . " +4 days");
                                    echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                                } else if (date('N', strtotime($walk->created_at)) == 4) {
                                    $dateweek = strtotime(date($formate, strtotime($walk->created_at)) . " +3 days");
                                    echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                                } else if (date('N', strtotime($walk->created_at)) == 5) {
                                    $dateweek = strtotime(date($formate, strtotime($walk->created_at)) . " +2 days");
                                    echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                                } else if (date('N', strtotime($walk->created_at)) == 6) {
                                    $dateweek = strtotime(date($formate, strtotime($walk->created_at)) . " +1 days");
                                    echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                                } else if (date('N', strtotime($walk->created_at)) == 7) {
                                    $dateweek = strtotime(date($formate, strtotime($walk->created_at)) . " +0 days");
                                    echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                                }
                                ?>
                            </td>
                            <td>
                                <form method="post" action="<?= web_url(); ?>/admin/requests_pdf">
                                    <input type="hidden" name="id" value="<?php echo $walk->id; ?>">
                                    <input type="hidden" name="weekend" value="<?php echo $weekend; ?>">
                                    <input type="hidden" name="total" value="<?php echo $walk->total; ?>">
                                    <input type="hidden" name="trips" value="<?php echo $walk->trips; ?>">
                                    <input type="hidden" name="pay_to_provider" value="<?php echo $walk->pay_to_provider; ?>">
                                    <input type="hidden" name="take_from_provider" value="<?php echo $walk->take_from_provider; ?>">
                                    <input type="submit" class="btn blue" value="Pdf">
                                </form>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>

            <div align="right" id="paglink"><?php echo $walks->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>



        </div>
    </div>
</div>


<script>

    $('#datetimepicker').datetimepicker({value: '2015/04/15 05:03', step: 10});

    $('#some_class').datetimepicker();
    $('#some_class1').datetimepicker();


</script>
@stop