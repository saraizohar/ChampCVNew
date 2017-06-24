define(['angular'], function (angular) {
    /*
        General statustucs tab component
    */
    function StatisticsCtrl(personalZoneService, closeQuestionsService) {
        this.personalZoneService = personalZoneService;
        this.closeQuestionsService = closeQuestionsService;

        this.questions = closeQuestionsService.getQuestionsList();
    }

    StatisticsCtrl.prototype = {
        /*
            Called When the "General Statistics" bindings was changed
        */
        $onChanges: function () {
            if (!this.statisticsInfo) {
                return;
            }

            var question;
            this.gradePerQuestion = this.statisticsInfo.gradePerQuestion;

            // set for UI
            angular.forEach(this.gradePerQuestion, function (question) {
                question.crowdAvg = parseFloat(question.crowdAvg).toFixed(2);
                question.userAvg = parseFloat(question.userAvg).toFixed(2);
            });

            this.numOfRankers = parseInt(this.statisticsInfo.numOfRankers);
            this.numOfRecruiters = parseInt(this.statisticsInfo.numOfRecruiters);
            this.isEnoughRanks = this.statisticsInfo.isEnoughRanks

            // Get question text per average
            angular.forEach(this.gradePerQuestion, function (value, key) {
                question = this.closeQuestionsService.getQuestionByID(key);
                value.questionText = question.text;
                
            }.bind(this));

        }
    }

    return {
        templateUrl: 'client/components/personal-zone/statistics/statistics.view.html',
        controller: ['personalZoneService', 'closeQuestionsService', StatisticsCtrl],
        bindings: {
            user: '<',
            statisticsInfo: '<'
        }
    }
});