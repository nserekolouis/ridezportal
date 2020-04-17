<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <title>Download Apps</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto|Roboto+Condensed&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/downloadapps.css')}}"/>
    <link rel="apple-touch-icon" sizes="57x57" href="/static/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/static/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/static/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/static/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/static/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/static/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/static/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/static/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/static/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/static/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/static/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/static/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/static/favicon/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
  </head>
  <body>
    <center><h3>Download</h3></center>
    <center><img src="http://truckz.mywworld.com/static/images/icon_downloading.gif" alt="Download Apps" height="80" width="110" 
                 style="border-radius:48%;height:60px;width:60px;">
    </center>
    <div class="container">
       <div onclick="download_client_application()" class="apps">
         <div class="apps-inner">
           <div>
             <img style="border-radius:10%;" src="http://truckz.mywworld.com/static/images/clientslogo.png" alt="Client" height="80" width="80">
           </div>
           <div class="apps-label">
             <label>Client</label>
           </div>
         </div>
      </div>
       <div onclick="download_driver_application()" class="apps">
        <div class="apps-inner">
          <div>
            <img src="http://truckz.mywworld.com/static/images/driverlogo.png" alt="Driver" height="80" width="80">
          </div>
          <div class="apps-label">
            <label>Driver</label>
          </div>
        </div>
      </div>
       <div onclick="download_agent_application()" class="apps" style="display: none;">
        <div class="apps-inner">
          <div>
            <img src="http://truckz.mywworld.com/static/images/agentslogo.png" alt="Agent" height="80" width="80">
          </div>
          <div class="apps-label">
            <label>Agent</label>
          </div>
        </div>
      </div>
    </div>
    <script text="text/javascript">
       // function download_agent_application(){
       //   var retVal = confirm("Do you want to download the Agent android application");
       //   if( retVal == true ) {
       //      //document.write ("User wants to continue!");
       //      window.location="http://192.168.8.104/uploads/apps/client/app-debug.apk";
       //      return true;
       //   } else {
       //      //document.write ("User does not want to continue!");
       //      return false;
       //   }
       // }
      
       function download_client_application(){
           var retVal = confirm("Do you want to download the Client android application");
           if( retVal == true ) {
              //document.write ("User wants to continue!");
              window.location="http://192.168.8.104/uploads/apps/client/app-debug.apk";
              return true;
           } else {
              //document.write ("User does not want to continue!");
              return false;
           }
       }
      
       function download_driver_application(){
           var retVal = confirm("Do you want to download the Driver android application");
           if( retVal == true ) {
              //document.write ("User wants to continue!");
              window.location="http://192.168.8.104/uploads/apps/driver/app-debug.apk";
              return true;
           } else {
              //document.write ("User does not want to continue!");
              return false;
           }
       } 
       
    </script>
  </body>
</html>