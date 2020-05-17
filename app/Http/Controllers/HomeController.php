<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Requests;
use App\Owner;
use App\Walker;
use App\ScheduledRequests;
use App\Icons;
use App\Theme;
use App\Settings;
use App\RequestMeta;
use App\Information;
use App\ProviderType;
use App\Document;
use App\PromoCodes;
use App\Helper;
use DB;
use View;
use Response;
use Lang;
use Config;
use Request;

use Illuminate\Support\Facades\Auth;




class HomeController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect('/report');
    }

    public function report(){
      //return view('home'); 
        $braintree_environment = \Config::get('app.braintree_environment');
        $braintree_merchant_id = \Config::get('app.braintree_merchant_id');
        $braintree_public_key = \Config::get('app.braintree_public_key');
        $braintree_private_key = \Config::get('app.braintree_private_key');
        $braintree_cse = \Config::get('app.braintree_cse');
        $twillo_account_sid = \Config::get('app.twillo_account_sid');
        $twillo_auth_token = \Config::get('app.twillo_auth_token');
        $twillo_number = \Config::get('app.twillo_number');
        $stripe_publishable_key = \Config::get('app.stripe_publishable_key');
        $default_payment = \Config::get('app.default_payment');
        $stripe_secret_key = \Config::get('app.stripe_secret_key');
        $mail_driver = \Config::get('mail.mail_driver');
        $email_name = \Config::get('mail.from.name');
        $email_address = \Config::get('mail.from.address');
        $mandrill_secret = \Config::get('services.mandrill_secret');
        /* DEVICE PUSH NOTIFICATION DETAILS */
        $customer_certy_url = \Config::get('app.customer_certy_url');
        $customer_certy_pass = \Config::get('app.customer_certy_pass');
        $customer_certy_type = \Config::get('app.customer_certy_type');
        $provider_certy_url = \Config::get('app.provider_certy_url');
        $provider_certy_pass = \Config::get('app.provider_certy_pass');
        $provider_certy_type = \Config::get('app.provider_certy_type');
        $gcm_browser_key = \Config::get('app.gcm_browser_key');
        /* DEVICE PUSH NOTIFICATION DETAILS END */
        $install = array(
            'braintree_environment' => $braintree_environment,
            'braintree_merchant_id' => $braintree_merchant_id,
            'braintree_public_key' => $braintree_public_key,
            'braintree_private_key' => $braintree_private_key,
            'braintree_cse' => $braintree_cse,
            'twillo_account_sid' => $twillo_account_sid,
            'twillo_auth_token' => $twillo_auth_token,
            'twillo_number' => $twillo_number,
            'stripe_publishable_key' => $stripe_publishable_key,
            'stripe_secret_key' => $stripe_secret_key,
            'mail_driver' => $mail_driver,
            'email_address' => $email_address,
            'email_name' => $email_name,
            'mandrill_secret' => $mandrill_secret,
            'default_payment' => $default_payment,
            /* DEVICE PUSH NOTIFICATION DETAILS */
            'customer_certy_url' => $customer_certy_url,
            'customer_certy_pass' => $customer_certy_pass,
            'customer_certy_type' => $customer_certy_type,
            'provider_certy_url' => $provider_certy_url,
            'provider_certy_pass' => $provider_certy_pass,
            'provider_certy_type' => $provider_certy_type,
            'gcm_browser_key' => $gcm_browser_key,
                /* DEVICE PUSH NOTIFICATION DETAILS END */
        );
        $start_date = Input::get('start_date');
        Session::put('start_date', $start_date);
        $end_date = Input::get('end_date');
        Session::put('end_date', $end_date);
        $submit = Input::get('submit');
        Session::put('submit', $submit);
        $walker_id = Input::get('walker_id');
        Session::put('walker_id', $walker_id);
        $owner_id = Input::get('owner_id');
        Session::put('owner_id', $owner_id);
        $status = Input::get('status');
        Session::put('status', $status);

        $start_time = date("Y-m-d H:i:s", strtotime($start_date));
        $end_time = date("Y-m-d H:i:s", strtotime($end_date));
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = date("Y-m-d", strtotime($end_date));

        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }

        if (Input::get('status') && Input::get('status') != 0) {
            if ($status == 1) {
                $query = $query->where('request.is_completed', '=', 1);
            } else {
                $query = $query->where('request.is_cancelled', '=', 1);
            }
        } else {

            $query = $query->where(function ($que) {
                $que->where('request.is_completed', '=', 1)
                        ->orWhere('request.is_cancelled', '=', 1);
            });
        }

        $walks = $query->select('request.request_start_time', 'walker_type.name as type', 'request.ledger_payment', 'request.card_payment', 'owner.first_name as owner_first_name', 'owner.last_name as owner_last_name', 'walker.first_name as walker_first_name', 'walker.last_name as walker_last_name', 'owner.id as owner_id', 'walker.id as walker_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_walker_arrived', 'request.payment_mode', 'request.is_completed', 'request.is_paid', 'request.is_walker_started', 'request.confirmed_walker'
                , 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.promo_payment');
        $walks = $walks->orderBy('id', 'DESC')->paginate(10);

        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }

        $completed_rides = $query->where('request.is_completed', 1)->count();


        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }
        $cancelled_rides = $query->where('request.is_cancelled', 1)->count();


        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }
        $card_payment = $query->where('request.payment_mode', 0)->where('request.is_completed', 1)->sum('request.card_payment');


        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }
        $promo_payment = $query->where('request.is_completed', 1)->sum('request.promo_payment');
        $credit_payment = $query->where('request.is_completed', 1)->sum('request.ledger_payment');
        $cash_payment = $query->where('request.payment_mode', 1)->sum('request.total');
        $scheduled_rides = ScheduledRequests::count();


        if (Input::get('submit') && Input::get('submit') == 'Download_Report') {

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=data.csv');
            $handle = fopen('php://output', 'w');
            $settings = Settings::where('key', 'default_distance_unit')->first();
            $unit = $settings->value;
            if ($unit == 0) {
                $unit_set = 'kms';
            } elseif ($unit == 1) {
                $unit_set = 'miles';
            }
            fputcsv($handle, array('ID', 'Date', 'Type of Service', 'Provider', 'Owner', 'Distance (' . $unit_set . ')', 'Time (Minutes)', 'Payment Mode', 'Earning', 'Referral Bonus', 'Promotional Bonus', 'Card Payment'));
            foreach ($walks as $request) {
                $pay_mode = "Card Payment";
                if ($request->payment_mode == 1) {
                    $pay_mode = "Cash Payment";
                }
                fputcsv($handle, array(
                    $request->id,
                    date('l, F d Y h:i A', strtotime($request->request_start_time)),
                    $request->type,
                    $request->walker_first_name . " " . $request->walker_last_name,
                    $request->owner_first_name . " " . $request->owner_last_name,
                    sprintf2($request->distance, 2),
                    sprintf2($request->time, 2),
                    $pay_mode,
                    sprintf2($request->total, 2),
                    sprintf2($request->ledger_payment, 2),
                    sprintf2($request->promo_payment, 2),
                    sprintf2($request->card_payment, 2),
                ));
            }

            fputcsv($handle, array());
            fputcsv($handle, array());
            fputcsv($handle, array('Total Trips', $completed_rides + $cancelled_rides));
            fputcsv($handle, array('Completed Trips', $completed_rides));
            fputcsv($handle, array('Cancelled Trips', $cancelled_rides));
            fputcsv($handle, array('Scheduled Trips', $scheduled_rides));
            fputcsv($handle, array('Total Payments', sprintf2(($credit_payment + $card_payment), 2)));
            fputcsv($handle, array('Card Payment', sprintf2($card_payment, 2)));
            fputcsv($handle, array('Referral Payment', sprintf2($credit_payment, 2)));
            fputcsv($handle, array('Cash Payment', sprintf2($cash_payment, 2)));
            fputcsv($handle, array('Promotional Payment', sprintf2($promo_payment, 2)));

            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );
        } else {
            /* $currency_selected = Keywords::where('alias', 'Currency')->first();
              $currency_sel = $currency_selected->keyword; */
            $currency_sel = \Config::get('app.generic_keywords.Currency');
            $walkers = Walker::get();
            $owners = Owner::get();
            $title = ucwords(trans('customize.Dashboard'));
            return View::make('dashboard')
                            ->with('title', $title)
                            ->with('page', 'dashboard')
                            ->with('walks', $walks)
                            ->with('owners', $owners)
                            ->with('walkers', $walkers)
                            ->with('completed_rides', $completed_rides)
                            ->with('cancelled_rides', $cancelled_rides)
                            ->with('card_payment', $card_payment)
                            ->with('install', $install)
                            ->with('currency_sel', $currency_sel)
                            ->with('cash_payment', $cash_payment)
                            ->with('promo_payment', $promo_payment)
                            ->with('scheduled_rides', $scheduled_rides)
                            ->with('credit_payment', $credit_payment);
        }  
    }

    public function map_view() {
        $settings = Settings::where('key', 'map_center_latitude')->first();
        $center_latitude = $settings->value;
        $settings = Settings::where('key', 'map_center_longitude')->first();
        $center_longitude = $settings->value;
        $title = ucwords(trans('customize.map_view')); /* 'Map View' */
        return View::make('map_view')
                        ->with('title', $title)
                        ->with('page', 'map-view')
                        ->with('center_longitude', $center_longitude)
                        ->with('center_latitude', $center_latitude);
    }

    public function walkers() {
        Session::forget('type');
        Session::forget('valu');
        Session::forget('che');
        //$query = "SELECT *,(select count(*) from request_meta where walker_id = walker.id  and status != 0 ) as total_requests,(select count(*) from request_meta where walker_id = walker.id and status=1) as accepted_requests FROM `walker`";
        //$walkers = DB::select(DB::raw($query));
        /* $walkers1 = DB::table('walker')
          ->leftJoin('request_meta', 'walker.id', '=', 'request_meta.walker_id')
          ->where('request_meta.status', '!=', 0)
          ->count();
          $walkers2 = DB::table('walker')
          ->leftJoin('request_meta', 'walker.id', '=', 'request_meta.walker_id')
          ->where('request_meta.status', '=', 1)
          ->count();

          $walkers = Walker::paginate(10); */
        $subQuery = DB::table('request_meta')
                ->select(DB::raw('count(*)'))
                ->whereRaw('walker_id = walker.id and status != 0');
        $subQuery1 = DB::table('request_meta')
                ->select(DB::raw('count(*)'))
                ->whereRaw('walker_id = walker.id and status=1');

        $walkers = DB::table('walker')
                ->select('walker.*', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))->where('deleted_at', NULL)
                /* ->where('walker.is_deleted', 0) */
                
                ->orderBy('walker.created_at', 'DESC')
                ->paginate(10);
        $title = ucwords(trans('customize.Provider') . 's');
        #var_dump($walkers);

         /* 'Providers' */
        return View::make('walkers')
                        ->with('title', $title)
                        ->with('page', 'walkers')
                        ->with('walkers', $walkers)
                        ->with('total_requests', $walkers)
                        ->with('accepted_requests', $walkers);
    }

    public function walks() {
        Session::forget('type');
        Session::forget('valu');
        $walks = DB::table('request')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->select('owner.first_name as owner_first_name', 'owner.last_name as owner_last_name', 'walker.first_name as walker_first_name', 'walker.last_name as walker_last_name', 'walker.phone as walker_phone', 'owner.id as owner_id', 'walker.id as walker_id', 'walker.merchant_id as walker_merchant', 'request.id as id', 'request.created_at as date', 'request.payment_mode', 'request.is_started', 'request.is_walker_arrived', 'request.payment_mode', 'request.is_completed', 'request.is_paid', 'request.is_walker_started', 'request.confirmed_walker'
                        , 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.transfer_amount')
                ->orderBy('request.created_at', 'DESC')
                                //->where('request.current_walker', '!=', 0)
                                ->where('request.confirmed_walker', '!=', 0)
                ->paginate(10);
        $setting = Settings::where('key', 'paypal')->first();
        #var_dump($setting);
        $title = ucwords(trans('customize.Request') . 's'); 
         #var_dump($title);
        /* 'Requests' */
        return View::make('walks')
                        ->with('title', $title)
                        ->with('page', 'walks')
                        ->with('walks', $walks)
                        ->with('setting', $setting);
    }

      public function walkers_xml() {
        $walkers = Walker::where('');
        $response = "";
        $response .= '<markers>';

        $walkers = DB::table('walker')->select('walker.*')->get();
        $walker_ids = array();
        foreach ($walkers as $walker) {
                        $walker->type = DB::table('walker_type')->select('walker_type.name')->where('id', '=', $walker->type)->get()[0]->name;
            if ($walker->is_active == 1 && $walker->is_available == 1 && $walker->is_approved == 1/* && $walker->is_deleted == 0 */) {
                $response .= '<marker ';
                $response .= 'name="' . $walker->first_name . " " . $walker->last_name . '" ';
                $response .= 'client_name="' . $walker->first_name . " " . $walker->last_name . '" ';
                $response .= 'contact="' . $walker->phone . '" ';
                $response .= 'amount="' . 0 . '" ';
                                $response .= 'service="' . $walker->type . '" ';
                $response .= 'angl="' . $walker->bearing . '" ';
                $response .= 'lat="' . $walker->latitude . '" ';
                $response .= 'lng="' . $walker->longitude . '" ';
                $response .= 'id="' . $walker->id . '" ';
                $response .= 'type="driver_free" ';
                $response .= '/>';
                array_push($walker_ids, $walker->id);
            } else if ($walker->is_active == 1 && $walker->is_available == 0 && $walker->is_approved == 1/* && $walker->is_deleted == 0 */) {
                $response .= '<marker ';
                $response .= 'name="' . $walker->first_name . " " . $walker->last_name . '" ';
                $response .= 'client_name="' . $walker->first_name . " " . $walker->last_name . '" ';
                $response .= 'contact="' . $walker->phone . '" ';
                $response .= 'amount="' . 0 . '" ';
                                $response .= 'service="' . $walker->type . '" ';
                $response .= 'angl="' . $walker->bearing . '" ';
                $response .= 'lat="' . $walker->latitude . '" ';
                $response .= 'lng="' . $walker->longitude . '" ';
                $response .= 'id="' . $walker->id . '" ';
                $response .= 'type="driver_on_trip" ';
                $response .= '/>';
                array_push($walker_ids, $walker->id);
            } else if (($walker->is_active == 0 || $walker->is_active == 1) && ($walker->is_available == 0 || $walker->is_available == 1) && $walker->is_approved == 0 /* && $walker->is_deleted == 0 */) {
                $response .= '<marker ';
                $response .= 'name="' . $walker->first_name . " " . $walker->last_name . '" ';
                $response .= 'client_name="' . $walker->first_name . " " . $walker->last_name . '" ';
                $response .= 'contact="' . $walker->phone . '" ';
                $response .= 'amount="' . 0 . '" ';
                                $response .= 'service="' . $walker->type . '" ';
                $response .= 'angl="' . $walker->bearing . '" ';
                $response .= 'lat="' . $walker->latitude . '" ';
                $response .= 'lng="' . $walker->longitude . '" ';
                $response .= 'id="' . $walker->id . '" ';
                $response .= 'type="driver_not_approved" ';
                $response .= '/>';
                array_push($walker_ids, $walker->id);
            } /* else if (($walker->is_active == 0 || $walker->is_active == 1) && ($walker->is_available == 0 || $walker->is_available == 1) && ($walker->is_approved == 0 || $walker->is_approved == 1) && $walker->is_deleted == 1) {
              $response .= '<marker ';
              $response .= 'name="' . $walker->first_name . " " . $walker->last_name . '" ';
              $response .= 'client_name="' . $walker->first_name . " " . $walker->last_name . '" ';
              $response .= 'contact="' . $walker->phone . '" ';
              $response .= 'amount="' . $walker->topup_bal . '" ';
                            $response .= 'service="' . $walker->type . '" ';
              $response .= 'licence_plate="' . $walker->licence_plate . '" ';
              $response .= 'lat="' . $walker->latitude . '" ';
              $response .= 'lng="' . $walker->longitude . '" ';
              $response .= 'id="' . $walker->id . '" ';
              $response .= 'company_name="' . $walker->company_name . '" ';
              $response .= 'type="driver_deleted" ';
              $response .= '/>';
              array_push($walker_ids, $walker->id);
              } */
        }

        /* // busy walkers
          $walkers = DB::table('walker')
          ->where('walker.is_active', 1)
          ->where('walker.is_available', 0)
          ->where('walker.is_approved', 1)
          ->select('walker.id', 'walker.phone', 'walker.first_name', 'walker.last_name', 'walker.latitude', 'walker.longitude')
          ->get();

          $walker_ids = array();


          foreach ($walkers as $walker) {
          $response .= '<marker ';
          $response .= 'name="' . $walker->first_name . " " . $walker->last_name . '" ';
          $response .= 'client_name="' . $walker->first_name . " " . $walker->last_name . '" ';
          $response .= 'contact="' . $walker->phone . '" ';
          $response .= 'amount="' . 0 . '" ';
                    $response .= 'service="' . $walker->type . '" ';
          $response .= 'lat="' . $walker->latitude . '" ';
          $response .= 'lng="' . $walker->longitude . '" ';
          $response .= 'id="' . $walker->id . '" ';
          $response .= 'type="client_pay_done" ';
          $response .= '/>';
          array_push($walker_ids, $walker->id);
          }

          $walker_ids = array_unique($walker_ids);
          $walker_ids_temp = implode(",", $walker_ids);

          $walkers = DB::table('walker')
          ->where('walker.is_active', 0)
          ->where('walker.is_approved', 1)
          ->select('walker.id', 'walker.phone', 'walker.first_name', 'walker.last_name', 'walker.latitude', 'walker.longitude')
          ->get();
          foreach ($walkers as $walker) {
          $response .= '<marker ';
          $response .= 'name="' . $walker->first_name . " " . $walker->last_name . '" ';
          $response .= 'client_name="' . $walker->first_name . " " . $walker->last_name . '" ';
          $response .= 'contact="' . $walker->phone . '" ';
          $response .= 'amount="' . 0 . '" ';
                    $response .= 'service="' . $walker->type . '" ';
          $response .= 'lat="' . $walker->latitude . '" ';
          $response .= 'lng="' . $walker->longitude . '" ';
          $response .= 'id="' . $walker->id . '" ';
          $response .= 'type="client_no_pay" ';
          $response .= '/>';
          array_push($walker_ids, $walker->id);
          }
          $walker_ids = array_unique($walker_ids);
          $walker_ids = implode(",", $walker_ids);
          if ($walker_ids) {
          $query = "select * from walker where is_approved = 1 and id NOT IN ($walker_ids)";
          } else {
          $query = "select * from walker where is_approved = 1";
          }
          // free walkers
          $walkers = DB::select(DB::raw($query));
          foreach ($walkers as $walker) {
          $response .= '<marker ';
          $response .= 'name="' . $walker->first_name . " " . $walker->last_name . '" ';
          $response .= 'client_name="' . $walker->first_name . " " . $walker->last_name . '" ';
          $response .= 'contact="' . $walker->phone . '" ';
          $response .= 'amount="' . 0 . '" ';
                    $response .= 'service="' . $walker->type . '" ';
          $response .= 'lat="' . $walker->latitude . '" ';
          $response .= 'lng="' . $walker->longitude . '" ';
          $response .= 'id="' . $walker->id . '" ';
          $response .= 'type="client" ';
          $response .= '/>';
          } */
        $response .= '</markers>';
        $content = View::make('walkers_xml')->with('response', $response);
        return Response::make($content, '200')->header('Content-Type', 'text/xml');
    }

    public function scheduled_walks() {
        Session::forget('type');
        Session::forget('valu');
        $schedules = DB::table('scheduled_requests')
                ->leftJoin('owner', 'scheduled_requests.owner_id', '=', 'owner.id')
                ->select('owner.first_name as owner_first_name', 'owner.last_name as owner_last_name', 'owner.id as owner_id', 'scheduled_requests.id as id', 'scheduled_requests.created_at as date', 'scheduled_requests.time_zone', 'scheduled_requests.src_address', 'scheduled_requests.dest_address', 'scheduled_requests.promo_code', 'scheduled_requests.server_start_time', 'scheduled_requests.start_time', 'scheduled_requests.payment_mode')
                ->orderBy('scheduled_requests.server_start_time', 'ASC')
                ->paginate(10);
        $total_schedules = ScheduledRequests::count();
        $setting = Settings::where('key', 'paypal')->first();
        $title = ucwords(trans('customize.Schedules') . " : Total = " . $total_schedules);
        return View::make('schedules')
                        ->with('title', $title)
                        ->with('page', 'schedule')
                        ->with('schedules', $schedules)
                        ->with('setting', $setting);
    }

    public function owners() {
        Session::forget('type');
        Session::forget('valu');
        $owners = Owner::orderBy('id', 'DESC')->paginate(10);
        $title = ucwords(trans('customize.User') . 's'); /* 'Owners' */
        return View::make('owners')
                        ->with('title', $title)
                        ->with('page', 'owners')
                        ->with('owners', $owners);
    }

     //Sort Owners
    public function sortur() {
        $valu = $_GET['valu'];
        $type = $_GET['type'];
        Session::put('valu', $valu);
        Session::put('type', $type);
        if ($type == 'userid') {
            $typename = "Owner ID";
            $users = Owner::orderBy('id', $valu)->paginate(10);
        } elseif ($type == 'username') {
            $typename = "Owner Name";
            $users = Owner::orderBy('first_name', $valu)->paginate(10);
        } elseif ($type == 'useremail') {
            $typename = "Owner Email";
            $users = Owner::orderBy('email', $valu)->paginate(10);
        }
        $title = ucwords(trans('customize.User') . 's' . " | Sorted by " . $typename . " in " . $valu); /* 'Owners | Sorted by ' . $typename . ' in ' . $valu */
        return View::make('owners')
                        ->with('title', $title)
                        ->with('page', 'owners')
                        ->with('owners', $users);
    }

    public function searchur() {
        $valu = $_GET['valu'];
        $type = $_GET['type'];
        Session::put('valu', $valu);
        Session::put('type', $type);
        if ($type == 'userid') {
            $owners = Owner::where('id', $valu)->paginate(10);
        } elseif ($type == 'username') {
            $owners = Owner::where('first_name', 'like', '%' . $valu . '%')->orWhere('last_name', 'like', '%' . $valu . '%')->where('deleted_at', NULL)->paginate(10);
        } elseif ($type == 'useremail') {
            $owners = Owner::where('email', 'like', '%' . $valu . '%')->paginate(10);
        } elseif ($type == 'useraddress') {
            $owners = Owner::where('address', 'like', '%' . $valu . '%')->orWhere('state', 'like', '%' . $valu . '%')->orWhere('country', 'like', '%' . $valu . '%')->paginate(10);
        }
        $title = ucwords(trans('customize.User') . "s" . " | Search Result"); /* 'Owners | Search Result' */
        return View::make('owners')
                        ->with('title', $title)
                        ->with('page', 'owners')
                        ->with('owners', $owners);
    }

    public function reviews() {
        Session::forget('type');
        Session::forget('valu');
        $provider_reviews = DB::table('review_walker')
                ->leftJoin('walker', 'review_walker.walker_id', '=', 'walker.id')
                ->leftJoin('owner', 'review_walker.owner_id', '=', 'owner.id')
                ->select('review_walker.id as review_id', 'review_walker.rating', 'review_walker.comment', 'owner.first_name as owner_first_name', 'owner.last_name as owner_last_name', 'walker.first_name as walker_first_name', 'walker.last_name as walker_last_name','walker.phone as walker_phone', 'owner.id as owner_id', 'walker.id as walker_id', 'review_walker.created_at')
                ->orderBy('review_walker.id', 'DESC')
                ->paginate(10);

        $user_reviews = DB::table('review_dog')
                ->leftJoin('walker', 'review_dog.walker_id', '=', 'walker.id')
                ->leftJoin('owner', 'review_dog.owner_id', '=', 'owner.id')
                ->select('review_dog.id as review_id', 'review_dog.rating', 'review_dog.comment', 'owner.first_name as owner_first_name', 'owner.last_name as owner_last_name', 'walker.first_name as walker_first_name', 'walker.last_name as walker_last_name', 'walker.phone as walker_phone', 'owner.id as owner_id', 'walker.id as walker_id', 'review_dog.created_at')
                ->orderBy('review_dog.id', 'DESC')
                ->paginate(10);
        $title = ucwords(trans('customize.Reviews')); /* 'Reviews' */
        return View::make('reviews')
                        ->with('title', $title)
                        ->with('page', 'reviews')
                        ->with('provider_reviews', $provider_reviews)
                        ->with('user_reviews', $user_reviews);
    }

    public function get_info_pages() {
        $informations = Information::paginate(10);
        $title = ucwords(trans('customize.Information') . " Pages"); /* 'Information Pages' */
        return View::make('list_info_pages')
                        ->with('title', $title)
                        ->with('page', 'information')
                        ->with('informations', $informations);
    }

    public function get_provider_types() {
        $settings = Settings::where('key', 'default_distance_unit')->first();
        $success = Input::get('success');
        $unit = $settings->value;
        if ($unit == 0) {
            $unit_set = 'kms';
        } elseif ($unit == 1) {
            $unit_set = 'miles';
        }
        $types = ProviderType::paginate(10);
        $title = ucwords(trans('customize.Provider') . " " . trans('customize.Types')); /* 'Provider Types' */
        return View::make('list_provider_types')
                        ->with('title', $title)
                        ->with('page', 'provider-type')
                        ->with('unit_set', $unit_set)
                        ->with('success', $success)
                        ->with('types', $types);
    }

    public function get_document_types() {
        Session::forget('type');
        Session::forget('valu');
        $types = Document::paginate(10);
        $title = ucwords(trans('customize.Documents')); /* 'Document Types' */
        return View::make('list_document_types')
                        ->with('title', $title)
                        ->with('page', 'document-type')
                        ->with('types', $types);
    }

    public function get_promo_codes() {
        Session::forget('type');
        Session::forget('valu');
        $success = Input::get('success');
        $promo_codes = PromoCodes::paginate(10);
        $title = ucwords(trans('customize.promo_codes')); /* 'Promo Codes' */
        return View::make('list_promo_codes')
                        ->with('title', $title)
                        ->with('page', 'promo_code')
                        ->with('success', $success)
                        ->with('promo_codes', $promo_codes);
    }

     public function edit_keywords() {
        $success = Input::get('success');
        /* $keywords = Keywords::all(); */
        $icons = Icons::all();

        $UIkeywords = array();

        $UIkeywords['keyProvider'] = Lang::get('customize.Provider');
        $UIkeywords['keyUser'] = Lang::get('customize.User');
        $UIkeywords['keyTaxi'] = Lang::get('customize.Taxi');
        $UIkeywords['keyTrip'] = Lang::get('customize.Trip');
        $UIkeywords['keyWalk'] = Lang::get('customize.Walk');
        $UIkeywords['keyRequest'] = Lang::get('customize.Request');
        $UIkeywords['keyDashboard'] = Lang::get('customize.Dashboard');
        $UIkeywords['keyMap_View'] = Lang::get('customize.map_view');
        $UIkeywords['keyReviews'] = Lang::get('customize.Reviews');
        $UIkeywords['keyInformation'] = Lang::get('customize.Information');
        $UIkeywords['keyTypes'] = Lang::get('customize.Types');
        $UIkeywords['keyDocuments'] = Lang::get('customize.Documents');
        $UIkeywords['keyPromo_Codes'] = Lang::get('customize.promo_codes');
        $UIkeywords['keyCustomize'] = Lang::get('customize.Customize');
        $UIkeywords['keyPayment_Details'] = Lang::get('customize.payment_details');
        $UIkeywords['keySettings'] = Lang::get('customize.Settings');
        $UIkeywords['keyAdmin'] = Lang::get('customize.Admin');
        $UIkeywords['keyAdmin_Control'] = Lang::get('customize.admin_control');
        $UIkeywords['keyLog_Out'] = Lang::get('customize.log_out');
        $UIkeywords['keySchedules'] = Lang::get('customize.Schedules');
        $UIkeywords['keyWeekStatement'] = Lang::get('customize.WeekStatement');
        $title = ucwords(trans('customize.Customize')); /* 'Customize' */
        return View::make('keywords')
                        ->with('title', $title)
                        ->with('page', 'keywords')
                        /* ->with('keywords', $keywords) */
                        ->with('icons', $icons)
                        ->with('Uikeywords', $UIkeywords)
                        ->with('success', $success);
    }

     public function payment_details() {
        $braintree_environment = Config::get('app.braintree_environment');
        $braintree_merchant_id = Config::get('app.braintree_merchant_id');
        $braintree_public_key = Config::get('app.braintree_public_key');
        $braintree_private_key = Config::get('app.braintree_private_key');
        $braintree_cse = Config::get('app.braintree_cse');
        $twillo_account_sid = Config::get('app.twillo_account_sid');
        $twillo_auth_token = Config::get('app.twillo_auth_token');
        $twillo_number = Config::get('app.twillo_number');
        $stripe_publishable_key = Config::get('app.stripe_publishable_key');
        $default_payment = Config::get('app.default_payment');
        $stripe_secret_key = Config::get('app.stripe_secret_key');
        $mail_driver = Config::get('mail.mail_driver');
        $email_name = Config::get('mail.from.name');
        $email_address = Config::get('mail.from.address');
        $mandrill_secret = Config::get('services.mandrill_secret');
        /* DEVICE PUSH NOTIFICATION DETAILS */
        $customer_certy_url = Config::get('app.customer_certy_url');
        $customer_certy_pass = Config::get('app.customer_certy_pass');
        $customer_certy_type = Config::get('app.customer_certy_type');
        $provider_certy_url = Config::get('app.provider_certy_url');
        $provider_certy_pass = Config::get('app.provider_certy_pass');
        $provider_certy_type = Config::get('app.provider_certy_type');
        $gcm_browser_key = Config::get('app.gcm_browser_key');
        /* DEVICE PUSH NOTIFICATION DETAILS END */
        $install = array(
            'braintree_environment' => $braintree_environment,
            'braintree_merchant_id' => $braintree_merchant_id,
            'braintree_public_key' => $braintree_public_key,
            'braintree_private_key' => $braintree_private_key,
            'braintree_cse' => $braintree_cse,
            'twillo_account_sid' => $twillo_account_sid,
            'twillo_auth_token' => $twillo_auth_token,
            'twillo_number' => $twillo_number,
            'stripe_publishable_key' => $stripe_publishable_key,
            'stripe_secret_key' => $stripe_secret_key,
            'mail_driver' => $mail_driver,
            'email_address' => $email_address,
            'email_name' => $email_name,
            'mandrill_secret' => $mandrill_secret,
            'default_payment' => $default_payment,
            /* DEVICE PUSH NOTIFICATION DETAILS */
            'customer_certy_url' => $customer_certy_url,
            'customer_certy_pass' => $customer_certy_pass,
            'customer_certy_type' => $customer_certy_type,
            'provider_certy_url' => $provider_certy_url,
            'provider_certy_pass' => $provider_certy_pass,
            'provider_certy_type' => $provider_certy_type,
            'gcm_browser_key' => $gcm_browser_key,
                /* DEVICE PUSH NOTIFICATION DETAILS END */
        );
        $start_date = Input::get('start_date');
        Session::put('start_date', $start_date);
        $end_date = Input::get('end_date');
        Session::put('end_date', $end_date);
        $submit = Input::get('submit');
        Session::put('submit', $submit);
        $walker_id = Input::get('walker_id');
        Session::put('walker_id', $walker_id);
        $owner_id = Input::get('owner_id');
        Session::put('owner_id', $owner_id);
        $status = Input::get('status');
        Session::put('status', $status);

        $start_time = date("Y-m-d H:i:s", strtotime($start_date));
        $end_time = date("Y-m-d H:i:s", strtotime($end_date));
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = date("Y-m-d", strtotime($end_date));

        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }

        if (Input::get('status') && Input::get('status') != 0) {
            if ($status == 1) {
                $query = $query->where('request.is_completed', '=', 1);
            } else {
                $query = $query->where('request.is_cancelled', '=', 1);
            }
        } else {

            $query = $query->where(function ($que) {
                $que->where('request.is_completed', '=', 1)
                        ->orWhere('request.is_cancelled', '=', 1);
            });
        }

        $walks = $query->select('request.request_start_time', 'walker_type.name as type', 'request.ledger_payment', 'request.card_payment', 'owner.first_name as owner_first_name', 'owner.last_name as owner_last_name', 'walker.first_name as walker_first_name', 'walker.last_name as walker_last_name', 'owner.id as owner_id', 'walker.id as walker_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_walker_arrived', 'request.payment_mode', 'request.is_completed', 'request.is_paid', 'request.is_walker_started', 'request.confirmed_walker', 'request.promo_id', 'request.promo_code'
                , 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.promo_payment');
        $walks = $walks->orderBy('id', 'DESC')->paginate(10);

        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }

        $completed_rides = $query->where('request.is_completed', 1)->count();


        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }
        $cancelled_rides = $query->where('request.is_cancelled', 1)->count();


        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }
        $card_payment = $query->where('request.payment_mode', 0)->where('request.is_completed', 1)->sum('request.card_payment');


        $query = DB::table('request')
                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                ->leftJoin('walker_type', 'walker.type', '=', 'walker_type.id');

        if (Input::get('start_date') && Input::get('end_date')) {
            $query = $query->where('request_start_time', '>=', $start_time)
                    ->where('request_start_time', '<=', $end_time);
        }

        if (Input::get('walker_id') && Input::get('walker_id') != 0) {
            $query = $query->where('request.confirmed_walker', '=', $walker_id);
        }

        if (Input::get('owner_id') && Input::get('owner_id') != 0) {
            $query = $query->where('request.owner_id', '=', $owner_id);
        }
        $promo_payment = $query->where('request.is_completed', 1)->sum('request.promo_payment');
        $credit_payment = $query->where('request.is_completed', 1)->sum('request.ledger_payment');
        $cash_payment = $query->where('request.payment_mode', 1)->sum('request.total');
        $scheduled_rides = ScheduledRequests::count();


        if (Input::get('submit') && Input::get('submit') == 'Download_Report') {

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=data.csv');
            $handle = fopen('php://output', 'w');
            $settings = Settings::where('key', 'default_distance_unit')->first();
            $unit = $settings->value;
            if ($unit == 0) {
                $unit_set = 'kms';
            } elseif ($unit == 1) {
                $unit_set = 'miles';
            }
            fputcsv($handle, array('ID', 'Date', 'Type of Service', 'Provider', 'Owner', 'Distance (' . $unit_set . ')', 'Time (Minutes)', 'Payment Mode', 'Earning', 'Referral Bonus', 'Promotional Bonus', 'Card Payment'));
            foreach ($walks as $request) {
                $pay_mode = "Card Payment";
                if ($request->payment_mode == 1) {
                    $pay_mode = "Cash Payment";
                }
                fputcsv($handle, array(
                    $request->id,
                    date('l, F d Y h:i A', strtotime($request->request_start_time)),
                    $request->type,
                    $request->walker_first_name . " " . $request->walker_last_name,
                    $request->owner_first_name . " " . $request->owner_last_name,
                    sprintf2($request->distance, 2),
                    sprintf2($request->time, 2),
                    $pay_mode,
                    sprintf2($request->total, 2),
                    sprintf2($request->ledger_payment, 2),
                    sprintf2($request->promo_payment, 2),
                    sprintf2($request->card_payment, 2),
                ));
            }

            fputcsv($handle, array());
            fputcsv($handle, array());
            fputcsv($handle, array('Total Trips', $completed_rides + $cancelled_rides));
            fputcsv($handle, array('Completed Trips', $completed_rides));
            fputcsv($handle, array('Cancelled Trips', $cancelled_rides));
            fputcsv($handle, array('Scheduled Trips', $scheduled_rides));
            fputcsv($handle, array('Total Payments', sprintf2(($credit_payment + $card_payment), 2)));
            fputcsv($handle, array('Card Payment', sprintf2($card_payment, 2)));
            fputcsv($handle, array('Referral Payment', $credit_payment));
            fputcsv($handle, array('Cash Payment', sprintf2($cash_payment, 2)));
            fputcsv($handle, array('Promotional Payment', sprintf2($promo_payment, 2)));

            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );
        } else {
            /* $currency_selected = Keywords::where('alias', 'Currency')->first();
              $currency_sel = $currency_selected->keyword; */
            $currency_sel = Config::get('app.generic_keywords.Currency');
            $walkers = Walker::paginate(10);
            $owners = Owner::paginate(10);
            $payment_default = ucfirst(Config::get('app.default_payment'));
            $title = ucwords(trans('customize.payment_details')); /* 'Payments' */
            return View::make('payment')
                            ->with('title', $title)
                            ->with('page', 'payments')
                            ->with('walks', $walks)
                            ->with('owners', $owners)
                            ->with('walkers', $walkers)
                            ->with('completed_rides', $completed_rides)
                            ->with('cancelled_rides', $cancelled_rides)
                            ->with('card_payment', $card_payment)
                            ->with('install', $install)
                            ->with('currency_sel', $currency_sel)
                            ->with('cash_payment', $cash_payment)
                            ->with('credit_payment', $credit_payment)
                            ->with('payment_default', $payment_default);
        }
    }

    public function walks_payment() {
        // $walks = DB::table('request')
        //         ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
        //         ->select(DB::raw('SUM(request.card_payment)as total,SUM(request.payment_remaining) as pay_to_provider,SUM(request.refund_remaining) as take_from_provider,COUNT(request.id)as trips,request.created_at,request.id, WEEK(request.created_at) as payoutweek'))
        //         ->where('request.status', '=', 1)
        //         ->where('request.is_completed', '=', 1)
        //         ->groupBy('payoutweek')
        //         ->orderBy('request.created_at', 'desc')
        //         ->paginate(10);
        // $response = Response::json($walks);
        $walks = [];


        // return $response;
        return View::make('walks_payment')
                        ->with('title', 'Payment Statement')
                        ->with('page', 'week_statement')
                        ->with('walks', $walks);
    }

    //settings
    public function get_settings() {
        $braintree_environment = Config::get('app.braintree_environment');
        $braintree_merchant_id = Config::get('app.braintree_merchant_id');
        $braintree_public_key = Config::get('app.braintree_public_key');
        $braintree_private_key = Config::get('app.braintree_private_key');
        $braintree_cse = Config::get('app.braintree_cse');
        $twillo_account_sid = Config::get('app.twillo_account_sid');
        $twillo_auth_token = Config::get('app.twillo_auth_token');
        $twillo_number = Config::get('app.twillo_number');
        $timezone = Config::get('app.timezone');
        $stripe_publishable_key = Config::get('app.stripe_publishable_key');
        $url = Config::get('app.url');
        $website_title = Config::get('app.website_title');
        $s3_bucket = Config::get('app.s3_bucket');
        $default_payment = Config::get('app.default_payment');
        $stripe_secret_key = Config::get('app.stripe_secret_key');
        $mail_driver = Config::get('mail.mail_driver');
        $email_name = Config::get('mail.from.name');
        $email_address = Config::get('mail.from.address');
        $mandrill_secret = Config::get('services.mandrill_secret');
        $host = Config::get('mail.host');
        /* DEVICE PUSH NOTIFICATION DETAILS */
        $customer_certy_url = Config::get('app.customer_certy_url');
        $customer_certy_pass = Config::get('app.customer_certy_pass');
        $customer_certy_type = Config::get('app.customer_certy_type');
        $provider_certy_url = Config::get('app.provider_certy_url');
        $provider_certy_pass = Config::get('app.provider_certy_pass');
        $provider_certy_type = Config::get('app.provider_certy_type');
        $gcm_browser_key = Config::get('app.gcm_browser_key');
        /* DEVICE PUSH NOTIFICATION DETAILS END */
        $install = array(
            'braintree_environment' => $braintree_environment,
            'braintree_merchant_id' => $braintree_merchant_id,
            'braintree_public_key' => $braintree_public_key,
            'braintree_private_key' => $braintree_private_key,
            'braintree_cse' => $braintree_cse,
            'twillo_account_sid' => $twillo_account_sid,
            'twillo_auth_token' => $twillo_auth_token,
            'twillo_number' => $twillo_number,
            'stripe_publishable_key' => $stripe_publishable_key,
            'stripe_secret_key' => $stripe_secret_key,
            'mail_driver' => $mail_driver,
            'email_address' => $email_address,
            'email_name' => $email_name,
            'mandrill_secret' => $mandrill_secret,
            'default_payment' => $default_payment,
            /* DEVICE PUSH NOTIFICATION DETAILS */
            'customer_certy_url' => $customer_certy_url,
            'customer_certy_pass' => $customer_certy_pass,
            'customer_certy_type' => $customer_certy_type,
            'provider_certy_url' => $provider_certy_url,
            'provider_certy_pass' => $provider_certy_pass,
            'provider_certy_type' => $provider_certy_type,
            'gcm_browser_key' => $gcm_browser_key,
            /* DEVICE PUSH NOTIFICATION DETAILS END */
        );
        $success = Input::get('success');
        $settings = Settings::all();
        /* $theme = Theme::all(); */
        $theme = Theme::first();
        if (isset($theme->id)) {
            $theme = Theme::first();
        } else {
            $theme = array();
        }
        $title = ucwords(trans('customize.Settings')); /* 'Settings' */
        return View::make('settings')
                        ->with('title', $title)
                        ->with('page', 'settings')
                        ->with('settings', $settings)
                        ->with('success', $success)
                        ->with('install', $install)
                        ->with('theme', $theme);
    }

  
   public function logout() {
        Auth::logout();
        return redirect('/login');
   }

   public function approve_walker() {
        $helper = new Helper();
        $id = Request::segment(3);
        $success = Input::get('success');
        $walker = Walker::find($id);
        $walker->is_approved = 1;

        $txt_approve = "Decline";
        if ($walker->is_approved) {
            $txt_approve = "Approved";
        }
        $response_array = array(
            'unique_id' => 5,
            'success' => true,
            'id' => $walker->id,
            'first_name' => $walker->first_name,
            'last_name' => $walker->last_name,
            'phone' => $walker->phone,
            'email' => $walker->email,
            'picture' => $walker->picture,
            'bio' => $walker->bio,
            'address' => $walker->address,
            'state' => $walker->state,
            'country' => $walker->country,
            'zipcode' => $walker->zipcode,
            'login_by' => $walker->login_by,
            'social_unique_id' => $walker->social_unique_id,
            'device_token' => $walker->device_token,
            'device_type' => $walker->device_type,
            'token' => $walker->token,
            'type' => $walker->type,
            'is_approved' => $walker->is_approved,
            'is_approved_txt' => $txt_approve,
        );
        $user_type = 1;
        $title = $helper->transl('you_approved',$id,$user_type);
        $message = $response_array;
        $helper->send_notifications($id, "walker", $title, $message, "imp");
        /* SMS */
        $settings = Settings::where('key', 'sms_walker_approve')->first();
        $pattern = $settings->value;
        $pattern = str_replace('%name%', $walker->first_name . " " . $walker->last_name, $pattern);
        # sms_notification($id, 'walker', $pattern);
        /* SMS END */
        /* EMAIL */
        /* $settings = Settings::where('key', 'email_walker_approve')->first();
          $pattern = $settings->value;
          $pattern = str_replace('%name%', $walker->first_name . " " . $walker->last_name, $pattern); */
        $settings = Settings::where('key', 'contact_us_email')->first();
        $admin_email = $settings->value;
        $pattern = array('name' => $walker->first_name . " " . $walker->last_name, 'contact_us_email' => $admin_email);
        $subject = "Welcome " . $walker->first_name . " " . $walker->last_name . " To " . \Config::get('app.website_title') . "";
        //email_notification($id, 'walker', $pattern, $subject, 'walker_approve');
        $helper->send_email($id, 'walker', $pattern, $subject, 'walker_approve');
        /* EMAIL END */
        $walker->save();
        /*  $pattern = "Hi " . $walker->first_name . ", Your Documents are verified by the Admin and your account is Activated, Please Login to Continue";
          $subject = "Your Account Activated";
          email_notification($walker->id, 'walker', $pattern, $subject); */
        //return Redirect::to("/providers");
        return redirect('/providers');
    }


    // public function get_provider_types() {
    //     $settings = Settings::where('key', 'default_distance_unit')->first();
    //     $success = Input::get('success');
    //     $unit = $settings->value;
    //     if ($unit == 0) {
    //         $unit_set = 'kms';
    //     } elseif ($unit == 1) {
    //         $unit_set = 'miles';
    //     }
    //     $types = ProviderType::paginate(10);
    //     $title = ucwords(trans('customize.Provider') . " " . trans('customize.Types')); /* 'Provider Types' */
    //     return View::make('list_provider_types')
    //                     ->with('title', $title)
    //                     ->with('page', 'provider-type')
    //                     ->with('unit_set', $unit_set)
    //                     ->with('success', $success)
    //                     ->with('types', $types);
    // }


    public function searchpvtype() {
        $success = Input::get('success');
        $valu = $_GET['valu'];
        $type = $_GET['type'];
        Session::put('valu', $valu);
        Session::put('type', $type);
        if ($type == 'provid') {
            $types = ProviderType::where('id', $valu)->paginate(10);
        } elseif ($type == 'provname') {
            $types = ProviderType::where('name', 'like', '%' . $valu . '%')->paginate(10);
        }
        $settings = Settings::where('key', 'default_distance_unit')->first();
        $unit = $settings->value;
        if ($unit == 0) {
            $unit_set = 'kms';
        } elseif ($unit == 1) {
            $unit_set = 'miles';
        }
        $title = ucwords(trans('customize.Provider') . " " . trans('customize.Types')); /* 'Provider Types' */
        return View::make('list_provider_types')
                        ->with('title', $title)
                        ->with('page', 'provider-type')
                        ->with('success', $success)
                        ->with('unit_set', $unit_set)
                        ->with('types', $types);
    }

    public function delete_provider_type() {
        $id = Request::segment(3);
        ProviderType::where('id', $id)->where('is_default', 0)->delete();
        return Redirect::to("/provider-types");
    }

    public function edit_provider_type() {
        //$path = public_path('uploads/' . '1552401533649391358.png');
        //echo $path;
        $id = Request::segment(3);
        $success = Input::get('success');
        $providers_type = ProviderType::find($id);
        $settings = Settings::where('key', 'default_distance_unit')->first();
        $unit = $settings->value;
        if ($unit == 0) {
            $unit_set = 'kms';
        } elseif ($unit == 1) {
            $unit_set = 'miles';
        }
        
        #echo json_encode($data);


        if ($providers_type) {
            $id = $providers_type->id;
            $name = $providers_type->name;
            $is_default = $providers_type->is_default;
            $base_price = $providers_type->base_price;
            $base_distance = $providers_type->base_distance;
            $price_per_unit_distance = $providers_type->price_per_unit_distance;
            $price_per_unit_time = $providers_type->price_per_unit_time;
            $icon = $providers_type->icon;
            $base_price = $providers_type->base_price;
            $max_size = $providers_type->max_size;
            $is_visible = $providers_type->is_visible;
            $title = ucwords("Edit Provider Type" . " : " . $name);
        } else {
            $id = 0;
            $name = "";
            $is_default = "";
            $base_distance = 1;
            $base_price = "";
            $price_per_unit_time = "";
            $price_per_unit_distance = "";
            $icon = "";
            $base_price = '';
            $max_size = '';
            $is_visible = "";
            $title = "Add New Provider Type";
        }

        return View::make('edit_provider_type')
                        ->with('title', $title)
                        ->with('page', 'provider-type')
                        ->with('success', $success)
                        ->with('id', $id)
                        ->with('base_price', $base_price)
                        ->with('base_distance', $base_distance)
                        ->with('max_size', $max_size)
                        ->with('name', $name)
                        ->with('is_default', $is_default)
                        ->with('base_price', $base_price)
                        ->with('icon', $icon)
                        ->with('is_visible', $is_visible)
                        ->with('price_per_unit_time', $price_per_unit_time)
                        ->with('price_per_unit_distance', $price_per_unit_distance)
                        ->with('unit_set', $unit_set);
    }

    public function update_provider_type() {
        $helper = new Helper();
        $id = Input::get('id');
        $name = ucwords(trim(Input::get('name')));
        $base_distance = trim(Input::get('base_distance'));
        if ($base_distance == "" || $base_distance == 0) {
            $base_distance = 1;
        }
        $base_price = trim(Input::get('base_price'));
        if ($base_price == "" || $base_price == 0) {
            $base_price = 0;
        }
        $distance_price = trim(Input::get('distance_price'));
        if ($distance_price == "" || $distance_price == 0) {
            $distance_price = 0;
        }
        $time_price = trim(Input::get('time_price'));
        if ($time_price == "" || $time_price == 0) {
            $time_price = 0;
        }
        $max_size = trim(Input::get('max_size'));
        if ($max_size == "" || $max_size == 0) {
            $max_size = 0;
        }
        $is_default = Input::get('is_default');
        $is_visible = trim(Input::get('is_visible'));

        if ($is_default) {
            if ($is_default == 1) {
                ProviderType::where('is_default', 1)->update(array('is_default' => 0));
            }
        } else {
            $is_default = 0;
        }


        if ($id == 0) {
            $providers_type = new ProviderType;
        } else {
            $providers_type = ProviderType::find($id);
        }
        if (Input::hasFile('icon')) {
            // Upload File
            $file_name = time();
            $file_name .= rand();
            $ext = Input::file('icon')->getClientOriginalExtension();
            list($width, $height) = getimagesize(Input::file('icon'));
            /* echo "width : " . $width;
              echo "height : " . $height; */
            if ($width == $height && $width >= 300 && $height >= 300) {
                Input::file('icon')->move(public_path() . "/uploads", $file_name . "." . $ext);
                $local_url = $file_name . "." . $ext;

                // Upload to S3
                if (\Config::get('app.s3_bucket') != "") {
                    $s3 = App::make('aws')->get('s3');
                    $pic = $s3->putObject(array(
                        'Bucket' => Config::get('app.s3_bucket'),
                        'Key' => $file_name,
                        'SourceFile' => public_path() . "/uploads/" . $local_url,
                    ));

                    $s3->putObjectAcl(array(
                        'Bucket' => Config::get('app.s3_bucket'),
                        'Key' => $file_name,
                        'ACL' => 'public-read'
                    ));

                    $s3_url = $s3->getObjectUrl(Config::get('app.s3_bucket'), $file_name);
                } else {
                    $s3_url = $helper->asset_url() . '/uploads/' . $local_url;
                }

                if (isset($providers_type->icon)) {
                    if ($providers_type->icon != "") {
                        $icon = $providers_type->icon;
                        unlink_image($icon);
                    }
                }

                $providers_type->icon = $s3_url;
            } else {
                return Redirect::to("/provider-types?success=4");
            }
        }
        if ($base_price <= 0 || $distance_price <= 0 || $time_price <= 0) {
            return Redirect::to("/provider-type/edit/$providers_type->id?success=3");
        }

        $providers_type->name = $name;
        $providers_type->base_distance = $base_distance;
        $providers_type->base_price = $base_price;
        $providers_type->price_per_unit_distance = $distance_price;
        $providers_type->price_per_unit_time = $time_price;
        $providers_type->max_size = $max_size;
        $providers_type->is_default = $is_default;
        $providers_type->is_visible = $is_visible;
        $providers_type->save();

        //return Redirect::to("/provider-type/edit/$providers_type->id?success=1");
        return redirect("/provider-type/edit/$providers_type->id?success=1");
    }
  
}
