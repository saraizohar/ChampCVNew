﻿define(['angular'], function (angular) {
    /*
        Answers tab component
    */
    function AnswersCtrl(personalZoneService) {
        this.personalZoneService = personalZoneService;
        
    }

    AnswersCtrl.prototype = {
        /*
            Called when binding are ready
        */
        $onInit: function () {
            setTimeout(function () {
                $(document).ready(function () {
                    $('.tooltipped').tooltip();
                });
            }, 3000);
            
        },
        /*
            User clicked on "Report" button 
        */
        report: function (answer) {
            var id = answer.id;

            this.personalZoneService.report(this.user.cid, id, true).then(function () {
                Materialize.toast('Thanks for your report', 2000);
            }.bind(this)).catch(function (result) {
                var errorMsg = result.data.error_message;
                if (this._isAlreadyReported(errorMsg)) {
                    Materialize.toast('You already reported this answer. We are checking this issue.', 2000);
                } else {
                    Materialize.toast('An error occured. Please try again later.', 2000);
                }
                
            }.bind(this));
        },
        /*
            If there were an integrity error, it means that the user has already reported this answer
        */
        _isAlreadyReported: function (errorMsg) {
            return errorMsg.indexOf('Integrity constraint violation') != -1
        }

    }

    return {
        templateUrl: 'client/components/personal-zone/answers/answers.view.html',
        controller: ['personalZoneService', AnswersCtrl],
        bindings: {
            user: '<',
            answersInfo: '<'
        }
    }
});