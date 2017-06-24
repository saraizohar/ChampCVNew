define(['angular'], function (angular) {
    /*
        Comments tab component
    */
    function AnswersCtrl(personalZoneService) {
        this.personalZoneService = personalZoneService;
        
    }

    AnswersCtrl.prototype = {
        /*
            Called when binding are ready
        */
        $onInit: function () {
            // Materialized initialization
            setTimeout(function () {
                $(document).ready(function () {
                    $('.tooltipped').tooltip();
                });
            }, 3000);
            
        },
        /*
            User clicked on "Report" button 
        */
        report: function (comment) {
            var id = comment.id;

            this.personalZoneService.report(this.user.cid, id, false).then(function () {
                Materialize.toast('Thanks for your report', 2000);
            }.bind(this)).catch(function () {
                Materialize.toast('An error occured. Please try again later.', 2000);
            });
        }
    }

    return {
        templateUrl: 'client/components/personal-zone/comments/comments.view.html',
        controller: ['personalZoneService', AnswersCtrl],
        bindings: {
            user: '<',
            commentsInfo: '<'
        }
    }
});