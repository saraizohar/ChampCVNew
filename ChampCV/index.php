<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    <title>ChampCV</title>
    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="client/css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="client/css/style.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="client/css/ChampCvApp.css" type="text/css" rel="stylesheet">
    
</head>
<body>
    <div ng-controller="champCvCtrl as champCvCtrl">
        <nav class="white" role="navigation" ng-if="champCvCtrl.isLoggedIn">
            <div class="nav-wrapper container">
                <a id="logo-container" ng-click="champCvCtrl.logoClicked()" ng-if="champCvCtrl.isLoggedIn" class="brand-logo"><img class="champcv-logo" src="./client/img/LOGO.jpg"></a>
                <ul class="right hide-on-med-and-down" ng-if="champCvCtrl.isLoggedIn">
                    <li><a ng-click="champCvCtrl.personalZoneClicked()"><i class="material-icons personal-zone-button">face</i><span class="personal-zone-button-text">{{champCvCtrl.user.name}}</span></a></li>
                    <li><a ng-click="champCvCtrl.settingsClicked()"><i class="material-icons">settings</i></a></li>
                    <li><a class="logout" ng-click="champCvCtrl.logoutClicked()">Log Out</a></li>
                </ul>
            </div>
        </nav>

        <login ng-if="champCvCtrl.pages[0]"></login>
        <registration ng-if="champCvCtrl.pages[1]"></registration>
        <home-page ng-if="champCvCtrl.pages[2]" data-user="champCvCtrl.user"></home-page>
        <grade-resume ng-if="champCvCtrl.pages[3]" data-resume="champCvCtrl.currentResume" data-user="champCvCtrl.user"></grade-resume>
        <settings ng-if="champCvCtrl.pages[4]" data-user="champCvCtrl.user"></settings>
        <personal-zone ng-if="champCvCtrl.pages[5]" data-user="champCvCtrl.user"></personal-zone>
    </div>

   

    <!--  Scripts-->
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="client/js/materialize.js"></script>
    <script src="client/js/init.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/processing.js/1.4.1/processing-api.min.js"></script>
    <script type="text/javascript" src="https://rawgithub.com/mozilla/pdf.js/gh-pages/build/pdf.js"></script>
    

    <script data-main="client/main" src="./client/libs/require.js"></script>
</body>
</html>
