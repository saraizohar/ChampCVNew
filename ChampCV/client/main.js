// Configuration Options
require.config({
    baseUrl: 'client',
    // paths: maps ids with paths (no extension)
    paths: {
        'angular': 'libs/angular',
        'googleCharts': 'https://www.gstatic.com/charts/loader',
        'angular-route': 'libs/angular-route',
        'pdf': 'https://rawgithub.com/mozilla/pdf.js/gh-pages/build/pdf.js'
    },
    // shim: makes external libraries reachable
    shim: {
        angular: {
            exports: 'angular'
        },
        googleCharts: {
            exports: 'googleCharts'
        },
        'angular-route': {
            exports: 'angular-route',
            deps: ['angular']
        },
        pdf: {
            exports: 'pdf'
        }
    }
});

// Angular Bootstrap 
require(['champCvApp'], function (champCvApp) {
    // initialisation code defined within app.js
    champCvApp.init();
});