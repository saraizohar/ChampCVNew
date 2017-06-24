define(function () {
    /**
    * Saves the closed question
    **/
    function CloseQuestionsService() {
        var _closeQuestions = [
                { id: 1, text: "Grade the resume's design", isNotRelevantBtn: false},
                { id: 2, text: "How much does the employment expirience is relevent to the wanted jobs?", isNotRelevantBtn: true},
                { id: 3, text: "Grade the employment expirience paragraph wording", isNotRelevantBtn: true},
                { id: 4, text: "Grade the education paragraph wording", isNotRelevantBtn: true},
                { id: 5, text: "How much does the education expirience is relevent to the wanted jobs?", isNotRelevantBtn: true},
                { id: 6, text: "grade the resume wording", isNotRelevantBtn: false},
                { id: 7, text: "grade the resume", isNotRelevantBtn: false},
                { id: 8, text: "How much does this resume was relevent for you?", isNotRelevantBtn: false}
        ];

        return {
            /*
                retuena new reference of the questions list
            */
            getQuestionsList: function () {
                return this.copy(_closeQuestions);
            },
            /*
                Get question object according to ID
            */
            getQuestionByID: function (id) {
                var wantedQuestion;
                for (var i = 0; i < _closeQuestions.length; i++) {
                    if (_closeQuestions[i].id == id) {
                        wantedQuestion = _closeQuestions[i];
                        break;
                    }
                }
                return wantedQuestion;
            },
            /*
                Copy the list 
            */
            copy: function () {
                var list = [];
                _closeQuestions.forEach(function (question) {
                    list.push(angular.merge({}, question));
                });

                return list;
            }
                    
         }
       
    }

    return CloseQuestionsService;
});