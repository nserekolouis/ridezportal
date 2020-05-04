<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/report', array('as' => 'AdminReport', 'uses' => 'HomeController@report'));
Route::get('/map_view', array('as' => 'AdminMapview', 'uses' => 'HomeController@map_view'));
Route::get('/admins', array('as' => 'AdminAdmins', 'uses' => 'HomeController@admins'));
Route::get('/logout', array('as' => 'AdminLogout', 'uses' => 'HomeController@logout'));
Route::get('/providers', array('as' => 'AdminProviders', 'uses' => 'HomeController@walkers'));
Route::get('/requests', array('as' => 'AdminRequests', 'uses' => 'HomeController@walks'));
Route::get('/schedule', array('as' => 'AdminSchedule', 'uses' => 'HomeController@scheduled_walks'));
Route::get('/users', array('as' => 'AdminUsers', 'uses' => 'HomeController@owners'));
Route::get('/reviews', array('as' => 'AdminReviews', 'uses' => 'HomeController@reviews'));
Route::get('/informations', array('as' => 'AdminInformations', 'uses' => 'HomeController@get_info_pages'));
Route::get('/provider-types', array('as' => 'AdminProviderTypes', 'uses' => 'HomeController@get_provider_types'));
Route::get('/document-types', array('as' => 'AdminDocumentTypes', 'uses' => 'HomeController@get_document_types'));
Route::get('/promo_code', array('as' => 'AdminPromoCodes', 'uses' => 'HomeController@get_promo_codes'));
Route::get('/notifications', array('as' => 'AdminPushNotifications', 'uses' => 'HomeController@get_notifications'));
Route::get('/edit_keywords', array('as' => 'AdminKeywords', 'uses' => 'HomeController@edit_keywords'));
Route::get('/details_payment', array('as' => 'AdminPayment', 'uses' => 'HomeController@payment_details'));
Route::get('/requests_payment', array('as' => 'AdminRequests_payment', 'uses' => 'HomeController@walks_payment'));
Route::get('/settings', array('as' => 'AdminSettings', 'uses' => 'HomeController@get_settings'));
Route::get('/providers_xml', array('as' => 'AdminProviderXml', 'uses' => 'HomeController@walkers_xml'));
Route::get('/provider/add', array('as' => 'AdminProviderAdd', 'uses' => 'HomeController@add_walker'));
Route::get('/sortpv', array('as' => '/sortpv', 'uses' => 'HomeController@sortpv'));
Route::get('/searchpv', array('as' => '/searchpv', 'uses' => 'HomeController@searchpv'));
Route::get('/provider/current', array('as' => 'AdminProviderCurrent', 'uses' => 'HomeController@current'));
Route::get('/provider/edit/{id}', array('as' => 'AdminProviderEdit', 'uses' => 'HomeController@edit_walker'));
Route::get('/provider/history/{id}', array('as' => 'AdminProviderHistory', 'uses' => 'HomeController@walker_history'));
Route::get('/provider/timelogs/{id}', array('as' => 'AdminProviderTimeLogs', 'uses' => 'HomeController@walker_time_logs'));
Route::get('/provider/decline/{id}', array('as' => 'AdminProviderDecline', 'uses' => 'HomeController@decline_walker'));
Route::get('/provider/delete/{id}', array('as' => 'AdminProviderDelete', 'uses' => 'HomeController@delete_walker'));
Route::get('/provider/availability/{id}', array('as' => 'AdminProviderAvailability', 'uses' => 'HomeController@availability_provider'));
Route::get('/sortreq', array('as' => '/sortreq', 'uses' => 'HomeController@sortreq'));
Route::get('/searchreq', array('as' => '/searchreq', 'uses' => 'HomeController@searchreq'));
Route::get('/request/map/{id}', array('as' => 'AdminRequestsMap', 'uses' => 'AdminController@view_map'));
//Route::get('/report','HomeController@report');

