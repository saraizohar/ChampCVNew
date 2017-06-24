define(['angular'], function (angular) {
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
            }.bind(this)).catch(function () {
                Materialize.toast('An error occured. Please try again later.', 2000);
            });
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