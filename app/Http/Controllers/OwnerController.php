<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Requests;
use App\Owner;
use App\Walker;
use App\ScheduledRequests;
use App\Icons;
use App\Theme;
use App\Settings;
use App\RequestMeta;
use App\Ledger;
use Response;
use Hash;
use Helper;

class OwnerController extends Controller
{
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

    public function login() {
        $login_by = Input::get('login_by');
        $device_token = 0;
        if (Input::has('device_token')) {
            $device_token = Input::get('device_token');
        }
        if (Input::has('fcm_token')) {
            $fcm_token = Input::get('fcm_token');
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
                                'password.required' => 28,
                                'email.required' => 29,
                                'device_token.required' => 32,
                                'device_type.required' => 33,
                                'login_by.required' => 34
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 36, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
                //Log::error('Validation error during manual login for owner = ' . print_r($error_messages, true));
            } else {
                if ($owner = Owner::where('email', '=', $email)->first()) {
                    if (Hash::check($password, $owner->password)) {
                        if ($login_by !== "manual") {
                            $response_array = array('success' => false, 'error' => 35, 'error_messages' => array(35), 'error_code' => 417);
                            $response_code = 200;
                        } else {
                            Owner::where('id', '!=', $owner->id)->where('device_token', '=', $device_token)->update(array('device_token' => 0));
                            /* if ($owner->device_type != $device_type) { */
                            $owner->device_type = $device_type;
                            /* }
                              if ($owner->device_token != $device_token) { */
                            $owner->device_token = $device_token;
                            /* } */
                            if (!isset($fcm_token)) {
                              $fcm_token = '';
                            }
                            $owner->fcm_token = $fcm_token;
                            $helper = new Helper();
                            $owner->token = $helper->generate_token();
                            $owner->token_expiry = $helper->generate_expiry();
                            $owner->save();
                            /* SEND REFERRAL & PROMO INFO */
                            $settings = Settings::where('key', 'referral_code_activation')->first();
                            $referral_code_activation = $settings->value;
                            if ($referral_code_activation) {
                                $referral_code_activation_txt = "referral on";
                            } else {
                                $referral_code_activation_txt = "referral off";
                            }

                            $settings = Settings::where('key', 'promotional_code_activation')->first();
                            $promotional_code_activation = $settings->value;
                            if ($promotional_code_activation) {
                                $promotional_code_activation_txt = "promo on";
                            } else {
                                $promotional_code_activation_txt = "promo off";
                            }
                            /* SEND REFERRAL & PROMO INFO */
                            $code_data = Ledger::where('owner_id', '=', $owner->id)->first();
                            $settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
                            $refered_user = $settings->value;
                            $settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
                            $refereel_user = $settings->value;
                            $response_array = array(
                                'success' => true,
                                'id' => $owner->id,
                                'first_name' => $owner->first_name,
                                'last_name' => $owner->last_name,
                                'phone' => $owner->phone,
                                'email' => $owner->email,
                                'picture' => $owner->picture?$owner->picture:"",
                                'bio' => $owner->bio,
                                'address' => $owner->address,
                                'state' => $owner->state,
                                'country' => $owner->country,
                                'zipcode' => $owner->zipcode,
                                'login_by' => $owner->login_by,
                                'social_unique_id' => $owner->social_unique_id,
                                'device_token' => $owner->device_token,
                                'device_type' => $owner->device_type,
                                'timezone' => $owner->timezone,
                                'token' => $owner->token,
                                'referral_code' => $code_data->referral_code,
                                'is_referee' => $owner->is_referee,
                                'promo_count' => $owner->promo_count,
                                'is_referral_active' => $referral_code_activation,
                                'is_referral_active_txt' => $referral_code_activation_txt,
                                'is_promo_active' => $promotional_code_activation,
                                'is_promo_active_txt' => $promotional_code_activation_txt,
                                'refered_user_bonus' => sprintf($refered_user, 2) . " " .\Config::get('app.generic_keywords.Currency'),
                                'refereel_user_bonus' => sprintf($refereel_user, 2) . " " .\Config::get('app.generic_keywords.Currency'),
                                'stripe_secret_key' => $stripe_secret_key,
                                'stripe_publishable_key' => $stripe_publishable_key,
                                'gcm_browser_key' => $gcm_browser_key,
                            );

                            // $dog = Dog::find($owner->dog_id);
                            /* if ($dog !== NULL) {
                              $response_array = array_merge($response_array, array(
                              'dog_id' => $dog->id,
                              'age' => $dog->age,
                              'name' => $dog->name,
                              'breed' => $dog->breed,
                              'likes' => $dog->likes,
                              'image_url' => $dog->image_url,
                              ));
                              } */

                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 36, 'error_messages' => array(36), 'error_code' => 403);
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 37, 'error_messages' => array(37), 'error_code' => 404);
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
                        'social_unique_id' => 'required|exists:owner,social_unique_id',
                        'device_token' => 'required',
                        'device_type' => 'required|in:android,ios',
                        'login_by' => 'required|in:manual,facebook,google'
                            ), array(
                        'social_unique_id.exists' => 26,
                        'device_token.required' => 32,
                        'device_type.in' => 33,
                        'login_by.in' => 34
                            )
            );

            if ($socialValidator->fails()) {
                $error_messages = $socialValidator->messages();
                //Log::error('Validation error during social login for owner = ' . print_r($error_messages, true));
                $error_messages = $socialValidator->messages()->all();
                $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                /* SEND REFERRAL & PROMO INFO */
                $settings = Settings::where('key', 'referral_code_activation')->first();
                $referral_code_activation = $settings->value;
                if ($referral_code_activation) {
                    $referral_code_activation_txt = "referral on";
                } else {
                    $referral_code_activation_txt = "referral off";
                }

                $settings = Settings::where('key', 'promotional_code_activation')->first();
                $promotional_code_activation = $settings->value;
                if ($promotional_code_activation) {
                    $promotional_code_activation_txt = "promo on";
                } else {
                    $promotional_code_activation_txt = "promo off";
                }
                /* SEND REFERRAL & PROMO INFO */
                if ($owner = Owner::where('social_unique_id', '=', $social_unique_id)->first()) {
                    if (!in_array($login_by, array('facebook', 'google'))) {
                        $response_array = array('success' => false, 'error' => 35, 'error_messages' => array(35), 'error_code' => 417);
                        $response_code = 200;
                    } else {
                        if ($owner->device_type != $device_type) {
                            $owner->device_type = $device_type;
                        }
                        if ($owner->device_token != $device_token) {
                            $owner->device_token = $device_token;
                        }
                        $owner->token_expiry = generate_expiry();
                        $owner->save();

                        $code_data = Ledger::where('owner_id', '=', $owner->id)->first();
                        $settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
                        $refered_user = $settings->value;
                        $settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
                        $refereel_user = $settings->value;
                        $response_array = array(
                            'success' => true,
                            'id' => $owner->id,
                            'first_name' => $owner->first_name,
                            'last_name' => $owner->last_name,
                            'phone' => $owner->phone,
                            'email' => $owner->email,
                            'picture' => $owner->picture?$owner->picture:"",
                            'bio' => $owner->bio,
                            'address' => $owner->address,
                            'state' => $owner->state,
                            'country' => $owner->country,
                            'zipcode' => $owner->zipcode,
                            'login_by' => $owner->login_by,
                            'social_unique_id' => $owner->social_unique_id,
                            'device_token' => $owner->device_token,
                            'device_type' => $owner->device_type,
                            'timezone' => $owner->timezone,
                            'token' => $owner->token,
                            'referral_code' => $code_data->referral_code,
                            'is_referee' => $owner->is_referee,
                            'promo_count' => $owner->promo_count,
                            'is_referral_active' => $referral_code_activation,
                            'is_referral_active_txt' => $referral_code_activation_txt,
                            'is_promo_active' => $promotional_code_activation,
                            'is_promo_active_txt' => $promotional_code_activation_txt,
                            'refered_user_bonus' => sprintf2($refered_user, 2) . " " . Config::get('app.generic_keywords.Currency'),
                            'refereel_user_bonus' => sprintf2($refereel_user, 2) . " " . Config::get('app.generic_keywords.Currency'),
                            'stripe_secret_key' => $stripe_secret_key,
                            'stripe_publishable_key' => $stripe_publishable_key,
                            'gcm_browser_key' => $gcm_browser_key,
                        );

                        /* $dog = Dog::find($owner->dog_id);
                          if ($dog !== NULL) {
                          $response_array = array_merge($response_array, array(
                          'dog_id' => $dog->id,
                          'age' => $dog->age,
                          'name' => $dog->name,
                          'breed' => $dog->breed,
                          'likes' => $dog->likes,
                          'image_url' => $dog->image_url,
                          ));
                          } */

                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 38, 'error_messages' => array(38), 'error_code' => 404);
                    $response_code = 200;
                }
            }
        } else {
            $response_array = array('success' => false, 'error' => 8, 'error_messages' => array(8), 'error_code' => 404);
            $response_code = 200;
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function register() {
        $first_name = ucwords(trim(Input::get('first_name')));
        $last_name = ucwords(trim(Input::get('last_name')));
        $email = Input::get('email');
        $phone = "";
        if (Input::has('phone')) {
            $phone = Input::get('phone');
        }    
        $password = Input::get('password');
        $picture = "";
        if (Input::hasfile('picture')) {
            $picture = Input::file('picture');
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
        $address = "";
        if (Input::has('address')) {
            $address = ucwords(trim(Input::get('address')));
        }
        $state = "";
        if (Input::has('state')) {
            $state = ucwords(trim(Input::get('state')));
        }
        $country = "";
        if (Input::has('country')) {
            $country = ucwords(trim(Input::get('country')));
        }
        $zipcode = "";
        if (Input::has('zipcode')) {
            $zipcode = Input::get('zipcode');
        }
        $login_by = Input::get('login_by');
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
                                'bio' => $bio,
                                'address' => $address,
                                'state' => $state,
                                'country' => $country,    
                                /* 'zipcode' => $zipcode, */
                                'login_by' => $login_by
                                    ), array(
                                'password' => 'required',
                                'email' => 'required|email',
                                'first_name' => 'required',
                                'last_name' => 'required',
                                /* 'picture' => 'mimes:jpeg,bmp,png', */
                                'picture' => '',
                                'device_token' => 'required',
                                'device_type' => 'required|in:android,ios',
                                'bio' => '',
                                'address' => '',
                                'state' => '',
                                'country' => '',    
                                /* 'zipcode' => 'integer', */
                                'login_by' => 'required|in:manual,facebook,google',
                            ), 
                            array(
                                'password.required' => 28,
                                'email.required' => 29,
                                'first_name.required' => 30,
                                'last_name.required' => 31,
                                /* 'picture' => 'mimes:jpeg,bmp,png', */
                                'picture' => '',
                                'device_token' => '',
                                'device_type' => '',
                                'bio' => '',      
                                'address' => '',
                                'state' => '',
                                'country' => '',
                                /* 'zipcode' => '', */
                                'login_by' => '',
                            )
            );

            $validatorPhone = Validator::make(
                            array(
                                'phone' => $phone,
                            ), 
                            array(
                        'phone' => 'phone'
                            ), 
                            array(
                        'phone.phone' => 25
                            )
            );
        } elseif ($social_unique_id != "" and $password == "") {
            $validator = Validator::make(
                            array(
                                'email' => $email,
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'picture' => $picture,
                                'device_token' => $device_token,
                                'device_type' => $device_type,
                                'bio' => $bio,
                                'address' => $address,
                                'state' => $state,
                                'country' => $country,
                                'zipcode' => $zipcode,
                                'login_by' => $login_by,
                                'social_unique_id' => $social_unique_id
                                    ), array(
                                'email' => 'required|email',
                                'first_name' => 'required',
                                'last_name' => 'required',
                                /* 'picture' => 'mimes:jpeg,bmp,png', */
                                'picture' => '',
                                'device_token' => 'required',
                                'device_type' => 'required|in:android,ios',
                                'bio' => '',
                                'address' => '',
                                'state' => '',
                                'country' => '',
                                'zipcode' => 'integer',
                                'login_by' => 'required|in:manual,facebook,google',
                                'social_unique_id' => 'required|unique:owner'
                            ), 
                            array(
                                'email.required' => 29,
                                'first_name.required' => 30,
                                'last_name.required' => 31,
                                /* 'picture' => 'mimes:jpeg,bmp,png', */
                                'picture' => '',
                                'device_token' => '',
                                'device_type' => '',
                                'bio' => '',
                                'address' => '',
                                'state' => '',
                                'country' => '',
                                'zipcode' => '',
                                'login_by' => '',
                                'social_unique_id.unique' => 104
                            )
            );

            $validatorPhone = Validator::make(
                            array(
                                 'phone' => $phone,
                            ), 
                            array(
                                 'phone' => 'phone',
                            ), 
                            array(
                                 'phone.phone' => 25,
                            )
            );
        } elseif ($social_unique_id != "" and $password != "") {
            $response_array = array('success' => false, 'error' => 8, 'error_messages' => array(8), 'error_code' => 401);
            $response_code = 200;
            goto response;
        }

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();