//the api
Route::post('/user/login', 'OwnerController@login');
Route::post('/user/register', 'OwnerController@register');
Route::post('/user/login', 'OwnerController@login');
Route::post('/user/register', 'OwnerController@register');
Route::post('/user/location', 'CustomerController@set_location');
Route::any('/user/details', 'OwnerController@details');
Route::post('/user/addcardtoken', 'OwnerController@addcardtoken');
Route::get('/user/braintreekey', 'OwnerController@get_braintree_token');
Route::post('/locale/change', 'WebController@change_locale');
Route::post('/user/deletecardtoken', 'OwnerController@deletecardtoken');
Route::post('/user/update', 'OwnerController@update_profile');
Route::post('/user/paydebt', 'OwnerController@pay_debt');
Route::post('/user/selectcard', 'OwnerController@select_card');
Route::post('/user/card_selection', 'OwnerController@card_selection');
Route::get('/user', 'OwnerController@getProfile');
Route::any('/user/thing', 'CustomerController@create');
Route::post('/user/updatething', 'CustomerController@update_thing');
Route::post('/user/createrequest', 'CustomerController@create_request_two');
Route::post('/user/createussdrequest', 'CustomerController@create_request_ussd');
Route::post('/user/payment_type', 'OwnerController@payment_type');
Route::post('/user/createrequestlater', 'CustomerController@create_request_later');
Route::post('/user/createfuturerequest', 'CustomerController@create_future_request');
Route::post('/user/getfuturerequest', 'CustomerController@get_future_request');
Route::post('/user/deletefuturerequest', 'CustomerController@delete_future_request');
/* Route::post('/user/getproviders', 'CustomerController@get_providers'); */
Route::post('/user/getproviders', 'CustomerController@get_providers_old');
Route::post('/user/getproviders_new', 'CustomerController@get_providers');
Route::post('/user/getprovidersall', 'CustomerController@get_providers_all');
Route::post('/user/getnearbyproviders', 'CustomerController@get_nearby_providers');
Route::post('/user/createrequestproviders', 'CustomerController@create_request_providers');
Route::post('/user/cancellation', 'CustomerController@cancellation');
Route::get('/user/getrequest', 'CustomerController@get_request');
Route::post('/user/getrunningrequest', 'CustomerController@get_running_request');
Route::post('/user/cancelrequest', 'CustomerController@cancel_request');
Route::get('/user/getrequestlocation', 'CustomerController@get_request_location');
Route::post('/user/rating', 'CustomerController@set_walker_rating');
Route::get('/user/requestinprogress', 'CustomerController@request_in_progress');
Route::get('/user/requestpath', 'CustomerController@get_walk_location');
Route::get('/provider/requestpath', 'ProviderController@get_walk_location');
Route::post('/user/referral', 'OwnerController@set_referral_code');
Route::get('/user/referral', 'OwnerController@get_referral_code');
Route::post('/user/apply-referral', 'OwnerController@apply_referral_code');
Route::post('/user/apply-promo', 'OwnerController@apply_promo_code');
Route::get('/user/cards', 'OwnerController@get_cards');
Route::get('/user/history', 'OwnerController@get_completed_requests');

Route::post('/user/paybypaypal', 'OwnerController@paybypaypal');
Route::post('/user/paybybitcoin', 'OwnerController@paybybitcoin');
Route::post('/user/acceptbitcoin', 'OwnerController@acceptbitcoin');
Route::get('/user/send_eta', 'OwnerController@send_eta');
Route::get('/user/current_eta', 'CustomerController@eta');
Route::get('/user/credits', 'OwnerController@get_credits');
Route::get('/user/payment_options', array('as' => '/user/payment_options', 'uses' => 'OwnerController@payment_options_allowed'));
Route::get('/user/check_promo_code', 'CustomerController@check_promo_code');
Route::post('/user/logout', 'OwnerController@logout');
Route::post('/user/payment_select', 'CustomerController@payment_select');
Route::post('/user/provider_list', 'CustomerController@get_provider_list');
Route::post('/user/setdestination', 'CustomerController@user_set_destination');
Route::post('/user/geteta', 'CustomerController@get_eta');

Route::post('/user/get_fare_estimate', 'CustomerController@get_fare_estimate');




// Walker APIs
Route::get('/provider/check_banking', 'ProviderController@check_banking');
Route::post('/provider/statistics', 'ProviderController@get_driver_stats');
Route::get('/provider/getrequests', 'ProviderController@get_requests');
Route::get('/provider/getrequest', 'ProviderController@get_request');
Route::post('/provider/respondrequest', 'ProviderController@respond_request');
Route::post('/provider/location', 'ProviderController@walker_location');
Route::post('/provider/requestwalkerstarted', 'ProviderController@request_walker_started');
Route::post('/provider/requestwalkerarrived', 'ProviderController@request_walker_arrived');
Route::post('/provider/requestwalkstarted', 'ProviderController@request_walk_started');
Route::post('/provider/requestwalkcancelled', 'ProviderController@request_walk_cancelled');
Route::post('/request/location', 'ProviderController@walk_location');
Route::post('/provider/requestwalkcompleted', 'ProviderController@request_walk_completed');
Route::post('/provider/prepayment', 'ProviderController@pre_payment');
Route::post('/provider/paymentselection', 'ProviderController@payment_selection');
Route::post('/provider/rating', 'ProviderController@set_dog_rating');
Route::post('/provider/login', 'ProviderController@login');
Route::post('/provider/register', 'ProviderController@register');
Route::post('/provider/update', 'ProviderController@update_profile');
Route::post('/provider_services/update', 'ProviderController@provider_services_update');
Route::get('/provider/services_details', 'ProviderController@services_details');
Route::get('/provider/requestinprogress', 'ProviderController@request_in_progress');
Route::get('/provider/checkstate', 'ProviderController@check_state');
Route::post('/provider/togglestate', 'ProviderController@toggle_state');
Route::get('/provider/history', 'ProviderController@get_completed_requests');
Route::post('panic', array('as' => 'panic', 'uses' => 'ProviderController@panic'));
Route::post('/provider/logout', 'ProviderController@logout');
Route::get('/provider/application/types', 'ApplicationController@types');
Route::get('/provider/application/pages', 'ApplicationController@pages');
Route::get('/provider/application/page/{id}', 'ApplicationController@get_page');
Route::post('/provider/application/forgot-password', 'ApplicationController@forgot_password');
Route::get('/provider/application/get_keys', 'ApplicationController@get_keys');

