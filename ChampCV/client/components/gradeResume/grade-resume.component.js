define(['angular'], function (angular) {
    /*
        Grade Resume page's components
    */
    function GradeResume($rootScope, $sce, gradeResumeService, fieldsService, closeQuestionsService) {
        this.$sce = $sce;
        this.gradeResumeService = gradeResumeService;
        this.$rootScope = $rootScope;
        this.fieldsService = fieldsService;
        this.closeQuestionsService = closeQuestionsService;

        this.clean();
    }

    GradeResume.prototype = {
        /*
         Called when all bindings are ready
        */
        $onInit: function () {
            this.resumeUrl = this.$sce.trustAsResourceUrl(this.resume.url);
            this.initFieldsList();
            // measure the time took for grading the CV
            this.startMeasureTime();

            $(document).ready(function () {
                // materialized initialization
                $('input#input_text, textarea#textarea1').characterCounter();
                $('.modal').modal({
                    complete: function () {
                        this.moveToNextResume();
                    }.bind(this),
                    dismissible: false,
                });
            }.bind(this));
        },
        /*
            User clicked "Report" button
        */
        report: function () {
            // notify report was clicked
            this.gradeResumeService.report(this.user.cid, this.resume.id).then(function (result) {
                // Show toasd notification
                Materialize.toast('Thanks for your report', 2000, '', function () {
                    this.$rootScope.$evalAsync(function () {
                        // After the toast notification was dismissed, move to next question
                        this.moveToNextResume();
                    }.bind(this));
                }.bind(this));
            }.bind(this)).catch(function (result) {
                var errorMsg = result.data.error_message, toast;
                if (this._isAlreadyReported(errorMsg)) {
                    toast = 'You already reported this resume. We are checking this issue.';
                } else {
                    toast = "An error occured. Let's move to the next CV.";
                }

                Materialize.toast(toast, 2000, '', function () {
                    this.$rootScope.$evalAsync(function () {
                        // After the toast notification was dismissed, move to next question
                        this.moveToNextResume();
                    }.bind(this));
                }.bind(this));
            }.bind(this));
        },
        /*
            User clicked "Skip" Button
        */
        skip: function () {
            this.moveToNextResume();
        },
        /*
            User clicked a star of one of the questions. 
            Need to update relevant question's grade
        */
        starClicked: function ($event, star) {
            var starID = $event.currentTarget.attributes.id.nodeValue;

            // Change the grade according to the star clicked
            star.$parent.question.grade = parseInt(starID);
            star.$parent.question.isNotRelevant = false;
        },
        /*
            User clicked "Not Relevant" button of one of the questions. 
            Need to update relevant question's grade
        */
        notRelevantClicked: function (id) {
            // When not elevant was clicked - the grade is 0
            var question = this.closeQuestions[id - 1];
            if (question.isNotRelevant) {
                question.grade = 0;
            }
        },
        /*
            User clicked "Submit" button
        */
        submit: function () {
            var answers = {},
                points,
                isBuy,
                toastText;

            this.stopMeasureTime();

            // validate the user answers before submiting
            if (this._validate()) {
                
                answers['closeQuestions'] = this.closeQuestions;
                answers['openQuestion'] = this.setVal(this.openAnswer);
                answers['comments'] = this.setVal(this.comments);
                answers['duration'] = this.getDuration();
                answers['isBuyContactDetails'] = this.isBuy;
                answers['isRecruiter'] = this.user.isRecruiter;
                answers['resumeId'] = this.resume.id;
                answers['price'] = this.resume.price;

                isBuy = this.isBuy;

                // Send user ranking to server
                this.gradeResumeService.grade(this.user.cid, answers).then(function (response) {
                    if (isBuy) {
                        // The user is a recruiter that asked to buy the cotnact details. 
                        // Show a modal with all details.
                        points = response.data.points;
                        this.user.points = points;
                        this.contactDetails = response.data.contactDetails;
                        this.isShowModal = true;
                        $('#modal1').modal('open');
                    } else {
                        toastText = 'Thanks!';
                        Materialize.toast(toastText, 3000, '', function () {
                            this.$rootScope.$evalAsync(function () {
                                points = response.data.points;
                                this.user.points = points;
                                this.moveToNextResume();
                            }.bind(this));

                        }.bind(this));
                    }
                }.bind(this)).catch(function (response) {
                    toastText = "An error occured. Let's move to the next CV.";
                    Materialize.toast(toastText, 3000, '', function () {
                        this.$rootScope.$evalAsync(function () {
                            this.moveToNextResume();
                        }.bind(this));

                    }.bind(this));
                }.bind(this));
            }

        },
        /*
            Validate user's answers
        */
        _validate: function () {
            var isAllAnswered = true;
            this.isError = false;

            // Check that the user answers all questions.
            angular.forEach(this.closeQuestions, function (question) {
                isAllAnswered = isAllAnswered && (question.grade > 0 || question.isNotRelevant);
            });

            if (!isAllAnswered) {
                // The user skiped a question - show an error
                this.isError = true;
                this.errorText = 'Please answer all questions';
                return false;
            }

            return true;
        },
        /*
            Clean grade resume form before moving to next question
        */
        clean: function () {
            this.initQuestions();
            this.startDate = null;
            this.endDate = null;
            this.openAnswer = "";
            this.comments = "";
            this.isBuy = false;
            this.isShowModal = false;
        },
        /*
            init array of fields which the resume contains 
        */
        initFieldsList: function () {
            // Check which fields are in the users resume
            var fieldMetadata;
            this.fieldsInResume = [];
            angular.forEach(this.resume.fields, function (value, key) {
                if (value) {
                    fieldMetadata = this.fieldsService.getFieldByID(key);
                    this.fieldsInResume.push(fieldMetadata.name);
                }
            }.bind(this));
        },
        /*
            init array of questions 
        */
        initQuestions: function () {
            this.closeQuestions = this.closeQuestionsService.getQuestionsList();
            this.closeQuestions.forEach(function (question) {
                question.grade = 0;
                question.isNotRelevant = false;
            });
        },
        /*
            functions to measure rankings duration
        */
        startMeasureTime: function () {
            this.startDate = new Date();
        },
        stopMeasureTime: function () {
            this.endDate = new Date();
        },
        getDuration: function () {
            if (this.startDate && this.endDate) {
                return (this.endDate - this.startDate)/1000;
            }
        },
        /*
            handles switching to the next CV
        */
        moveToNextResume: function () {
            var errorMsg;

            this.clean();
            // Get next resume to grade.
            this.gradeResumeService.next().then(function (result) {
                Materialize.fadeInImage('#grade-form');
                this.resume = result;
                this.resumeUrl = this.$sce.trustAsResourceUrl(this.resume.url);
                this.initFieldsList();
                this.startMeasureTime();
            }.bind(this)).catch(function (result) {
                this.$rootScope.$emit('movePage:homePage');
            }.bind(this));
        },
        /*
            when the val is 'undefined' the JSON.encode remove the property from the object.
            Therefore, we need to put NULL instead of undefined.
        */
        setVal: function (val) {
            return val ? val : null;
        },
        /*
            If there were an integrity error, it means that the user has already reported this resume
        */
        _isAlreadyReported: function (errorMsg) {
            return errorMsg.indexOf('Integrity constraint violation') != -1
        }
    }

    return {
        templateUrl: 'client/components/gradeResume/grade-resume.view.html',
        controller: ['$rootScope', '$sce', 'gradeResumeService', 'fieldsService', 'closeQuestionsService', GradeResume],
        bindings: {
            resume: '<',
            user: '<'
        }
    }
});