            //Log::info('Error while during owner registration = ' . print_r($error_messages, true));
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else if ($validatorPhone->fails()) {
           
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 24, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {

            
            if (Owner::onlyTrashed()->where('email', '=', $email)->first()) {
                $response_array = array('success' => false, 'error' => 105, 'error_messages' => array(105), 'error_code' => 430);
                $response_code = 200;
            } else if (Owner::where('email', '=', $email)->first()) {
                $response_array = array('success' => false, 'error' => 27, 'error_messages' => array(27), 'error_code' => 430);
                $response_code = 200;
            } else {
                $settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
                $refered_user = $settings->value;
                $settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
                $refereel_user = $settings->value;
                /* SEND REFERRAL & PROMO INFO */
                $settings = Settings::where('key', 'referral_code_activation')->first();
                $referral_code_activation = $settings->value;
                if ($referral_code_activation) {
                    $referral_code_activation_txt = "referral on";
                } else {
                    $referral_code_activation_txt = "referral off";
                }

                $settings = Settings::where('key', 'promotional_code_activation')->first();
                $promotional_code_activation = $settings->value;
                if ($promotional_code_activation) {
                    $promotional_code_activation_txt = "promo on";
                } else {
                    $promotional_code_activation_txt = "promo off";
                }
                /* SEND REFERRAL & PROMO INFO */
                Owner::where('device_token', '=', $device_token)->update(array('device_token' => 0));
                $owner = new Owner;
                $owner->first_name = $first_name;
                $owner->last_name = $last_name;
                $owner->email = $email;
                
                $owner->phone = $phone;
                if ($password != "") {
                    $owner->password = Hash::make($password);
                }
                $owner->token = generate_token();
                $owner->token_expiry = generate_expiry();

                // upload image
                $file_name = time();
                $file_name .= rand();
                $file_name = sha1($file_name);
                if ($picture) {
                    $ext = Input::file('picture')->getClientOriginalExtension();
                    Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
                    $local_url = $file_name . "." . $ext;

                    // Upload to S3
                    if (Config::get('app.s3_bucket') != "") {
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
                        $s3_url = web_url() . '/uploads/' . $local_url;
                    }
                    $owner->picture = $s3_url;
                }
                $owner->device_token = $device_token;
                $owner->device_type = $device_type;
                $owner->bio = "";
                if (Input::has('bio'))
                    $owner->bio = $bio;
                $owner->address = "";
                if (Input::has('address'))
                    $owner->address = $address;
                $owner->state = "";
                if (Input::has('state'))
                    $owner->state = $state;
                $owner->login_by = $login_by;
                $owner->country = "";
                if (Input::has('country'))
                    $owner->country = $country;
                $owner->zipcode = "0";
                if (Input::has('zipcode'))
                    $owner->zipcode = $zipcode;
                if ($social_unique_id != "") {
                    $password = my_random6_number();
                    $owner->social_unique_id = $social_unique_id;
                    $owner->password = Hash::make($password);
                }
                $owner->timezone = 'UTC';
                If (Input::has('timezone')) {
                    $owner->timezone = Input::get('timezone');
                }
                $owner->is_referee = 0;
                $owner->promo_count = 0;
                $owner->save();


                /* $zero_in_code = Config::get('app.referral_zero_len') - strlen($owner->id);
                  $referral_code = Config::get('app.referral_prefix');
                  for ($i = 0; $i < $zero_in_code; $i++) {
                  $referral_code .= "0";
                  }
                  $referral_code .= $owner->id; */
                regenerate:
                $referral_code = my_random6_number();
                if (Ledger::where('referral_code', $referral_code)->count()) {
                    goto regenerate;
                }
                /* Referral entry */
                $ledger = new Ledger;
                $ledger->owner_id = $owner->id;
                $ledger->referral_code = $referral_code;
                $ledger->save();
                if ($social_unique_id != "") {
                    $pattern = "Hello... ! " . ucwords($first_name) . " . Your " . Config::get('app.website_title') . " Web Login Password is : " . $password;
                    sms_notification($owner->id, 'owner', $pattern);
                    $subject = "Your " . Config::get('app.website_title') . " Web Login Password";
                    email_notification($owner->id, 'owner', $pattern, $subject);
                }
                /* Referral entry end */

                // send email
                /* $subject = "Welcome On Board";
                  $email_data['name'] = $owner->first_name;

                  send_email($owner->id, 'owner', $email_data, $subject, 'userregister'); */

                if ($owner->picture == NULL) {
                    $owner_picture = "";
                } else {
                    $owner_picture = $owner->picture;
                }
                if ($owner->bio == NULL) {
                    $owner_bio = "";
                } else {
                    $owner_bio = $owner->bio;
                }
                if ($owner->address == NULL) {
                    $owner_address = "";
                } else {
                    $owner_address = $owner->address;
                }
                if ($owner->state == NULL) {
                    $owner_state = "";
                } else {
                    $owner_state = $owner->state;
                }
                if ($owner->country == NULL) {
                    $owner_country = "";
                } else {
                    $owner_country = $owner->country;
                }
                if ($owner->zipcode == NULL) {
                    $owner_zipcode = "";
                } else {
                    $owner_zipcode = $owner->zipcode;
                }
                if ($owner->timezone == NULL) {
                    $owner_time = Config::get('app.timezone');
                } else {
                    $owner_time = $owner->timezone;
                }

                $stripe_secret_key = Config::get('app.stripe_secret_key');
                $stripe_publishable_key = Config::get('app.stripe_publishable_key');
                $gcm_browser_key = Config::get('app.gcm_browser_key');

                $settings = Settings::where('key', 'contact_us_email')->first();
                $admin_email = $settings->value;
                $pattern = array('contact_us_email' => $admin_email, 'name' => ucwords($owner->first_name . " " . $owner->last_name), 'web_url' => web_url());
                $subject = "Welcome to". ucwords(Config::get('app.website_title')) . ", " . ucwords($owner->first_name . " " . $owner->last_name) . "";
                email_notification($owner->id, 'owner', $pattern, $subject, 'user_register', null);
                $response_array = array(
                    'success' => true,
                    'id' => $owner->id,
                    'first_name' => $owner->first_name,
                    'last_name' => $owner->last_name,
                    'phone' => $owner->phone,
                    'email' => $owner->email,
                    'picture' => $owner_picture,
                    'bio' => $owner_bio,
                    'address' => $owner_address,
                    'state' => $owner_state,
                    'country' => $owner_country,
                    'zipcode' => $owner_zipcode,
                    'login_by' => $owner->login_by,
                    'social_unique_id' => $owner->social_unique_id ? $owner->social_unique_id : "",
                    'device_token' => $owner->device_token,
                    'device_type' => $owner->device_type,
                    'timezone' => $owner_time,
                    'token' => $owner->token,
                    'referral_code' => $referral_code,
                    'is_referee' => $owner->is_referee,
                    'promo_count' => $owner->promo_count,
                    'is_referral_active' => $referral_code_activation,
                    'is_referral_active_txt' => $referral_code_activation_txt,
                    'is_promo_active' => $promotional_code_activation,
                    'is_promo_active_txt' => $promotional_code_activation_txt,
                    'refered_user_bonus' => sprintf2($refered_user, 2) . " " . Config::get('app.generic_keywords.Currency'),
                    'refereel_user_bonus' => sprintf2($refereel_user, 2) . " " . Config::get('app.generic_keywords.Currency'),
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

    public function details() {
        if (Request::isMethod('post')) {
            $address = Input::get('address');
            $state = Input::get('state');
            $zipcode = Input::get('zipcode');
            $token = Input::get('token');
            $owner_id = Input::get('id');

            $validator = Validator::make(
                            array(
                        'address' => $address,
                        'state' => $state,
                        'zipcode' => $zipcode,
                        'token' => $token,
                        'owner_id' => $owner_id,
                            ), array(
                        'address' => 'required',
                        'state' => 'required',
                        'zipcode' => 'required|integer',
                        'token' => 'required',
                        'owner_id' => 'required|integer'
                            ), array(
                        'address' => '',
                        'state' => '',
                        'zipcode' => '',
                        'token.required' => 5,
                        'owner_id.required' => 6
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $is_admin = $this->isAdmin($token);
                if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                    // check for token validity
                    if (is_token_active($owner_data->token_expiry) || $is_admin) {
                        // Do necessary operations

                        $owner = Owner::find($owner_data->id);
                        $owner->address = $address;
                        $owner->state = $state;
                        $owner->zipcode = $zipcode;
                        $owner->save();

                        $response_array = array('success' => true);
                        $response_code = 200;
                    } else {
                        $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $var = Keywords::where('id', 2)->first();
                          $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        } else {
            //handles get request
            $token = Input::get('token');
            $owner_id = Input::get('id');
            $validator = Validator::make(
                            array(
                        'token' => $token,
                        'owner_id' => $owner_id,
                            ), array(
                        'token' => 'required',
                        'owner_id' => 'required|integer'
                            ), array(
                        'token.required' => 5,
                        'owner_id.required' => 6
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {

                $is_admin = $this->isAdmin($token);
                if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                    // check for token validity
                    if (is_token_active($owner_data->token_expiry) || $is_admin) {

                        $response_array = array(
                            'success' => true,
                            'address' => $owner_data->address,
                            'state' => $owner_data->state,
                            'zipcode' => $owner_data->zipcode,
                        );
                        $response_code = 200;
                    } else {
                        $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        /* $var = Keywords::where('id', 2)->first();
                          $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                        $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function addcardtoken() {
        $payment_token = Input::get('payment_token');
        $last_four = Input::get('last_four');
        $token = Input::get('token');
        $owner_id = Input::get('id');
        if (Input::has('card_type')) {
            $card_type = strtoupper(Input::get('card_type'));
        } else {
            $card_type = strtoupper("VISA");
        }
        $validator = Validator::make(
                        array(
                    'last_four' => $last_four,
                    'payment_token' => $payment_token,
                    'token' => $token,
                    'owner_id' => $owner_id,
                        ), array(
                    'last_four' => 'required',
                    'payment_token' => 'required',
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    'last_four.required' => 39,
                    'payment_token.required' => 40,
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );
        $payments = array();

        if ($validator->fails()) {
            $error_messages = $validator->messages();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages, 'payments' => $payments);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {

                    try {

                        if (Config::get('app.default_payment') == 'stripe') {
                            Stripe::setApiKey(Config::get('app.stripe_secret_key'));

                            $customer = Stripe_Customer::create(array(
                                        "card" => $payment_token,
                                        "description" => $owner_data->email)
                            );
                            /* Log::info('customer = ' . print_r($customer, true)); */
                            if ($customer) {
                                $card_count = DB::table('payment')->where('owner_id', '=', $owner_id)->count();

                                $customer_id = $customer->id;
                                $payment = new Payment;
                                $payment->owner_id = $owner_id;
                                $payment->customer_id = $customer_id;
                                $payment->last_four = $last_four;
                                $payment->card_type = $card_type;
                                $payment->card_token = $customer->sources->data[0]->id;
                                if ($card_count > 0) {
                                    $payment->is_default = 0;
                                } else {
                                    $payment->is_default = 1;
                                }
                                $payment->save();

                                $payment_data = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                                foreach ($payment_data as $data1) {
                                    $default = $data1->is_default;
                                    if ($default == 1) {
                                        $data['is_default_text'] = "default";
                                    } else {
                                        $data['is_default_text'] = "not_default";
                                    }
                                    $data['id'] = $data1->id;
                                    $data['owner_id'] = $data1->owner_id;
                                    $data['customer_id'] = $data1->customer_id;
                                    $data['last_four'] = $data1->last_four;
                                    $data['card_token'] = $data1->card_token;
                                    $data['card_type'] = $data1->card_type;
                                    $data['card_id'] = $data1->card_token;
                                    $data['is_default'] = $default;
                                    array_push($payments, $data);
                                }
                                $response_array = array(
                                    'success' => true,
                                    'payments' => $payments,
                                );
                                $response_code = 200;
                            } else {
                                $payment_data = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                                foreach ($payment_data as $data1) {
                                    $default = $data1->is_default;
                                    if ($default == 1) {
                                        $data['is_default_text'] = "default";
                                    } else {
                                        $data['is_default_text'] = "not_default";
                                    }
                                    $data['id'] = $data1->id;
                                    $data['owner_id'] = $data1->owner_id;
                                    $data['customer_id'] = $data1->customer_id;
                                    $data['last_four'] = $data1->last_four;
                                    $data['card_token'] = $data1->card_token;
                                    $data['card_type'] = $data1->card_type;
                                    $data['card_id'] = $data1->card_token;
                                    $data['is_default'] = $default;
                                    array_push($payments, $data);
                                }
                                $response_array = array(
                                    'success' => false,
                                    'error' => 41,
                                    'error_messages' => array(41),
                                    'error_code' => 450,
                                    'payments' => $payments,
                                );
                                $response_code = 200;
                            }
                        } else {
                            Braintree_Configuration::environment(Config::get('app.braintree_environment'));
                            Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
                            Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
                            Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
                            $result = Braintree_Customer::create(array(
                                        'paymentMethodNonce' => $payment_token
                            ));
                            //Log::info('result = ' . print_r($result, true));
                            if ($result->success) {
                                $card_count = DB::table('payment')->where('owner_id', '=', $owner_id)->count();

                                $customer_id = $result->customer->id;
                                $payment = new Payment;
                                $payment->owner_id = $owner_id;
                                $payment->customer_id = $customer_id;
                                $payment->last_four = $last_four;
                                $payment->card_type = $card_type;
                                $payment->card_token = $result->customer->creditCards[0]->token;
                                if ($card_count > 0) {
                                    $payment->is_default = 0;
                                } else {
                                    $payment->is_default = 1;
                                }
                                $payment->save();

                                $payment_data = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                                foreach ($payment_data as $data1) {
                                    $default = $data1->is_default;
                                    if ($default == 1) {
                                        $data['is_default_text'] = "default";
                                    } else {
                                        $data['is_default_text'] = "not_default";
                                    }
                                    $data['id'] = $data1->id;
                                    $data['owner_id'] = $data1->owner_id;
                                    $data['customer_id'] = $data1->customer_id;
                                    $data['last_four'] = $data1->last_four;
                                    $data['card_token'] = $data1->card_token;
                                    $data['card_type'] = $data1->card_type;
                                    $data['card_id'] = $data1->card_token;
                                    $data['is_default'] = $default;
                                    array_push($payments, $data);
                                }

                                $response_array = array(
                                    'success' => true,
                                    'payments' => $payments,
                                );
                                $response_code = 200;
                            } else {
                                $payment_data = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                                foreach ($payment_data as $data1) {
                                    $default = $data1->is_default;
                                    if ($default == 1) {
                                        $data['is_default_text'] = "default";
                                    } else {
                                        $data['is_default_text'] = "not_default";
                                    }
                                    $data['id'] = $data1->id;
                                    $data['owner_id'] = $data1->owner_id;
                                    $data['customer_id'] = $data1->customer_id;
                                    $data['last_four'] = $data1->last_four;
                                    $data['card_token'] = $data1->card_token;
                                    $data['card_type'] = $data1->card_type;
                                    $data['card_id'] = $data1->card_token;
                                    $data['is_default'] = $default;
                                    array_push($payments, $data);
                                }
                                $response_array = array(
                                    'success' => false,
                                    'error' => 41,
                                    'error_messages' => array(41),
                                    'error_code' => 450,
                                    'payments' => $payments,
                                );
                                $response_code = 200;
                            }
                        }
                    } catch (Exception $e) {
                        $response_array = array('success' => false, 'error' => $e, 'error_messages' => array(41), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function deletecardtoken() {
        $card_id = Input::get('card_id');
        $token = Input::get('token');
        $owner_id = Input::get('id');

        $validator = Validator::make(
                        array(
                    'card_id' => $card_id,
                    'token' => $token,
                    'owner_id' => $owner_id,
                        ), array(
                    'card_id' => 'required',
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    'card_id.required' => 42,
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );

        /* $var = Keywords::where('id', 2)->first(); */

        if ($validator->fails()) {
            $error_messages = $validator->messages();
            $response_array = array('success' => false, 'error' => 8, 'error_messages' => $error_messages, 'error_code' => 401);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    if ($payment = Payment::find($card_id)) {
                        if ($payment->owner_id == $owner_id) {
                            $flg = 0;
                            $card_count = DB::table('payment')->where('owner_id', '=', $owner_id)->count();
                            if ($request = Requests::where('owner_id', '=', $owner_id)->where('confirmed_walker', '!=', 0)->where('status', '=', 1)->where('is_completed', '=', 0)->where('is_cancelled', '=', 0)->orderBy('created_at', 'desc')->first()) {
                                if (isset($request->id)) {
                                    if ($card_count > 1) {
                                        $flg = 0;
                                    } else {
                                        $flg = 1;
                                    }
                                } else {
                                    $flg = 0;
                                }
                            } else {
                                $flg = 0;
                            }
                            if ($flg == 0) {
                                if (Config::get('app.default_payment') == 'stripe') {
                                    Stripe::setApiKey(Config::get('app.stripe_secret_key'));
                                    try {
                                        $get_customer = Stripe_Customer::retrieve($payment->customer_id);
                                        $get_customer->delete();
                                    } catch (Exception $e) {
                                        
                                    }
                                }
                                if (Config::get('app.default_payment') == 'braintree') {
                                    Braintree_Configuration::environment(Config::get('app.braintree_environment'));
                                    Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
                                    Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
                                    Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
                                    try {
                                        $get_customer = Braintree_Customer::delete($payment->customer_id);
                                    } catch (Exception $e) {
                                        
                                    }
                                }

                                $pdn = Payment::where('id', $card_id)->first();
                                $check = trim($pdn->is_default);
                                Payment::find($card_id)->delete();
                                if ($check == 1) {
                                    $card_count = DB::table('payment')->where('owner_id', '=', $owner_id)->count();
                                    if ($card_count) {
                                        $paymnt = Payment::where('owner_id', $owner_id)->first();
                                        $paymnt->is_default = 1;
                                        $paymnt->save();
                                    }
                                }

                                $payments = array();
                                $card_count = DB::table('payment')->where('owner_id', '=', $owner_id)->count();
                                if ($card_count) {
                                    $paymnt = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                                    /* foreach ($paymnt as $data1) {
                                      $default = $data1->is_default;
                                      if ($default == 1) {
                                      $data['is_default_text'] = "default";
                                      } else {
                                      $data['is_default_text'] = "not_default";
                                      }
                                      $data['id'] = $data1->id;
                                      $data['customer_id'] = $data1->customer_id;
                                      $data['card_id'] = $data1->card_token;
                                      $data['last_four'] = $data1->last_four;
                                      $data['is_default'] = $default;
                                      array_push($payments, $data);
                                      } */
                                    foreach ($paymnt as $data1) {
                                        $default = $data1->is_default;
                                        if ($default == 1) {
                                            $data['is_default_text'] = "default";
                                        } else {
                                            $data['is_default_text'] = "not_default";
                                        }
                                        $data['id'] = $data1->id;
                                        $data['owner_id'] = $data1->owner_id;
                                        $data['customer_id'] = $data1->customer_id;
                                        $data['last_four'] = $data1->last_four;
                                        $data['card_token'] = $data1->card_token;
                                        $data['card_type'] = $data1->card_type;
                                        $data['card_id'] = $data1->card_token;
                                        $data['is_default'] = $default;
                                        array_push($payments, $data);
                                    }
                                    $response_array = array(
                                        'success' => true,
                                        'payments' => $payments,
                                    );
                                    $response_code = 200;
                                } else {
                                    $response_code = 200;
                                    $response_array = array(
                                        'success' => true,
                                        'error' => 46,
                                        'error_messages' => array(46),
                                        'error_code' => 541,
                                    );
                                }
                            } else {
                                $response_array = array('success' => false, 'error' => 21, 'error_messages' => array(21), 'error_code' => 440);
                                $response_code = 200;
                            }
                        } else {
                            /* $response_array = array('success' => false, 'error' => 'Card ID and ' . $var->keyword . ' ID Doesnot matches', 'error_code' => 440); */
                            $response_array = array('success' => false, 'error' => 43, 'error_messages' => array(43), 'error_code' => 440);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 44, 'error_messages' => array(44), 'error_code' => 441);
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function set_referral_code() {
        $code = Input::get('referral_code');
        $token = Input::get('token');
        $owner_id = Input::get('id');

        $validator = Validator::make(
                        array(
                    /* 'code' => $code, */
                    'token' => $token,
                    'owner_id' => $owner_id,
                        ), array(
                    /* 'code' => 'required', */
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    /* 'code' => 'required', */
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    // Do necessary operations

                    /* $ledger_count = Ledger::where('referral_code', $code)->count();
                      if ($ledger_count > 0) {
                      $response_array = array('success' => false, 'error' => 'This Code already is taken by another user', 'error_messages' => array('This Code already is taken by another user'), 'error_code' => 484);
                      } else {
                      $led = Ledger::where('owner_id', $owner_id)->first();
                      if ($led) {
                      $ledger = Ledger::where('owner_id', $owner_id)->first();
                      } else {
                      $ledger = new Ledger;
                      $ledger->owner_id = $owner_id;
                      }
                      $ledger->referral_code = $code;
                      $ledger->save();

                      $response_array = array('success' => true);
                      } */
                    /* $zero_in_code = Config::get('app.referral_zero_len') - strlen($owner_id);
                      $referral_code = Config::get('app.referral_prefix');
                      for ($i = 0; $i < $zero_in_code; $i++) {
                      $referral_code .= "0";
                      }
                      $referral_code .= $owner_id; */
                    regenerate:
                    $referral_code = my_random6_number();
                    if (Ledger::where('referral_code', $referral_code)->count()) {
                        goto regenerate;
                    }
                    /* $referral_code .= my_random6_number(); */
                    if (Ledger::where('owner_id', $owner_id)->count()) {
                        Ledger::where('owner_id', $owner_id)->update(array('referral_code' => $referral_code));
                    } else {
                        $ledger = new Ledger;
                        $ledger->owner_id = $owner_id;
                        $ledger->referral_code = $referral_code;
                        $ledger->save();
                    }
                    /* $ledger = Ledger::where('owner_id', $owner_id)->first();
                      $ledger->referral_code = $code;
                      $ledger->save(); */
                    /* SEND REFERRAL & PROMO INFO */
                    $settings = Settings::where('key', 'referral_code_activation')->first();
                    $referral_code_activation = $settings->value;
                    if ($referral_code_activation) {
                        $referral_code_activation_txt = "referral on";
                    } else {
                        $referral_code_activation_txt = "referral off";
                    }

                    $settings = Settings::where('key', 'promotional_code_activation')->first();
                    $promotional_code_activation = $settings->value;
                    if ($promotional_code_activation) {
                        $promotional_code_activation_txt = "promo on";
                    } else {
                        $promotional_code_activation_txt = "promo off";
                    }
                    /* SEND REFERRAL & PROMO INFO */
                    $response_array = array(
                        'success' => true,
                        'id' => $owner_data->id,
                        'first_name' => $owner_data->first_name,
                        'last_name' => $owner_data->last_name,
                        'phone' => $owner_data->phone,
                        'email' => $owner_data->email,
                        'picture' => $owner_data->picture,
                        'bio' => $owner_data->bio,
                        'address' => $owner_data->address,
                        'state' => $owner_data->state,
                        'country' => $owner_data->country,
                        'zipcode' => $owner_data->zipcode,
                        'login_by' => $owner_data->login_by,
                        'social_unique_id' => $owner_data->social_unique_id,
                        'device_token' => $owner_data->device_token,
                        'device_type' => $owner_data->device_type,
                        'timezone' => $owner_data->timezone,
                        'token' => $owner_data->token,
                        'referral_code' => $referral_code,
                        'is_referee' => $owner_data->is_referee,
                        'promo_count' => $owner_data->promo_count,
                        'is_referral_active' => $referral_code_activation,
                        'is_referral_active_txt' => $referral_code_activation_txt,
                        'is_promo_active' => $promotional_code_activation,
                        'is_promo_active_txt' => $promotional_code_activation_txt,
                    );

                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function get_referral_code() {

        $token = Input::get('token');                                                                              
        $owner_id = Input::get('id');

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
            $refered_user = $settings->value;
            $settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
            $refereel_user = $settings->value;
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    // Do necessary operations

                    $ledger = Ledger::where('owner_id', $owner_id)->first();
                    if ($ledger) {
                        $response_array = array(
                            'success' => true,
                            'referral_code' => $ledger->referral_code,
                            'total_referrals' => $ledger->total_referrals,
                            'amount_earned' => $ledger->amount_earned,
                            'amount_spent' => $ledger->amount_spent,
                            'balance_amount' => $ledger->amount_earned - $ledger->amount_spent,
                            'currency' => Config::get('app.currency_symb'),
                            'refered_user_bonus' => sprintf2($refered_user, 2) . " " . Config::get('app.generic_keywords.Currency'),
                            'refereel_user_bonus' => sprintf2($refereel_user, 2) . " " . Config::get('app.generic_keywords.Currency'),
                        );
                    } else {
                        $response_array = array('success' => false, 'error' => 45, 'error_messages' => array(45));
                    }


                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function get_cards() {

        $token = Input::get('token');
        $owner_id = Input::get('id');
        if (Input::has('card_id')) {
            $card_id = Input::get('card_id');
            Payment::where('owner_id', $owner_id)->update(array('is_default' => 0));
            Payment::where('owner_id', $owner_id)->where('id', $card_id)->update(array('is_default' => 1));
        }

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    // Do necessary operations
                    $payments = array();
                    $card_count = DB::table('payment')->where('owner_id', '=', $owner_id)->count();
                    if ($card_count) {
                        $paymnt = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                        foreach ($paymnt as $data1) {
                            $default = $data1->is_default;
                            if ($default == 1) {
                                $data['is_default_text'] = "default";
                            } else {
                                $data['is_default_text'] = "not_default";
                            }
                            $data['id'] = $data1->id;
                            $data['owner_id'] = $data1->owner_id;
                            $data['customer_id'] = $data1->customer_id;
                            $data['last_four'] = $data1->last_four;
                            $data['card_token'] = $data1->card_token;
                            $data['card_type'] = $data1->card_type;
                            $data['card_id'] = $data1->card_token;
                            $data['is_default'] = $default;
                            array_push($payments, $data);
                        }
                        $response_array = array(
                            'success' => true,
                            'payments' => $payments
                        );
                    } else {
                        $response_array = array(
                            'success' => false,
                            'error' => 46,
                            'error_messages' => array(46),
                        );
                    }


                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function card_selection() {

        $token = Input::get('token');
        $owner_id = Input::get('id');
        $default_card_id = Input::get('default_card_id');
        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                    'default_card_id' => $default_card_id,
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer',
                    'default_card_id' => 'required'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6,
                    'default_card_id.required' => 42
                        )
        );
        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $payments = array();
            /* $payments['none'] = ""; */
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {

                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    // Do necessary operations
                    Payment::where('owner_id', $owner_id)->update(array('is_default' => 0));
                    Payment::where('owner_id', $owner_id)->where('id', $default_card_id)->update(array('is_default' => 1));
                    $payment_data = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                    foreach ($payment_data as $data1) {
                        $default = $data1->is_default;
                        if ($default == 1) {
                            $data['is_default_text'] = "default";
                        } else {
                            $data['is_default_text'] = "not_default";
                        }
                        $data['id'] = $data1->id;
                        $data['owner_id'] = $data1->owner_id;
                        $data['customer_id'] = $data1->customer_id;
                        $data['last_four'] = $data1->last_four;
                        $data['card_token'] = $data1->card_token;
                        $data['card_type'] = $data1->card_type;
                        $data['is_default'] = $default;
                        array_push($payments, $data);
                    }
                    $owner = Owner::find($owner_id);

                    $response_array = array(
                        'success' => true,
                        'id' => $owner->id,
                        'first_name' => $owner->first_name,
                        'last_name' => $owner->last_name,
                        'phone' => $owner->phone,
                        'email' => $owner->email,
                        'picture' => $owner->picture,
                        'bio' => $owner->bio,
                        'address' => $owner->address,
                        'state' => $owner->state,
                        'country' => $owner->country,
                        'zipcode' => $owner->zipcode,
                        'login_by' => $owner->login_by,
                        'social_unique_id' => $owner->social_unique_id,
                        'device_token' => $owner->device_token,
                        'device_type' => $owner->device_type,
                        'token' => $owner->token,
                        'default_card_id' => $default_card_id,
                        'payment_type' => 0,
                        'is_referee' => $owner->is_referee,
                        'promo_count' => $owner->promo_count,
                        'payments' => $payments
                    );



                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    $response_array = array('success' => false, 'error' => 10, 'error_messages' => array(10), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function get_completed_requests() {

        $token = Input::get('token');
        $owner_id = Input::get('id');
        $from = Input::get('from_date'); // 2015-03-11 07:45:01
        $to_date = Input::get('to_date'); //2015-03-11 07:45:01
        $to_date = date('Y-m-d', strtotime($to_date . "+1 days"));

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    // Do necessary operations
                    if ($from != "" && $to_date != "") {
                        $request_data = DB::table('request')
                                ->where('request.owner_id', $owner_id)
                                ->where('is_completed', 1)
                                //->where('is_cancelled', 0)
                                ->whereBetween('request_start_time', array($from, $to_date))
                                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                                ->leftJoin('walker_services', 'walker.id', '=', 'walker_services.provider_id')
                                ->leftJoin('walker_type', 'walker_type.id', '=', 'walker_services.type')
                                ->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
                                ->select('request.*', 'request.request_start_time', 'request.promo_code', 'walker.first_name', 'walker.id as walker_id', 'walker.last_name', 'walker.phone', 'walker.email', 'walker.picture', 'walker.bio', 'walker.rate', 'walker_type.name as type', 'walker_type.icon', 'request.distance', 'request.time', 'request_services.base_price as req_base_price', 'request_services.distance_cost as req_dis_cost', 'request_services.time_cost as req_time_cost', 'request_services.type as req_typ', 'request.total')
                                ->groupBy('request.id')
                                ->get();
                    } else {
                        $request_data = DB::table('request')
                                ->where('request.owner_id', $owner_id)
                                ->where('is_completed', 1)
                                //->where('is_cancelled', 0)
                                ->leftJoin('walker', 'request.confirmed_walker', '=', 'walker.id')
                                ->leftJoin('walker_services', 'walker.id', '=', 'walker_services.provider_id')
                                ->leftJoin('walker_type', 'walker_type.id', '=', 'walker_services.type')
                                ->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
                                ->select('request.*', 'request.request_start_time', 'request.promo_code', 'walker.first_name', 'walker.id as walker_id', 'walker.last_name', 'walker.phone', 'walker.email', 'walker.picture', 'walker.bio', 'walker.rate', 'walker_type.name as type', 'walker_type.icon', 'request.distance', 'request.time', 'request_services.base_price as req_base_price', 'request_services.distance_cost as req_dis_cost', 'request_services.time_cost as req_time_cost', 'request_services.type as req_typ', 'request.total')
                                ->groupBy('request.id')
                                ->get();
                    }

                    $requests = array();

                    $settings = Settings::where('key', 'default_distance_unit')->first();
                    $unit = $settings->value;
                    if ($unit == 0) {
                        $unit_set = 'kms';
                    } elseif ($unit == 1) {
                        $unit_set = 'miles';
                    }

                    /* $currency_selected = Keywords::find(5); */
                    foreach ($request_data as $data) {
                        $request_typ = ProviderType::where('id', '=', $data->req_typ)->first();

                        /* $setbase_price = Settings::where('key', 'base_price')->first();
                          $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
                          $settime_price = Settings::where('key', 'price_per_unit_time')->first(); */
                        $setbase_distance = $request_typ->base_distance;
                        $setbase_price = $request_typ->base_price;
                        $setdistance_price = $request_typ->price_per_unit_distance;
                        $settime_price = $request_typ->price_per_unit_time;

                        $locations = WalkLocation::where('request_id', $data->id)->orderBy('id')->get();
                        $count = round(count($locations) / 50);
                        $start = $end = $map = "";
                        $id = $data->id;
                        if (count($locations) >= 1) {
                            $start = WalkLocation::where('request_id', $id)
                                    ->orderBy('id')
                                    ->first();
                            $end = WalkLocation::where('request_id', $id)
                                    ->orderBy('id', 'desc')
                                    ->first();
                            $map = "https://maps-api-ssl.google.com/maps/api/staticmap?key=".Config::get('app.gcm_browser_key')."&size=600x250&scale=2&markers=shadow:true|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-start@2x.png|$start->latitude,$start->longitude&markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-finish@2x.png|$end->latitude,$end->longitude&path=color:0x2dbae4ff|weight:4";
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

                        $walker = Walkers::where('id', $data->walker_id)->first();

                        if ($walker != NULL) {
                            $user_timezone = $walker->timezone;
                        } else {
                            $user_timezone = 'UTC';
                        }

                        $default_timezone = Config::get('app.timezone');

                        $date_time = get_user_time($default_timezone, $user_timezone, $data->request_start_time);

                        $dist = number_format($data->distance, 2, '.', '');
                        $request['id'] = $data->id;
                        $request['date'] = $date_time;
                        $request['distance'] = (string) $dist;
                        $request['unit'] = $unit_set;
                        $request['time'] = $data->time;
                        $discount = 0;
                        if ($data->promo_code != "") {
                            if ($data->promo_code != "") {
                                $promo_code = PromoCodes::where('id', $data->promo_code)->first();
                                if ($promo_code) {
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
                        }

                        $request['promo_discount'] = currency_converted($discount);

                        $is_multiple_service = Settings::where('key', 'allow_multiple_service')->first();
                        if ($is_multiple_service->value == 0) {

                            $request['base_price'] = currency_converted($data->req_base_price);

                            $request['distance_cost'] = currency_converted($data->req_dis_cost);


                            $request['time_cost'] = currency_converted($data->req_time_cost);

                            $request['setbase_distance'] = $setbase_distance;
                            $request['total'] = currency_converted($data->total);
                            $request['actual_total'] = currency_converted($data->total + $data->ledger_payment + $discount);
                            $request['type'] = $data->type;
                            $request['type_icon'] = $data->icon;
                        } else {
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
                                $typs['price'] = currency_converted($typp1);
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
                            $request['base_price'] = currency_converted($base_price);
                            $request['distance_cost'] = currency_converted($distance_cost);
                            $request['time_cost'] = currency_converted($time_cost);
                            $request['total'] = currency_converted($total_price);
                        }

                        $pt_new = ProviderType::where('id', $walker->type)->first();

                        $ps_new = ProviderServices::where('id', $walker->type)->first();

                        if ($pt_new->base_price != 0) {

                            $request['price_per_unit_distance'] = currency_converted($pt_new->price_per_unit_distance);
                            $request['price_per_unit_time'] = currency_converted($pt_new->price_per_unit_time);
                        } else {

                            $request['price_per_unit_distance'] = currency_converted($ps_new->price_per_unit_distance);

                            $request['price_per_unit_time'] = currency_converted($ps_new->price_per_unit_time);
                        }


                        $rate = WalkerReview::where('request_id', $data->id)->where('walker_id', $data->confirmed_walker)->first();
                        if ($rate != NULL) {
                            $request['walker']['rating'] = $rate->rating;
                        } else {
                            $request['walker']['rating'] = '0.0';
                        }






                        /* $request['currency'] = $currency_selected->keyword; */
                        $request['src_address'] = $data->src_address;
                        $request['dest_address'] = $data->dest_address;
                        $request['base_price'] = currency_converted($data->req_base_price);
                        $request['distance_cost'] = currency_converted($data->req_dis_cost);
                        $request['time_cost'] = currency_converted($data->req_time_cost);
                        $tot = currency_converted($data->total - $data->ledger_payment - $data->promo_payment);
                        if ($tot <= 0) {
                            $tot = 0;
                        }
                        $request['total'] = $tot;
                        $request['main_total'] = currency_converted($data->total);
                        $request['referral_bonus'] = currency_converted($data->ledger_payment);
                        $request['promo_bonus'] = currency_converted($data->promo_payment);
                        $request['payment_type'] = $data->payment_mode;
                        $request['is_paid'] = $data->is_paid;
                        $request['promo_id'] = $data->promo_id;
                        $request['promo_code'] = $data->promo_code;
                        $request['currency'] = Config::get('app.generic_keywords.Currency');
                        $request['walker']['first_name'] = $data->first_name;
                        $request['walker']['last_name'] = $data->last_name;
                        $request['walker']['phone'] = $data->phone;
                        $request['walker']['email'] = $data->email;
                        $request['walker']['picture'] = $data->picture;
                        $request['walker']['bio'] = $data->bio;
                        $request['walker']['type'] = $data->type;
                        /* $request['walker']['rating'] = $data->rate; */
                        array_push($requests, $request);
                    }

                    $response_array = array(
                        'success' => true,
                        'requests' => $requests
                    );

                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function update_profile() {

        $token = Input::get('token');
        $owner_id = Input::get('id');
        $first_name = $last_name = $phone = $password = $picture = $bio = $address = $state = $country = $zipcode = 0;
        if (Input::has('first_name'))
            $first_name = Input::get('first_name');
        if (Input::has('last_name'))
            $last_name = Input::get('last_name');
        if (Input::has('phone'))
            $phone = Input::get('phone');
        if (Input::has('password'))
            $password = Input::get('password');
        if (Input::hasFile('picture'))
            $picture = Input::file('picture');
        if (Input::has('bio'))
            $bio = Input::get('bio');
        if (Input::has('address'))
            $address = Input::get('address');
        if (Input::has('state'))
            $state = Input::get('state');
        if (Input::has('country'))
            $country = Input::get('country');
        if (Input::has('zipcode'))
            $zipcode = Input::get('zipcode');
        $new_password = Input::get('new_password');
        $old_password = Input::get('old_password');
        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                    'picture' => $picture,
                    'zipcode' => $zipcode
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer',
                    /* 'picture' => 'mimes:jpeg,bmp,png', */
                    'picture' => '',
                    'zipcode' => 'integer'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6,
                    /* 'picture' => 'mimes:jpeg,bmp,png', */
                    'picture.required' => 7,
                    'zipcode' => ''
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    if ($new_password != "" || $new_password != NULL) {
                        if ($old_password != "" || $old_password != NULL) {
                            if (Hash::check($old_password, $owner_data->password)) {
                                // Do necessary operations
                                $owner = Owner::find($owner_id);
                                if ($first_name) {
                                    $owner->first_name = $first_name;
                                }
                                if ($last_name) {
                                    $owner->last_name = $last_name;
                                }
                                if ($phone) {
                                    $owner->phone = $phone;
                                }
                                if ($bio) {
                                    $owner->bio = $bio;
                                }
                                if ($address) {
                                    $owner->address = $address;
                                }
                                if ($state) {
                                    $owner->state = $state;
                                }
                                if ($country) {
                                    $owner->country = $country;
                                }
                                if ($zipcode) {
                                    $owner->zipcode = $zipcode;
                                }
                                if ($new_password) {
                                    $owner->password = Hash::make($new_password);
                                }
                                if (Input::hasFile('picture')) {
                                    if ($owner->picture != "") {
                                        $path = $owner->picture;
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
                                    Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
                                    $local_url = $file_name . "." . $ext;

                                    // Upload to S3
                                    if (Config::get('app.s3_bucket') != "") {
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
                                        $s3_url = web_url() . '/uploads/' . $local_url;
                                    }

                                    if (isset($owner->picture)) {
                                        if ($owner->picture != "") {
                                            $icon = $owner->picture;
                                            unlink_image($icon);
                                        }
                                    }

                                    $owner->picture = $s3_url;
                                }
                                If (Input::has('timezone')) {
                                    $owner->timezone = Input::get('timezone');
                                }
                                $owner->save();
                                $code_data = Ledger::where('owner_id', '=', $owner->id)->first();

                                /* SEND REFERRAL & PROMO INFO */
                                $settings = Settings::where('key', 'referral_code_activation')->first();
                                $referral_code_activation = $settings->value;
                                if ($referral_code_activation) {
                                    $referral_code_activation_txt = "referral on";
                                } else {
                                    $referral_code_activation_txt = "referral off";
                                }

                                $settings = Settings::where('key', 'promotional_code_activation')->first();
                                $promotional_code_activation = $settings->value;
                                if ($promotional_code_activation) {
                                    $promotional_code_activation_txt = "promo on";
                                } else {
                                    $promotional_code_activation_txt = "promo off";
                                }
                                /* SEND REFERRAL & PROMO INFO */

                                $response_array = array(
                                    'success' => true,
                                    'id' => $owner->id,
                                    'first_name' => $owner->first_name,
                                    'last_name' => $owner->last_name,
                                    'phone' => $owner->phone,
                                    'email' => $owner->email,
                                    'picture' => $owner->picture,
                                    'bio' => $owner->bio,
                                    'address' => $owner->address,
                                    'state' => $owner->state,
                                    'country' => $owner->country,
                                    'zipcode' => $owner->zipcode,
                                    'login_by' => $owner->login_by,
                                    'social_unique_id' => $owner->social_unique_id,
                                    'device_token' => $owner->device_token,
                                    'device_type' => $owner->device_type,
                                    'timezone' => $owner->timezone,
                                    'token' => $owner->token,
                                    'referral_code' => $code_data->referral_code,
                                    'is_referee' => $owner->is_referee,
                                    'promo_count' => $owner->promo_count,
                                    'is_referral_active' => $referral_code_activation,
                                    'is_referral_active_txt' => $referral_code_activation_txt,
                                    'is_promo_active' => $promotional_code_activation,
                                    'is_promo_active_txt' => $promotional_code_activation_txt,
                                );
                                $response_code = 200;
                            } else {
                                $response_array = array('success' => false, 'error' => 47, 'error_messages' => array(47), 'error_code' => 501);
                                $response_code = 200;
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 48, 'error_messages' => array(48), 'error_code' => 502);
                            $response_code = 200;
                        }
                    } else {
                        // Do necessary operations
                        $owner = Owner::find($owner_id);
                        if ($first_name) {
                            $owner->first_name = $first_name;
                        }
                        if ($last_name) {
                            $owner->last_name = $last_name;
                        }
                        if ($phone) {
                            $owner->phone = $phone;
                        }
                        if ($bio) {
                            $owner->bio = $bio;
                        }
                        if ($address) {
                            $owner->address = $address;
                        }
                        if ($state) {
                            $owner->state = $state;
                        }
                        if ($country) {
                            $owner->country = $country;
                        }
                        if ($zipcode) {
                            $owner->zipcode = $zipcode;
                        }
                        if (Input::hasFile('picture')) {
                            if ($owner->picture != "") {
                                $path = $owner->picture;
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
                            Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
                            $local_url = $file_name . "." . $ext;

                            // Upload to S3
                            if (Config::get('app.s3_bucket') != "") {
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
                                $s3_url = web_url() . '/uploads/' . $local_url;
                            }

                            if (isset($owner->picture)) {
                                if ($owner->picture != "") {
                                    $icon = $owner->picture;
                                    unlink_image($icon);
                                }
                            }

                            $owner->picture = $s3_url;
                        }
                        If (Input::has('timezone')) {
                            $owner->timezone = Input::get('timezone');
                        }
                        $owner->save();
                        $code_data = Ledger::where('owner_id', '=', $owner->id)->first();

                        /* SEND REFERRAL & PROMO INFO */
                        $settings = Settings::where('key', 'referral_code_activation')->first();
                        $referral_code_activation = $settings->value;
                        if ($referral_code_activation) {
                            $referral_code_activation_txt = "referral on";
                        } else {
                            $referral_code_activation_txt = "referral off";
                        }

                        $settings = Settings::where('key', 'promotional_code_activation')->first();
                        $promotional_code_activation = $settings->value;
                        if ($promotional_code_activation) {
                            $promotional_code_activation_txt = "promo on";
                        } else {
                            $promotional_code_activation_txt = "promo off";
                        }
                        /* SEND REFERRAL & PROMO INFO */

                        $response_array = array(
                            'success' => true,
                            'id' => $owner->id,
                            'first_name' => $owner->first_name,
                            'last_name' => $owner->last_name,
                            'phone' => $owner->phone,
                            'email' => $owner->email,
                            'picture' => $owner->picture,
                            'bio' => $owner->bio,
                            'address' => $owner->address,
                            'state' => $owner->state,
                            'country' => $owner->country,
                            'zipcode' => $owner->zipcode,
                            'login_by' => $owner->login_by,
                            'social_unique_id' => $owner->social_unique_id,
                            'device_token' => $owner->device_token,
                            'device_type' => $owner->device_type,
                            'timezone' => $owner->timezone,
                            'token' => $owner->token,
                            'referral_code' => $code_data->referral_code,
                            'is_referee' => $owner->is_referee,
                            'promo_count' => $owner->promo_count,
                            'is_referral_active' => $referral_code_activation,
                            'is_referral_active_txt' => $referral_code_activation_txt,
                            'is_promo_active' => $promotional_code_activation,
                            'is_promo_active_txt' => $promotional_code_activation_txt,
                        );
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function payment_type() {
        $token = Input::get('token');
        $owner_id = Input::get('id');
        $request_id = Input::get('request_id');
        $cash_or_card = Input::get('cash_or_card');
        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                    'cash_or_card' => $cash_or_card,
                    'request_id' => $request_id,
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer',
                    'cash_or_card' => 'required',
                    'request_id' => 'required',
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6,
                    'cash_or_card.required' => 97,
                    'request_id.required' => 19,
                        )
        );
        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $payments = array();
            /* $payments['none'] = ""; */
            $def_card = 0;
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    if ($cash_or_card != 1) {
                        $card_count = Payment::where('owner_id', '=', $owner_id)->count();
                        if ($card_count <= 0) {
                            $response_array = array('success' => false, 'error' => 59, 'error_messages' => array(59), 'error_code' => 417);
                            $response_code = 200;
                            $response = Response::json($response_array, $response_code);
                            return $response;
                        }
                    }
                    // Do necessary operations
                    $owner = Owner::find($owner_id);
                    $payment_data = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                    foreach ($payment_data as $data1) {
                        $default = $data1->is_default;
                        if ($default == 1) {
                            $def_card = $data1->id;
                            $data['is_default_text'] = "default";
                        } else {
                            $data['is_default_text'] = "not_default";
                        }
                        $data['id'] = $data1->id;
                        $data['owner_id'] = $data1->owner_id;
                        $data['customer_id'] = $data1->customer_id;
                        $data['last_four'] = $data1->last_four;
                        $data['card_token'] = $data1->card_token;
                        $data['card_type'] = $data1->card_type;
                        $data['card_id'] = $data1->card_token;
                        $data['is_default'] = $default;
                        array_push($payments, $data);
                    }
                    if ($request = Requests::find($request_id)) {
                        $request->payment_mode = $cash_or_card;
                        $request->save();

                        $walker = Walker::where('id', $request->confirmed_walker)->first();
                        if ($walker) {
                            $msg_array = array();
                            $msg_array['unique_id'] = 3;
                            $msg_array['request_id'] = $request_id;
                            $response_array = array(
                                'success' => true,
                                'id' => $owner->id,
                                'first_name' => $owner->first_name,
                                'last_name' => $owner->last_name,
                                'phone' => $owner->phone,
                                'email' => $owner->email,
                                'picture' => $owner->picture,
                                'bio' => $owner->bio,
                                'address' => $owner->address,
                                'state' => $owner->state,
                                'country' => $owner->country,
                                'zipcode' => $owner->zipcode,
                                'login_by' => $owner->login_by,
                                'social_unique_id' => $owner->social_unique_id,
                                'device_token' => $owner->device_token,
                                'device_type' => $owner->device_type,
                                'token' => $owner->token,
                                'default_card_id' => $def_card,
                                'payment_type' => $request->payment_mode,
                                'is_referee' => $owner->is_referee,
                                'promo_count' => $owner->promo_count,
                                'payments' => $payments,
                            );
                            $response_array['unique_id'] = 3;
                            $response_code = 200;
                            $msg_array['owner_data'] = $response_array;
                            $title = "Payment Type Change";
                            $message = $msg_array;
                            if ($request->confirmed_walker == $request->current_walker) {
                                send_notifications($request->confirmed_walker, "walker", $title, $message);
                            }
                        } else {
                            $response_array = array('success' => false, 'error' => 13, 'error_messages' => array(13), 'error_code' => 408);
                            $response_code = 200;
                        }
                    } else {
                        $response_array = array('success' => false, 'error' => 52, 'error_messages' => array(52), 'error_code' => 408);
                        $response_code = 200;
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    $response_array = array('success' => false, 'error' => 10, 'error_messages' => array(10), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function select_card() {
        $token = Input::get('token');
        $owner_id = Input::get('id');
        $card_token = Input::get('card_id');

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                    'card' => $card_token
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer',
                    'card' => 'required|integer'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6,
                    'card.required' => 42
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {

                    Payment::where('owner_id', $owner_id)->update(array('is_default' => 0));
                    Payment::where('owner_id', $owner_id)->where('id', $card_token)->update(array('is_default' => 1));

                    $payments = array();
                    $card_count = DB::table('payment')->where('owner_id', '=', $owner_id)->count();
                    if ($card_count) {
                        $paymnt = Payment::where('owner_id', $owner_id)->orderBy('is_default', 'DESC')->get();
                        foreach ($paymnt as $data1) {
                            $default = $data1->is_default;
                            if ($default == 1) {
                                $data['is_default_text'] = "default";
                            } else {
                                $data['is_default_text'] = "not_default";
                            }
                            $data['id'] = $data1->id;
                            $data['owner_id'] = $data1->owner_id;
                            $data['customer_id'] = $data1->customer_id;
                            $data['last_four'] = $data1->last_four;
                            $data['card_token'] = $data1->card_token;
                            $data['card_type'] = $data1->card_type;
                            $data['card_id'] = $data1->card_token;
                            $data['is_default'] = $default;
                            array_push($payments, $data);
                        }
                        $response_array = array(
                            'success' => true,
                            'payments' => $payments
                        );
                    } else {
                        $response_array = array(
                            'success' => false,
                            'error' => 46,
                            'error_messages' => array(46),
                        );
                    }
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function pay_debt() {
        $token = Input::get('token');
        $owner_id = Input::get('id');

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    $total = $owner_data->debt;
                    if ($total == 0) {
                        $response_array = array('success' => true);
                        $response_code = 200;
                        $response = Response::json($response_array, $response_code);
                        return $response;
                    }
                    $payment_data = Payment::where('owner_id', $owner_id)->where('is_default', 1)->first();
                    if (!$payment_data)
                        $payment_data = Payment::where('owner_id', $request->owner_id)->first();

                    if ($payment_data) {
                        $customer_id = $payment_data->customer_id;

                        if (Config::get('app.default_payment') == 'stripe') {
                            Stripe::setApiKey(Config::get('app.stripe_secret_key'));

                            try {
                                Stripe_Charge::create(array(
                                    "amount" => $total * 100,
                                    "currency" => "usd",
                                    "customer" => $customer_id)
                                );
                            } catch (Stripe_InvalidRequestError $e) {
                                // Invalid parameters were supplied to Stripe's API
                                $ownr = Owner::find($owner_id);
                                $ownr->debt = $total;
                                $ownr->save();
                                $response_array = array('error' => $e->getMessage());
                                $response_code = 200;
                                $response = Response::json($response_array, $response_code);
                                return $response;
                            }
                            $owner_data->debt = 0;
                            $owner_data->save();
                        } else {
                            $amount = $total;
                            Braintree_Configuration::environment(Config::get('app.braintree_environment'));
                            Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
                            Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
                            Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
                            $card_id = $payment_data->card_token;
                            $result = Braintree_Transaction::sale(array(
                                        'amount' => $amount,
                                        'paymentMethodToken' => $card_id
                            ));

                            //Log::info('result = ' . print_r($result, true));
                            if ($result->success) {
                                $owner_data->debt = $total;
                            } else {
                                $owner_data->debt = 0;
                            }
                            $owner_data->save();
                        }
                    }
                    $response_array = array('success' => true);
                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function paybypaypal() {
        $token = Input::get('token');
        $owner_id = Input::get('id');
        $request_id = Input::get('request_id');
        $paypal_id = Input::get('paypal_id');

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                    'paypal_id' => $paypal_id
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer',
                    'paypal_id' => 'required'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6,
                    'paypal_id.required' => 14
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    //Log::info('paypal_id = ' . print_r($paypal_id, true));
                    $req = Requests::find($request_id);
                    //Log::info('req = ' . print_r($req, true));
                    $req->is_paid = 1;
                    $req->payment_id = $paypal_id;
                    $req->save();
                    $response_array = array('success' => true);
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function paybybitcoin() {
        // $token = Input::get('token');
        // $owner_id = Input::get('id');
        // $request_id = Input::get('request_id');
        // $validator = Validator::make(
        //  array(
        //      'token' => $token,
        //      'owner_id' => $owner_id,
        //  ),
        //  array(
        //      'token' => 'required',
        //      'owner_id' => 'required|integer',
        //  )
        // );
        // if ($validator->fails()) {
        //  $error_messages = $validator->messages()->all();
        //      $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages );
        //      $response_code = 200;
        // } else {
        //  $is_admin = $this->isAdmin($token);
        //  if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
        //      // check for token validity
        //      if (is_token_active($owner_data->token_expiry) || $is_admin) {
        $coinbaseAPIKey = Config::get('app.coinbaseAPIKey');
        $coinbaseAPISecret = Config::get('app.coinbaseAPISecret');
        // coinbase
        $coinbase = Coinbase::withApiKey($coinbaseAPIKey, $coinbaseAPISecret);
        // $balance = $coinbase->getBalance() . " BTC";
        $user = $coinbase->getUser();
        // $contacts = $coinbase->getContacts("user");
        // $currencies = $coinbase->getCurrencies();
        // $rates = $coinbase->getExchangeRate();
        // $paymentButton = $coinbase->createButton(
        //     "Request ID",
        //     "19.99", 
        //     "USD", 
        //     "TRACKING_CODE_1", 
        //     array(
        //            "description" => "My 19.99 USD donation to PL",
        //            "cancel_url" => "http://localhost:8000/user/acceptbitcoin",
        //            "success_url" => "http://localhost:8000/user/acceptbitcoin"
        //        )
        // );
        //Log::info('user = ' . print_r($user, true));

        $response_array = array('success' => true);
        //      }else{
        //          $response_array = array('success' => false);
        //          //Log::error('1');
        //      }
        //  }else{
        //      $response_array = array('success' => false);
        //      //Log::error('2');
        //  }
        // }
        $response_code = 200;
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function acceptbitcoin() {
        $response = Input::get('response');
        /*
          Sample Response
          {
          "order": {
          "id": "5RTQNACF",
          "created_at": "2012-12-09T21:23:41-08:00",
          "status": "completed",
          "event": {
          "type": "completed"
          },
          "total_btc": {
          "cents": 100000000,
          "currency_iso": "BTC"
          },
          "total_native": {
          "cents": 1253,
          "currency_iso": "USD"
          },
          "total_payout": {
          "cents": 2345,
          "currency_iso": "USD"
          },
          "custom": "order1234",
          "receive_address": "1NhwPYPgoPwr5hynRAsto5ZgEcw1LzM3My",
          "button": {
          "type": "buy_now",
          "name": "Alpaca Socks",
          "description": "The ultimate in lightweight footwear",
          "id": "5d37a3b61914d6d0ad15b5135d80c19f"
          },
          "transaction": {
          "id": "514f18b7a5ea3d630a00000f",
          "hash": "4a5e1e4baab89f3a32518a88c31bc87f618f76673e2cc77ab2127b7afdeda33b",
          "confirmations": 0
          },
          "refund_address": "1HcmQZarSgNuGYz4r7ZkjYumiU4PujrNYk"
          },
          "customer": {
          "email": "coinbase@example.com",
          "shipping_address": [
          "John Smith",
          "123 Main St.",
          "Springfield, OR 97477",
          "United States"
          ]
          }
          }
         */
        //Log::info('response = ' . print_r($response, true));
        return Response::json(200, $response);
    }

    public function send_eta() {
        $token = Input::get('token');
        $owner_id = Input::get('id');
        $phones = Input::get('phone');
        $request_id = Input::get('request_id');
        $eta = Input::get('eta');

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                    'phones' => $phones,
                    'eta' => $eta,
                        ), array(
                    'token' => 'required',
                    'phones' => 'required',
                    'owner_id' => 'required|integer',
                    'eta' => 'required'
                        ), array(
                    'token.required' => 5,
                    'phones.required' => 15,
                    'owner_id.required' => 6,
                    'eta.required' => 16
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry) || $is_admin) {
                    // If phones is not an array
                    if (!is_array($phones)) {
                        $phones = explode(',', $phones);
                    }

                    //Log::info('phones = ' . print_r($phones, true));

                    foreach ($phones as $key) {

                        $owner = Owner::where('id', $owner_id)->first();
                        $secret = str_random(6);

                        $request = Requests::where('id', $request_id)->first();
                        $request->security_key = $secret;
                        $request->save();
                        $msg = $owner->first_name . ' ' . $owner->last_name . ' ETA : ' . $eta;
                        send_eta($key, $msg);
                        //Log::info('Send ETA MSG  = ' . print_r($msg, true));
                    }

                    $response_array = array('success' => true);
                    $response_code = 200;
                } else {
                    $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                    $response_code = 200;
                }
            } else {
                if ($is_admin) {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_messages' => array('' . $var->keyword . ' ID not Found'), 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
                $response_code = 200;
            }
        }

        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function payment_options_allowed() {
        $token = Input::get('token');
        $owner_id = Input::get('id');

        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );

        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry)) {
                    // Payment options allowed
                    $payment_options = array();

                    $payments = Payment::where('owner_id', $owner_id)->count();

                    if ($payments) {
                        $payment_options['stored_cards'] = 1;
                    } else {
                        $payment_options['stored_cards'] = 0;
                    }
                    $codsett = Settings::where('key', 'cod')->first();
                    if ($codsett->value == 1) {
                        $payment_options['cod'] = 1;
                    } else {
                        $payment_options['cod'] = 0;
                    }

                    $paypalsett = Settings::where('key', 'paypal')->first();
                    if ($paypalsett->value == 1) {
                        $payment_options['paypal'] = 1;
                    } else {
                        $payment_options['paypal'] = 0;
                    }

                    //Log::info('payment_options = ' . print_r($payment_options, true));
                    /* SEND REFERRAL & PROMO INFO */
                    $settings = Settings::where('key', 'referral_code_activation')->first();
                    $referral_code_activation = $settings->value;
                    if ($referral_code_activation) {
                        $referral_code_activation_txt = "referral on";
                    } else {
                        $referral_code_activation_txt = "referral off";
                    }

                    $settings = Settings::where('key', 'promotional_code_activation')->first();
                    $promotional_code_activation = $settings->value;
                    if ($promotional_code_activation) {
                        $promotional_code_activation_txt = "promo on";
                    } else {
                        $promotional_code_activation_txt = "promo off";
                    }
                    /* SEND REFERRAL & PROMO INFO */

                    // Promo code allowed
                    /* $promosett = Settings::where('key', 'promo_code')->first(); */
                    if ($promotional_code_activation == 1) {
                        $promo_allow = 1;
                    } else {
                        $promo_allow = 0;
                    }

                    $response_array = array(
                        'success' => true,
                        'payment_options' => $payment_options,
                        'promo_allow' => $promo_allow,
                        'is_referral_active' => $referral_code_activation,
                        'is_referral_active_txt' => $referral_code_activation_txt,
                        'is_promo_active' => $promotional_code_activation,
                        'is_promo_active_txt' => $promotional_code_activation_txt,
                    );
                } else {
                    /* $var = Keywords::where('id', 2)->first();
                      $response_array = array('success' => false, 'error' => '' . $var->keyword . ' ID not Found', 'error_code' => 410); */
                    $response_array = array('success' => false, 'error' => 53, 'error_messages' => array(53), 'error_code' => 410);
                }
            } else {
                $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
            }
            $response_code = 200;
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function get_credits() {
        $token = Input::get('token');
        $owner_id = Input::get('id');
        $validator = Validator::make(
                        array(
                    'token' => $token,
                    'owner_id' => $owner_id,
                        ), array(
                    'token' => 'required',
                    'owner_id' => 'required|integer'
                        ), array(
                    'token.required' => 5,
                    'owner_id.required' => 6
                        )
        );
        if ($validator->fails()) {
            $error_messages = $validator->messages()->all();
            $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
            $response_code = 200;
        } else {
            $is_admin = $this->isAdmin($token);
            if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                // check for token validity
                if (is_token_active($owner_data->token_expiry)) {
                    /* $currency_selected = Keywords::find(5); */
                    $ledger = Ledger::where('owner_id', $owner_id)->first();
                    if ($ledger) {
                        $credits['balance'] = currency_converted($ledger->amount_earned - $ledger->amount_spent);
                        /* $credits['currency'] = $currency_selected->keyword; */
                        $credits['currency'] = Config::get('app.generic_keywords.Currency');
                        $response_array = array('success' => true, 'credits' => $credits);
                    } else {
                        $response_array = array('success' => false, 'error' => 17, 'error_messages' => array(17), 'error_code' => 475);
                    }
                } else {
                    $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                }
            } else {
                $response_array = array('success' => false, 'error' => 10, 'error_messages' => array(10), 'error_code' => 402);
            }
            $response_code = 200;
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function logout() {
        if (Request::isMethod('post')) {
            $token = Input::get('token');
            $owner_id = Input::get('id');

            $validator = Validator::make(
                            array(
                        'token' => $token,
                        'owner_id' => $owner_id,
                            ), array(
                        'token' => 'required',
                        'owner_id' => 'required|integer'
                            ), array(
                        'token.required' => 5,
                        'owner_id.required' => 6
                            )
            );

            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $response_array = array('success' => false, 'error' => 8, 'error_code' => 401, 'error_messages' => $error_messages);
                $response_code = 200;
            } else {
                $is_admin = $this->isAdmin($token);
                if ($owner_data = $this->getOwnerData($owner_id, $token, $is_admin)) {
                    // check for token validity
                    if (is_token_active($owner_data->token_expiry) || $is_admin) {

                        $owner_data->latitude = 0;
                        $owner_data->longitude = 0;
                        $owner_data->device_token = 0;
                        $owner_data->fcm_token = '';
                        /* $owner_data->is_login = 0; */
                        $owner_data->save();

                        $response_array = array('success' => true, 'error' => 18, 'error_messages' => array(18),);
                        $response_code = 200;
                    } else {
                        $response_array = array('success' => false, 'error' => 9, 'error_messages' => array(9), 'error_code' => 405);
                        $response_code = 200;
                    }
                } else {
                    if ($is_admin) {
                        $response_array = array('success' => false, 'error' => 10, 'error_messages' => array(10), 'error_code' => 410);
                    } else {
                        $response_array = array('success' => false, 'error' => 11, 'error_messages' => array(11), 'error_code' => 406);
                    }
                    $response_code = 200;
                }
            }
        }
        $response = Response::json($response_array, $response_code);
        return $response;
    }

}