// Info Page API
Route::get('/application/pages', 'ApplicationController@pages');
Route::get('/application/types', 'ApplicationController@types');
Route::get('/application/page/{id}', 'ApplicationController@get_page');
Route::post('/application/forgot-password', 'ApplicationController@forgot_password');
// Get keys value from admin panel and return it in service for ios app
Route::get('/application/get_keys', 'ApplicationController@get_keys');

Route::get('/providers_xml', array('as' => 'AdminProviderXml', 'uses' => 'HomeController@walkers_xml'));
Route::get('/application/offline_walkers', 'ProviderController@remove_offline_walkers');
//Route::get('/downloadpage', 'ProviderController@remove_offline_walkers');

Route::get('/downloadapps', function () {
    return view('download');
});

Route::get('/termsandconditions', function () {
    return view('termsandconditions');
});

Route::get('/sortur', array('as' => '/sortur', 'uses' => 'HomeController@sortur'));
Route::get('/searchur', array('as' => '/searchur', 'uses' => 'HomeController@searchur'));
Route::get('/user/edit/{id}', array('as' => 'AdminUserEdit', 'uses' => 'HomeController@edit_owner'));
Route::get('/user/history/{id}', array('as' => 'AdminUserHistory', 'uses' => 'HomeController@owner_history'));
Route::get('/user/referral/{id}', array('as' => 'AdminUserReferral', 'uses' => 'HomeController@referral_details'));
Route::get('/addreq/{id}', array('as' => 'AdminAddRequest', 'uses' => 'AdminController@add_request'));
Route::get('/user/delete/{id}', array('as' => 'AdminDeleteUser', 'uses' => 'AdminController@delete_owner'));
Route::get('/searchrev', array('as' => '/searchrev', 'uses' => 'AdminController@searchrev'));
Route::get('/reviews/delete/{id}', array('as' => 'AdminReviewsDelete', 'uses' => 'AdminController@delete_review'));
Route::get('/reviews/delete_client/{id}', array('as' => 'AdminReviewsDeleteDog', 'uses' => 'AdminController@delete_review_owner'));
Route::get('/information/edit/{id}', array('as' => 'AdminInformationEdit', 'uses' => 'AdminController@edit_info_page'));
Route::get('/searchinfo', array('as' => '/searchinfo', 'uses' => 'AdminController@searchinfo'));
Route::get('/information/delete/{id}', array('as' => 'AdminInformationDelete', 'uses' => 'AdminController@delete_info_page'));
Route::get('/provider-type/edit/{id}', array('as' => 'AdminProviderTypeEdit', 'uses' => 'AdminController@edit_provider_type'));
Route::get('/sortpvtype', array('as' => '/sortpvtype', 'uses' => 'AdminController@sortpvtype'));
Route::get('/searchpvtype', array('as' => '/searchpvtype', 'uses' => 'AdminController@searchpvtype'));
Route::get('/document-type/edit/{id}', array('as' => 'AdminDocumentTypesEdit', 'uses' => 'AdminController@edit_document_type'));
Route::get('/searchdoc', array('as' => '/searchdoc', 'uses' => 'AdminController@searchdoc'));
Route::get('/document-type/delete/{id}', array('as' => 'AdminDocumentTypesDelete', 'uses' => 'AdminController@delete_document_type'));
Route::get('/promo_code/add', array('as' => 'AdminPromoAdd', 'uses' => 'AdminController@add_promo_code'));
Route::get('/sortpromo', array('as' => '/sortpromo', 'uses' => 'AdminController@sortpromo'));
Route::get('/searchpromo', array('as' => '/searchpromo', 'uses' => 'AdminController@searchpromo'));
Route::get('/promo_code/edit/{id}', array('as' => 'AdminPromoCodeEdit', 'uses' => 'AdminController@edit_promo_code'));
Route::get('/promo_code/deactivate/{id}', array('as' => 'AdminPromoCodeDeactivate', 'uses' => 'AdminController@deactivate_promo_code'));
  Route::get('/promo_code/activate/{id}', array('as' => 'AdminPromoCodeActivate', 'uses' => 'AdminController@activate_promo_code'));
Route::post('/save_keywords', array('as' => 'AdminKeywordsSave', 'uses' => 'AdminController@save_keywords'));
Route::post('/save_keywords_ui', array('as' => 'AdminUIKeywordsSave', 'uses' => 'AdminController@save_keywords_UI'));
Route::post('/adminCurrency', array('as' => 'adminCurrency', 'uses' => 'AdminController@adminCurrency'));
Route::get('/settings/installation', array('as' => 'AdminSettingInstallation', 'uses' => 'AdminController@installation_settings'));
Route::post('/settings', array('as' => 'AdminSettingsSave', 'uses' => 'AdminController@save_settings'));
Route::post('/theme', array('as' => 'AdminTheme', 'uses' => 'AdminController@theme'));
Route::post('/provider/location', 'ProviderController@walker_location');

Route::post('/user/apply-referral', 'OwnerController@apply_referral_code');