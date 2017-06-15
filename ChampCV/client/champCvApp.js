define(['angular',
    'googleCharts',
    'angular-route',
    './controllers/champCvCtrl',
    './components/login/login.component',
    './components/registration/registration.component',
    './components/homePage/home-page.component',
    './components/gradeResume/grade-resume.component',
    './components/settings/settings.component',
    './components/personal-zone/personal-zone.component',
    './components/personal-zone/answers/answers.component',
    './components/personal-zone/comments/comments.component',
    './components/personal-zone/statistics/statistics.component',
    './services/login.service',
    './services/registration.service',
    './services/file-upload.service',
    './services/home-page.service',
    './services/grade-resume.service',
    './services/fields.service',
    './services/settings.service',
    './services/personal-zone.service',
    './services/charts.service',
    './services/close-questions.service', ],
    function (angular, googleCharts, angularRoute, champCvCtrl,
            loginCmp,
            registrationCmp,
            homePageCmp,
            gradeResumeCmp,
            settings,
            personalZoneCmp,
            answersCmp,
            commentsCmp,
            statisticsCmp,
            loginService,
            registrationService,
            fileUploadService,
            homePageService,
            gradeResumeService,
            fieldsService,
            settingsService,
            personalZoneService,
            chartsService,
            closeQuestionsService) {
        var app = angular.module('champCvApp', ['ngRoute']);

    app.init = function () {
        angular.bootstrap(document, ['champCvApp']);
    };

  
    app.factory('loginService', ['$http', '$q', loginService]);
    app.factory('registrationService', ['$http', '$q', registrationService]);
    app.factory('fileUploadService', ['$http', '$q', fileUploadService]);
    app.factory('homePageService', ['$http', '$q', homePageService]);
    app.factory('gradeResumeService', ['$http', '$q', 'homePageService', '$rootScope', gradeResumeService]);
    app.factory('fieldsService', [fieldsService]);
    app.factory('settingsService', ['$http', '$q', settingsService]);
    app.factory('personalZoneService', ['$http', '$q', personalZoneService]);
    app.factory('chartsService', ['$q', chartsService]);
    app.factory('closeQuestionsService', [closeQuestionsService]);

    app.controller('champCvCtrl', ['$rootScope', 'gradeResumeService', champCvCtrl]);

    app.component('login', loginCmp);
    app.component('registration', registrationCmp);
    app.component('homePage', homePageCmp);
    app.component('gradeResume', gradeResumeCmp);
    app.component('settings', settings);
    app.component('personalZone', personalZoneCmp);
    app.component('answers', answersCmp);
    app.component('comments', commentsCmp);
    app.component('statistics', statisticsCmp);

    app.directive('customOnChange', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                var onChangeHandler = scope.$eval(attrs.customOnChange);
                element.bind('change', onChangeHandler.bind(scope));
            }
        };
    });
   
    /*app.config(['$routeProvider', function ($routeProvider) {
        debugger;
        $routeProvider.when('/sarai', {
            template: '<login></login>'
        });
    }]);*/
    //app.controller('feelingLuckyCtrl', ['tweetyFactory', 'chartsFactory', 'langFactory', '$rootScope', feelingLuckyCtrl]);

    return app;
    });
