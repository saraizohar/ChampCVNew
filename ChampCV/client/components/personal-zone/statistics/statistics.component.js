define(['angular'], function (angular) {

    function StatisticsCtrl(personalZoneService, chartsService, closeQuestionsService) {
        this.personalZoneService = personalZoneService;
        this.chartsService = chartsService;
        this.closeQuestionsService = closeQuestionsService;

        this.questions = closeQuestionsService.getQuestionsList();
    }

    StatisticsCtrl.prototype = {
        $onInit: function () {
        },
        $onChanges: function () {
            if (!this.statisticsInfo) {
                return;
            }

            var cols, rows = [], title = 'Average Grade for question', question;
            this.gradePerQuestion = this.statisticsInfo.gradePerQuestion;
            angular.forEach(this.gradePerQuestion, function (question) {
                question.crowdAvg = parseFloat(question.crowdAvg).toFixed(2);
                question.userAvg = parseFloat(question.userAvg).toFixed(2);
            });

            this.numOfRankers = this.statisticsInfo.numOfRankers;
            this.numOfRecruiters = this.statisticsInfo.numOfRecruiters;
            this.isEnoughRanks = this.statisticsInfo.isEnoughRanks

            angular.forEach(this.gradePerQuestion, function (value, key) {
                question = this.closeQuestionsService.getQuestionByID(key);
                value.questionText = question.text;
                //rows.push(['Q' + key, value.crowdAvg, value.userAvg]);
                
            }.bind(this));

            cols = [{ type: 'string', name: 'Question Number' }, { type: 'number', name: 'Crowd AVG.' }, { type: 'number', name: 'Your AVG.' }];
            this.chartsService.drawChart(rows, title, 'AverageGradePerQuestion', "columnChart", cols, 'Question Number', 'Score (scale of 0-5)');
        }
    }

    return {
        templateUrl: 'client/components/personal-zone/statistics/statistics.view.html',
        controller: ['personalZoneService', 'chartsService', 'closeQuestionsService', StatisticsCtrl],
        bindings: {
            user: '<',
            statisticsInfo: '<'
        }
    }
});