<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Requests;
use App\Owner;
use App\Walker;
use App\ScheduledRequests;
use App\Icons;
use App\Theme;
use App\Settings;
use App\RequestMeta;
use App\RequestServices;
use App\ProviderServices;
use App\ProviderType;
use App\Payment;
use App\WalkLocation;
use App\User;
use App\WalkerReview;
use App\DogReview;
use App\PromoCodes;
use App\Ledger;
use DB;
use View;
use Response;
use Hash;
use Helper;
use Request;




class ProviderController extends Controller
{

    use RegistersUsers;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

        public function isAdmin($token) {
        return false;
    }

    public function getWalkerData($walker_id, $token, $is_admin) {

        if ($walker_data = Walker::where('token', '=', $token)->where('id', '=', $walker_id)->first()) {
            return $walker_data;
        } elseif ($is_admin) {
            $walker_data = Walker::where('id', '=', $walker_id)->first();
            if (!$walker_data) {
                return false;
            }
            return $walker_data;
        } else {
            return false;
        }
    }

    public function register() {
        $helper = new Helper();
        $first_name = ucwords(trim(Input::get('first_name')));
        $last_name = ucwords(trim(Input::get('last_name')));
        $email = Input::get('email');
        $phone = "";
        $language = 0;

        if (Input::has('language')) {
            $language = Input::get('language');
        }
        if (Input::has('phone')) {
            $phone = Input::get('phone');
                        $phone = "+256".substr($phone, -9);
        }
        $password = Input::get('password');
        $type = Input::get('type');
        $picture = Input::file('picture');
        if (Input::hasfile('picture')) {
            $picture = Input::file('picture');
        } else {
            $picture = "";
        }
        $device_token = 0;
        if (Input::has('device_token')) {
            $device_token = Input::get('device_token');
        }
        $device_type = Input::get('device_type');
        $bio = "";
        if (Input::has('bio')) {
            $bio = Input::get('bio');
        }
        $address = ucwords(trim(Input::get('address')));
        $state = ucwords(trim(Input::get('state')));
        //$country = ucwords(trim(Input::get('country')));
                $country = "Uganda";
        $zipcode = 0;
        if (Input::has('zipcode')) {
            $zipcode = Input::get('zipcode');
        }
        $login_by = Input::get('login_by');
        $car_model = 0;
        if (Input::has('car_model')) {
            $car_model = ucwords(trim(Input::get('car_model')));
        }
        $car_number = 0;
        if (Input::has('car_number')) {
            $car_number = Input::get('car_number');
        }
        $social_unique_id = Input::get('social_unique_id');

        if ($password != "" and $social_unique_id == "") {
            $validator = Validator::make(
                            array(
                        'password' => $password,
                        'email' => $email,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'picture' => $picture,
                        'device_token' => $device_token,
                        'device_type' => $device_type,
                        /* 'zipcode' => $zipcode, */
                        'login_by' => $login_by
                            ), array(
                        'password' => 'required',
                        'email' => 'required|email',
                        'first_name' => 'required',
                        'last_name' => 'required',
                        /* 'picture' => 'required|mimes:jpeg,bmp,png', */
                        'picture' => '',
                        'device_token' => 'required',
                        'device_type' => 'required|in:android,ios',
                        /* 'zipcode' => 'integer', */
                        'login_by' => 'required|in:manual,facebook,google',
                            ), array(
                        'password.required' => 2,
                        'email.required' => 3,
                        'first_name.required' => 4,
                        'last_name.required' => 5,
                        /* 'picture' => 'required|mimes:jpeg,bmp,png', */
                        'picture.required' => 6,
                        'device_token.required' => 7,
                        'device_type.required' => 8,
                        /* 'zipcode' => '', */
                        'login_by.required' => 9,
                            )
            );

            $validatorPhone = Validator::make(
                            array(
                        'phone' => $phone,
                            ), array(
                        'phone' => ''
                            ), array(
                        'phone.phone' => 10
                            )
            );
        } elseif ($social_unique_id != "" and $password == "") {
            $validator = Validator::make(
                            array(
                        'email' => $email,
                        'phone' => $phone,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'picture' => $picture,
                        'device_token' => $device_token,
                        'device_type' => $device_type,
                        'bio' => $bio,
                        'address' => $address,
                        'state' => $state,
                        'country' => $country,
                        /* 'zipcode' => $zipcode, */
                        'login_by' => $login_by,
                        'social_unique_id' => $social_unique_id
                            ), array(
                        'email' => 'required|email',
                        'phone' => 'required',
                        'first_name' => 'required',
                        'last_name' => 'required',
                        /* 'picture' => 'required|mimes:jpeg,bmp,png', */
                        'picture' => '',
                        'device_token' => 'required',
                        'device_type' => 'required|in:android,ios',
                        'bio' => '',
                        'address' => '',
                        'state' => '',
                        'country' => '',
                        /* 'zipcode' => 'integer', */
                        'login_by' => 'required|in:manual,facebook,google',
                        'social_unique_id' => 'required|unique:walker'
                            ), array(
                        'email.required' => 3,
                        'phone.required' => 10,
                        'first_name.required' => 4,
                        'last_name.required' => 5,
                        /* 'picture' => 'required|mimes:jpeg,bmp,png', */
                        'picture.required' => 6,
                        'device_token.required' => 7,
                        'device_type.required' => 8,
                        'bio' => '',
                        'address' => '',
                        'state' => '',
                        'country' => '',
                        /* 'zipcode' => '', */
                        'login_by' => '',
                        'social_unique_id.unique' => 42
                            )
            );

            $validatorPhone = Validator::make(
                            array(
                        'phone' => $phone,
                            ), array(
                        'phone' => ''
                            ), array(
                        'phone.phone' => 10
                            )
            );
        } elseif ($social_unique_id != "" and $password != "") {
            $response_array = array('success' => false, 'error' => 12, 'error_messages' => array(12), 'error_code' => 401);
            $response_code = 200;
            goto response;
        }

        if ($validator->fails()) {
            $error_messages = $validator->messages();
            //Log::info('Error while during walker registration = ' . print_r($error_messages, true));
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else if ($validatorPhone->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 13, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {


            if (Walker::onlyTrashed()->where('email', '=', $email)->first()) {
                $response_array = array('success' => false, 'error' => 43, 'error_messages' => array(43), 'error_code' => 430);
                $response_code = 200;
            } else if (Walker::where('email', '=', $email)->first()) {
                $response_array = array('success' => false, 'error' => 14, 'error_messages' => array(14), 'error_code' => 430);
                $response_code = 200;
            } else {

                if (!$type) {
                    // choose default type
                    $provider_type = ProviderType::where('is_default', 1)->first();

                    if (!$provider_type) {
                        $type = 0;
                    } else {
                        $type = $provider_type->id;
                    }
                }
                $activation_code = uniqid();
                Walker::where('device_token', '=', $device_token)->update(array('device_token' => 0));
                $walker = new Walker;
                $walker->first_name = $first_name;
                $walker->last_name = $last_name;
                $walker->email = $email;
                $walker->phone = $phone;
                $walker->activation_code = $activation_code;
                $walker->email_activation = 0;

                $walker->language = $language;
                if ($password != "") {
                    $walker->password = Hash::make($password);
                }
                $walker->token = $helper->generate_token();
                $walker->token_expiry = $helper->generate_expiry();
                // upload image
                $file_name = time();
                $file_name .= rand();
                $file_name = sha1($file_name);

                $s3_url = "";
                if (Input::hasfile('picture')) {
                    $ext = Input::file('picture')->getClientOriginalExtension();
                    Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
                    $local_url = $file_name . "." . $ext;

                    // Upload to S3
                    if (\Config::get('app.s3_bucket') != "") {
                        $s3 = App::make('aws')->get('s3');
                        $pic = $s3->putObject(array(
                            'Bucket' => \Config::get('app.s3_bucket'),
                            'Key' => $file_name,
                            'SourceFile' => public_path() . "/uploads/" . $local_url,
                        ));

                        $s3->putObjectAcl(array(
                            'Bucket' => \Config::get('app.s3_bucket'),
                            'Key' => $file_name,
                            'ACL' => 'public-read'
                        ));

                        $s3_url = $s3->getObjectUrl(\Config::get('app.s3_bucket'), $file_name);
                    } else {
                        $s3_url = $local_url;
                    }
                }
                $walker->picture = $s3_url;
                $walker->device_token = $device_token;
                $walker->device_type = $device_type;
                $walker->bio = $bio;
                $walker->address = $address;
                $walker->state = $state;
                $walker->country = $country;
                $walker->zipcode = $zipcode;
                $walker->login_by = $login_by;
                $walker->is_available = 1;
                $walker->is_active = 0;
                $walker->is_approved = 0;
                $walker->type = $type;
                $walker->car_model = $car_model;
                $walker->car_number = $car_number;
                $walker->email_activation = 1;
                if ($social_unique_id != "") {
                    $password = my_random6_number();
                    $walker->social_unique_id = $social_unique_id;
                    $walker->password = Hash::make($password);
                }
                $walker->timezone = "EAT";
                If (Input::has('timezone')) {
                    $walker->timezone = Input::get('timezone');
                }

                $walker->save();
                if ($social_unique_id != "") {
                    $pattern = "Hello... ! " . ucwords($first_name) . ". Your " . \Config::get('app.website_title') . " Web Login Password is : " . $password;
                    sms_notification($walker->id, 'walker', $pattern);
                    $subject = "Your " . \Config::get('app.website_title') . " Web Login Password";
                    email_notification($walker->id, 'walker', $pattern, $subject);
                }
                if (Input::has('type') != NULL) {
                    $ke = Input::get('type');
                    $proviserv = ProviderServices::where('provider_id', $walker->id)->first();
                    if ($proviserv != NULL) {
                        DB::delete("delete from walker_services where provider_id = '" . $walker->id . "';");
                    }
                    $base_price = Input::get('service_base_price');
                    $service_price_distance = Input::get('service_price_distance');
                    $service_price_time = Input::get('service_price_time');

                    $type = Input::get('type');
                    $myType = explode(',', $type);
                    $cnkey = count($myType);

                    if (Input::has('service_base_price')) {
                        $base_price = Input::get('service_base_price');
                        $base_price_array = explode(',', $base_price);
                    }

                    //Log::info('cnkey = ' . print_r($cnkey, true));
                    for ($i = 0; $i < $cnkey; $i++) {
                        $key = $myType[$i];
                        $prserv = new ProviderServices;
                        $prserv->provider_id = $walker->id;
                        $prserv->type = $key;
                        //Log::info('key = ' . print_r($key, true));

                        if (Input::has('service_base_price')) {

                            $prserv->base_price = $base_price_array[$i];
                        } else {
                            $prserv->base_price = 0;
                        }
                        if (Input::has('service_price_distance')) {
                            $prserv->price_per_unit_distance = $service_price_distance[$i];
                        } else {
                            $prserv->price_per_unit_distance = 0;
                        }
                        if (Input::has('service_price_time')) {
                            $prserv->price_per_unit_time = $service_price_time[$i];
                        } else {
                            $prserv->price_per_unit_distance = 0;
                        }
                        $prserv->save();
                    }
                }

                $stripe_secret_key = \Config::get('app.stripe_secret_key');
                $stripe_publishable_key = \Config::get('app.stripe_publishable_key');
                $gcm_browser_key = \Config::get('app.gcm_browser_key');
                /* $subject = "Welcome On Board";
                  $email_data['name'] = $walker->first_name;
                  $url = URL::to('/provider/activation') . '/' . $activation_code;
                  $email_data['url'] = $url;
                  send_email($walker->id, 'walker', $email_data, $subject, 'providerregister'); */
                $settings = Settings::where('key', 'contact_us_email')->first();
                $admin_email = $settings->value;
                $pattern = array('contact_us_email' => $admin_email, 'name' => ucwords($walker->first_name . " " . $walker->last_name), 'web_url' => $helper->web_url());
                $subject = "Welcome to " . ucwords(\Config::get('app.website_title')) . ", " . ucwords($walker->first_name . " " . $walker->last_name) . "";
                //email_notification($walker->id, 'walker', $pattern, $subject, 'walker_register', null);
                $helper->send_email($walker->id, 'walker', $pattern, $subject, 'walker_register');
                $txt_approve = "Decline";
                if ($walker->is_approved) {
                    $txt_approve = "Approved";
                }
                $response_array = array(
                    'success' => true,
                    'id' => $walker->id,
                    'first_name' => $walker->first_name,
                    'last_name' => $walker->last_name,
                    'phone' => $walker->phone,
                    'email' => $walker->email,
                    'picture' => $walker->picture ? $walker->picture : "",
                    'bio' => $walker->bio,
                    'address' => $walker->address,
                    'state' => $walker->state,
                    'country' => $walker->country,
                    'zipcode' => $walker->zipcode,
                    'login_by' => $walker->login_by,
                    'social_unique_id' => $walker->social_unique_id ? $walker->social_unique_id : "",
                    'device_token' => $walker->device_token,
                    'device_type' => $walker->device_type,
                    'token' => $walker->token,
                    'timezone' => $walker->timezone,
                    'car_model' => $walker->car_model,
                    'car_number' => $walker->car_number,
                    /* 'type' => $myType, */
                    'type' => $walker->type,
                    'is_approved' => $walker->is_approved,
                    'is_approved_txt' => $txt_approve,
                    'is_available' => $walker->is_active,
                    'stripe_secret_key' => $stripe_secret_key,
                    'stripe_publishable_key' => $stripe_publishable_key,
                    'gcm_browser_key' => $gcm_browser_key,
                );
                $response_code = 200;
            }
        }

        response:
        $response = Response::json($response_array, $response_code);
        return $response;
    }
    
    
    public function login() {
        $login_by = Input::get('login_by');
        $device_token = 0;
        if (Input::has('device_token')) {
            $device_token = Input::get('device_token');
        }
        $device_type = Input::get('device_type');
        $stripe_secret_key = \Config::get('app.stripe_secret_key');
        $stripe_publishable_key = \Config::get('app.stripe_publishable_key');
        $gcm_browser_key = \Config::get('app.gcm_browser_key');

        if (Input::has('email') && Input::has('password')) {
            $email = Input::get('email');
            $password = Input::get('password');

            $validator = Validator::make(
                            array(
                        'password' => $password,
                        'email' => $email,
                        'device_token' => $device_token,
                        'device_type' => $device_type,
                        'login_by' => $login_by
                            ), array(
                        'password' => 'required',
                        'email' => 'required|email',
                        'device_token' => 'required',
                        'device_type' => 'required|in:android,ios',
                        'login_by' => 'required|in:manual,facebook,google'
                            ), array(
                        'password.required' => 2,
                        'email.required' => 3,
                        'device_token.required' => 7,
                        'device_type.required' => 8,
                        'login_by' => ''
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages();
                //Log::error('Validation error during manual login for walker = ' . print_r($error_messages, true));
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                if ($walker = Walker::where('email', '=', $email)->first()) {
                    if (Hash::check($password, $walker->password)) {
                        if ($login_by != "manual") {
                            $response_array = array('success' => false, 'error' => 15, 'error_messages' => array(15), 'error_code' => 417);
                            $response_code = 200;
                        } else {
                            Walker::where('id', '!=', $walker->id)->where('device_token', '=', $device_token)->update(array('device_token' => 0));
                            /* if ($walker->device_type != $device_type) { */
                            $walker->device_type = $device_type;
                            /* }
                              if ($walker->device_token != $device_token) { */
                            $walker->device_token = $device_token;
                            /* } */
                            $helper = new Helper();
                            $walker->token = $helper->generate_token();
                            $walker->token_expiry = $helper->generate_expiry();
                            $walker->save();
                            $txt_approve = "Decline";
                            if ($walker->is_approved) {
                                $txt_approve = "Approved";
                            }
                            $response_array = array(
                                'success' => true,
                                'id' => $walker->id,
                                'first_name' => $walker->first_name,
                                'last_name' => $walker->last_name,
                                'phone' => $walker->phone,
                                'email' => $walker->email,
                                'picture' => $walker->picture ? $walker->picture : "",
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
                                'timezone' => $walker->timezone,
                                'is_approved' => $walker->is_approved,
                                'car_model' => $walker->car_model,
                                'car_number' => $walker->car_number,
                                'is_approved_txt' => $txt_approve,
                                'is_available' => $walker->is_active,
                                'stripe_secret_key' => $stripe_secret_key,
                                'stripe_publishable_key' => $stripe_publishable_key,
                                'gcm_browser_key' => $gcm_browser_key,
                            );
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 16, 'error_messages' => array(16), 'error_code' => 403);
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 17, 'error_messages' => array(17), 'error_code' => 404);
                    $response_code = 200;
                }
            }
        } elseif (Input::has('social_unique_id')) {
            $social_unique_id = Input::get('social_unique_id');
            $socialValidator = Validator::make(
                            array(
                        'social_unique_id' => $social_unique_id,
                        'device_token' => $device_token,
                        'device_type' => $device_type,
                        'login_by' => $login_by
                            ), array(
                        'social_unique_id' => 'required|exists:walker,social_unique_id',
                        'device_token' => 'required',
                        'device_type' => 'required|in:android,ios',
                        'login_by' => 'required|in:manual,facebook,google'
                            ), array(
                        'social_unique_id.exists' => 11,
                        'device_token.required' => 7,
                        'device_type.required' => 8,
                        'login_by' => ''
                            )
            );
            if ($socialValidator->fails()) {
                $error_messages = $socialValidator->messages();
                //Log::error('Validation error during social login for walker = ' . print_r($error_messages, true));
                $error_messages = $socialValidator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                if ($walker = Walker::where('social_unique_id', '=', $social_unique_id)->first()) {
                    if (!in_array($login_by, array('facebook', 'google'))) {
                        $response_array = array('success' => false, 'error' => 15, 'error_messages' => array(15), 'error_code' => 417);
                        $response_code = 200;
                    } else {
                        if ($walker->device_type != $device_type) {
                            $walker->device_type = $device_type;
                        }
                        if ($walker->device_token != $device_token) {
                            $walker->device_token = $device_token;
                        }
                        $walker->token_expiry = generate_expiry();
                        $walker->save();
                        $txt_approve = "Decline";
                        if ($walker->is_approved) {
                            $txt_approve = "Approved";
                        }

                        $response_array = array(
                            'success' => true,
                            'id' => $walker->id,
                            'first_name' => $walker->first_name,
                            'last_name' => $walker->last_name,
                            'phone' => $walker->phone,
                            'email' => $walker->email,
                            'picture' => $walker->picture ? $walker->picture : "",
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
                            'timezone' => $walker->timezone,
                            'type' => $walker->type,
                            'is_approved' => $walker->is_approved,
                            'car_model' => $walker->car_model,
                            'car_number' => $walker->car_number,
                            'is_approved_txt' => $txt_approve,
                            'is_available' => $walker->is_active,
                            'stripe_secret_key' => $stripe_secret_key,
                            'stripe_publishable_key' => $stripe_publishable_key,
                            'gcm_browser_key' => $gcm_browser_key,
                        );
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 18, 'error_messages' => array(18), 'error_code' => 404);
                    $response_code = 200;
                }
            }
        } else {
            $response_array = array('success' => false, 'error' => 12, 'error_messages' => array(12));
            $response_code = 200;
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Rate Dog

    public function set_dog_rating() {
        $helper = new Helper();
        if (Request::isMethod('post')) {
            $comment = "";
            if (Input::has('comment')) {
                $comment = Input::get('comment');
            }
            $request_id = Input::get('request_id');
            $rating = 0;
            if (Input::has('rating')) {
                $rating = Input::get('rating');
            }
            $token = Input::get('token');
            $walker_id = Input::get('id');

            $validator = Validator::make(
                            array(
                        'request_id' => $request_id,
                        /* 'rating' => $rating, */
                        'token' => $token,
                        'walker_id' => $walker_id,
                            ), array(
                        'request_id' => 'required|integer',
                        /* 'rating' => 'required|integer', */
                        'token' => 'required',
                        'walker_id' => 'required|integer'
                            ), array(
                        'request_id.required' => 19,
                        /* 'rating' => 'required|integer', */
                        'token.required' => 24,
                        'walker_id.required' => 20
                            )
            );
            /* $var = Keywords::where('id', 1)->first(); */
            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        // Do necessary operations
                        if ($request = Requests::find($request_id)) {
                            if ($request->confirmed_walker == $walker_id) {

                                if ($request->is_dog_rated == 0) {

                                    $owner = Owner::find($request->owner_id);

                                    $dog_review = new DogReview;
                                    $dog_review->request_id = $request_id;
                                    $dog_review->walker_id = $walker_id;
                                    $dog_review->rating = $rating;
                                    $dog_review->owner_id = $owner->id;
                                    $dog_review->comment = $comment;
                                    $dog_review->save();

                                    $request->is_dog_rated = 1;
                                    $request->save();

                                    if ($rating) {
                                        if ($owner = Owner::find($request->owner_id)) {
                                            $old_rate = $owner->rate;
                                            $old_rate_count = $owner->rate_count;
                                            $new_rate_counter = ($owner->rate_count + 1);
                                            $new_rate = (($owner->rate * $owner->rate_count) + $rating) / $new_rate_counter;
                                            $owner->rate_count = $new_rate_counter;
                                            $owner->rate = $new_rate;
                                            $owner->save();
                                        }
                                    }

                                    $response_array = array('success' => true);
                                    $response_code = 200;
                                } else {
                                    $response_array = array('success' => false, 'error' => 21, 'error_messages' => array(21), 'error_code' => 409);
                                    $response_code = 200;
                                }
                            } else {
                                /* $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $var->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $var->keyword . ' ID'), 'error_code' => 407); */
                                $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'error_code' => 407);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Cancel Walk

    public function cancel_walk() {
        if (Request::isMethod('post')) {
            $walk_id = Input::get('walk_id');
            $token = Input::get('token');
            $walker_id = Input::get('id');

            $validator = Validator::make(
                            array(
                        'walk_id' => $walk_id,
                        'token' => $token,
                        'walker_id' => $walker_id,
                            ), array(
                        'walk_id' => 'required|integer',
                        'token' => 'required',
                        'walker_id' => 'required|integer'
                            ), array(
                        'walk_id.required' => 19,
                        'token.required' => 24,
                        'walker_id.required' => 20
                            )
            );

            /* $var = Keywords::where('id', 1)->first(); */

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    $helper = new Helper();
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        // Do necessary operations
                        if ($walk = Walk::find($walk_id)) {
                            if ($walk->walker_id == $walker_id) {

                                if ($walk->is_walk_started == 0) {
                                    $walk->walker_id = 0;
                                    $walk->is_confirmed = 0;
                                    $walk->save();
                                                                        $walker = Walker::find($walker_id);
                                                                        $walker->is_available = 1;
                                                                        $walker->save();
                                    $response_array = array('success' => true);
                                    $response_code = 200;
                                } else {
                                    $response_array = array('success' => false, 'error' => 27, 'error_messages' => array(27), 'error_code' => 416);
                                    $response_code = 200;
                                }
                            } else {
                                /* $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with' . $var->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with' . $var->keyword . ' ID'), 'error_code' => 407); */
                                $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'error_code' => 407);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Add walker Location Data
    public function walker_location() {
        if (Request::isMethod('post')) {
            $token = Input::get('token');
            $walker_id = Input::get('id');
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');
            if (Input::has('bearing')) {
                $angle = Input::get('bearing');
            }
            if (Input::has('token')) {
                $fcm = Input::get('token');
            }
            if (Input::has('app_version')) {
                $app_version = Input::get('app_version');
            }
            if (Input::has('os_version')) {
                $os_version = Input::get('os_version');
            }
            if (Input::has('device')) {
                $device = Input::get('device');
            }

            $validator = Validator::make(
                            array(
                                'token' => $token,
                                'walker_id' => $walker_id,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                            ), array(
                                'token' => 'required',
                                'walker_id' => 'required|integer',
                                'latitude' => 'required',
                                'longitude' => 'required',
                            ), array(
                                'token.required' => 24,
                                'walker_id.required' => 20,
                                'latitude.required' => 28,
                                'longitude.required' => 28,
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    $status_txt = "not active";
                    if ($walker_data->is_active) {
                        $status_txt = "active";
                    }
                    $helper = new Helper();
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        $walker = Walker::find($walker_id);
                        $location = $helper->get_location($latitude, $longitude);
                        $latitude = $location['lat'];
                        $longitude = $location['long'];

                        if (!isset($angle)) {
                            $angle = $helper->get_angle($walker->latitude, $walker->longitude, $latitude, $longitude);
                        }
                        $walker->old_latitude = $walker->latitude;
                        $walker->old_longitude = $walker->longitude;
                        $walker->latitude = $latitude;
                        $walker->longitude = $longitude;
                        $walker->bearing = $angle;
                                                if (!isset($fcm)) {
                                                    $fcm = '';
                                                }
                                                if (!isset($app_version)) {
                                                    $app_version = '';
                                                }
                                                if (!isset($os_version)) {
                                                    $os_version = '';
                                                }
                                                if (!isset($device)) {
                                                    $device = '';
                                                }
                        $walker->token = $fcm;
                                                $walker->device = $device;
                                                $walker->app_version = $app_version;
                                                $walker->os_version = $os_version;
                        $walker->save();

                        $response_array = array(
                            'success' => true,
                            'is_active' => $walker_data->is_active,
                            'is_approved' => $walker_data->is_approved,
                            'is_active_txt' => $status_txt,
                        );
                    } else {
                        $response_array = array(
                            'success' => false,
                            'error' => 24,
                            'error_messages' => array(24),
                            'error_code' => 412,
                            'is_active' => $walker_data->is_active,
                            'is_approved' => $walker_data->is_approved,
                            'is_active_txt' => $status_txt,
                        );
                    }
                } else {
                    if ($is_admin) {
                        /* $driver = Keywords::where('id', 1)->first();
                          $response_array = array('success' => false, 'error' => '' . $driver->keyword . ' ID not Found', 'error_messages' => array(24), 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                }
                $response_code = 200;
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Get Profile

    public function get_requests() {
        $token = Input::get('token');
        $walker_id = Input::get('id');
        $validator = Validator::make(
                        array(
                             'token' => $token,
                             'walker_id' => $walker_id,
                        ), array(
                             'token' => 'required',
                             'walker_id' => 'required|integer'
                        ), array(
                            'token.required' => 24,
                            'walker_id.required' => 20
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                $helper = new Helper();
                if ($helper->is_token_active($walker_data->token_expiry)) {
                    $txt_approve = "Decline";
                    if ($walker_data->is_approved) {
                        $txt_approve = "Approved";
                    }
                    $time = date("Y-m-d H:i:s");
                    $provider_timeout = Settings::where('key', 'provider_timeout')->first();
                    $timeout = $provider_timeout->value;

                    $query = "SELECT request.*,id, later, D_latitude, D_longitude, payment_mode, payment_msisdn, request_start_time , owner_id,TIMESTAMPDIFF(SECOND,updated_at, '$time') as diff from request where is_cancelled = 0 and status = 0 and current_walker=$walker_id and TIMESTAMPDIFF(SECOND,updated_at, '$time') <= $timeout";

                    $requests = DB::select(DB::raw($query));
                    $all_requests = array();
                    $counter = 0;
                    if (sizeof($requests) > 0) {
                        foreach ($requests as $request) {
                            $counter++;
                            $data['request_id'] = $request->id;
                            $requestData = RequestServices::where('request_id', $request->id)->first();

                            ////
                            $settings = Settings::where('key', 'default_distance_unit')->first();
                            $unit = $settings->value;
                            if ($unit == 0) {
                                $unit_set = 'kms';
                            } elseif ($unit == 1) {
                                $unit_set = 'miles';
                            }

                            $data['unit'] = $unit_set;
                            ////


                            $data['request_services'] = $requestData->type;

                            $rservc = RequestServices::where('request_id', $request->id)->get();
                            $typs = array();
                            $typi = array();
                            $typp = array();
                            $totalPrice = 0;

                            foreach ($rservc as $typ) {
                                $typ1 = ProviderType::where('id', $typ->type)->first();
                                $typ_price = ProviderServices::where('provider_id', $walker_id)->where('type', $typ->type)->first();

                                if (isset($typ_price->base_price)) {
                                    if ($typ_price->base_price > 0) {
                                        $typp1 = 0.00;
                                        $typp1 = $typ_price->base_price;
                                    } else {
                                        $typp1 = 0.00;
                                    }
                                } else {
                                    $typp1 = 0.00;
                                }

                                $typs['name'] = $typ1->name;
                                $typs['price'] = $typp1;
                                $totalPrice = $totalPrice + $typp1;

                                array_push($typi, $typs);
                            }
                            $data['type'] = $typi;

                            if ($request->later == 0)
                                $data['time_left_to_respond'] = $timeout - $request->diff;
                            else
                                $data['time_left_to_respond'] = $timeout;

                            $owner = Owner::find($request->owner_id);
                            $user_timezone = $owner->timezone;
                            $default_timezone = \Config::get('app.timezone');

                            $helper = new Helper(); 
                            $date_time = $helper->get_user_time($default_timezone, $user_timezone, $request->request_start_time);


                            $data['later'] = $request->later;
                            $data['datetime'] = $date_time;

                            $request_data = array();
                            $request_data['owner'] = array();
                            $request_data['owner']['name'] = $owner->first_name . " " . $owner->last_name;
                            $request_data['owner']['picture'] = $owner->picture;
                            $request_data['owner']['phone'] = $owner->phone;
                            $request_data['owner']['address'] = $owner->address;
                            $request_data['owner']['latitude'] = $request->latitude;
                            $request_data['owner']['longitude'] = $request->longitude;
                            $request_data['owner']['src_address'] = $request->src_address;
                            $request_data['owner']['dest_latitude'] = $request->D_latitude;
                            $request_data['owner']['dest_longitude'] = $request->D_longitude;
                            $request_data['owner']['dest_address'] = $request->dest_address;
                            if ($request->D_latitude != NULL) {
                                /* Log::info('D_latitude = ' . print_r($request->D_latitude, true)); */
                                $request_data['owner']['d_latitude'] = $request->D_latitude;
                                $request_data['owner']['d_longitude'] = $request->D_longitude;
                            }
                            $request_data['owner']['rating'] = $owner->rate;
                            $request_data['owner']['num_rating'] = $owner->rate_count;
                            /* $request_data['owner']['rating'] = DB::table('review_dog')->where('owner_id', '=', $owner->id)->avg('rating') ? : 0;
                              $request_data['owner']['num_rating'] = DB::table('review_dog')->where('owner_id', '=', $owner->id)->count(); */
                            $request_data['owner']['payment_type'] = $request->payment_mode;
                            $request_data['payment_mode'] = $request->payment_mode;
                            $request_data['payment_msisdn'] = $request->payment_msisdn;
                            $request_data['dog'] = array();
                            /* if ($dog = Dog::find($owner->dog_id)) {

                              $request_data['dog']['name'] = $dog->name;
                              $request_data['dog']['age'] = $dog->age;
                              $request_data['dog']['breed'] = $dog->breed;
                              $request_data['dog']['likes'] = $dog->likes;
                              $request_data['dog']['picture'] = $dog->image_url;
                              } */
                            $data['request_data'] = $request_data;
                            array_push($all_requests, $data);
                        }
                    }

                    /* if ($counter) { */
                    $response_array = array('success' => true, 'is_approved' => $walker_data->is_approved, 'is_approved_txt' => $txt_approve, 'is_available' => $walker_data->is_active, 'incoming_requests' => $all_requests);
                    $response_code = 200;
                    /* } else {
                      $response_array = array('success' => false, 'error' => 'no request found', 'error_messages' => array('no request found'), 'error_code' => 505);
                      $response_code = 200;
                      } */
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Respond To Request


    function validate_parameters($data){
        $validator = Validator::make(
            array(
                 'token' => $data['token'],
                 'walker_id' => $data['walker_id'],
                 'request_id' => $data['request_id'],
                 'accepted' => $data['accepted']
                 ), 
            array(
                 'token' => 'required',
                 'walker_id' => 'required|integer',
                 'accepted' => 'required|integer',
                 'request_id' => 'required|integer'
                 ),
            array(
                 'token.required' => 24,
                 'walker_id.required' => 20,
                 'accepted.required' => 29,
                 'request_id.required' => 19
            )
        );
        return $validator;
    }

    function error_12(){
        $error_messages = $validator->messages()->all();
        $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
        return $response_array();
    }

    function get_walker_info($walker_id){
        $walker = Walker::find($walker_id);
        $walker_data = array();
        $walker_data['first_name'] = $walker->first_name;
        $walker_data['last_name'] = $walker->last_name;
        $walker_data['phone'] = $walker->phone;
        $walker_data['bio'] = $walker->bio;
        $walker_data['picture'] = $walker->picture;
        $walker_data['latitude'] = $walker->latitude;
        $walker_data['longitude'] = $walker->longitude;
        $walker_data['type'] = $walker->type;
        $walker_data['rating'] = $walker->rate;
        $walker_data['num_rating'] = $walker->rate_count;
        $walker_data['car_model'] = $walker->car_model;
        $walker_data['car_number'] = $walker->car_number;
        return $walker_data;
    }

    function set_billing_info($request){
        $helper = new Helper();
        $settings = Settings::where('key', 'default_distance_unit')->first();
        $unit = $settings->value;
        if ($unit == 0) {
            $unit_set = 'kms';
        } elseif ($unit == 1) {
            $unit_set = 'miles';
        }

        $bill = array();
        if ($request->is_completed == 1) {
            $bill['distance'] = (string) $helper->convert($request->distance, $unit);
            $bill['unit'] = $unit_set;
            $bill['time'] = $request->time;
            $bill['base_price'] = $request->base_price;
            $bill['distance_cost'] = $request->distance_cost;
            $bill['time_cost'] = $request->time_cost;
            $bill['total'] = $request->total;
            $bill['is_paid'] = $request->is_paid;
        }
        return $bill;
    }

    function set_provider_availability($request){
        $helper = new Helper();
        $date_time = $request->request_start_time;
        $datewant = new DateTime($date_time);
        $datetime = $datewant->format('Y-m-d H:i:s');
        $end_time = $datewant->add(new DateInterval('P0Y0M0DT2H0M0S'))->format('Y-m-d H:i:s');
        $provavail = ProviderAvail::where('provider_id', $walker_id)->where('start', '<=', $datetime)->where('end', '>=', $end_time)->first();
        $starttime = $provavail->start;
        $endtime = $provavail->end;
        $provavail->delete();
        if ($starttime == $datetime) {
            $provavail1 = new ProviderAvail;
            $provavail1->provider_id = $walker_id;
            $provavail1->start = $end_time;
            $provavail1->end = $endtime;
            $provavail1->save();
        } elseif ($endtime == $end_time) {
            $provavail1 = new ProviderAvail;
            $provavail1->provider_id = $walker_id;
            $provavail1->start = $starttime;
            $provavail1->end = $datetime;
            $provavail1->save();
        } else {
            $provavail1 = new ProviderAvail;
            $provavail1->provider_id = $walker_id;
            $provavail1->start = $starttime;
            $provavail1->end = $datetime;
            $provavail1->save();

            $provavail2 = new ProviderAvail;
            $provavail2->provider_id = $walker_id;
            $provavail2->start = $end_time;
            $provavail2->end = $endtime;
            $provavail2->save();
        }
    }

    // public function getOwnerData($owner_id) {
    //     //if ($owner_data = Owner::where('token', '=', $token)->where('id', '=', $owner_id)->first()) {
    //      //   return $owner_data;
    //     //} elseif ($is_admin) {
    //         $owner_data = Owner::where('id', '=', $owner_id)->first();
    //         //if (!$owner_data) {
    //            // return false;
    //         //}
    //         return $owner_data;
    //     //} else {
    //      //   return false;
    //    // }
    // }

    // function notify_client($request,$request_id,$walker_data,$bill){

    //     $response_array = array(
    //         'success' => true,
    //         'request_id' => $request_id,
    //         'status' => $request->status,
    //         'confirmed_walker' => $request->confirmed_walker,
    //         'is_walker_started' => $request->is_walker_started,
    //         'is_walker_arrived' => $request->is_walker_arrived,
    //         'is_walk_started' => $request->is_started,
    //         'is_completed' => $request->is_completed,
    //         'is_walker_rated' => $request->is_walker_rated,
    //         'is_cancelled' => $request->is_cancelled,
    //         'walker' => $walker_data,
    //         'bill' => $bill
    //     );

    //     $user_type = 0;
    //     $id = $request->owner_id;
    //     $title = transl('accept_request_push', $id, $user_type);
    //     $message = $response_array;
    //     //send_data_message();
    //     //notification($request->owner_id, "owner", "request_accepted", $message);
    //     $owner_data=$this->getOwnerData($request->owner_id);


    //     $url="https://fcm.googleapis.com/fcm/send";
    //     $header= array(
    //                    'Authorization: key='.Config::get('fcm.key'),
    //                    'Content-Type:application/json');
    //     $info['registration_ids']=[$owner_data->device_token];
    //     $info['notification']=array(
    //                                 'title' => 'Ridez',
    //                                 'body' => 'content of your notification');
        
    //      $info['key1']='value1';
    //      $info['key2']='value2';
    //      // $info['data']=json_encode(array('key1' => 'value1',
    //      //                   'key2' => 'value2'));

    //     $method='POST';
    //     $response=$this->Callapi($header,$method,$url,$info);
    //     return $response;
    // }

    // function notify_client_2($request_id,$request){
    //     $time = date("Y-m-d H:i:s");
    //     $query = "SELECT id,owner_id,current_walker,TIMESTAMPDIFF(SECOND,request_start_time, '$time') as diff from request where id = '$request_id'";

    //     $results = DB::select(DB::raw($query));
    //     $settings = Settings::where('key', 'provider_timeout')->first();
    //     $timeout = $settings->value;

    //     # archiving older walker
    //     RequestMeta::where('request_id', '=', $request_id)->where('walker_id', '=', $walker_id)->update(array('status' => 3));
    //     $request_meta = RequestMeta::where('request_id', '=', $request_id)->where('status', '=', 0)->orderBy('created_at')->first();

    //    # query 
    //     if (isset($request_meta->walker_id)) {
    //             Requests::where('id', '=', $request_id)->update(array('current_walker' => $request_meta->walker_id, 'request_start_time' => date("Y-m-d H:i:s")));

    //             # Send Notification
    //             $walker = Walker::find($request_meta->walker_id);
    //             $walker->is_available = 0;
    //             $walker->save();
                
    //             $settings = Settings::where('key', 'provider_timeout')->first();
    //             $time_left = $settings->value;

    //             $owner = Owner::find($request->owner_id);
    //             $msg_array = array();
    //             $msg_array['unique_id'] = 1;
    //             $msg_array['request_id'] = $request->id;
    //             $msg_array['id'] = $request_meta->walker_id;

    //             if ($walker) {
    //                 $msg_array['token'] = $walker->token;
    //             }

    //             $msg_array['time_left_to_respond'] = $time_left;
    //             $msg_array['payment_mode'] = $request->payment_mode;
    //             $msg_array['payment_type'] = $request->payment_mode;
    //             $msg_array['time_left_to_respond'] = $timeout;
    //             $msg_array['client_profile'] = array();
    //             $msg_array['client_profile']['name'] = $owner->first_name . " " . $owner->last_name;
    //             $msg_array['client_profile']['picture'] = $owner->picture;
    //             $msg_array['client_profile']['bio'] = $owner->bio;
    //             $msg_array['client_profile']['address'] = $owner->address;
    //             $msg_array['client_profile']['phone'] = $owner->phone;

    //             $request_data = array();
    //             $request_data['owner'] = array();
    //             $request_data['owner']['name'] = $owner->first_name . " " . $owner->last_name;
    //             $request_data['owner']['picture'] = $owner->picture;
    //             $request_data['owner']['phone'] = $owner->phone;
    //             $request_data['owner']['address'] = $owner->address;
    //             $request_data['owner']['latitude'] = $request->latitude;
    //             $request_data['owner']['longitude'] = $request->longitude;

    //             if ($request->d_latitude != NULL) {
    //                 $request_data['owner']['d_latitude'] = $request->D_latitude;
    //                 $request_data['owner']['d_longitude'] = $request->D_longitude;
    //             }

                
    //             $request_data['owner']['owner_dist_lat'] = $request->D_latitude;
    //             $request_data['owner']['owner_dist_long'] = $request->D_longitude;
    //             $request_data['owner']['dest_latitude'] = $request->D_latitude;
    //             $request_data['owner']['dest_longitude'] = $request->D_longitude;
    //             $request_data['owner']['payment_type'] = $request->payment_mode;
    //             $request_data['owner']['rating'] = $owner->rate;
    //             $request_data['owner']['num_rating'] = $owner->rate_count;

    //             /* $request_data['owner']['rating'] = DB::table('review_dog')->where('owner_id', '=', $owner->id)->avg('rating') ? : 0;
    //               $request_data['owner']['num_rating'] = DB::table('review_dog')->where('owner_id', '=', $owner->id)->count(); */
    //             $request_data['dog'] = array();
    //             /* if ($dog = Dog::find($owner->dog_id)) {

    //               $request_data['dog']['name'] = $dog->name;
    //               $request_data['dog']['age'] = $dog->age;
    //               $request_data['dog']['breed'] = $dog->breed;
    //               $request_data['dog']['likes'] = $dog->likes;
    //               $request_data['dog']['picture'] = $dog->image_url;
    //               } */
    //             $msg_array['request_data'] = $request_data;
    //             $user_type = 1;
    //             $id = $request_meta->walker_id;
    //             $title = transl('new_req', $id, $user_type);
    //             $message = $msg_array;
    //             //notification($request_meta->walker_id, "walker", "new_request", $message);
    //             send_data_message();

    //      }else{
    //             # request ended
    //             Requests::where('id', '=', $request_id)->update(array('current_walker' => 0, 'status' => 1));
    //             $owne = Owner::where('id', $request->owner_id)->first();
    //             $driver_keyword = Config::get('app.generic_keywords.Provider');
    //             $owner_data_id = $owne->id;
    //             $id = $owner_data_id;
    //             $user_type = 0;
    //             $title = transl('no_provider_found', $id, $user_type);
    //             $message = 'Hello, we were unable to find you a ride. Please try again.';
    //             send_sms_message($owne->phone, $message);                               
    //         }
        
    //         $response_array = array('success' => true,
    //                                 'request_id' => $request_id,
    //                                 'status' => $request->status,
    //                                 'confirmed_walker' => $request->confirmed_walker,
    //                                 'is_walker_started' => $request->is_walker_started,
    //                                 'is_walker_arrived' => $request->is_walker_arrived,
    //                                 'is_walk_started' => $request->is_started,
    //                                 'is_completed' => $request->is_completed,
    //                                 'is_walker_rated' => $request->is_walker_rated,
    //                                 'is_cancelled' => $request->is_cancelled);
    //     return $response_array;
    // }

    // public function respond_request() {

    //     $data['token'] = Input::get('token');
    //     $data['walker_id'] = Input::get('id');
    //     $data['request_id'] = Input::get('request_id');
    //     $data['accepted'] = Input::get('accepted');
    //     $data['date_time'] = Input::get('datetime');

    //     $validator=$this->validate_parameters($data);

    //     if ($validator->fails()) {
    //         $response_array =$this->error_12();
    //         $response_code = 200;
    //     } else {
    //         $token = $data['token'];
    //         $walker_id = $data['walker_id'];
    //         $request_id = $data['request_id'];
    //         $accepted = $data['accepted'];
    //         $date_time = $data['date_time'];
            
    //         $is_admin = $this->isAdmin($token);
    //         $walker_data = $this->getWalkerData($walker_id, $token, $is_admin);
    //         if ($walker_data){
    //             $session_token=is_token_active($walker_data->token_expiry);

    //             if ( $session_token || $is_admin) {

    //                 # Retrive and validate the Request
    //                 if ($request = Requests::find($request_id)) {
    //                     # request is not cancelled
    //                     if ($request->is_cancelled != 1) {
    //                         #current walker is confirmed walker
    //                         if ($request->current_walker == $walker_id) {
    //                             if ($accepted == 1) {
    //                                         if ($request->later == 1) {
    //                                             // request ended
    //                                             Requests::where('id', '=', $request_id)->update(array('confirmed_walker' => $walker_id, 'status' => 1));
    //                                         } else {
    //                                             # sheduled request
    //                                             Requests::where('id', '=', $request_id)->update(array('confirmed_walker' => $walker_id, 'status' => 1, 'request_start_time' => date('Y-m-d H:i:s')));
    //                                         }

    //                                         // confirm walker
    //                                         RequestMeta::where('request_id', '=', $request_id)->where('walker_id', '=', $walker_id)->update(array('status' => 1));

    //                                         // Update Walker availability
    //                                         Walker::where('id', '=', $walker_id)->update(array('is_available' => 0));

    //                                         // remove other schedule_meta
    //                                         RequestMeta::where('request_id', '=', $request_id)->where('status', '=', 0)->delete();

    //                                         # Send Notification
    //                                         $walker_data=$this->get_walker_info($walker_id);

    //                                         # billing information
    //                                         $bill=$this->set_billing_info($request);

                                            
    //                                         if ($request->later == 1) {
    //                                             $this->set_provider_availability($request);
    //                                         }
                                                                            
    //                                      $request->is_walker_started = 1;
    //                                      $request->save();



    //                                         $response = $this->notify_client($request,$request_id,$walker_data,$bill);

    //                                         $response_array = array(
    //                                             'success' => true,
    //                                             'request_id' => $request_id,
    //                                             'status' => $request->status,
    //                                             'confirmed_walker' => $request->confirmed_walker,
    //                                             'is_walker_started' => $request->is_walker_started,
    //                                             'is_walker_arrived' => $request->is_walker_arrived,
    //                                             'is_walk_started' => $request->is_started,
    //                                             'is_completed' => $request->is_completed,
    //                                             'is_walker_rated' => $request->is_walker_rated,
    //                                             'is_cancelled' => $request->is_cancelled,
    //                                             'walker' => $walker_data,
    //                                             'bill' => $bill
    //                                         );

    //                                         $response_code = 200;


    //                                         #$this->send_sms_to_client($request,$walker);
    //                             }else{
    //                                  $response_array=$this->notify_client_2($request_id,$request);
    //                                  $response_code = 200;     
    //                             }
    //                         }else{
    //                              $response_array = array('success' => false, 'error' => 30, 'error_messages' => array(30), 'error_code' => 472);
    //                              $response_code = 200;
    //                         }     
    //                     }else{
    //                           $response_array = array('success' => false, 'error' => 31, 'error_messages' => array(31), 'error_code' => 405);
    //                           $response_code = 200;
    //                     }

    //                 }else{
    //                     $response_array = array('success' => false, 'error' => 32, 'error_messages' => array(32), 'error_code' => 405);
    //                     $response_code = 200;
    //                 }
                    
    //             }else{
    //                 $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
    //                 $response_code = 200;
    //             }
    //         }else{

    //             if ($is_admin){
    //                 $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
    //             }else{
    //                 $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
    //             }
                
    //             $response_code = 200;
    //         }
    //     }

    //     $response = Response::json($response, $response_code);
    //     return $response;
    // }

    public function respond_request() {
            $helper = new Helper();
            $token = Input::get('token');
            $walker_id = Input::get('id');
            $request_id = Input::get('request_id');
            $accepted = Input::get('accepted');
            $date_time = Input::get('datetime');
            $validator = Validator::make(
                            array(
                        'token' => $token,
                        'walker_id' => $walker_id,
                        'request_id' => $request_id,
                        'accepted' => $accepted,
                            ), array(
                        'token' => 'required',
                        'walker_id' => 'required|integer',
                        'accepted' => 'required|integer',
                        'request_id' => 'required|integer'
                            ), array(
                        'token.required' => 24,
                        'walker_id.required' => 20,
                        'accepted.required' => 29,
                        'request_id.required' => 19
                            )
            );
            /* $driver = Keywords::where('id', 1)->first(); */
            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    $helper = new Helper();
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        // Retrive and validate the Request
                        if ($request = Requests::find($request_id)) {
                            if ($request->is_cancelled != 1) {
                                if ($request->current_walker == $walker_id) {
                                    if ($accepted == 1) {
                                        if ($request->later == 1) {
                                            // request ended
                                            Requests::where('id', '=', $request_id)->update(array('confirmed_walker' => $walker_id, 'status' => 1));
                                        } else {
                                            Requests::where('id', '=', $request_id)->update(array('confirmed_walker' => $walker_id, 'status' => 1, 'request_start_time' => date('Y-m-d H:i:s')));
                                        }
                                        // confirm walker
                                        RequestMeta::where('request_id', '=', $request_id)->where('walker_id', '=', $walker_id)->update(array('status' => 1));
                                        // Update Walker availability
                                        Walker::where('id', '=', $walker_id)->update(array('is_available' => 0));
                                        // remove other schedule_meta
                                        RequestMeta::where('request_id', '=', $request_id)->where('status', '=', 0)->delete();
                                        // Send Notification
                                        $walker = Walker::find($walker_id);
                                        $walker_data = array();
                                        $walker_data['first_name'] = $walker->first_name;
                                        $walker_data['last_name'] = $walker->last_name;
                                        $walker_data['phone'] = $walker->phone;
                                        $walker_data['bio'] = $walker->bio;
                                        $walker_data['picture'] = $walker->picture;
                                        $walker_data['latitude'] = $walker->latitude;
                                        $walker_data['longitude'] = $walker->longitude;
                                        $walker_data['type'] = $walker->type;
                                        $walker_data['rating'] = $walker->rate;
                                        $walker_data['num_rating'] = $walker->rate_count;
                                        $walker_data['car_model'] = $walker->car_model;
                                        $walker_data['car_number'] = $walker->car_number;
                                        /* $walker_data['rating'] = DB::table('review_walker')->where('walker_id', '=', $walker->id)->avg('rating') ? : 0;
                                          $walker_data['num_rating'] = DB::table('review_walker')->where('walker_id', '=', $walker->id)->count(); */
                                        $settings = Settings::where('key', 'default_distance_unit')->first();
                                        $unit = $settings->value;
                                        if ($unit == 0) {
                                            $unit_set = 'kms';
                                        } elseif ($unit == 1) {
                                            $unit_set = 'miles';
                                        }
                                        $bill = array();
                                        if ($request->is_completed == 1) {
                                            $bill['distance'] = (string) $helper->convert($request->distance, $unit);
                                            $bill['unit'] = $unit_set;
                                            $bill['time'] = $request->time;
                                            $bill['base_price'] = $request->base_price;
                                            $bill['distance_cost'] = $request->distance_cost;
                                            $bill['time_cost'] = $request->time_cost;
                                            $bill['total'] = $request->total;
                                            $bill['is_paid'] = $request->is_paid;
                                        }
                                        /* $setting = Settings::where('key', 'allow_calendar')->first();
                                          if ($request->later == 1 && $setting->value == 1) { */
                                        if ($request->later == 1) {
                                            $date_time = $request->request_start_time;
                                            $datewant = new DateTime($date_time);
                                            $datetime = $datewant->format('Y-m-d H:i:s');
                                            $end_time = $datewant->add(new DateInterval('P0Y0M0DT2H0M0S'))->format('Y-m-d H:i:s');
                                            $provavail = ProviderAvail::where('provider_id', $walker_id)->where('start', '<=', $datetime)->where('end', '>=', $end_time)->first();
                                            $starttime = $provavail->start;
                                            $endtime = $provavail->end;
                                            $provavail->delete();
                                            if ($starttime == $datetime) {
                                                $provavail1 = new ProviderAvail;
                                                $provavail1->provider_id = $walker_id;
                                                $provavail1->start = $end_time;
                                                $provavail1->end = $endtime;
                                                $provavail1->save();
                                            } elseif ($endtime == $end_time) {
                                                $provavail1 = new ProviderAvail;
                                                $provavail1->provider_id = $walker_id;
                                                $provavail1->start = $starttime;
                                                $provavail1->end = $datetime;
                                                $provavail1->save();
                                            } else {
                                                $provavail1 = new ProviderAvail;
                                                $provavail1->provider_id = $walker_id;
                                                $provavail1->start = $starttime;
                                                $provavail1->end = $datetime;
                                                $provavail1->save();
                                                $provavail2 = new ProviderAvail;
                                                $provavail2->provider_id = $walker_id;
                                                $provavail2->start = $end_time;
                                                $provavail2->end = $endtime;
                                                $provavail2->save();
                                            }
                                        }
                                                                        
                                                                            $request->is_walker_started = 1;
                                                                            $request->save();
                                        $response_array = array(
                                            'success' => true,
                                            'request_id' => $request_id,
                                            'status' => $request->status,
                                            'confirmed_walker' => $request->confirmed_walker,
                                            'is_walker_started' => $request->is_walker_started,
                                            'is_walker_arrived' => $request->is_walker_arrived,
                                            'is_walk_started' => $request->is_started,
                                            'is_completed' => $request->is_completed,
                                            'is_walker_rated' => $request->is_walker_rated,
                                            'is_cancelled' => $request->is_cancelled,
                                            'walker' => $walker_data,
                                            'bill' => $bill,
                                        );
                                        /* $driver = Keywords::where('id', 1)->first(); */
                                        /* $trip = Keywords::where('id', 4)->first(); */
                                        /* $title = '' . $driver->keyword . ' has accepted the ' . $trip->keyword; */
                                        $user_type = 0;
                                        $id = $request->owner_id;
                                        $helper = new Helper();
                                        $title = $helper->transl('accept_request_push', $id, $user_type);
                                        $message = $response_array;
                                        $helper = new Helper();
                                        $helper->send_notifications($request->owner_id, "owner", "request_accepted", $message);
                                        $owner = Owner::find($request->owner_id);
                                        $message = "Hello, ".$walker->first_name." is picking you in a ".$walker->car_model." ".$walker->car_number.". Contact on ".$walker->phone;
                                        $helper->send_sms_message($owner->phone, $message);
                                    } else {
                                        $time = date("Y-m-d H:i:s");
                                        $query = "SELECT id,owner_id,current_walker,TIMESTAMPDIFF(SECOND,request_start_time, '$time') as diff from request where id = '$request_id'";
                                        $results = DB::select(DB::raw($query));
                                        $settings = Settings::where('key', 'provider_timeout')->first();
                                        $timeout = $settings->value;
                                        // Archiving Old Walker
                                        RequestMeta::where('request_id', '=', $request_id)->where('walker_id', '=', $walker_id)->update(array('status' => 3));
                                        $request_meta = RequestMeta::where('request_id', '=', $request_id)->where('status', '=', 0)->orderBy('created_at')->first();
                                                                            
                                                                            //$old_walker = Walker::find($walker_id);
                                                                            //$old_walker->is_available = 1;
                                                                            //$old_walker->save();
                                        // update request
                                        if (isset($request_meta->walker_id)) {
                                            Requests::where('id', '=', $request_id)->update(array('current_walker' => $request_meta->walker_id, 'request_start_time' => date("Y-m-d H:i:s")));
                                            // Send Notification
                                            $walker = Walker::find($request_meta->walker_id);
                                                                                    $walker->is_available = 0;
                                                                                    $walker->save();
                                            $settings = Settings::where('key', 'provider_timeout')->first();
                                            $time_left = $settings->value;
                                            $owner = Owner::find($request->owner_id);
                                            $msg_array = array();
                                            $msg_array['unique_id'] = 1;
                                            $msg_array['request_id'] = $request->id;
                                            $msg_array['id'] = $request_meta->walker_id;
                                            if ($walker) {
                                                $msg_array['token'] = $walker->token;
                                            }
                                            $msg_array['time_left_to_respond'] = $time_left;
                                            $msg_array['payment_mode'] = $request->payment_mode;
                                            $msg_array['payment_type'] = $request->payment_mode;
                                            $msg_array['time_left_to_respond'] = $timeout;
                                            $msg_array['client_profile'] = array();
                                            $msg_array['client_profile']['name'] = $owner->first_name . " " . $owner->last_name;
                                            $msg_array['client_profile']['picture'] = $owner->picture;
                                            $msg_array['client_profile']['bio'] = $owner->bio;
                                            $msg_array['client_profile']['address'] = $owner->address;
                                            $msg_array['client_profile']['phone'] = $owner->phone;
                                            $request_data = array();
                                            $request_data['owner'] = array();
                                            $request_data['owner']['name'] = $owner->first_name . " " . $owner->last_name;
                                            $request_data['owner']['picture'] = $owner->picture;
                                            $request_data['owner']['phone'] = $owner->phone;
                                            $request_data['owner']['address'] = $owner->address;
                                            $request_data['owner']['latitude'] = $request->latitude;
                                            $request_data['owner']['longitude'] = $request->longitude;
                                            if ($request->d_latitude != NULL) {
                                                $request_data['owner']['d_latitude'] = $request->D_latitude;
                                                $request_data['owner']['d_longitude'] = $request->D_longitude;
                                            }
                                            $request_data['owner']['owner_dist_lat'] = $request->D_latitude;
                                            $request_data['owner']['owner_dist_long'] = $request->D_longitude;
                                            $request_data['owner']['dest_latitude'] = $request->D_latitude;
                                            $request_data['owner']['dest_longitude'] = $request->D_longitude;
                                            $request_data['owner']['payment_type'] = $request->payment_mode;
                                            $request_data['owner']['rating'] = $owner->rate;
                                            $request_data['owner']['num_rating'] = $owner->rate_count;
                                            /* $request_data['owner']['rating'] = DB::table('review_dog')->where('owner_id', '=', $owner->id)->avg('rating') ? : 0;
                                              $request_data['owner']['num_rating'] = DB::table('review_dog')->where('owner_id', '=', $owner->id)->count(); */
                                            $request_data['dog'] = array();
                                            /* if ($dog = Dog::find($owner->dog_id)) {
                                              $request_data['dog']['name'] = $dog->name;
                                              $request_data['dog']['age'] = $dog->age;
                                              $request_data['dog']['breed'] = $dog->breed;
                                              $request_data['dog']['likes'] = $dog->likes;
                                              $request_data['dog']['picture'] = $dog->image_url;
                                              } */
                                            $msg_array['request_data'] = $request_data;
                                            $user_type = 1;
                                            $id = $request_meta->walker_id;
                                            $title = $helper->transl('new_req', $id, $user_type);
                                            $message = $msg_array;
                                            $helper->send_notifications($request_meta->walker_id, "walker", "new_request", $message);
                                        } 
                                                                        else {
                                            // request ended
                                            Requests::where('id', '=', $request_id)->update(array('current_walker' => 0, 'status' => 1));
                                            /* $driver = Keywords::where('id', 1)->first(); */
                                            $owne = Owner::where('id', $request->owner_id)->first();
                                            /* $driver_keyword = $driver->keyword; */
                                            $driver_keyword = \Config::get('app.generic_keywords.Provider');
                                            $owner_data_id = $owne->id;
                                            $id = $owner_data_id;
                                            $user_type = 0;
                                            $helper = new Helper();
                                            $title = $helper->transl('no_provider_found', $id, $user_type);
                                            //send_notifications($owner_data_id, "owner", $title, 'No ' . $driver_keyword . ' are available right now in your area. Kindly try after sometime.');
                                                $message = 'Hello, we were unable to find you a ride. Please try again.';
                                            $helper->send_sms_message($owne->phone, $message);
                                                                            }
                                    }
                                    
                                    $response_array = array('success' => true,
                                        'request_id' => $request_id,
                                        'status' => $request->status,
                                        'confirmed_walker' => $request->confirmed_walker,
                                        'is_walker_started' => $request->is_walker_started,
                                        'is_walker_arrived' => $request->is_walker_arrived,
                                        'is_walk_started' => $request->is_started,
                                        'is_completed' => $request->is_completed,
                                        'is_walker_rated' => $request->is_walker_rated,
                                        'is_cancelled' => $request->is_cancelled,);
                                    $response_code = 200;
                                } else {
                                    /* $response_array = array('success' => false, 'error' => 'Request ID does not matches' . $driver->keyword . ' ID', 'error_code' => 472); */
                                    $response_array = array('success' => false, 'error' => 30, 'error_messages' => array(30), 'error_code' => 472);
                                    $response_code = 200;
                                }
                            } else {
                                $response_array = array('success' => false, 'error' => 31, 'error_messages' => array(31), 'error_code' => 405);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 32, 'error_messages' => array(32), 'error_code' => 405);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $response_array = array('success' => false, 'error' => '' . $driver->keyword . ' ID not Found', 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
            $response = Response::json($response_array, $response_code);
            return $response;
        }

    // Get Request Status
    public function request_in_progress() {

        $token = Input::get('token');
        $walker_id = Input::get('id');

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'walker_id' => $walker_id,
                        ), array(
                    'token' => 'required',
                    'walker_id' => 'required|integer',
                        ), array(
                    'token.required' => 24,
                    'walker_id.required' => 20,
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $stripe_secret_key = \Config::get('app.stripe_secret_key');
            $stripe_publishable_key = \Config::get('app.stripe_publishable_key');
            $gcm_browser_key = \Config::get('app.gcm_browser_key');

            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                $helper = new Helper();
                if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {

                    $request = Requests::where('status', '=', 1)->where('is_cancelled', '=', 0)->where('is_completed', '=', 0)->where('confirmed_walker', '=', $walker_id)->first();
                    if ($request) {
                        $request_id = $request->id;
                    } else {
                        $request_id = -1;
                    }

                    $txt_approve = "Decline";
                    if ($walker_data->is_approved) {
                        $txt_approve = "Approved";
                    }

                    $response_array = array(
                        'request_id' => $request_id,
                        'is_approved' => $walker_data->is_approved,
                        'is_available' => $walker_data->is_active,
                        'is_approved_txt' => $txt_approve,
                        'stripe_secret_key' => $stripe_secret_key,
                        'stripe_publishable_key' => $stripe_publishable_key,
                        'gcm_browser_key' => $gcm_browser_key,
                        'success' => true,
                    );
                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $driver = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $driver->keyword . ' ID not Found', 'error_messages' => array('' . $driver->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Get Request Status
    public function get_request() {
        $helper = new Helper();
        $request_id = Input::get('request_id');
        $token = Input::get('token');
        $walker_id = Input::get('id');

        $validator = Validator::make(
                        array(
                    'request_id' => $request_id,
                    'token' => $token,
                    'walker_id' => $walker_id,
                        ), array(
                    'request_id' => 'required|integer',
                    'token' => 'required',
                    'walker_id' => 'required|integer',
                        ), array(
                    'request_id.required' => 19,
                    'token.required' => 24,
                    'walker_id.required' => 20,
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            $stripe_secret_key = \Config::get('app.stripe_secret_key');
            $stripe_publishable_key = \Config::get('app.stripe_publishable_key');
            $gcm_browser_key = \Config::get('app.gcm_browser_key');

            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                $helper = new Helper();
                if ($helper->is_token_active($walker_data->token_expiry)) {
                    $txt_approve = "Decline";
                    if ($walker_data->is_approved) {
                        $txt_approve = "Approved";
                    }
                    // Do necessary operations
                    if ($request = Requests::find($request_id)) {
                        if ($request->confirmed_walker == $walker_id) {

                            $owner = Owner::find($request->owner_id);
                            $request_data = array();
                            $request_data['is_walker_started'] = $request->is_walker_started;
                            $request_data['is_walker_arrived'] = $request->is_walker_arrived;
                            $request_data['is_started'] = $request->is_started;
                            $request_data['is_completed'] = $request->is_completed;
                            $request_data['is_dog_rated'] = $request->is_dog_rated;
                            $request_data['is_cancelled'] = $request->is_cancelled;
                            $request_data['dest_latitude'] = $request->D_latitude;
                            $request_data['dest_longitude'] = $request->D_longitude;
                            $request_data['dest_address'] = $request->dest_address;
                            $request_data['src_address'] = $request->src_address;

                            $user_timezone = $owner->timezone;
                            $default_timezone = \Config::get('app.timezone');
                            $helper = new Helper();
                            $date_time = $helper->get_user_time($default_timezone, $user_timezone, $request->request_start_time);

                            $request_data['accepted_time'] = $date_time;
                            $request_data['payment_mode'] = $request->payment_mode;
                            $request_data['payment_type'] = $request->payment_mode;
                            if ($request->promo_code != "") {
                                if ($request->promo_code != "") {
                                    $promo_code = PromoCodes::where('id', $request->promo_id)->first();
                                    $promo_value = $promo_code->value;
                                    $promo_type = $promo_code->type;
                                    if ($promo_type == 1) {
                                        $discount = $request->total * $promo_value / 100;
                                    } elseif ($promo_type == 2) {
                                        $discount = $promo_value;
                                    }
                                    $request_data['promo_discount'] = $discount;
                                }
                            }
                            if ($request->is_started == 1) {

                                $time = DB::table('walk_location')
                                        ->where('request_id', $request_id)
                                        ->min('created_at');

                                $date_time = $helper->get_user_time($default_timezone, $user_timezone, $time);

                                $request_data['start_time'] = $date_time;

                                $settings = Settings::where('key', 'default_distance_unit')->first();
                                $unit = $settings->value;

                                $distance = DB::table('walk_location')->where('request_id', $request_id)->max('distance');
                                $request_data['distance'] = (string) $helper->convert($distance, $unit);
                                if ($unit == 0) {
                                    $unit_set = 'kms';
                                } elseif ($unit == 1) {
                                    $unit_set = 'miles';
                                }
                                $request_data['unit'] = $unit_set;

                                $loc1 = WalkLocation::where('request_id', $request->id)->first();
                                $loc2 = WalkLocation::where('request_id', $request->id)->orderBy('id', 'desc')->first();
                                if ($loc1) {
                                    $time1 = strtotime($loc2->created_at);
                                    $time2 = strtotime($loc1->created_at);
                                    $difference = intval(($time1 - $time2) / 60);
                                } else {
                                    $difference = 0;
                                }
                                $request_data['time'] = $difference;
                                $request_data['time'] = $request->time;
                            }

                            if ($request->is_completed == 1) {
                                $time = DB::table('walk_location')
                                        ->where('request_id', $request_id)
                                        ->min('created_at');

                                $date_time = $helper->get_user_time($default_timezone, $user_timezone, $time);

                                $request_data['start_time'] = $date_time;

                                $settings = Settings::where('key', 'default_distance_unit')->first();
                                $unit = $settings->value;

                                $distance = DB::table('walk_location')->where('request_id', $request_id)->max('distance');
                                $request_data['distance'] = (string) $helper->convert($distance, $unit);
                                if ($unit == 0) {
                                    $unit_set = 'kms';
                                } elseif ($unit == 1) {
                                    $unit_set = 'miles';
                                }
                                $request_data['unit'] = $unit_set;

                                $time = DB::table('walk_location')
                                        ->where('request_id', $request_id)
                                        ->max('created_at');

                                $end_time = $helper->get_user_time($default_timezone, $user_timezone, $time);

                                $request_data['end_time'] = $end_time;
                            }

                            $request_data['owner'] = array();
                            $request_data['owner']['name'] = $owner->first_name . " " . $owner->last_name;
                            $request_data['owner']['picture'] = $owner->picture;
                            $request_data['owner']['phone'] = $owner->phone;
                            $request_data['owner']['address'] = $owner->address;
                            $request_data['owner']['latitude'] = $request->latitude;
                            $request_data['owner']['longitude'] = $request->longitude;
                            $request_data['owner']['src_address'] = $request->src_address;
                            $request_data['owner']['dest_address'] = $request->dest_address;
                            if ($request->D_latitude != NULL) {
                                $request_data['owner']['d_latitude'] = $request->D_latitude;
                                $request_data['owner']['d_longitude'] = $request->D_longitude;
                            }
                            $request_data['owner']['owner_dist_lat'] = $request->D_latitude;
                            $request_data['owner']['owner_dist_long'] = $request->D_longitude;
                            $request_data['owner']['dest_latitude'] = $request->D_latitude;
                            $request_data['owner']['dest_longitude'] = $request->D_longitude;

                            $request_data['owner']['rating'] = $owner->rate;
                            $request_data['owner']['num_rating'] = $owner->rate_count;
                            /* $request_data['owner']['rating'] = DB::table('review_dog')->where('owner_id', '=', $owner->id)->avg('rating') ? : 0;
                              $request_data['owner']['num_rating'] = DB::table('review_dog')->where('owner_id', '=', $owner->id)->count(); */
                            $request_data['keys'] = array();
                            $request_data['keys']['stripe_secret_key'] = $stripe_secret_key;
                            $request_data['keys']['stripe_publishable_key'] = $stripe_publishable_key;
                            $request_data['keys']['gcm_browser_key'] = $gcm_browser_key;

                            /*   if ($dog = Dog::find($owner->dog_id)) {

                              $request_data['dog']['name'] = $dog->name;
                              $request_data['dog']['age'] = $dog->age;
                              $request_data['dog']['breed'] = $dog->breed;
                              $request_data['dog']['likes'] = $dog->likes;
                              $request_data['dog']['picture'] = $dog->image_url;
                              } */
                            $request_data['bill'] = array();
                            $bill = array();
                            $settings = Settings::where('key', 'default_distance_unit')->first();
                            $unit = $settings->value;
                            if ($unit == 0) {
                                $unit_set = 'kms';
                            } elseif ($unit == 1) {
                                $unit_set = 'miles';
                            }
                            $requestserv = RequestServices::where('request_id', $request->id)->first();

                            $request_typ = ProviderType::where('id', '=', $requestserv->type)->first();
                            $setbase_distance = $request_typ->base_distance;
                            $base_price = $request_typ->base_price;
                            $price_per_unit_distance = $request_typ->price_per_unit_distance;
                            $price_per_unit_time = $request_typ->price_per_unit_time;

                            $pt_new = ProviderType::where('id', $walker_data->type)->first();

                            $ps_new = ProviderServices::where('id', $walker_data->type)->first();

                            /* $currency_selected = Keywords::find(5); */
                            if ($request->is_completed == 1) {
                                $bill['distance'] = (string) $request->distance;
                                $bill['unit'] = $unit_set;
                                $bill['time'] = $request->time;
                                if ($requestserv->base_price != 0) {
                                    $bill['base_distance'] = $setbase_distance;
                                    $bill['base_price'] = $helper->currency_converted($requestserv->base_price);
                                    $bill['distance_cost'] = $helper->currency_converted($requestserv->distance_cost);
                                    $bill['time_cost'] = $helper->currency_converted($requestserv->time_cost);
                                } else {
                                    /* $setbase_price = Settings::where('key', 'base_price')->first();
                                      $bill['base_price'] = currency_converted($setbase_price->value);
                                      $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
                                      $bill['distance_cost'] = currency_converted($setdistance_price->value);
                                      $settime_price = Settings::where('key', 'price_per_unit_time')->first();
                                      $bill['time_cost'] = currency_converted($settime_price->value); */
                                    $bill['base_distance'] = $setbase_distance;
                                    $bill['base_price'] = $helper->currency_converted($base_price);
                                    $bill['distance_cost'] = $helper->currency_converted($price_per_unit_distance);
                                    $bill['time_cost'] = $helper->currency_converted($price_per_unit_time);
                                }
                                if ($pt_new->base_price != 0) {

                                    $bill['price_per_unit_distance'] = $helper->currency_converted($pt_new->price_per_unit_distance);
                                    $bill['price_per_unit_time'] = $helper->currency_converted($pt_new->price_per_unit_time);
                                } else {

                                    $bill['price_per_unit_distance'] = $helper->currency_converted($ps_new->price_per_unit_distance);

                                    $bill['price_per_unit_time'] = $helper->currency_converted($ps_new->price_per_unit_time);
                                }

                                $admins = User::first();
                                $walker = Walker::where('id', $walker_id)->first();
                                $bill['walker']['email'] = $walker->email;
                                $bill['admin']['email'] = $admins->username;
                                if ($request->transfer_amount != 0) {
                                    $bill['walker']['amount'] = $helper->currency_converted($request->total - $request->transfer_amount);
                                    $bill['admin']['amount'] = $helper->currency_converted($request->transfer_amount);
                                } else {
                                    $bill['walker']['amount'] = $helper->currency_converted($request->transfer_amount);
                                    $bill['admin']['amount'] = $helper->currency_converted($request->total - $request->transfer_amount);
                                }
                                $discount = 0;
                                if ($request->promo_code != "") {
                                    if ($request->promo_code != "") {
                                        $promo_code = PromoCodes::where('id', $request->promo_code)->first();
                                        if ($promo_code) {
                                            $promo_value = $promo_code->value;
                                            $promo_type = $promo_code->type;
                                            if ($promo_type == 1) {
                                                // Percent Discount
                                                $discount = $request->total * $promo_value / 100;
                                            } elseif ($promo_type == 2) {
                                                // Absolute Discount
                                                $discount = $promo_value;
                                            }
                                        }
                                    }
                                }
                                /* $bill['currency'] = $currency_selected->keyword; */
                                $bill['currency'] = \Config::get('app.generic_keywords.Currency');
                                /* $bill['total'] = currency_converted($request->total); */
                                $bill['main_total'] = $helper->currency_converted($request->total);
                                $bill['actual_total'] = $helper->currency_converted($request->total);
                                $tot = $helper->currency_converted($request->total - $request->ledger_payment - $request->promo_payment);
                                if ($tot <= 0) {
                                    $tot = 0;
                                }
                                $bill['total'] = $tot;
                                $bill['referral_bonus'] = $helper->currency_converted($request->ledger_payment);
                                $bill['promo_bonus'] = $helper->currency_converted($request->promo_payment);
                                $bill['payment_type'] = $request->payment_mode;
                                $bill['payment_msisdn'] = $request->payment_msisdn;
                                $bill['is_paid'] = $request->is_paid;
                            }
                            $request_data['bill'] = $bill;

                            $cards = "";
                            $cardlist = Payment::where('owner_id', $owner->id)->where('is_default', 1)->first();
                            if (is_array($cardlist)) {
                                $cards = array();
                                $default = $cardlist->is_default;
                                if ($default == 1) {
                                    $cards['is_default_text'] = "default";
                                } else {
                                    $cards['is_default_text'] = "not_default";
                                }
                                $cards['card_id'] = $cardlist->id;
                                $cards['owner_id'] = $cardlist->owner_id;
                                $cards['customer_id'] = $cardlist->customer_id;
                                $cards['last_four'] = $cardlist->last_four;
                                $cards['card_token'] = $cardlist->card_token;
                                $cards['card_type'] = $cardlist->card_type;
                                $cards['is_default'] = $default;
                            }
                            $request_data['card_details'] = $cards;

                            $chagre = array();

                            /* $settings = Settings::where('key', 'default_distance_unit')->first();
                              $unit = $settings->value;
                              if ($unit == 0) {
                              $unit_set = 'kms';
                              } elseif ($unit == 1) {
                              $unit_set = 'miles';
                              } */
                            $chagre['unit'] = $unit_set;

                            $requestserv = RequestServices::where('request_id', $request->id)->first();
                            if ($requestserv->base_price != 0) {
                                $chagre['base_price'] = $requestserv->base_price;
                                $chagre['distance_price'] = $requestserv->distance_cost;
                                $chagre['price_per_unit_time'] = $requestserv->time_cost;
                            } else {
                                /* $setbase_price = Settings::where('key', 'base_price')->first();
                                  $chagre['base_price'] = $setbase_price->value;
                                  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
                                  $chagre['distance_price'] = $setdistance_price->value;
                                  $settime_price = Settings::where('key', 'price_per_unit_time')->first();
                                  $chagre['price_per_unit_time'] = $settime_price->value; */
                                  $helper = new Helper();
                                $chagre['base_distance'] = $setbase_distance;
                                $chagre['base_price'] = $helper->currency_converted($base_price);
                                $chagre['distance_price'] = $helper->currency_converted($price_per_unit_distance);
                                $chagre['price_per_unit_time'] = $helper->currency_converted($price_per_unit_time);
                            }
                            $chagre['total'] = $request->total;
                            $chagre['is_paid'] = $request->is_paid;



                            $request_data['charge_details'] = $chagre;

                            $response_array = array('success' => true, 'is_available' => $walker_data->is_active, 'is_approved' => $walker_data->is_approved, 'is_approved_txt' => $txt_approve, 'request' => $request_data, 'bill' => $bill);
                            $response_code = 200;
                        } else {
                            /* $driver = Keywords::where('id', 1)->first();
                              $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $driver->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $driver->keyword . ' ID'), 'error_code' => 407); */
                            $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'is_available' => $walker_data->is_active, 'is_approved' => $walker_data->is_approved, 'is_approved_txt' => $txt_approve, 'error_code' => 407);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'is_available' => $walker_data->is_active, 'is_approved' => $walker_data->is_approved, 'is_approved_txt' => $txt_approve, 'error_code' => 408);
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $driver = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $driver->keyword . ' ID not Found', 'error_messages' => array('' . $driver->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Get Request Status
    public function get_walk_location() {
        $helper = new Helper();
        $request_id = Input::get('request_id');
        $token = Input::get('token');
        $walker_id = Input::get('id');
        $timestamp = Input::get('ts');

        $validator = Validator::make(
                        array(
                    'request_id' => $request_id,
                    'token' => $token,
                    'walker_id' => $walker_id,
                        ), array(
                    'request_id' => 'required|integer',
                    'token' => 'required',
                    'walker_id' => 'required|integer',
                        ), array(
                    'request_id.required' => 19,
                    'token.required' => 24,
                    'walker_id.required' => 20,
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                $helper = new Helper();
                if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                    $status_txt = "not active";
                    if ($walker_data->is_active) {
                        $status_txt = "active";
                    }
                    // Do necessary operations
                    if ($request = Requests::find($request_id)) {
                        if ($request->confirmed_walker == $walker_id) {

                            if (isset($timestamp)) {
                                $walk_locations = WalkLocation::where('request_id', '=', $request_id)->where('created_at', '>', $timestamp)->orderBy('created_at')->get();
                            } else {
                                $walk_locations = WalkLocation::where('request_id', '=', $request_id)->orderBy('created_at')->get();
                            }
                            $locations = array();
                            $settings = Settings::where('key', 'default_distance_unit')->first();
                            $unit = $settings->value;
                            foreach ($walk_locations as $walk_location) {
                                $location = array();
                                $location['latitude'] = $walk_location->latitude;
                                $location['longitude'] = $walk_location->longitude;
                                $location['distance'] = $helper->convert($walk_location->distance, $unit);
                                $location['bearing'] = $walk_location->bearing;
                                $location['timestamp'] = $walk_location->created_at;
                                array_push($locations, $location);
                            }

                            $response_array = array(
                                'success' => true,
                                'is_active' => $walker_data->is_active,
                                'is_approved' => $walker_data->is_approved,
                                'locationdata' => $locations,
                            );
                            $response_code = 200;
                        } else {
                            /* $driver = Keywords::where('id', 1)->first();
                              $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $driver->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $driver->keyword . ' ID'), 'error_code' => 407); */
                            $response_array = array(
                                'success' => false,
                                'is_active' => $walker_data->is_active,
                                'is_approved' => $walker_data->is_approved,
                                'error' => 22,
                                'error_messages' => array(22),
                                'error_code' => 407,
                            );
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array(
                            'success' => false,
                            'is_active' => $walker_data->is_active,
                            'is_approved' => $walker_data->is_approved,
                            'error' => 23,
                            'error_messages' => array(23),
                            'error_code' => 408,
                        );
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $driver = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $driver->keyword . ' ID not Found', 'error_messages' => array('' . $driver->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }


    // walker started
    public function request_walker_started() {
        if (Request::isMethod('post')) {
            $request_id = Input::get('request_id');
            $token = Input::get('token');
            $walker_id = Input::get('id');
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');
            if (Input::has('bearing')) {
                $angle = Input::get('bearing');
            }

            $validator = Validator::make(
                            array(
                        'request_id' => $request_id,
                        'token' => $token,
                        'walker_id' => $walker_id,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                            ), array(
                        'request_id' => 'required|integer',
                        'token' => 'required',
                        'walker_id' => 'required|integer',
                        'latitude' => 'required',
                        'longitude' => 'required',
                            ), array(
                        'request_id.required' => 19,
                        'token.required' => 24,
                        'walker_id.required' => 20,
                        'latitude.required' => 28,
                        'longitude.required' => 28,
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $helper = new Helper();
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    $helper = new Helper();
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        // Do necessary operations
                        if ($request = Requests::find($request_id)) {
                            if ($request->confirmed_walker == $walker_id) {

                                if ($request->confirmed_walker != 0) {
                                    $request->is_walker_started = 1;
                                    $request->save();

                                    $location = $helper->get_location($latitude, $longitude);
                                    $latitude = $location['lat'];
                                    $longitude = $location['long'];

                                    if (!isset($angle)) {
                                        $angle = $helper->get_angle($walker_data->latitude, $walker_data->longitude, $latitude, $longitude);
                                    }

                                    $walker_data->old_latitude = $walker_data->latitude;
                                    $walker_data->old_longitude = $walker_data->longitude;
                                    $walker_data->bearing = $angle;
                                    $walker_data->latitude = $latitude;
                                    $walker_data->longitude = $longitude;
                                    $walker_data->save();

                                    // Send Notification
                                    $msg_array = array();
                                    $walker = Walker::find($request->confirmed_walker);
                                    $walker_data = array();
                                    $walker_data['first_name'] = $walker->first_name;
                                    $walker_data['last_name'] = $walker->last_name;
                                    $walker_data['phone'] = $walker->phone;
                                    $walker_data['bio'] = $walker->bio;
                                    $walker_data['picture'] = $walker->picture;
                                    $walker_data['latitude'] = $walker->latitude;
                                    $walker_data['longitude'] = $walker->longitude;
                                    $walker_data['type'] = $walker->type;
                                    $walker_data['rating'] = $walker->rate;
                                    $walker_data['num_rating'] = $walker->rate_count;
                                    $walker_data['car_model'] = $walker->car_model;
                                    $walker_data['car_number'] = $walker->car_number;
                                    /* $walker_data['rating'] = DB::table('review_walker')->where('walker_id', '=', $walker->id)->avg('rating') ? : 0;
                                      $walker_data['num_rating'] = DB::table('review_walker')->where('walker_id', '=', $walker->id)->count(); */

                                    $settings = Settings::where('key', 'default_distance_unit')->first();
                                    $unit = $settings->value;
                                    if ($unit == 0) {
                                        $unit_set = 'kms';
                                    } elseif ($unit == 1) {
                                        $unit_set = 'miles';
                                    }
                                    $bill = array();
                                    if ($request->is_completed == 1) {
                                        $bill['distance'] = (string) $helper->convert($request->distance, $unit);
                                        $bill['unit'] = $unit_set;
                                        $bill['time'] = $request->time;
                                        $bill['base_price'] = $request->base_price;
                                        $bill['distance_cost'] = $request->distance_cost;
                                        $bill['time_cost'] = $request->time_cost;
                                        $bill['total'] = $request->total;
                                        $bill['is_paid'] = $request->is_paid;
                                    }

                                    $response_array = array(
                                        'success' => true,
                                        'request_id' => $request_id,
                                        'status' => $request->status,
                                        'confirmed_walker' => $request->confirmed_walker,
                                        'is_walker_started' => $request->is_walker_started,
                                        'is_walker_arrived' => $request->is_walker_arrived,
                                        'is_walk_started' => $request->is_started,
                                        'is_completed' => $request->is_completed,
                                        'is_walker_rated' => $request->is_walker_rated,
                                        'payment_mode' => $request->payment_data,
                                        'walker' => $walker_data,
                                        'bill' => $bill,
                                    );

                                    $message = $response_array;
                                    /* $driver = Keywords::where('id', 1)->first();
                                      $title = '' . $driver->keyword . ' has started moving towards you'; */
                                    $user_type = 0;
                                    $id = $request->owner_id;
                                    $title = $helper->transl('walker_started_push', $id, $user_type);
                                    //$this->send_walker_started_notification($request->owner_id, "owner", "driver_started", $message);
                                    
                                    $title = "Trip Status";
                                    $body = "Rider coming to you.";
                                $helper->send_trip_notifications($request->owner_id, "owner", "driver_started",null,$message,$title,$body);

                                    $response_array = array('success' => true);
                                    $response_code = 200;
                                } else {
                                    /* $driver = Keywords::where('id', 1)->first();
                                      $response_array = array('success' => false, 'error' => '' . $driver->keyword . ' not yet confirmed', 'error_messages' => array('' . $driver->keyword . ' not yet confirmed'), 'error_code' => 413); */
                                    $response_array = array('success' => false, 'error' => 33, 'error_messages' => array(33), 'error_code' => 413);
                                    $response_code = 200;
                                }
                            } else {
                                /* $driver = Keywords::where('id', 1)->first();
                                  $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $driver->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $driver->keyword . ' ID'), 'error_code' => 407); */
                                $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'error_code' => 407);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $driver = Keywords::where('id', 1)->first();
                          $response_array = array('success' => false, 'error' => '' . $driver->keyword . ' ID not Found', 'error_messages' => array('' . $driver->keyword . ' ID not Found'), 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    

    // walked arrived
    public function request_walker_arrived() {
        if (Request::isMethod('post')) {
            $request_id = Input::get('request_id');
            $token = Input::get('token');
            $walker_id = Input::get('id');
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');
            if (Input::has('bearing')) {
                $angle = Input::get('bearing');
            }

            $validator = Validator::make(
                            array(
                        'request_id' => $request_id,
                        'token' => $token,
                        'walker_id' => $walker_id,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                            ), array(
                        'request_id' => 'required|integer',
                        'token' => 'required',
                        'walker_id' => 'required|integer',
                        'latitude' => 'required',
                        'longitude' => 'required',
                            ), array(
                        'request_id.required' => 19,
                        'token.required' => 24,
                        'walker_id.required' => 20,
                        'latitude.required' => 28,
                        'longitude.required' => 28,
                            )
            );

            /* $driver = Keywords::where('id', 1)->first(); */

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $helper = new Helper();
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    $helper = new Helper();
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        // Do necessary operations
                        if ($request = Requests::find($request_id)) {
                            if ($request->confirmed_walker == $walker_id) {
                                                            $request->is_walker_started = 1;
                                if ($request->is_walker_started == 1) {
                                    $request->is_walker_arrived = 1;
                                    $request->save();
                                    $location = $helper->get_location($latitude, $longitude);
                                    $latitude = $location['lat'];
                                    $longitude = $location['long'];
                                    if (!isset($angle)) {
                                        $angle = $helper->get_angle($walker_data->latitude, $walker_data->longitude, $latitude, $longitude);
                                    }
                                    $walker_data->old_latitude = $walker_data->latitude;
                                    $walker_data->old_longitude = $walker_data->longitude;
                                    $walker_data->bearing = $angle;
                                    $walker_data->latitude = $latitude;
                                    $walker_data->longitude = $longitude;
                                    $walker_data->save();

                                    // Send Notification
                                    $walker = Walker::find($request->confirmed_walker);
                                    $walker_data = array();
                                    $walker_data['first_name'] = $walker->first_name;
                                    $walker_data['last_name'] = $walker->last_name;
                                    $walker_data['phone'] = $walker->phone;
                                    $walker_data['bio'] = $walker->bio;
                                    $walker_data['picture'] = $walker->picture;
                                    $walker_data['latitude'] = $walker->latitude;
                                    $walker_data['longitude'] = $walker->longitude;
                                    $walker_data['type'] = $walker->type;
                                    $walker_data['rating'] = $walker->rate;
                                    $walker_data['num_rating'] = $walker->rate_count;
                                    $walker_data['car_model'] = $walker->car_model;
                                    $walker_data['car_number'] = $walker->car_number;
                                    /* $walker_data['rating'] = DB::table('review_walker')->where('walker_id', '=', $walker->id)->avg('rating') ? : 0;
                                      $walker_data['num_rating'] = DB::table('review_walker')->where('walker_id', '=', $walker->id)->count(); */


                                    $settings = Settings::where('key', 'default_distance_unit')->first();
                                    $unit = $settings->value;
                                    if ($unit == 0) {
                                        $unit_set = 'kms';
                                    } elseif ($unit == 1) {
                                        $unit_set = 'miles';
                                    }
                                    $bill = array();
                                    if ($request->is_completed == 1) {
                                        $bill['distance'] = (string) $helper->convert($request->distance, $unit);
                                        $bill['unit'] = $unit_set;
                                        $bill['time'] = $request->time;
                                        $bill['base_price'] = $request->base_price;
                                        $bill['distance_cost'] = $request->distance_cost;
                                        $bill['time_cost'] = $request->time_cost;
                                        $bill['total'] = $request->total;
                                        $bill['is_paid'] = $request->is_paid;
                                    }

                                    $response_array = array(
                                        'success' => true,
                                        'request_id' => $request_id,
                                        'status' => $request->status,
                                        'confirmed_walker' => $request->confirmed_walker,
                                        'is_walker_started' => $request->is_walker_started,
                                        'is_walker_arrived' => $request->is_walker_arrived,
                                        'is_walk_started' => $request->is_started,
                                        'is_completed' => $request->is_completed,
                                        'is_walker_rated' => $request->is_walker_rated,
                                        'walker' => $walker_data,
                                        'payment_mode' => $request->payment_data,
                                        'bill' => $bill,
                                    );
                                    /* $driver = Keywords::where('id', 1)->first();

                                      $title = '' . $driver->keyword . ' has arrived at your place'; */
                                    $id = $request->owner_id;
                                    $user_type = 0;
                                    $title = $helper->transl('walker_arrived_push', $id, $user_type);

                                    $message = $response_array;
                                                                    
                                    //$helper->send_walker_arrived_notification($request->owner_id, "owner", "driver_arrived", $message);

                                    $title = "Trip Status";
                                    $body = "Rider has arrived.";
                                $helper->send_trip_notifications($request->owner_id, "owner", "driver_arrived",null,$message,$title,$body);
                                    $response_array = array('success' => true);
                                    $response_code = 200;
                                } else {
                                    $response_array = array('success' => false, 'error' => 34, 'error_messages' => array(34), 'error_code' => 413);
                                    $response_code = 200;
                                }
                            } else {
                                /* $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $driver->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $driver->keyword . ' ID'), 'error_code' => 407); */
                                $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'error_code' => 407);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $response_array = array('success' => false, 'error' => '' . $driver->keyword . ' ID not Found', 'error_messages' => array('' . $driver->keyword . ' ID not Found'), 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

   

    // walk started
    public function request_walk_started() {
        $helper = new Helper();
        if (Request::isMethod('post')) {
            $request_id = Input::get('request_id');
            $token = Input::get('token');
            $walker_id = Input::get('id');
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');
            if (Input::has('bearing')) {
                $angle = Input::get('bearing');
            }

            $validator = Validator::make(
                            array(
                        'request_id' => $request_id,
                        'token' => $token,
                        'walker_id' => $walker_id,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                            ), array(
                        'request_id' => 'required|integer',
                        'token' => 'required',
                        'walker_id' => 'required|integer',
                        'latitude' => 'required',
                        'longitude' => 'required',
                            ), array(
                        'request_id.required' => 19,
                        'token.required' => 24,
                        'walker_id.required' => 20,
                        'latitude.required' => 28,
                        'longitude.required' => 28,
                            )
            );

            /* $var = Keywords::where('id', 1)->first(); */

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $helper = new Helper();
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    $helper = new Helper();
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        // Do necessary operations
                        if ($request = Requests::find($request_id)) {
                            if ($request->confirmed_walker == $walker_id) {

                                if ($request->is_walker_arrived == 1) {
                                    $request->is_started = 1;
                                    $request->save();

                                    $location = $helper->get_location($latitude, $longitude);
                                    $latitude = $location['lat'];
                                    $longitude = $location['long'];
                                    if (!isset($angle)) {
                                        $angle = $helper->get_angle($walker_data->latitude, $walker_data->longitude, $latitude, $longitude);
                                    }
                                    $walk_location = new WalkLocation;
                                    $walk_location->latitude = $latitude;
                                    $walk_location->longitude = $longitude;
                                    $walk_location->request_id = $request_id;
                                    $walk_location->bearing = $angle;
                                    $walk_location->save();

                                    // Send Notification
                                    $walker = Walker::find($request->confirmed_walker);
                                    $walker->old_latitude = $walker->latitude;
                                    $walker->old_longitude = $walker->longitude;
                                    $walker->latitude = $latitude;
                                    $walker->longitude = $longitude;
                                    $walker->bearing = $angle;
                                    $walker->save();

                                    $walker_data = array();
                                    $walker_data['first_name'] = $walker->first_name;
                                    $walker_data['last_name'] = $walker->last_name;
                                    $walker_data['phone'] = $walker->phone;
                                    $walker_data['bio'] = $walker->bio;
                                    $walker_data['picture'] = $walker->picture;
                                    $walker_data['latitude'] = $walker->latitude;
                                    $walker_data['longitude'] = $walker->longitude;
                                    $walker_data['type'] = $walker->type;
                                    $walker_data['rating'] = $walker->rate;
                                    $walker_data['num_rating'] = $walker->rate_count;
                                    $walker_data['car_model'] = $walker->car_model;
                                    $walker_data['car_number'] = $walker->car_number;
                                    /* $walker_data['rating'] = DB::table('review_walker')->where('walker_id', '=', $walker->id)->avg('rating') ? : 0;
                                      $walker_data['num_rating'] = DB::table('review_walker')->where('walker_id', '=', $walker->id)->count(); */

                                    $settings = Settings::where('key', 'default_distance_unit')->first();
                                    $unit = $settings->value;
                                    if ($unit == 0) {
                                        $unit_set = 'kms';
                                    } elseif ($unit == 1) {
                                        $unit_set = 'miles';
                                    }
                                    $bill = array();
                                    if ($request->is_completed == 1) {
                                        $bill['distance'] = (string) $helper->convert($request->distance, $unit);
                                        $bill['unit'] = $unit_set;
                                        $bill['time'] = $request->time;
                                        $bill['base_price'] = $request->base_price;
                                        $bill['distance_cost'] = $request->distance_cost;
                                        $bill['time_cost'] = $request->time_cost;
                                        $bill['total'] = $request->total;
                                        $bill['is_paid'] = $request->is_paid;
                                    }

                                    $response_array = array(
                                        'success' => true,
                                        'request_id' => $request_id,
                                        'status' => $request->status,
                                        'confirmed_walker' => $request->confirmed_walker,
                                        'is_walker_started' => $request->is_walker_started,
                                        'is_walker_arrived' => $request->is_walker_arrived,
                                        'is_walk_started' => $request->is_started,
                                        'is_completed' => $request->is_completed,
                                        'is_walker_rated' => $request->is_walker_rated,
                                        'walker' => $walker_data,
                                        'payment_mode' => $request->payment_data,
                                        'bill' => $bill,
                                    );
                                    /* $var = Keywords::where('id', 4)->first();
                                      $title = 'Your ' . $var->keyword . ' has been started'; */
                                    $user_type = 0;
                                    $id = $request->owner_id;
                                    $title = $helper->transl('walk_started_push', $id, $user_type);

                                    $message = $response_array;
                                                                    
                                    //$this->send_walk_started_notification($request->owner_id, "owner", $title, $message);

                                    $t = "Trip Status";
                                    $body = "Trip started.";
                                    $helper->send_trip_notifications($request->owner_id, "owner", $title, $message,null,$t,$body);
                                    $response_array = array('success' => true);
                                    $response_code = 200;
                                } else {
                                    /* $response_array = array('success' => false, 'error' => '' . $var->keyword . ' not yet arrived', 'error_messages' => array('' . $var->keyword . ' not yet arrived'), 'error_code' => 413); */
                                    $response_array = array('success' => false, 'error' => 35, 'error_messages' => array(35), 'error_code' => 413);
                                    $response_code = 200;
                                }
                            } else {
                                /* $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $var->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $var->keyword . ' ID'), 'error_code' => 407); */
                                $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'error_code' => 407);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    
    // walk completed
    public function request_walk_completed() {
        $helper = new Helper();
        if (Request::isMethod('post')) {
            $request_id = Input::get('request_id');
            $token = Input::get('token');
            $walker_id = Input::get('id');
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');
            $distance = Input::get('distance');
            $time = Input::get('time');
            if (Input::has('bearing')) {
                    $angle = Input::get('bearing');
            }
            $dest_address = Input::get('dest_address');
            $validator = Validator::make(
                array(
                    'request_id' => $request_id,
                    'token' => $token,
                    'walker_id' => $walker_id,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'distance' => $distance,
                    'dest_address' => $dest_address,
                    ), array(
                    'request_id' => 'required|integer',
                    'token' => 'required',
                    'walker_id' => 'required|integer',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'distance' => 'required',
                    'dest_address' => 'required',
                            /* 'time' => 'required', */
                    ), array(
                    'request_id.required' => 19,
                    'token.required' => 24,
                    'walker_id.required' => 20,
                    'latitude.required' => 28,
                    'longitude.required' => 28,
                    'distance.required' => 36,
                    'dest_address.required' => 41,
                )
            );
            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $cash_card_user = "";
                $payment_type = "";
                $is_event_promo = $event_owner_id = 0;
                $walker_payment_remaining = $provider_refund_remaining = 0;
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        $providertype = ProviderType::where('id', $walker_data->type)->first();
                        // Do necessary operations 
                        if ($request = Requests::find($request_id)) {
                            //$time = $request->time;
                            if ($request->confirmed_walker == $walker_id) {

                                if ($request->is_started == 1) {
                                    $settings = Settings::where('key', 'default_charging_method_for_users')->first();
                                    $pricing_type = $settings->value;
                                    $settings = Settings::where('key', 'default_distance_unit')->first();
                                    $unit = $settings->value;

                                    //Log::info('distance = ' . print_r($distance, true));

                                    $reqserv = RequestServices::where('request_id', $request_id)->get();
                                    $actual_total = 0;
                                    $price_per_unit_distance = 0;
                                    $price_per_unit_time = 0;
                                    $base_price = 0;
                                    
                                    $free_promo = Settings::where('key', 'free_trip_promo')->first()->value;
                                    $free_amount = Settings::where('key', 'free_promo_amount')->first()->value;
                                    $free_distance = Settings::where('key', 'free_promo_distance')->first()->value;
                                    $free_promo_total = 0;

                                    foreach ($reqserv as $rse) {
                                        //Log::info('type = ' . print_r($rse->type, true));
                                        $protype = ProviderType::where('id', $rse->type)->first();
                                        $pt = ProviderServices::where('provider_id', $walker_id)->where('type', $rse->type)->first();
                                        if ($pt->base_price == 0) {
                                            $base_price = $providertype->base_price;
                                            $rse->base_price = $base_price;
                                        } else {
                                            $base_price = $pt->base_price;
                                            $rse->base_price = $base_price;
                                        }

                                        $is_multiple_service = Settings::where('key','allow_multiple_service')->first();
                                        if ($pt->price_per_unit_distance == 0) {
                                            if ($distance <= $providertype->base_distance) {
                                                $price_per_unit_distance = 0;
                                            }else{
                                                $price_per_unit_distance = $providertype->price_per_unit_distance * ($distance - $providertype->base_distance);
                                            }
                                            $rse->distance_cost = $price_per_unit_distance;
                                        } else {
                                            if ($distance <= $providertype->base_distance) {
                                                $price_per_unit_distance = 0;
                                            } else {
                                                $price_per_unit_distance = $pt->price_per_unit_distance * ($distance - $providertype->base_distance);
                                            }
                                            $rse->distance_cost = $price_per_unit_distance;
                                        }
                                        if ($pt->price_per_unit_time == 0) {
                                            if ($distance <= $providertype->base_distance) {
                                                $price_per_unit_time = 0;
                                            } else {
                                                $price_per_unit_time = $providertype->price_per_unit_time * $time;
                                            }
                                            $rse->time_cost = $price_per_unit_time;
                                        } else {
                                            if ($distance <= $providertype->base_distance) {
                                                $price_per_unit_time = 0;
                                            } else {
                                                $price_per_unit_time = $pt->price_per_unit_time * $time;
                                            }
                                            $rse->time_cost = $price_per_unit_time;
                                        }
                                        $actual_total = $base_price + $price_per_unit_distance + $price_per_unit_time;
                                        if ($free_promo == 1 && $distance > 0.5 && $time > 2) {
                                            if($distance > $free_distance){
                                                $free_promo_total = $free_amount;
                                                $actual_total =  $actual_total - $free_amount;
                                            }else{
                                                $free_promo_total = $actual_total;
                                                $actual_total = 0;
                                            }
                                        }else{
                                            $free_promo_total = 0;
                                        }
                                        $rse->total = $actual_total;
                                        $rse->save();
                                    }
                                    pay_fail:
                                    $rs = RequestServices::where('request_id', $request_id)->get();
                                    $settings = Settings::where('key', 'provider_amount_for_each_request_in_percentage')->first();
                                    $provider_percentage = $settings->value;
                                    $total = 0;

                                    foreach ($rs as $key) {
                                        //Log::info('total = ' . print_r($key->total, true));
                                        $total = $total + $key->total;
                                    }

                                    $total = round($total, -2);
                                    $request = Requests::find($request_id);
                                    $request->is_completed = 1;
                                    $request->distance = $distance;
                                    $request->time = $time;
                                    $request->dest_address = $dest_address;
                                    $request->security_key = NULL;
                                    $request->total = $total;
                                    if ($request->promo_id) {
                                        if ($pcode = PromoCodes::where('id', $request->promo_id)->first()) {
                                            if ($pcode->is_event) {
                                                $is_event_promo = 1;
                                                $event_data = UserEvents::where('id', '=', $pcode->event_id)->first();
                                                $event_owner_id = $event_data->owner_id;
                                            }
                                        }
                                    }

                                    $owner_data = Owner::where('id', $request->owner_id)->first();
                                    /* GET REFERRAL & PROMO INFO */
                                    $prom_act = $prom_for_card = $prom_for_cash = $ref_act = $ref_for_card = $ref_for_cash = $ref_total = $promo_total = 0;
                                    $settings = Settings::where('key', 'promotional_code_activation')->first();
                                    $prom_act = $settings->value;

                                    $settings = Settings::where('key', 'referral_code_activation')->first();
                                    $ref_act = $settings->value;
                                    /* GET REFERRAL & PROMO INFO END */
                                    $cash_card_user = $request->payment_mode;
                                    if ($request->payment_mode == 0) {
                                        $walker_payment_remaining = (($total * $provider_percentage) / 100);
                                        if ($prom_act == 1 || $is_event_promo == 1) {
                                            $settings = Settings::where('key', 'get_promotional_profit_on_card_payment')->first();
                                            $prom_for_card = $settings->value;
                                            if ($prom_for_card == 1 || $is_event_promo == 1) {
                                                if ($total > 0) {
                                                    if ($pcode = PromoCodes::where('id', $request->promo_id)->first()){
                                                        if ($pcode->type == 1) {
                                                            $promo_total = $total * (($pcode->value) / 100);
                                                            $total = $total - $promo_total;
                                                            if ($total <= 0) {
                                                                $total = 0;
                                                            }
                                                        } else {
                                                            $promo_total = $pcode->value;
                                                            $total = $total - $promo_total;
                                                            if ($total <= 0) {
                                                                $total = 0;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if ($ref_act) {
                                            $settings = Settings::where('key', 'get_referral_profit_on_card_payment')->first();
                                            $ref_for_card = $settings->value;

                                            if ($ref_for_card) {
                                                // charge client
                                                $ledger = Ledger::where('owner_id', $request->owner_id)->first();
                                                if ($ledger) {
                                                    $balance = $ledger->amount_earned - $ledger->amount_spent;
                                                    //Log::info('ledger balance = ' . print_r($balance, true));
                                                    if ($balance > 0) {
                                                        if ($total > 0) {
                                                            if ($total > $balance) {
                                                                $ref_total = $balance;
                                                                $ledger_temp = Ledger::find($ledger->id);
                                                                $ledger_temp->amount_spent = $ledger_temp->amount_spent + $balance;
                                                                $ledger_temp->save();
                                                                $total = $total - $balance;
                                                            } else {
                                                                $ref_total = $total;
                                                                $ledger_temp = Ledger::find($ledger->id);
                                                                $ledger_temp->amount_spent = $ledger_temp->amount_spent + $total;
                                                                $ledger_temp->save();
                                                                $total = 0;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else if ($request->payment_mode == 1) {
                                        $walker_payment_remaining = 0;
                                        $provider_refund_remaining = (($total * (100 - $provider_percentage)) / 100);
                                        if ($prom_act == 1 || $is_event_promo == 1) {
                                            $settings = Settings::where('key', 'get_promotional_profit_on_cash_payment')->first();
                                            $prom_for_cash = $settings->value;
                                            if ($prom_for_cash == 1 || $is_event_promo == 1) {
                                                if ($total > 0) {
                                                    if ($pcode = PromoCodes::where('id', $request->promo_id)->first()) {
                                                        if ($pcode->type == 1) {
                                                            $promo_total = $total * (($pcode->value) / 100);
                                                            $total = $total - $promo_total;
                                                            if ($total <= 0) {
                                                                $total = 0;
                                                            }
                                                        } else {
                                                            $promo_total = $pcode->value;
                                                            $total = $total - $promo_total;
                                                            if ($total <= 0) {
                                                                $total = 0;
                                                            }
                                                        }
                                                        $walker_payment_remaining = $walker_payment_remaining + $promo_total;
                                                    }
                                                }
                                            }
                                        }
                                        if ($ref_act) {
                                            $settings = Settings::where('key', 'get_referral_profit_on_cash_payment')->first();
                                            $ref_for_cash = $settings->value;

                                            if ($ref_for_cash) {
                                                // charge client
                                                $ledger = Ledger::where('owner_id', $request->owner_id)->first();
                                                if ($ledger) {
                                                    $balance = $ledger->amount_earned - $ledger->amount_spent;
                                                    //Log::info('ledger balance = ' . print_r($balance, true));
                                                    if ($balance > 0) {
                                                        if ($total > 0) {
                                                            if ($total > $balance) {
                                                                $ref_total = $balance;
                                                                $ledger_temp = Ledger::find($ledger->id);
                                                                $walker_payment_remaining = $walker_payment_remaining + $balance;
                                                                $ledger_temp->amount_spent = $ledger_temp->amount_spent + $balance;
                                                                $ledger_temp->save();
                                                                $total = $total - $balance;
                                                            } else {
                                                                $ref_total = $total;
                                                                $ledger_temp = Ledger::find($ledger->id);
                                                                $walker_payment_remaining = $walker_payment_remaining + $total;
                                                                $ledger_temp->amount_spent = $ledger_temp->amount_spent + $total;
                                                                $ledger_temp->save();
                                                                $total = 0;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $cod_sett = Settings::where('key', 'cod')->first();
                                    $allow_cod = $cod_sett->value;
                                    if ($request->payment_mode == 1) {
                                        $request->is_paid = 1;
                                        $payment_type = 'Payment By cash';
                                        //Log::info('allow_cod');
                                    } else if ($request->payment_mode == 2) {
                                        // Mobile
                                        $payment_type = 'Payment By mobile money';
                                        $status = $helper->process_mobile_money_transaction($request->payment_msisdn, $total);

                                        if($status == 0){
                                            $request->is_paid = 1;
                                        }else{
                                            $request->is_paid = 0;
                                            $change_to_cash = Requests::find($request_id);
                                            $request->payment_mode = $change_to_cash->payment_mode = 1;
                                            $change_to_cash->save();

                                            /* Client Side Push */
                                            $id = $request->owner_id;
                                            $user_type = 0;
                                            $title = $helper->transl('decline_card_pay_provider', $id, $user_type);
                                            $response_array = array(
                                                'request_id' => $request_id, 'success' => true, 'message' => $title,);
                                            $message = $response_array;
                                            
                            //send_notifications($request->owner_id, "owner", $title, $message);

                            //$helper->send_trip_notifications($id,"owner","change_to_cash", $message, $is_imp = NULL,$title,$body);


                                            /* Client Side Push END */
                                            /* Driver Side Push */
                                            $user_type = 1;
                                            $id = $walker_id;

                                            $title = $helper->transl('collect_cash', $id, $user_type);
                                            $response_array = array(
                                                'request_id' => $request_id,
                                                'success' => true,
                                                'message' => $title,
                                            );
                                            $message = $response_array;
                                            //send_notifications($walker_id, "walker", $title, $message);
                                            /* Driver Side Push END */
                                            goto pay_fail;
                                        }
                                        //Log::info('mobile money payment');
                                    } else {
                                        //Log::info('normal payment. Stored cards');
                                        if ($total == 0) {
                                            $request->is_paid = 1;
                                        } else {
                                            $payment_data = Payment::where('owner_id', $request->owner_id)->where('is_default', 1)->first();
                                            if (!$payment_data)
                                                $payment_data = Payment::where('owner_id', $request->owner_id)->first();

                                            if ($payment_data) {
                                                $customer_id = $payment_data->customer_id;

                                                $setransfer = Settings::where('key', 'transfer')->first();
                                                $transfer_allow = $setransfer->value;
                                                if (\Config::get('app.default_payment') == 'stripe') {
                                                    //dd($customer_id);
                                                    Stripe::setApiKey(\Config::get('app.stripe_secret_key'));
                                                    try {
                                                        if ($is_event_promo) {
                                                            $event_owner_data = Owner::where('id', '=', $event_owner_id)->first();
                                                            $event_payment_data = Payment::where('owner_id', $event_owner_id)->where('is_default', 1)->first();
                                                            if (!$event_payment_data) {
                                                                $event_payment_data = Payment::where('owner_id', $event_owner_id)->first();
                                                            }
                                                            if ($event_payment_data) {
                                                                $event_customer_id = $event_payment_data->customer_id;
                                                                $charge_event = Stripe_Charge::create(array(
                                                                            "amount" => ($promo_total) * 100,
                                                                            "currency" => "usd",
                                                                            "customer" => $event_customer_id)
                                                                );
                                                                if ($charge_event->paid) {
                                                                    
                                                                } else {
                                                                    $total = $total + $promo_total;
                                                                    $promo_total = 0;
                                                                }
                                                            } else {
                                                                $total = $total + $promo_total;
                                                                $promo_total = 0;
                                                            }
                                                        }
                                                        $charge = Stripe_Charge::create(array(
                                                                    "amount" => ($total) * 100,
                                                                    "currency" => "usd",
                                                                    "customer" => $customer_id)
                                                        );
                                                        if ($charge->paid) {
                                                            $request->is_paid = 1;
                                                            $payment_type = "Creditcard Card Payment Successfully";
                                                        } else {
                                                            $request->is_paid = 0;
                                                            $payment_type = "Creditcard Card Payment Fail";
                                                            $ledger = Ledger:: where('owner_id', $request->owner_id)->first();
                                                            if ($ledger) {
                                                                $ledger_temp = Ledger::find($ledger->id);
                                                                $ledger_temp->amount_spent = $ledger_temp->amount_spent - $ref_total;
                                                                $ledger_temp->save();
                                                            }
                                                            $change_to_cash = Requests::find($request_id);
                                                            $request->payment_mode = $change_to_cash->payment_mode = 1;
                                                            $change_to_cash->save();

                                                            /* Client Side Push */
                                                            $id = $request->owner_id;
                                                            $user_type = 0;
                                                            $title = transl('decline_card_pay_provider', $id, $user_type);
                                                            $response_array = array(
                                                                'request_id' => $request_id, 'success' => true, 'message' => $title,);
                                                            $message = $response_array;
                                                            //send_notifications($request->owner_id, "owner", $title, $message);
                                                            /* Client Side Push END */
                                                            /* Driver Side Push */
                                                            $user_type = 1;
                                                            $id = $walker_id;

                                                            $title = transl('collect_cash', $id, $user_type);
                                                            $response_array = array(
                                                                'request_id' => $request_id,
                                                                'success' => true,
                                                                'message' => $title,
                                                            );
                                                            $message = $response_array;
                                                            //send_notifications($walker_id, "walker", $title, $message);
                                                            /* Driver Side Push END */
                                                            goto pay_fail;
                                                        }
                                                    } catch (Stripe_RateLimitError $e) {
                                                        $request->is_paid = 0;
                                                        // Invalid parameters were supplied to Stripe's API
                                                        $ownr = Owner::find($request->owner_id);
                                                        $ownr->debt = $total;
                                                        $ownr->save();
                                                        $response_array = array('error' => $e->getMessage());
                                                        $response_code = 200;
                                                        $response = Response::json($response_array, $response_code);
                                                        return $response;
                                                    } catch (Stripe_InvalidRequestError $e) {
                                                        $request->is_paid = 0;
                                                        // Invalid parameters were supplied to Stripe's API
                                                        $ownr = Owner::find($request->owner_id);
                                                        $ownr->debt = $total;
                                                        $ownr->save();
                                                        $response_array = array('error' => $e->getMessage());
                                                        $response_code = 200;
                                                        $response = Response::json($response_array, $response_code);
                                                        return $response;
                                                    } catch (Stripe_AuthenticationError $e) {
                                                        $request->is_paid = 0;
                                                        // Invalid parameters were supplied to Stripe's API
                                                        $ownr = Owner::find($request->owner_id);
                                                        $ownr->debt = $total;
                                                        $ownr->save();
                                                        $response_array = array('error' => $e->getMessage());
                                                        $response_code = 200;
                                                        $response = Response::json($response_array, $response_code);
                                                        return $response;
                                                    } catch (Stripe_CardError $e) {
                                                        $request->is_paid = 0;
                                                        // Invalid parameters were supplied to Stripe's API
                                                        $ownr = Owner::find($request->owner_id);
                                                        $ownr->debt = $total;
                                                        $ownr->save();
                                                        $response_array = array('error' => $e->getMessage());
                                                        $response_code = 200;
                                                        $response = Response::json($response_array, $response_code);
                                                        return $response;
                                                    } catch (Stripe_ApiError $e) {
                                                        $request->is_paid = 0;
                                                        // Invalid parameters were supplied to Stripe's API
                                                        $ownr = Owner::find($request->owner_id);
                                                        $ownr->debt = $total;
                                                        $ownr->save();
                                                        $response_array = array('error' => $e->getMessage());
                                                        $response_code = 200;
                                                        $response = Response::json($response_array, $response_code);
                                                        return $response;
                                                    }
                                                    $settng = Settings::where('key', 'service_fee')->first();
                                                    if ($transfer_allow == 1 && $walker_data->merchant_id != "" && \Config::get('app.generic_keywords.Currency') == '$') {

                                                        /* $transfer = Stripe_Transfer::create(array(
                                                          "amount" => ($total - ($settng->value * $total / 100)) * 100, // amount in cents
                                                          "currency" => "usd",
                                                          "recipient" => $walker_data->merchant_id)
                                                          ); */
                                                        $request->transfer_amount = ($total - $settng->value * $total / 100);
                                                    }
                                                } else {
                                                    try {
                                                        Braintree_Configuration::environment(\Config::get('app.braintree_environment'));
                                                        Braintree_Configuration::merchantId(\Config::get('app.braintree_merchant_id'));
                                                        Braintree_Configuration::publicKey(\Config::get('app.braintree_public_key'));
                                                        Braintree_Configuration::privateKey(\Config::get('app.braintree_private_key'));
                                                        if ($is_event_promo) {
                                                            $event_owner_data = Owner::where('id', '=', $event_owner_id)->first();
                                                            $event_payment_data = Payment::where('owner_id', $event_owner_id)->where('is_default', 1)->first();
                                                            if (!$event_payment_data) {
                                                                $event_payment_data = Payment::where('owner_id', $event_owner_id)->first();
                                                            }
                                                            if ($event_payment_data) {
                                                                $event_customer_id = $event_payment_data->customer_id;
                                                                $charge_event = Braintree_Transaction::sale(array(
                                                                            'amount' => $total,
                                                                            /* 'paymentMethodNonce' => $event_customer_id, */
                                                                            'customerId' => $event_customer_id,
                                                                ));
                                                                if ($charge_event->success) {
                                                                    
                                                                } else {
                                                                    $total = $total + $promo_total;
                                                                    $promo_total = 0;
                                                                }
                                                            } else {
                                                                $total = $total + $promo_total;
                                                                $promo_total = 0;
                                                            }
                                                        }
                                                        if ($transfer_allow == 1) {
                                                            $result = Braintree_Transaction::sale(array(
                                                                        'amount' => $total,
                                                                        /* 'paymentMethodNonce' => $customer_id, */
                                                                        'customerId' => $customer_id,
                                                            ));
                                                            /* $sevisett = Settings::where('key', 'service_fee')->first();
                                                              $service_fee = $sevisett->value * $total / 100;
                                                              $result = Braintree_Transaction::sale(array(
                                                              'amount' => $total - $service_fee,
                                                             *//* 'paymentMethodNonce' => $customer_id, *//*
                                                              'customerId' => $customer_id,
                                                              'merchantAccountId' => $walker_data->merchant_id,
                                                              'options' => array(
                                                              'submitForSettlement' => true,
                                                              'holdInEscrow' => true,
                                                              ),
                                                              'serviceFeeAmount' => $service_fee
                                                              )); */
                                                        } else {
                                                            $result = Braintree_Transaction::sale(array(
                                                                        'amount' => $total,
                                                                        /* 'paymentMethodNonce' => $customer_id, */
                                                                        'customerId' => $customer_id,
                                                            ));
                                                        }

                                                        if ($result->success) {
                                                            $request->is_paid = 1;
                                                            $payment_type = "Creditcard Card Payment Successfully";
                                                        } else {
                                                            $request->is_paid = 0;
                                                            $payment_type = "Creditcard Card Payment Fail";
                                                            $ledger = Ledger::where('owner_id', $request->owner_id)->first();
                                                            if ($ledger) {
                                                                $ledger_temp = Ledger::find($ledger->id);
                                                                $ledger_temp->amount_spent = $ledger_temp->amount_spent - $ref_total;
                                                                $ledger_temp->save();
                                                            }
                                                            $change_to_cash = Requests::find($request_id);
                                                            $request->payment_mode = $change_to_cash->payment_mode = 1;
                                                            $change_to_cash->save();
                                                            /* Client Side Push */

                                                            $id = $request->owner_id;
                                                            $user_type = 0;
                                                            $title = transl('decline_card_pay_provider', $id, $user_type);
                                                            $response_array = array(
                                                                'success' => true,
                                                                'request_id' => $request_id,
                                                                'message' => $title,
                                                                'payment_type' => $change_to_cash->payment_mode,
                                                            );
                                                            $message = $response_array;
                                                            //send_notifications($request->owner_id, "owner", $title, $message);
                                                            /* Client Side Push END */
                                                            /* Driver Side Push */
                                                            $id = $walker_id;
                                                            $user_type = 1;
                                                            $title = transl('collect_cash', $id, $user_type);
                                                            $response_array = array('success' => true,
                                                                'message' => $title,
                                                                'request_id' => $request_id,
                                                                'payment_type' => $change_to_cash->payment_mode,
                                                            );
                                                            $message = $response_array;
                                                            //send_notifications($walker_id, "walker", $title, $message);
                                                            /* Driver Side Push END */
                                                            goto pay_fail;
                                                        }
                                                    } catch (Exception $e) {
                                                        $response_array = array('success' => false, 'error' => $e, 'error_messages' => array(12), 'error_code' => 405);
                                                        $response_code = 200;
                                                        $response = Response::json($response_array, $response_code);
                                                        return $response;
                                                    }
                                                }
                                                $request->card_payment = $total;
                                                $request->ledger_payment = $request->total - $total;
                                            }
                                        }
                                    }
                                    $request->card_payment = $total;
                                    $request->payment_remaining = $walker_payment_remaining;
                                    $request->refund_remaining = $provider_refund_remaining;
                                    $request->ledger_payment = $ref_total;
                                    $request->promo_payment = $promo_total;
                                    $request->payment_mode = $cash_card_user;
                                    $request->save();

                                    /*if ($request->is_paid == 1) {

                                        $owner = Owner::find($request->owner_id);
                                        $settings = Settings::where('key', 'sms_payment_generated')->first();
                                        $pattern = $settings->value;
                                        $pattern = str_replace('%user%', $owner->first_name . " " . $owner->last_name, $pattern);
                                        $pattern = str_replace('%id%', $request->id, $pattern);
                                        $pattern = str_replace('%user_mobile%', $owner->phone, $pattern);
                                        sms_notification(1, 'admin', $pattern);
                                    }*/

                                    $walker = Walker::find($walker_id);
                                    $walker->is_available = 1;
                                    $helper = new Helper();
                                    $location = $helper->get_location($latitude, $longitude);
                                    $latitude = $location['lat'];
                                    $longitude = $location['long'];
                                    if (!isset($angle)) {
                                        $angle = $helper->get_angle($walker->latitude, $walker->longitude, $latitude, $longitude);
                                    }
                                    $walker->payment_remaining = $walker->payment_remaining + $walker_payment_remaining;
                                    $walker->refund_remaining = $walker->refund_remaining + $provider_refund_remaining;
                                    $walker->old_latitude = $walker->latitude;
                                    $walker->old_longitude = $walker->longitude;
                                    $walker->latitude = $latitude;
                                    $walker->longitude = $longitude;
                                    $walker->bearing = $angle;
                                    $walker->save();
                                    //Log::info('distance walk location = ' . print_r($distance, true));
                                    $walk_location = new WalkLocation;
                                    $walk_location->latitude = $latitude;
                                    $walk_location->longitude = $longitude;
                                    $walk_location->request_id = $request_id;
                                    $walk_location->distance = $distance;
                                    $walk_location->bearing = $angle;
                                    $walk_location->save();

                                    // Send Notification
                                    $walker = Walker::find($request->confirmed_walker);
                                    $walker_data = array();
                                    $walker_data['first_name'] = $walker->first_name;
                                    $walker_data['last_name'] = $walker->last_name;
                                    $walker_data['phone'] = $walker->phone;
                                    $walker_data['bio'] = $walker->bio;
                                    $walker_data['picture'] = $walker->picture;
                                    $walker_data['latitude'] = $walker->latitude;
                                    $walker_data['longitude'] = $walker->longitude;
                                    $walker_data['type'] = $walker->type;
                                    $walker_data['rating'] = $walker->rate;
                                    $walker_data['num_rating'] = $walker->rate_count;
                                    $walker_data['car_model'] = $walker->car_model;
                                    $walker_data['car_number'] = $walker->car_number;

                                    $pt_new = ProviderType::where('id', $walker->type)->first();
                                    $ps_new = ProviderServices::where('id', $walker->type)->first();
                                    $requestserv = RequestServices::where('request_id', $request->id)->first();
                                    $bill = array();
                                    /* $currency_selected = Keywords::find(5); */
                                    if ($request->is_completed == 1) {
                                        $settings = Settings::where('key', 'default_distance_unit')->first();
                                        $unit = $settings->value;
                                        $bill['payment_mode'] = $request->payment_mode;
                                        $bill['distance'] = (string) $distance;
                                        if ($unit == 0) {
                                            $unit_set = 'kms';
                                        } elseif ($unit == 1) {
                                            $unit_set = 'miles';
                                        }
                                        $bill['unit'] = $unit_set;
                                        $bill['time'] = floatval(sprintf($request->time, 2));
                                        if ($requestserv->base_price != 0) {
                                            $bill['base_price'] = $helper->currency_converted($requestserv->base_price);
                                            $bill['distance_cost'] = $helper->currency_converted($requestserv->distance_cost);
                                            $bill['time_cost'] = $helper->currency_converted(floatval(sprintf($requestserv->time_cost, 2)));
                                            //$distance_cost = $distance * $pt_new->price_per_unit_distance;
                                            //$bill['distance_cost'] = $helper->currency_converted($distance_cost);
                                            //$time_cost = floatval(sprintf($request->time, 2)) * 200;
                                            //$bill['time_cost'] = $helper->currency_converted(floatval(sprintf($time_cost, 2)));
                                        } else {
                                            $bill['base_price'] = $helper->currency_converted($providertype->base_price);
                                            $bill['distance_cost'] = $helper->currency_converted($providertype->price_per_unit_distance);
                                            $bill['time_cost'] = $helper->currency_converted(floatval(sprintf($providertype->price_per_unit_time, 2)));
                                        }

                                        if ($pt_new->base_price != 0) {
                                            $bill['price_per_unit_distance'] = $helper->currency_converted($pt_new->price_per_unit_distance);
                                            $bill['price_per_unit_time'] = $helper->currency_converted($pt_new->price_per_unit_time);
                                        } else {
                                            $bill['price_per_unit_distance'] = $helper->currency_converted($ps_new->price_per_unit_distance);
                                            $bill['price_per_unit_time'] = $helper->currency_converted($ps_new->price_per_unit_time);
                                        }

                                        $admins = User::first();
                                        $bill['walker']['email'] = $walker->email;
                                        $bill['admin']['email'] = $admins->username;
                                        if ($request->transfer_amount != 0) {
                                            $bill['walker']['amount'] = $helper->currency_converted($request->total - $request->transfer_amount);
                                            $bill['admin']['amount'] = $helper->currency_converted($request->transfer_amount);
                                        } else {
                                            $bill['walker']['amount'] = $helper->currency_converted($request->transfer_amount);
                                            $bill['admin']['amount'] = $helper->currency_converted($request->total - $request->transfer_amount);
                                        }
                                        /* $bill['currency'] = $currency_selected->keyword; */
                                        $bill['currency'] = \Config::get('app.generic_keywords.Currency');
                                        $bill['actual_total'] = $helper->currency_converted($request->total);
                                        /* $bill['total'] = currency_converted($request->total); */
                                        $bill['is_paid'] = $request->is_paid;
                                        $bill['promo_discount'] = $helper->currency_converted($promo_total);

                                        $bill['main_total'] = $helper->currency_converted($request->total);
                                        $tot = $helper->currency_converted($request->total - $request->ledger_payment - $request->promo_payment);
                                        if ($tot <= 0) {
                                            $tot = 0;
                                        }
                                        $bill['total'] = $tot;
                                        $bill['referral_bonus'] = $helper->currency_converted($request->ledger_payment);
                                        $bill['promo_bonus'] = $helper->currency_converted($request->promo_payment);
                                        $bill['payment_type'] = $request->payment_mode;
                                        $bill['is_paid'] = $request->is_paid;
                                    }

                                    $rservc = RequestServices::where('request_id', $request->id)->get();
                                    $typs = array();
                                    $typi = array();
                                    $typp = array();
                                    foreach ($rservc as $typ) {
                                        $typ1 = ProviderType::where('id', $typ->type)->first();
                                        $typ_price = ProviderServices::where('provider_id', $request->confirmed_walker)->where('type', $typ->type)->first();

                                        if ($typ_price->base_price > 0) {
                                            $typp1 = 0.00;
                                            $typp1 = $typ_price->base_price;
                                        } elseif ($typ_price->price_per_unit_distance > 0) {
                                            $typp1 = 0.00;
                                            foreach ($rservc as $key) {
                                                $typp1 = $typp1 + $key->distance_cost;
                                            }
                                        } else
                                            $typp1 = 0.00;

                                        $typs['name'] = $typ1->name;
                                        // $typs['icon']=$typ1->icon;
                                        $typs['price'] = $typ1->base_price;

                                        array_push($typi, $typs);
                                    }
                                    $bill['type'] = $typi;
                                    $rserv = RequestServices::where('request_id', $request_id)->get();
                                    $typs = array();
                                    foreach ($rserv as $typ) {
                                        $typ1 = ProviderType::where('id', $typ->type)->first();
                                        array_push($typs, $typ1->name);
                                    }

                                    $response_array = array(
                                        'success' => true,
                                        'request_id' => $request_id,
                                        'status' => $request->status,
                                        'confirmed_walker' => $request->confirmed_walker,
                                        'is_walker_started' => $request->is_walker_started,
                                        'is_walker_arrived' => $request->is_walker_arrived,
                                        'is_walk_started' => $request->is_started,
                                        'is_completed' => $request->is_completed,
                                        'is_walker_rated' => $request->is_walker_rated,
                                        'walker' => $walker_data,
                                        'payment_mode' => $request->payment_mode,
                                        'bill' => $bill,
                                        'payment_option' => $request->payment_mode,
                                        'is_paid' => $request->is_paid,
                                    );
                                    $owner_data1 = array();
                                    $owner_data1['name'] = $owner_data->first_name . " " . $owner_data->last_name;
                                    $owner_data1['picture'] = $owner_data->picture;
                                    $owner_data1['phone'] = $owner_data->phone;
                                    $owner_data1['address'] = $owner_data->address;
                                    $owner_data1['bio'] = $owner_data->bio;
                                    $owner_data1['latitude'] = $request->latitude;
                                    $owner_data1['longitude'] = $request->longitude;
                                    $owner_data1['owner_dist_lat'] = $request->D_latitude;
                                    $owner_data1['owner_dist_long'] = $request->D_longitude;
                                    $owner_data1['dest_latitude'] = $request->D_latitude;
                                    $owner_data1['dest_longitude'] = $request->D_longitude;
                                    $owner_data1['payment_type'] = $request->payment_mode;
                                    $owner_data1['rating'] = $owner_data->rate;
                                    $owner_data1['num_rating'] = $owner_data->rate_count;
                                    $title = "Trip Completed";
                                    $dog1 = array();

                                    $cards = "";
                                    $cardlist = Payment::where('owner_id', $owner_data->id)->where('is_default', 1)->first();
                                    if (is_array($cardlist)){
                                        $cards = array();
                                        $default = $cardlist->is_default;
                                        if ($default == 1) {
                                            $cards['is_default_text'] = "default";
                                        } else {
                                            $cards['is_default_text'] = "not_default";
                                        }
                                        $cards['card_id'] = $cardlist->id;
                                        $cards['owner_id'] = $cardlist->owner_id;
                                        $cards['customer_id'] = $cardlist->customer_id;
                                        $cards['last_four'] = $cardlist->last_four;
                                        $cards['card_token'] = $cardlist->card_token;
                                        $cards['card_type'] = $cardlist->card_type;
                                        $cards['is_default'] = $default;
                                    }

                                    $chagre = array();
                                    $settings = Settings::where('key', 'default_distance_unit')->first();
                                    $unit = $settings->value;
                                    if ($unit == 0) {
                                        $unit_set = 'kms';
                                    } elseif ($unit == 1) {
                                        $unit_set = 'miles';
                                    }
                                    $chagre['unit'] = $unit_set;
                                    $requestserv = RequestServices::where('request_id', $request->id)->first();
                                    if ($requestserv->base_price != 0) {
                                        $chagre['base_price'] = $helper->currency_converted($requestserv->base_price);
                                        $chagre['distance_price'] = $helper->currency_converted($requestserv->distance_cost);
                                        $chagre['price_per_unit_time'] = $helper->currency_converted($requestserv->time_cost);
                                    } else {
                                        $chagre['base_price'] = $helper->currency_converted($providertype->base_price);
                                        $chagre['distance_price'] = $helper->currency_converted($providertype->price_per_unit_distance);
                                        $chagre['price_per_unit_time'] = $helper->currency_converted($providertype->price_per_unit_time);
                                    }
                                    $chagre['total'] = $helper->currency_converted($request->total);
                                    $chagre['is_paid'] = $request->is_paid;
                                    /* $var = Keywords::where('id', 4)->first(); */
                                    $id = $request->owner_id;
                                    $user_type = 0;
                                    $title = $helper->transl('walk_end_push', $id, $user_type);

                                    $message = $response_array;

                                    $t = "Trip status";
                                    $body = "Trip ended.";
                                    $helper->send_trip_notifications($request->owner_id, "owner",$title,$message,null,$t,$body);

                                    $owner = Owner::find($request->owner_id);
                                    if($free_promo == 1){
                                        if($distance >= 10){
                                            $driver_msg = 'Hello, QuickTaxi owes you '.$free_promo_total.'UGX for the first 10KM and the client pays '.$total.'UGX. REF #'.$request->id;
                                            $client_msg = 'Hello, your bill for the first 10KM was covered by QuickTaxi.';
                                        }else{
                                            $driver_msg = 'Hello, QuickTaxi owes you '.$free_promo_total.'UGX for this trip. REF #'.$request->id;
                                            $client_msg = 'Hello, your bill for this trip was covered by QuickTaxi.';
                                        }
                                        $helper->send_sms_message($walker->phone, $driver_msg);
                                        $helper->send_sms_message($owner->phone, $client_msg);
                                    }
                                    $id = $request->id;
                                    
                                    // send email
                                    $email_data = array();
                                    $email_data['name'] = $owner->first_name;
                                    $email_data['emailType'] = 'user';
                                    $email_data['base_price'] = $bill['base_price'];
                                    $email_data['distance'] = $bill['distance'];
                                    $email_data['time'] = $bill['time'];
                                    $email_data['unit'] = $bill['unit'];
                                    $email_data['total'] = $bill['total'];
                                    $email_data['payment_mode'] = $bill['payment_mode'];
                                    $email_data['actual_total'] = $helper->currency_converted($actual_total);
                                    $email_data['is_paid'] = $request->is_paid;
                                    $email_data['promo_discount'] = $helper->currency_converted($promo_total);

                                    $request_services = RequestServices::where('request_id', $request->id)->first();

                                    $locations = WalkLocation::where('request_id', $request->id)
                                            ->orderBy('id')
                                            ->get();
                                    $count = round(count($locations) / 50);
                                    $start = WalkLocation::where('request_id', $request->id)
                                            ->orderBy('id')
                                            ->first();
                                    $end = WalkLocation::where('request_id', $request->id)
                                            ->orderBy('id', 'desc')
                                            ->first();

                                    $map = "https://maps-api-ssl.google.com/maps/api/staticmap?size=249x249&style=feature:landscape|visibility:off&style=feature:poi|visibility:off&style=feature:transit|visibility:off&style=feature:road.highway|element:geometry|lightness:39&style=feature:road.local|element:geometry|gamma:1.45&style=feature:road|element:labels|gamma:1.22&style=feature:administrative|visibility:off&style=feature:administrative.locality|visibility:on&style=feature:landscape.natural|visibility:on&scale=2&markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-start@2x.png|$start->latitude,$start->longitude&markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-finish@2x.png|$end->latitude,$end->longitude&path=color:0x2dbae4ff|weight:4";
                                    $skip = 0;
                                    foreach ($locations as $location) {
                                        if ($skip == $count) {
                                            $map .= "|$location->latitude,$location->longitude";
                                            $skip = 0;
                                        }
                                        $skip ++;
                                    }

                                    $start_location = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$start->latitude,$start->longitude"), TRUE);
                                    $start_address = "Address not found";
                                    if (isset($start_location['results'][0]['formatted_address'])) {
                                        $start_address = $start_location['results'][0]['formatted_address'];
                                    }
                                    $end_location = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$end->latitude,$end->longitude"), TRUE);
                                    $end_address = "Address not found";
                                    if (isset($end_location['results'][0]['formatted_address'])) {
                                        $end_address = $end_location['results'][0]['formatted_address'];
                                    }

                                    $email_data['start_location'] = $start_location;
                                    $email_data['end_location'] = $end_location;

                                    $walker = Walker::find($request->confirmed_walker);
                                    $walker_review = WalkerReview::where('request_id', $id)->first();
                                    if ($walker_review) {
                                        $rating = round($walker_review->rating);
                                    } else {
                                        $rating = 0;
                                    }

                                    $email_data['map'] = $map;
                                    $settings = Settings::where('key', 'contact_us_email')->first();
                                    $admin_email = $settings->value;
                                    $requestserv = RequestServices::where('request_id', $request->id)->orderBy('id', 'DESC')->first();
                                    $get_type_name = ProviderType::where('id', $requestserv->type)->first();
                                    $detail = array(
                                        'contact_us_email' => $admin_email,
                                        'request' => $request,
                                        'start_address' => $start_address,
                                        'end_address' => $end_address,
                                        'start' => $start,
                                        'end' => $end,
                                        'map_url' => $map,
                                        'walker' => $walker,
                                        'rating' => $rating,
                                        'base_price' => $requestserv->base_price,
                                        'price_per_time' => $price_per_unit_time,
                                        'price_per_dist' => $price_per_unit_distance,
                                        'ref_bonus' => $request->ledger_payment,
                                        'promo_bonus' => "",
                                        'dist_cost' => $requestserv->distance_cost,
                                        'time_cost' => $requestserv->time_cost,
                                        'type_name' => ucwords($get_type_name->name)
                                    );

                                    $owner = Owner::find($request->owner_id);
                                    $detail_owner = array(
                                        'contact_us_email' => $admin_email,
                                        'request' => $request,
                                        'start_address' => $start_address,
                                        'end_address' => $end_address,
                                        'start' => $start,
                                        'end' => $end,
                                        'map_url' => $map,
                                        'walker' => $owner,
                                        'rating' => $rating,
                                        'actual_total' => $actual_total,
                                        'base_price' => $requestserv->base_price,
                                        'price_per_time' => $price_per_unit_time,
                                        'price_per_dist' => $price_per_unit_distance,
                                        'ref_bonus' => $request->ledger_payment,
                                        'promo_bonus' => $request->promo_payment,
                                        'dist_cost' => $requestserv->distance_cost,
                                        'time_cost' => $requestserv->time_cost,
                                        'type_name' => ucwords($get_type_name->name)
                                    );
                                    //send email to owner
                                    /* $subject = "Invoice Generated";
                                      send_email($request->owner_id, 'owner', $email_data, $subject, 'invoice'); */
                                    if($request->type == 'USSD'){
                                        $message = "Your trip with ".$walker->first_name." cost UGX ".$actual_total.". REF:".$request->id.". Thank you";
                                        $helper->send_sms_message($owner->phone, $message);
                                    }

                                    $subject = "Trip Invoice";
                                    $helper->email_notification($request->owner_id, 'owner', $detail_owner, $subject, 'invoice');

                                    $subject = "Request Completed";
                                    //email_notification(1, 'admin', $detail, $subject, 'invoice');

                                    //send email to walker
                                    /* $subject = "Invoice Generated";
                                      $email_data['emailType'] = 'walker';
                                      send_email($request->confirmed_walker, 'walker', $email_data, $subject, 'invoice'); */
                                    $subject = "Trip Invoice";
                                    $helper->email_notification($request->confirmed_walker, 'walker', $detail, $subject, 'invoice');

                                    if ($request->is_paid == 1) {
                                        // send email
                                        /* $settings = Settings::where('key', 'email_payment_charged')->first();
                                          $pattern = $settings->value;

                                          $pattern = str_replace('%id%', $request->id, $pattern);
                                          $pattern = str_replace('%url%', web_url() . "/admin/request/map/" . $request->id, $pattern);

                                          $subject = "Payment Charged";
                                          email_notification(1, 'admin', $pattern, $subject); */
                                        $settings = Settings::where('key', 'admin_email_address')->first();
                                        $admin_email = $settings->value;
                                        $pattern = array('admin_eamil' => $admin_email, 'name' => 'Administrator', 'amount' => $request->total, 'req_id' => $request_id, 'web_url' => $helper->web_url());
                                        $subject = "Payment Done With " . $request_id . "";
                                        //email_notification(1, 'admin', $pattern, $subject, 'pay_charged', null);
                                    } else {
                                        // send email
                                        /* $pattern = "Payment Failed for the request id " . $request->id . ".";

                                          $subject = "Payment Failed";
                                          email_notification(1, 'admin', $pattern, $subject); */
                                    }
                                    $settings = Settings::where('key', 'default_distance_unit')->first();
                                    $unit = $settings->value;
                                    if ($unit == 0) {
                                        $unit_set = 'kms';
                                    } elseif ($unit == 1) {
                                        $unit_set = 'miles';
                                    }
                                    $distance = DB::table('walk_location')->where('request_id', $request_id)->max('distance');

                                    $end_time = DB::table('walk_location')
                                            ->where('request_id', $request_id)
                                            ->max('created_at');
                                    $request_data_1 = array(
                                        'request_id' => $request_id,
                                        'status' => $request->status,
                                        'confirmed_walker' => $request->confirmed_walker,
                                        'is_walker_started' => $request->is_walker_started,
                                        'is_walker_arrived' => $request->is_walker_arrived,
                                        'is_started' => $request->is_started,
                                        'is_walk_started' => $request->is_started,
                                        'is_completed' => $request->is_completed,
                                        'is_dog_rated' => $request->is_dog_rated,
                                        'is_cancelled' => $request->is_cancelled,
                                        'is_walker_rated' => $request->is_walker_rated,
                                        'dest_latitude' => $request->D_latitude,
                                        'dest_longitude' => $request->D_longitude,
                                        'accepted_time' => $request->request_start_time,
                                        'payment_type' => $request->payment_mode,
                                        'distance' => (string) $helper->convert($distance, $unit),
                                        'unit' => $unit_set,
                                        'end_time' => $end_time,
                                        'owner' => $owner_data1,
                                        'dog' => $dog1,
                                        'bill' => $bill,
                                        'card_details' => $cards,
                                        'charge_details' => $chagre,
                                        'payment_option' => $request->is_paid);
                                        $response_array = array(
                                                'success' => true,
                                                'total' => $helper->currency_converted($total),
                                                'error' => $payment_type,
                                                /* 'currency' => $currency_selected->keyword, */
                                                'currency' => \Config::get('app.generic_keywords.Currency'),
                                                'is_paid' => $request->is_paid,
                                                'request_id' => $request_id,
                                                'status' => $request->status,
                                                'confirmed_walker' => $request->confirmed_walker,
                                                'is_walker_started' => $request->is_walker_started,
                                                'is_walker_arrived' => $request->is_walker_arrived,
                                                'is_walk_started' => $request->is_started,
                                                'is_completed' => $request->is_completed,
                                                'is_walker_rated' => $request->is_walker_rated,
                                                'walker' => $walker_data,
                                                'payment_mode' => $request->payment_mode,
                                                'bill' => $bill,
                                                'owner' => $owner_data1,
                                                'payment_option' => $request->is_paid,
                                                'request' => $request_data_1,
                                        );
                                    $response_code = 200;
                                } else {
                                    $response_array = array('success' => false, 'error' => 34, 'error_messages' => array(34), 'error_code' => 413);
                                    $response_code = 200;
                                }
                            } else {
                                /* $var = Keywords::where('id', 1)->first();
                                  $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $var->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $var->keyword . ' ID'), 'error_code' => 407); */
                                $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'error_code' => 407);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $var = Keywords::where('id', 1)->first();
                          $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }
    
    public function request_walk_cancelled() {
        if (Request::isMethod('post')) {
            $request_id = Input::get('request_id');
            $token = Input::get('token');
            $walker_id = Input::get('id');
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');
            if (Input::has('bearing')) {
                $angle = Input::get('bearing');
            }

            $validator = Validator::make(
                            array(
                        'request_id' => $request_id,
                        'token' => $token,
                        'walker_id' => $walker_id,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                            ), array(
                        'request_id' => 'required|integer',
                        'token' => 'required',
                        'walker_id' => 'required|integer',
                        'latitude' => 'required',
                        'longitude' => 'required',
                            ), array(
                        'request_id.required' => 19,
                        'token.required' => 24,
                        'walker_id.required' => 20,
                        'latitude.required' => 28,
                        'longitude.required' => 28,
                            )
            );

            /* $var = Keywords::where('id', 1)->first(); */

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $helper = new Helper();
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    $helper = new Helper();
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        // Do necessary operations
                        if ($request = Requests::find($request_id)) {
                            if ($request->confirmed_walker == $walker_id) {

                                if ($request->is_walker_started != 1) {
                                    $request->is_cancelled = 1;
                                    $request->save();

                                    $location = $helper->get_location($latitude, $longitude);
                                    $latitude = $location['lat'];
                                    $longitude = $location['long'];
                                    if (!isset($angle)) {
                                        $angle = $helper->get_angle($walker_data->latitude, $walker_data->longitude, $latitude, $longitude);
                                    }
                                    $walk_location = new WalkLocation;
                                    $walk_location->latitude = $latitude;
                                    $walk_location->longitude = $longitude;
                                    $walk_location->request_id = $request_id;
                                    $walk_location->bearing = $angle;
                                    $walk_location->save();

                                    // Send Notification
                                    $walker = Walker::find($request->confirmed_walker);
                                    $walker->old_latitude = $walker->latitude;
                                    $walker->old_longitude = $walker->longitude;
                                    $walker->latitude = $latitude;
                                    $walker->longitude = $longitude;
                                    $walker->bearing = $angle;
                                    $walker->is_available = 1;
                                    $walker->save();

                                    $walker_data = array();
                                    $walker_data['first_name'] = $walker->first_name;
                                    $walker_data['last_name'] = $walker->last_name;
                                    $walker_data['phone'] = $walker->phone;
                                    $walker_data['bio'] = $walker->bio;
                                    $walker_data['picture'] = $walker->picture;
                                    $walker_data['latitude'] = $walker->latitude;
                                    $walker_data['longitude'] = $walker->longitude;
                                    $walker_data['type'] = $walker->type;
                                    $walker_data['rating'] = $walker->rate;
                                    $walker_data['num_rating'] = $walker->rate_count;
                                    $walker_data['car_model'] = $walker->car_model;
                                    $walker_data['car_number'] = $walker->car_number;

                                    $settings = Settings::where('key', 'default_distance_unit')->first();
                                    $unit = $settings->value;
                                    if ($unit == 0) {
                                        $unit_set = 'kms';
                                    } elseif ($unit == 1) {
                                        $unit_set = 'miles';
                                    }
                                    $bill = array();
                                    if ($request->is_completed == 1) {
                                        $bill['distance'] = (string) $helper->convert($request->distance, $unit);
                                        $bill['unit'] = $unit_set;
                                        $bill['time'] = $request->time;
                                        $bill['base_price'] = $request->base_price;
                                        $bill['distance_cost'] = $request->distance_cost;
                                        $bill['time_cost'] = $request->time_cost;
                                        $bill['total'] = $request->total;
                                        $bill['is_paid'] = $request->is_paid;
                                    }

                                    $response_array = array(
                                        'success' => true,
                                        'request_id' => $request_id,
                                        'status' => $request->status,
                                        'confirmed_walker' => $request->confirmed_walker,
                                        'is_walker_started' => $request->is_walker_started,
                                        'is_walker_arrived' => $request->is_walker_arrived,
                                        'is_walk_started' => $request->is_started,
                                        'is_completed' => $request->is_completed,
                                        'is_walker_rated' => $request->is_walker_rated,
                                        'walker' => $walker_data,
                                        'payment_mode' => $request->payment_data,
                                        'bill' => $bill,
                                    );
                                    /* $var = Keywords::where('id', 4)->first();
                                      $title = 'Your ' . $var->keyword . ' has been started'; */
                                    $user_type = 0;
                                    $id = $request->owner_id;
                                    $title = $helper->transl('walk_started_push', $id, $user_type);

                                    $message = $response_array;

                                    //send_notifications($request->owner_id, "owner", $title, $message);

                                    $response_array = array('success' => true);
                                    $response_code = 200;
                                } else {
                                    /* $response_array = array('success' => false, 'error' => '' . $var->keyword . ' not yet arrived', 'error_messages' => array('' . $var->keyword . ' not yet arrived'), 'error_code' => 413); */
                                    $response_array = array('success' => false, 'error' => 35, 'error_messages' => array(35), 'error_code' => 413);
                                    $response_code = 200;
                                }
                            } else {
                                /* $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $var->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $var->keyword . ' ID'), 'error_code' => 407); */
                                $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'error_code' => 407);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Payment before starting
    public function pre_payment() {
        if (Request::isMethod('post')) {
            $request_id = Input::get('request_id');
            $token = Input::get('token');
            $walker_id = Input::get('id');
            $time = Input::get('time');

            $validator = Validator::make(
                            array(
                        'request_id' => $request_id,
                        'token' => $token,
                        'walker_id' => $walker_id,
                        'time' => $time,
                            ), array(
                        'request_id' => 'required|integer',
                        'token' => 'required',
                        'walker_id' => 'required|integer',
                        'time' => 'required',
                            ), array(
                        'request_id.required' => 19,
                        'token.required' => 24,
                        'walker_id.required' => 20,
                        'time.required' => 37,
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    // check for token validity
                    $helper = new Helper();
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        // Do necessary operations
                        if ($request = Requests::find($request_id)) {
                            if ($request->confirmed_walker == $walker_id) {
                                $request_service = RequestServices::find($request_id);
                                $request_typ = ProviderType::where('id', '=', $request_service->type)->first();

                                if (!$walker_data->type) {
                                    $price_per_unit_distance = $request_typ->price_per_unit_distance;
                                    $price_per_unit_time = $request_typ->price_per_unit_time;
                                    $base_price = $request_typ->base_price;
                                } else {
                                    $provider_type = ProviderServices::find($walker_data->type);
                                    $base_price = $provider_type->base_price;
                                    $price_per_unit_distance = $provider_type->price_per_unit_distance;
                                    $price_per_unit_time = $provider_type->price_per_unit_time;
                                }

                                $settings = Settings::where('key', 'default_charging_method_for_users')->first();
                                $pricing_type = $settings->value;
                                $settings = Settings::where('key', 'default_distance_unit')->first();
                                $unit = $settings->value;
                                if ($pricing_type == 1) {
                                    $distance_cost = $price_per_unit_distance;
                                    $time_cost = $price_per_unit_time;
                                    $total = $base_price + $distance_cost + $time_cost;
                                } else {
                                    $distance_cost = 0;
                                    $time_cost = 0;
                                    $total = $base_price;
                                }

                                //Log::info('req');
                                $request_service = RequestServices::find($request_id);
                                $request_service->base_price = $base_price;
                                $request_service->distance_cost = $distance_cost;
                                $request_service->time_cost = $time_cost;
                                $request_service->total = $total;
                                $request_service->save();
                                $request->distance = $distance_cost;
                                $request->time = $time_cost;
                                $request->total = $total;

                                //Log::info('in ');
                                // charge client
                                $ledger = Ledger::where('owner_id', $request->owner_id)->first();

                                if ($ledger) {
                                    $balance = $ledger->amount_earned - $ledger->amount_spent;
                                    if ($balance > 0) {
                                        if ($total > $balance) {
                                            $ledger_temp = Ledger::find($ledger->id);
                                            $ledger_temp->amount_spent = $ledger_temp->amount_spent + $balance;
                                            $ledger_temp->save();
                                            $total = $total - $balance;
                                        } else {
                                            $ledger_temp = Ledger::find($ledger->id);
                                            $ledger_temp->amount_spent = $ledger_temp->amount_spent + $total;
                                            $ledger_temp->save();
                                            $total = 0;
                                        }
                                    }
                                }

                                //Log::info('out');
                                if ($total == 0) {
                                    $request->is_paid = 1;
                                } else {

                                    $payment_data = Payment::where('owner_id', $request->owner_id)->where('is_default', 1)->first();
                                    if (!$payment_data)
                                        $payment_data = Payment::where('owner_id', $request->owner_id)->first();

                                    if ($payment_data) {
                                        $customer_id = $payment_data->customer_id;
                                        try {
                                            if (\Config::get('app.default_payment') == 'stripe') {
                                                Stripe::setApiKey(\Config::get('app.stripe_secret_key'));

                                                try {
                                                    Stripe_Charge::create(array(
                                                        "amount" => ($total) * 100,
                                                        "currency" => "usd",
                                                        "customer" => $customer_id)
                                                    );
                                                } catch (Stripe_InvalidRequestError $e) {
                                                    // Invalid parameters were supplied to Stripe's API
                                                    $ownr = Owner::find($request->owner_id);
                                                    $ownr->debt = $total;
                                                    $ownr->save();
                                                    $response_array = array('error' => $e->getMessage());
                                                    $response_code = 200;
                                                    $response = Response::json($response_array, $response_code);
                                                    return $response;
                                                }
                                                $request->is_paid = 1;

                                                $setting = Settings::where('key', 'paypal')->first();
                                                $settng1 = Settings::where('key', 'service_fee')->first();
                                                if ($setting->value == 2 && $walker_data->merchant_id != NULL) {
                                                    // dd($amount$request->transfer_amount);
                                                    $transfer = Stripe_Transfer::create(array(
                                                                "amount" => ($total - $settng1->value) * 100, // amount in cents
                                                                "currency" => "usd",
                                                                "recipient" => $walker_data->merchant_id)
                                                    );
                                                }
                                            } else {
                                                $amount = $total;
                                                Braintree_Configuration::environment(\Config::get('app.braintree_environment'));
                                                Braintree_Configuration::merchantId(\Config::get('app.braintree_merchant_id'));
                                                Braintree_Configuration::publicKey(\Config::get('app.braintree_public_key'));
                                                Braintree_Configuration::privateKey(\Config::get('app.braintree_private_key'));
                                                $card_id = $payment_data->card_token;
                                                $setting = Settings::where('key', 'paypal')->first();
                                                $settng1 = Settings::where('key', 'service_fee')->first();
                                                if ($setting->value == 2 && $walker_data->merchant_id != NULL) {
                                                    // escrow
                                                    $result = Braintree_Transaction::sale(array(
                                                                'amount' => $amount,
                                                                'paymentMethodToken' => $card_id
                                                    ));
                                                } else {
                                                    $result = Braintree_Transaction::sale(array(
                                                                'amount' => $amount,
                                                                'paymentMethodToken' => $card_id
                                                    ));
                                                }
                                                //Log::info('result = ' . print_r($result, true));
                                                if ($result->success) {
                                                    $request->is_paid = 1;
                                                } else {
                                                    $request->is_paid = 0;
                                                }
                                            }
                                        } catch (Exception $e) {
                                            $response_array = array('success' => false, 'error' => $e, 'error_messages' => array(12), 'error_code' => 405);
                                            $response_code = 200;
                                            $response = Response::json($response_array, $response_code);
                                            return $response;
                                        }
                                    }
                                }

                                if ($request->payment_mode == 1) {
                                    $request->cash_payment = $total;
                                } else {
                                    $request->card_payment = $total;
                                }
                                $request->ledger_payment = $request->total - $total;

                                $request->save();
                                //Log::info('Request = ' . print_r($request, true));

                                if ($request->is_paid == 1) {
                                    $owner = Owner::find($request->owner_id);
                                    $settings = Settings::where('key', 'sms_payment_generated')->first();
                                    $pattern = $settings->value;
                                    $pattern = str_replace('%user%', $owner->first_name . " " . $owner->last_name, $pattern);
                                    $pattern = str_replace('%id%', $request->id, $pattern);
                                    $pattern = str_replace('%user_mobile%', $owner->phone, $pattern);
                                    sms_notification(1, 'admin', $pattern);
                                }

                                $walker = Walker::find($walker_id);
                                $walker->is_available = 1;
                                $walker->save();

                                // Send Notification
                                $walker = Walker::find($request->confirmed_walker);
                                $walker_data = array();
                                $walker_data['first_name'] = $walker->first_name;
                                $walker_data['last_name'] = $walker->last_name;
                                $walker_data['phone'] = $walker->phone;
                                $walker_data['bio'] = $walker->bio;
                                $walker_data['picture'] = $walker->picture;
                                $walker_data['type'] = $walker->type;
                                $walker_data['rating'] = $walker->rate;
                                $walker_data['num_rating'] = $walker->rate_count;
                                $walker_data['car_model'] = $walker->car_model;
                                $walker_data['car_number'] = $walker->car_number;
                                                            
                                $settings = Settings::where('key', 'default_distance_unit')->first();
                                $unit = $settings->value;
                                if ($unit == 0) {
                                    $unit_set = 'kms';
                                } elseif ($unit == 1) {
                                    $unit_set = 'miles';
                                }
                                $bill = array();
                                if ($request->is_paid == 1) {
                                    $bill['distance'] = (string) $helper->convert($request->distance, $unit);
                                    $bill['unit'] = $unit_set;
                                    $bill['time'] = $request->time;
                                    $bill['base_price'] = currency_converted($base_price);
                                    $bill['distance_cost'] = currency_converted($distance_cost);
                                    $bill['time_cost'] = currency_converted($time_cost);
                                    $bill['total'] = currency_converted($request->total);
                                    $bill['is_paid'] = $request->is_paid;
                                }

                                $response_array = array(
                                    'success' => true,
                                    'request_id' => $request_id,
                                    'status' => $request->status,
                                    'confirmed_walker' => $request->confirmed_walker,
                                    'walker' => $walker_data,
                                    'bill' => $bill,
                                );
                                $title = "Payment Was Made";

                                $message = $response_array;

                                //send_notifications($walker->id, "walker", $title, $message);

                                $settings = Settings::where('key', 'email_notification')->first();
                                $condition = $settings->value;
                                if ($condition == 1) {
                                    $settings = Settings::where('key', 'contact_us_email')->first();
                                    $admin_email = $settings->value;
                                    $pattern = array('contact_us_email' => $admin_email, 'name' => ucwords($walker->first_name . " " . $walker->last_name), 'amount' => $total, 'req_id' => $request_id, 'web_url' => web_url());
                                    $subject = "Payment Done With " . $request_id . "";
                                    email_notification($walker->id, 'walker', $pattern, $subject, 'pre_payment', null);
                                }

                                // Send SMS
                                $owner = Owner::find($request->owner_id);
                                $settings = Settings::where('key', 'sms_when_provider_completes_job')->first();
                                $pattern = $settings->value;
                                $pattern = str_replace('%user%', $owner->first_name . " " . $owner->last_name, $pattern);
                                $pattern = str_replace('%driver%', $walker->first_name . " " . $walker->last_name, $pattern);
                                $pattern = str_replace('%driver_mobile%', $walker->phone, $pattern);
                                $pattern = str_replace('%amount%', $request->total, $pattern);
                                sms_notification($request->owner_id, 'owner', $pattern);

                                $email_data = array();

                                $email_data['name'] = $owner->first_name;
                                $email_data['emailType'] = 'user';
                                $email_data['base_price'] = $bill['base_price'];
                                $email_data['distance'] = $bill['distance'];
                                $email_data['time'] = $bill['time'];
                                $email_data['unit'] = $bill['unit'];
                                $email_data['total'] = $bill['total'];

                                if ($bill['payment_mode']) {
                                    $email_data['payment_mode'] = $bill['payment_mode'];
                                } else {
                                    $email_data['payment_mode'] = '---';
                                }

                                /* $subject = "Invoice Generated";
                                  send_email($request->owner_id, 'owner', $email_data, $subject, 'invoice');

                                  $subject = "Invoice Generated";
                                  $email_data['emailType'] = 'walker';
                                  send_email($request->confirmed_walker, 'walker', $email_data, $subject, 'invoice');
                                 */
                                if ($request->is_paid == 1) {
                                    // send email
                                    /* $settings = Settings::where('key', 'email_payment_charged')->first();
                                      $pattern = $settings->value;

                                      $pattern = str_replace('%id%', $request->id, $pattern);
                                      $pattern = str_replace('%url%', web_url() . "/admin/request/" . $request->id, $pattern);

                                      $subject = "Payment Charged";
                                      email_notification(1, 'admin', $pattern, $subject); */
                                    $settings = Settings::where('key', 'admin_email_address')->first();
                                    $admin_email = $settings->value;
                                    $pattern = array('admin_eamil' => $admin_email, 'name' => 'Administrator', 'amount' => $total, 'req_id' => $request_id, 'web_url' => web_url());
                                    $subject = "Payment Done With " . $request_id . "";
                                    //email_notification(1, 'admin', $pattern, $subject, 'pay_charged', null);
                                }

                                $response_array = array(
                                    'success' => true,
                                    'base_fare' => $helper->currency_converted($base_price),
                                    'distance_cost' => $helper->currency_converted($distance_cost),
                                    'time_cost' => $helper->currency_converted($time_cost),
                                    'total' => $helper->currency_converted($total),
                                    'is_paid' => $request->is_paid,
                                );
                                $response_code = 200;
                            } else {
                                /* $var = Keywords::where('id', 1)->first();
                                  $response_array = array('success' => false, 'error' => 'Service ID doesnot matches with ' . $var->keyword . ' ID', 'error_messages' => array('Service ID doesnot matches with ' . $var->keyword . ' ID'), 'error_code' => 407); */
                                $response_array = array('success' => false, 'error' => 22, 'error_messages' => array(22), 'error_code' => 407);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $var = Keywords::where('id', 1)->first();
                          $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }
        
    // Add Location Data
  public function walk_location() {
    $helper = new Helper();
    if (Request::isMethod('post')) {
       $request_id = Input::get('request_id');
       $token = Input::get('token');
       $walker_id = Input::get('id');
       $latitude = Input::get('latitude');
       $longitude = Input::get('longitude');
       if (Input::has('bearing')) {
          $angle = Input::get('bearing');
       }
      if (Input::has('fcm_token')) {
          $fcm = Input::get('fcm_token');
      }
      
      $validator = Validator::make(
                            array(
                                'request_id' => $request_id,
                                'token' => $token,
                                'walker_id' => $walker_id,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                            ), array(
                                'request_id' => 'required|integer',
                                'token' => 'required',
                                'walker_id' => 'required|integer',
                                'latitude' => 'required',
                                'longitude' => 'required',
                            ), array(
                                'request_id.required' => 19,
                                'token.required' => 24,
                                'walker_id.required' => 20,
                                'latitude.required' => 28,
                                'longitude.required' => 28,
                            ));
      
      if($validator->fails()){
         $error_messages = $validator->messages()->all();
         $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
         $response_code = 200;
      }else{
        $unit = $unit_set = -1;
        $settings = Settings::where('key', 'default_distance_unit')->first();
        $unit = $settings->value;
        if ($unit == 0){
           $unit_set = 'kms';
        }elseif($unit == 1){
          $unit_set = 'miles';
        }
        $is_admin = $this->isAdmin($token);
        if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
           // check for token validity
          if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
            // Do necessary operations
              $settings = Settings::where('key', 'request_time_costing_type')->first();
              $time_fare_type = $settings->value;
            if ($request = Requests::find($request_id)) {
              if ($request->confirmed_walker == $walker_id) {
                if ($request->is_started == 1) {
                  $walk_location_last = WalkLocation::where('request_id', $request_id)->orderBy('created_at', 'desc')->first();
                  if ($walk_location_last) {
                    $distance_old = $walk_location_last->distance;
                    $distance_new = $helper->distanceGeoPoints($walk_location_last->latitude, $walk_location_last->longitude, $latitude, $longitude);
                    $distance_new = $helper->convert($distance_new, $unit);
                    $distance = $distance_old + $distance_new;
                  }else{
                    $distance = 0;
                  }
                  
                    $walker = Walker::find($walker_id);
                    $helper = new Helper();
                    $location = $helper->get_location($latitude, $longitude);
                    $latitude = $location['lat'];
                    $longitude = $location['long'];
                    if (!isset($angle)) {
                      $angle = $helper->get_angle($walker->latitude, $walker->longitude, $latitude, $longitude);
                    }
                    $walker->old_latitude = $walker->latitude;
                    $walker->old_longitude = $walker->longitude;
                    $walker->latitude = $latitude;
                    $walker->longitude = $longitude;
                    $walker->bearing = $angle;
                    if (!isset($fcm)) {
                        $fcm = '';
                    }
                    //$walker->device_token = $fcm;
                    $walker->save();
                    /* GET SECOND LAST ENTY FOR TIME */
                    if ($time_fare_type) {
                            $loc1 = WalkLocation::where('request_id', $request->id)->orderBy('id', 'desc')->first();
                    } else {
                            $loc1 = WalkLocation::where('request_id', $request->id)->orderBy('id', 'asc')->first();
                    }
                    /* GET SECOND LAST ENTY FOR TIME END */
                    if ($request->is_completed != 1) {
                            $walk_location = new WalkLocation;
                            $walk_location->request_id = $request_id;
                            $walk_location->latitude = $latitude;
                            $walk_location->longitude = $longitude;
                            $walk_location->distance = $distance;
                            $walk_location->bearing = $angle;
                            $walk_location->save();
                    }
                    $one_minut_old_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - 60);
                    /* $loc1 = WalkLocation::where('request_id', $request->id)->first(); */
                    /* print $loc1; */
                    $loc2 = WalkLocation::where('request_id', $request->id)->orderBy('id', 'desc')->first();
                    if ($loc1) {
                            $time1 = strtotime($loc2->created_at);
                            $time2 = strtotime($loc1->created_at);
                            /* echo $difference = intval(($time1 - $time2) / 60); */
                            $difference = ($time1 - $time2) / 60;
                            if ($loc1min = WalkLocation::where('request_id', $request->id)->where('created_at', '<=', $one_minut_old_time)
                                                       ->orderBy('id', 'desc')->first()) {
                              $distence = $helper->distanceGeoPoints($loc1min->latitude, $loc1min->longitude, $latitude, $longitude);
                              if ($request->is_completed != 1) {
                                  if ($time_fare_type){
                                          if ($distence <= 50){
                                            $request->time = $request->time + $difference;
                                          }else{
                                            $request->time = $request->time;
                                          }
                                  } else {
                                             $request->time = $difference;
                                  }
                              }
                            }else{
                                    $request->time = 0;
                            }
                    }else{
                          $request->time = 0;
                    }
                    $request->distance = $distance;
                    $request->save();
                    $response_array = array(
                            'success' => true,
                            'dest_latitude' => $request->D_latitude,
                            'dest_longitude' => $request->D_longitude,
                            'payment_type' => $request->payment_mode,
                            'is_cancelled' => $request->is_cancelled,
                            'distance' => $distance,
                            'unit' => $unit_set,
                            'time' => $request->time,
                    );
                    $response_code = 200;
              } else {
                  $walker = Walker::find($walker_id);
                  $helper = new Helper();
                  $location = $helper->get_location($latitude, $longitude);
                  $latitude = $location['lat'];
                  $longitude = $location['long'];
                  if (!isset($angle)) {
                          $angle = $helper->get_angle($walker->latitude, $walker->longitude, $latitude, $longitude);
                  }
                  $walker->old_latitude = $walker->latitude;
                  $walker->old_longitude = $walker->longitude;
                  $walker->latitude = $latitude;
                  $walker->longitude = $longitude;
                  $walker->bearing = $angle;
                  if (isset($fcm)) {
                          $walker->fcm_token = $fcm;
                  }
                  $walker->save();
                  $response_array = array(
                          'success' => false,
                          'dest_latitude' => $request->D_latitude,
                          'dest_longitude' => $request->D_longitude,
                          'payment_type' => $request->payment_mode,
                          'is_cancelled' => $request->is_cancelled,
                          'unit' => $unit_set,
                          'error' => 34,
                          'error_messages' => array(34),
                          'error_code' => 414,
                  );
                  $response_code = 200;
            }
        } else {
                /* $var = Keywords::where('id', 1)->first();
                    $response_array = array('success' => false, 'error' => 'Request ID doesnot matches with ' . $var->keyword . ' ID', 'error_messages' => array('Request ID doesnot matches with ' . $var->keyword . ' ID'), 'error_code' => 407); */
                $response_array = array(
                        'success' => false,
                        'dest_latitude' => $request->D_latitude,
                        'dest_longitude' => $request->D_longitude,
                        'payment_type' => $request->payment_mode,
                        'is_cancelled' => $request->is_cancelled,
                        'unit' => $unit_set,
                        'error' => 22,
                        'error_messages' => array(22),
                        'error_code' => 407);
                $response_code = 200;
        }
      } else {
              $response_array = array('success' => false, 'error' => 23, 'error_messages' => array(23), 'error_code' => 408);
              $response_code = 200;
      }
      } else {
              $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
              $response_code = 200;
      }
      } else {
              if ($is_admin) {
                      /* $var = Keywords::where('id', 1)->first();
                          $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                      $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
              } else {
                      $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
              }
              $response_code = 200;
      }
    }
   }
    $response = Response::json($response_array, $response_code);
    return $response;
  }

    // Check online/offline state
    public function check_state() {

        $walker_id = Input::get('id');
        $token = Input::get('token');

        $validator = Validator::make(
            array(
            'walker_id' => $walker_id,
            'token' => $token,
            ),
            array(
            'walker_id' => 'required|integer',
            'token' => 'required',
            ),
            array(
            'walker_id.required' => 20,
            'token.required' => 24,
            )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                $helper = new Helper();
                if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {

                    $response_array = array('success' => true, 'is_active' => $walker_data->is_active);
                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Toggle online/offline state
    public function toggle_state() {

        $walker_id = Input::get('id');
        $token = Input::get('token');

        $validator = Validator::make(
                        array(
                    'walker_id' => $walker_id,
                    'token' => $token,
                        ), array(
                    'walker_id' => 'required|integer',
                    'token' => 'required',
                        ), array(
                    'walker_id.required' => 20,
                    'token.required' => 24,
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                $helper = new Helper();
                if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                    $walker = Walker::find($walker_id);
                    $walker->is_active = ($walker->is_active + 1) % 2;
                    $walker->save();
                    $response_array = array('success' => true, 'is_active' => $walker->is_active);
                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    // Update Profile
    public function update_profile() {
        $token = Input::get('token');
        $walker_id = Input::get('id');
        $first_name = Input::get('first_name');
        $last_name = Input::get('last_name');
        $phone = Input::get('phone');
        $password = Input::get('password');
        $new_password = Input::get('new_password');
        $old_password = Input::get('old_password');
        $picture = Input::file('picture');
        $bio = Input::get('bio');
        $address = Input::get('address');
        $state = Input::get('state');
        $country = Input::get('country');
        $zipcode = Input::get('zipcode');
        $car_model = $car_number = "";
        if (Input::has('car_model')) {
            $car_model = trim(Input::get('car_model'));
        }
        if (Input::has('car_number')) {
            $car_number = trim(Input::get('car_number'));
        }

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'walker_id' => $walker_id,
                    'picture' => $picture,
                        /* 'zipcode' => $zipcode */
                        ), array(
                    'token' => 'required',
                    'walker_id' => 'required|integer',
                    /* 'picture' => 'mimes:jpeg,bmp,png', */
                    'picture' => '',
                        /* 'zipcode' => 'integer' */
                        ), array(
                    'token.required' => 24,
                    'walker_id.required' => 20,
                    /* 'picture' => 'mimes:jpeg,bmp,png', */
                    'picture' => '',
                        /* 'zipcode' => '' */
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                $helper = new Helper();
                if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                    if ($new_password != "" || $new_password != NULL) {
                        if ($old_password != "" || $old_password != NULL) {
                            if (Hash::check($old_password, $walker_data->password)) {
                                $walker = Walker::find($walker_id);
                                if ($first_name) {
                                    $walker->first_name = $first_name;
                                }
                                if ($last_name) {
                                    $walker->last_name = $last_name;
                                }
                                if ($phone) {
                                    $walker->phone = $phone;
                                }
                                if ($bio) {
                                    $walker->bio = $bio;
                                }
                                if ($address) {
                                    $walker->address = $address;
                                }
                                if ($state) {
                                    $walker->state = $state;
                                }
                                if ($country) {
                                    $walker->country = $country;
                                }
                                if ($zipcode) {
                                    $walker->zipcode = $zipcode;
                                }
                                if ($new_password) {
                                    $walker->password = Hash::make($new_password);
                                }
                                if ($car_model != "") {
                                    $walker->car_model = $car_model;
                                }
                                if ($car_number != "") {
                                    $walker->car_number = $car_number;
                                }

                                if (Input::hasFile('picture')) {
                                    if ($walker->picture != "") {
                                        $path = $walker->picture;
                                        //Log::info($path);
                                        $filename = basename($path);
                                        //Log::info($filename);
                                        if (file_exists($path)) {
                                            unlink(public_path() . "/uploads/" . $filename);
                                        }
                                    }
                                    // upload image
                                    $file_name = time();
                                    $file_name .= rand();
                                    $file_name = sha1($file_name);

                                    $ext = Input::file('picture')->getClientOriginalExtension();
                                    //Log::info('ext = ' . print_r($ext, true));
                                    Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
                                    $local_url = $file_name . "." . $ext;

                                    // Upload to S3

                                    if (\Config::get('app.s3_bucket') != "") {
                                        $s3 = App::make('aws')->get('s3');
                                        $pic = $s3->putObject(array(
                                            'Bucket' => \Config::get('app.s3_bucket'),
                                            'Key' => $file_name,
                                            'SourceFile' => public_path() . "/uploads/" . $local_url,
                                        ));

                                        $s3->putObjectAcl(array(
                                            'Bucket' => \Config::get('app.s3_bucket'),
                                            'Key' => $file_name,
                                            'ACL' => 'public-read'
                                        ));

                                        $s3_url = $s3->getObjectUrl(\Config::get('app.s3_bucket'), $file_name);
                                    } else {
                                        $s3_url = web_url() . '/uploads/' . $local_url;
                                    }

                                    if (isset($walker->picture)) {
                                        if ($walker->picture != "") {
                                            $icon = $walker->picture;
                                            unlink_image($icon);
                                        }
                                    }

                                    $walker->picture = $s3_url;
                                }
                                If (Input::has('timezone')) {
                                    $walker->timezone = Input::get('timezone');
                                }

                                $walker->save();

                                $response_array = array(
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
                                    'timezone' => $walker->timezone,
                                    'type' => $walker->type,
                                    'car_model' => $walker->car_model,
                                    'car_number' => $walker->car_number,
                                );
                                $response_code = 200;
                            } else {
                                $response_array = array('success' => false, 'error' => 38, 'error_messages' => array(38), 'error_code' => 501);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 39, 'error_messages' => array(39), 'error_code' => 502);
                            $response_code = 200;
                        }
                    } else {

                        $walker = Walker::find($walker_id);
                        if ($first_name) {
                            $walker->first_name = $first_name;
                        }
                        if ($last_name) {
                            $walker->last_name = $last_name;
                        }
                        if ($phone) {
                            $walker->phone = $phone;
                        }
                        if ($bio) {
                            $walker->bio = $bio;
                        }
                        if ($address) {
                            $walker->address = $address;
                        }
                        if ($state) {
                            $walker->state = $state;
                        }
                        if ($country) {
                            $walker->country = $country;
                        }
                        if ($zipcode) {
                            $walker->zipcode = $zipcode;
                        }
                        if ($car_model != "") {
                            $walker->car_model = $car_model;
                        }
                        if ($car_number != "") {
                            $walker->car_number = $car_number;
                        }

                        if (Input::hasFile('picture')) {
                            if ($walker->picture != "") {
                                $path = $walker->picture;
                                //Log::info($path);
                                $filename = basename($path);
                                //Log::info($filename);
                                if (file_exists($path)) {
                                    unlink(public_path() . "/uploads/" . $filename);
                                }
                            }
                            // upload image
                            $file_name = time();
                            $file_name .= rand();
                            $file_name = sha1($file_name);

                            $ext = Input::file('picture')->getClientOriginalExtension();
                            //Log::info('ext = ' . print_r($ext, true));
                            Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
                            $local_url = $file_name . "." . $ext;

                            // Upload to S3

                            if (\Config::get('app.s3_bucket') != "") {
                                $s3 = App::make('aws')->get('s3');
                                $pic = $s3->putObject(array(
                                    'Bucket' => \Config::get('app.s3_bucket'),
                                    'Key' => $file_name,
                                    'SourceFile' => public_path() . "/uploads/" . $local_url,
                                ));

                                $s3->putObjectAcl(array(
                                    'Bucket' => \Config::get('app.s3_bucket'),
                                    'Key' => $file_name,
                                    'ACL' => 'public-read'
                                ));

                                $s3_url = $s3->getObjectUrl(\Config::get('app.s3_bucket'), $file_name);
                            } else {
                                $s3_url = web_url() . '/uploads/' . $local_url;
                            }

                            if (isset($walker->picture)) {
                                if ($walker->picture != "") {
                                    $icon = $walker->picture;
                                    unlink_image($icon);
                                }
                            }

                            $walker->picture = $s3_url;
                        }
                        If (Input::has('timezone')) {
                            $walker->timezone = Input::get('timezone');
                        }

                        $walker->save();

                        $response_array = array(
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
                            'timezone' => $walker->timezone,
                            'type' => $walker->type,
                            'car_model' => $walker->car_model,
                            'car_number' => $walker->car_number,
                        );
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }
    
    public function get_completed_requests() {
        $helper = new Helper();
        $walker_id = Input::get('id');
        $token = Input::get('token');
        $from = Input::get('from_date'); // 2015-03-25 07:45:01
        $to_date = Input::get('to_date'); //2015-03-25 07:45:01
        $to_date = date('Y-m-d', strtotime($to_date . "+1 days"));

        $validator = Validator::make(
                        array(
                    'walker_id' => $walker_id,
                    'token' => $token,
                        ), array(
                    'walker_id' => 'required|integer',
                    'token' => 'required',
                        ), array(
                    'walker_id.required' => 20,
                    'token.required' => 24,
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                    if ($from != "" && $to_date != "") {
                        $request_data = DB::table('request')
                                ->where('request.confirmed_walker', $walker_id)
                                ->where('request.is_completed', 1)
                                ->where('request_start_time', '>=', $from)
                                ->where('request_start_time', '<=', $to_date)
                                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                                ->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
                                ->select('request.*', 'request.request_start_time', 'request.transfer_amount', 'owner.first_name', 'owner.last_name', 'owner.phone', 'owner.email', 'owner.picture', 'owner.bio', 'request.distance', 'request.time', 'request.promo_code', 'request_services.base_price', 'request_services.distance_cost', 'request_services.time_cost', 'request.total')
                                ->groupBy('request.id')
                                ->get();
                    } else {
                        $request_data = DB::table('request')
                                ->where('request.confirmed_walker', $walker_id)
                                ->where('request.is_completed', 1)
                                ->leftJoin('owner', 'request.owner_id', '=', 'owner.id')
                                ->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
                                ->select('request.*', 'request.request_start_time', 'request.transfer_amount', 'owner.first_name', 'owner.last_name', 'owner.phone', 'owner.email', 'owner.picture', 'owner.bio', 'request.distance', 'request.time', 'request.promo_code', 'request_services.base_price', 'request_services.distance_cost', 'request_services.time_cost', 'request.total')
                                ->groupBy('request.id')
                                ->get();
                    }
                    $requests = array();
                    $settings = Settings::where('key', 'default_distance_unit')->first();

                    /* $setbase_price = Settings::where('key', 'base_price')->first();
                      $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
                      $settime_price = Settings::where('key', 'price_per_unit_time')->first(); */

                    $unit = $settings->value;
                    if ($unit == 0) {
                        $unit_set = 'kms';
                    } elseif ($unit == 1) {
                        $unit_set = 'miles';
                    }
                    $walker = Walkers::where('id', $walker_id)->first();
                    foreach ($request_data as $data) {
                        $discount = 0;
                        if ($data->promo_id != "") {
                            $promo_code = PromoCodes::where('id', $data->promo_id)->first();
                            if (isset($promo_code->id)) {
                                $promo_value = $promo_code->value;
                                $promo_type = $promo_code->type;
                                if ($promo_type == 1) {
                                    // Percent Discount
                                    $discount = $data->total * $promo_value / 100;
                                } elseif ($promo_type == 2) {
                                    // Absolute Discount
                                    $discount = $promo_value;
                                }
                            }
                        }
                        $is_multiple_service = Settings::where('key', 'allow_multiple_service')->first();
                        if ($is_multiple_service->value == 0) {

                            $requestserv = RequestServices::where('request_id', $data->id)->first();

                            $request_typ = ProviderType::where('id', '=', $requestserv->type)->first();

                            $pt_new = ProviderType::where('id', $walker->type)->first();

                            $ps_new = ProviderServices::where('id', $walker->type)->first();


                            $request['id'] = $data->id;
                            $request['date'] = $data->request_start_time;
                            $request['distance'] = (string) $data->distance;
                            $request['unit'] = $unit_set;
                            $request['time'] = $data->time;
                            $request['base_distance'] = $request_typ->base_distance;
                            /* $currency = Keywords::where('alias', 'Currency')->first();
                              $request['currency'] = $currency->keyword; */
                            $request['currency'] = \Config::get('app.generic_keywords.Currency');
                            if ($requestserv->base_price != 0) {
                                $request['base_price'] = $helper->currency_converted($data->base_price);
                                $request['distance_cost'] = $helper->currency_converted($data->distance_cost);
                                $request['time_cost'] = $helper->currency_converted($data->time_cost);
                            } else {
                                /* $setbase_price = Settings::where('key', 'base_price')->first();
                                  $request['base_price'] = currency_converted($setbase_price->value);
                                  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
                                  $request['distance_cost'] = currency_converted($setdistance_price->value);
                                  $settime_price = Settings::where('key', 'price_per_unit_time')->first();
                                  $request['time_cost'] = currency_converted($settime_price->value); */
                                $request['base_price'] = $helper->currency_converted($data->base_price);
                                $request['distance_cost'] = $helper->currency_converted($data->distance_cost);
                                $request['time_cost'] = $helper->currency_converted($data->time_cost);
                            }

                            if ($pt_new->base_price != 0) {

                                $request['price_per_unit_distance'] = $helper->currency_converted($pt_new->price_per_unit_distance);
                                $request['price_per_unit_time'] = $helper->currency_converted($pt_new->price_per_unit_time);
                            } else {

                                $request['price_per_unit_distance'] = $helper->currency_converted($ps_new->price_per_unit_distance);

                                $request['price_per_unit_time'] = $helper->currency_converted($ps_new->price_per_unit_time);
                            }


                            $admins = Admin::first();
                            $request['walker']['email'] = $walker->email;
                            $request['admin']['email'] = $admins->username;
                            if ($data->transfer_amount != 0) {
                                $request['walker']['amount'] = $helper->currency_converted($data->total - $data->transfer_amount);
                                $request['admin']['amount'] = $helper->currency_converted($data->transfer_amount);
                            } else {
                                $request['walker']['amount'] = $helper->currency_converted($data->transfer_amount);
                                $request['admin']['amount'] = $helper->currency_converted($data->total - $data->transfer_amount);
                            }

                            $request['total'] = $helper->currency_converted($data->total + $data->ledger_payment + $discount);
                        } else {

                            $request['id'] = $data->id;
                            $request['date'] = $data->request_start_time;
                            $request['distance'] = (string) $data->distance;
                            $request['unit'] = $unit_set;
                            $request['time'] = $data->time;
                            /* $currency = Keywords::where('alias', 'Currency')->first();
                              $request['currency'] = $currency->keyword; */
                            $request['currency'] = \Config::get('app.generic_keywords.Currency');

                            $rserv = RequestServices::where('request_id', $data->id)->get();
                            $typs = array();
                            $typi = array();
                            $typp = array();
                            $total_price = 0;
                            foreach ($rserv as $typ) {
                                $typ1 = ProviderType::where('id', $typ->type)->first();
                                $typ_price = ProviderServices::where('provider_id', $data->confirmed_walker)->where('type', $typ->type)->first();

                                if ($typ_price->base_price > 0) {
                                    $typp1 = 0.00;
                                    $typp1 = $typ_price->base_price;
                                } elseif ($typ_price->price_per_unit_distance > 0) {
                                    $typp1 = 0.00;
                                    foreach ($rserv as $key) {
                                        $typp1 = $typp1 + $key->distance_cost;
                                    }
                                } else {
                                    $typp1 = 0.00;
                                }
                                $typs['name'] = $typ1->name;
                                $typs['price'] = $helper->currency_converted($typp1);
                                $total_price = $total_price + $typp1;
                                array_push($typi, $typs);
                            }
                            $request['type'] = $typi;

                            $base_price = 0;
                            $distance_cost = 0;
                            $time_cost = 0;
                            foreach ($rserv as $key) {
                                $base_price = $base_price + $key->base_price;
                                $distance_cost = $distance_cost + $key->distance_cost;
                                $time_cost = $time_cost + $key->time_cost;
                            }
                            $request['base_price'] = $helper->currency_converted($base_price);
                            $request['distance_cost'] = $helper->currency_converted($distance_cost);
                            $request['time_cost'] = $helper->currency_converted($time_cost);
                            $request['total'] = $helper->currency_converted($total_price);
                        }
                        /* path */
                        $id = $data->id;
                        $locations = WalkLocation::where('request_id', $data->id)->orderBy('id')->get();
                        $count = round(count($locations) / 50);
                        $start = $end = $map = "";
                        if (count($locations) >= 1) {
                            $start = WalkLocation::where('request_id', $id)
                                    ->orderBy('id')
                                    ->first();
                            $end = WalkLocation::where('request_id', $id)
                                    ->orderBy('id', 'desc')
                                    ->first();
                            $map = "https://maps-api-ssl.google.com/maps/api/staticmap?key=".\Config::get('app.gcm_browser_key')."&size=249x249&scale=2&markers=shadow:true|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-start@2x.png|$start->latitude,$start->longitude&markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-finish@2x.png|$end->latitude,$end->longitude&path=color:0x2dbae4ff|weight:4";
                            $skip = 0;
                            foreach ($locations as $location) {
                                if ($skip == $count) {
                                    $map .= "|$location->latitude,$location->longitude";
                                    $skip = 0;
                                }
                                $skip ++;
                            }
                            /* $map.="&key=" . Config::get('app.gcm_browser_key'); */
                        }
                        $request['start_lat'] = "";
                        if (isset($start->latitude)) {
                            $request['start_lat'] = $start->latitude;
                        }
                        $request['start_long'] = "";
                        if (isset($start->longitude)) {
                            $request['start_long'] = $start->longitude;
                        }
                        $request['end_lat'] = "";
                        if (isset($end->latitude)) {
                            $request['end_lat'] = $end->latitude;
                        }
                        $request['end_long'] = "";
                        if (isset($end->longitude)) {
                            $request['end_long'] = $end->longitude;
                        }
                        $request['map_url'] = $map;
                        $request['src_address'] = $data->src_address;
                        $request['dest_address'] = $data->dest_address;
                        $request['base_price'] = $helper->currency_converted($data->base_price);
                        $request['distance_cost'] = $helper->currency_converted($data->distance_cost);
                        $request['time_cost'] = $helper->currency_converted($data->time_cost);
                        $tot = $helper->currency_converted($data->total - $data->ledger_payment - $data->promo_payment);
                        if ($tot <= 0) {
                            $tot = 0;
                        }
                        $request['total'] = $tot;
                        $request['main_total'] = $helper->currency_converted($data->total);
                        $request['referral_bonus'] = $helper->currency_converted($data->ledger_payment);
                        $request['promo_bonus'] = $helper->currency_converted($data->promo_payment);
                        $request['payment_type'] = $data->payment_mode;
                        $request['is_paid'] = $data->is_paid;
                        $request['promo_id'] = $data->promo_id;
                        $request['promo_code'] = $data->promo_code;
                        $request['owner']['first_name'] = $data->first_name;
                        $request['owner']['last_name'] = $data->last_name;
                        $request['owner']['phone'] = $data->phone;
                        $request['owner']['email'] = $data->email;
                        $request['owner']['picture'] = $data->picture;
                        $request['owner']['bio'] = $data->bio;
                        $request['owner']['payment_opt'] = $data->payment_mode;
                        array_push($requests, $request);
                    }

                    $response_array = array(
                        'success' => true,
                        'requests' => $requests
                    );
                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function provider_services_update() {
        $helper = new Helper();
        $token = Input::get('token');
        $walker_id = Input::get('id');

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'walker_id' => $walker_id,
                        ), array(
                    'token' => 'required',
                    'walker_id' => 'required|integer',
                        ), array(
                    'token.required' => 24,
                    'walker_id.required' => 20,
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
            //Log::info('validation error =' . print_r($response_array, true));
        } else {
            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                    foreach (Input::get('service') as $key) {
                        $serv = ProviderType::where('id', $key)->first();
                        $pserv[] = $serv->name;
                    }
                    foreach (Input::get('service') as $ke) {
                        $proviserv = ProviderServices::where('provider_id', $walker_id)->first();
                        if ($proviserv != NULL) {
                            DB::delete("delete from walker_services where provider_id = '" . $walker_id . "';");
                        }
                    }
                    $base_price = Input::get('service_base_price');
                    $service_price_distance = Input::get('service_price_distance');
                    $service_price_time = Input::get('service_price_time');
                    foreach (Input::get('service') as $key) {
                        $prserv = new ProviderServices;
                        $prserv->provider_id = $walker_id;
                        $prserv->type = $key;
                        $prserv->base_price = $base_price[$key - 1];
                        $prserv->price_per_unit_distance = $service_price_distance[$key - 1];
                        $prserv->price_per_unit_time = $service_price_time[$key - 1];
                        $prserv->save();
                    }
                    $response_array = array(
                        'success' => true,
                    );
                    $response_code = 200;
                    //Log::info('success = ' . print_r($response_array, true));
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }
        $response = Response::json($response_array, $response_code);
        //Log::info('repsonse final = ' . print_r($response, true));
        return $response;
    }

    public function services_details() {
        $walker_id = Input::get('id');
        $token = Input::get('token');

        $validator = Validator::make(
                        array(
                    'walker_id' => $walker_id,
                    'token' => $token,
                        ), array(
                    'walker_id' => 'required|integer',
                    'token' => 'required',
                        ), array(
                    'walker_id.required' => 20,
                    'token.required' => 24,
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                // check for token validity
                if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                    $provserv = ProviderServices::where('provider_id', $walker_id)->get();
                    foreach ($provserv as $key) {
                        $type = ProviderType::where('id', $key->type)->first();
                        $serv_name[] = $type->name;
                        $serv_base_price[] = $key->base_price;
                        $serv_per_distance[] = $key->price_per_unit_distance;
                        $serv_per_time[] = $key->price_per_unit_time;
                    }
                    $response_array = array(
                        'success' => true,
                        'serv_name' => $serv_name,
                        'serv_base_price' => $serv_base_price,
                        'serv_per_distance' => $serv_per_distance,
                        'serv_per_time' => $serv_per_time
                    );
                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 1)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
        }

    public function panic() {
        $helper = new Helper();
        $token = Input::get('token');
        $walker_id = Input::get('id');
        $is_admin = $this->isAdmin($token);
        if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
            // check for token validity
            if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                $lat = Input::get('latitude');
                $long = Input::get('longitude');
                $location = 'http://maps.google.com/maps?z=12&t=m&q=loc:lat+long';
                $location = str_replace('lat', $lat, $location);
                $location = str_replace('long', $long, $location);

                /* $var = Keywords::where('id', 1)->first(); */

                /* $email_body = '' . $var->keyword . ' id = ' . $walker_id . '. And my current location is:  <br/>' . $location; */
                $email_body = '' . \Config::get('app.generic_keywords.Provider') . ' id = ' . $walker_id . '. And my current location is:  <br/>' . $location;
                $subject = 'Panic Alert';
                email_notification($walker_id, 'admin', $email_body, $subject);
                $response_array = array('success' => true, 'is_active' => $walker_data->is_active);
                $response_code = 200;
            } else {
                $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                $response_code = 200;
            }
        } else {
            if ($is_admin) {
                /* $var = Keywords::where('id', 1)->first();
                  $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
            } else {
                $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
            }
            $response_code = 200;
                }
    }

    public function check_banking() {
        $helper = new Helper();
        $token = Input::get('token');
        $walker_id = Input::get('id');
        $is_admin = $this->isAdmin($token);
        if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
            // check for token validity
            if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                // do
                $default_banking = \Config::get('app.default_payment');
                $resp = array();
                $resp['default_banking'] = $default_banking;
                $walker = Walker::where('id', $walker_id)->first();
                if ($walker->merchant_id != NULL) {
                    $resp['walker']['merchant_id'] = $walker->merchant_id;
                }
                $response_array = array('success' => true, 'details' => $resp);
                $response_code = 200;
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function logout() {
        $helper = new Helper();
        if (Request::isMethod('post')) {
            $walker_id = Input::get('id');
            $token = Input::get('token');

            $validator = Validator::make(
                            array(
                        'walker_id' => $walker_id,
                        'token' => $token,
                            ), array(
                        'walker_id' => 'required|integer',
                        'token' => 'required',
                            ), array(
                        'walker_id.required' => 20,
                        'token.required' => 24,
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 12, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $is_admin = $this->isAdmin($token);
                if ($walker_data = $this->getWalkerData($walker_id, $token, $is_admin)) {
                    if ($helper->is_token_active($walker_data->token_expiry) || $is_admin) {
                        $walker = Walker::find($walker_id);
                        $walker->latitude = 0;
                        $walker->longitude = 0;
                        $walker->token = '';
                        $walker->old_latitude = 0;
                        $walker->old_longitude = 0;
                        $walker->device_token = 0;
                        /* $walker_data->is_login = 0; */
                        $walker->save();

                        $response_array = array('success' => true, 'error' => 40, 'error_messages' => array(40));
                        $response_code = 200;
                    } else {
                        $response_array = array('success' => false, 'error' => 24, 'error_messages' => array(24), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        $response_array = array('success' => false, 'error' => 25, 'error_messages' => array(25), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 26, 'error_messages' => array(26), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }
    
    public function get_driver_stats(){
        $walker_id = Input::get('id');
        $subQuery = DB::table('request_meta')->select(DB::raw('count(*)'))->whereRaw('walker_id = walker.id and status != 0');
        $subQuery1 = DB::table('request_meta')->select(DB::raw('count(*)'))->whereRaw('walker_id = walker.id and status=1');
        $walker = DB::table('walker')
                            ->select('walker.*', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))->where('deleted_at', NULL)
                            ->where('walker.id', $walker_id)->get()[0];
        $response_array = array('success' => true, 'driver'=> $walker);
        $response_code = 200;
        $response = Response::json($response_array, $response_code);
        return $response;
    }
  
    public function clear_offline_drivers(){
        $time_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        Walker::where('updated_at', '<', $time_ago)->update(array('is_active' => 0));
    }

    public function remove_offline_walkers(){
        DB::table('walker')
              ->where('is_available','1')
              ->update(['is_active' => '0']);
    }

    public function calculate_online_time(){
            $time_ago = date('Y-m-d H:i:s', strtotime('-1 minutes'));
            //Walker::where('updated_at', '<', $time_ago)->increment('online_time');
            DB::table('walker')->where('updated_at', '>', $time_ago)->where('is_active', '=', 1)->increment('online_time');
    }
    
    public function send_mail_test(){
        $helper = new Helper();
        /*$address = str_replace(" ", "+", "Muyenga");
        $map_key = "AIzaSyA2_jffb_zhFN3dATMcejg0xYVVnA1dX3w";
        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false&key=".$map_key);
        */
        $json = $helper->send_sms_message("0781650001", "Test 2");
        //$json = json_decode($json);
        //var_dump($json);
    }
    
    public function send_fcm_test(){
        $helper = new Helper();
        $walker = Walker::find(1);
        $data = array('message' => 'You have an incoming request','action' => 'request');
        //send_fcm_notification($walker->fcm_token, $data);
        $helper->send_notifications(3576,'owner','Test',$data, NULL);
        var_dump("St");
    }

    function Callapi($header,$method, $url, $data = false){
      $curl = curl_init();
      switch ($method){
          case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);

              if ($data)
                  curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
              break;
          case "PUT":
              curl_setopt($curl, CURLOPT_PUT, 1);
              break;
          default:
              if ($data)
                  $url = sprintf("%s?%s", $url, http_build_query($data));
      }

      # Optional Authentication:
      curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curl, CURLOPT_USERPWD, "username:password");
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $result = curl_exec($curl);
      curl_close($curl);
      return $result;
  }
}
