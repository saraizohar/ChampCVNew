define(['angular'], function (angular) {

    function PersonalZoneCtrl(personalZoneService) {
        this.personalZoneService = personalZoneService;
        this.isLoading = true;
        this.isError = false;
    }

    PersonalZoneCtrl.prototype = {
        $onInit: function () {
            this.personalZoneService.getAnalyze(this.user.cid).then(function (result) {
                this.isUploadedResume = result.data.isUploadedResume
                this.answers = result.data.answers;
                this.comments = result.data.comments;
                this.statistic = result.data.statistic;
                this.grades = result.data.grades;
            }.bind(this)).catch(function () {
                this.isError = true;
            }.bind(this)).finally(function () {
                this.isLoading = false;
            }.bind(this));

            $(document).ready(function () {
                $('ul.tabs').tabs();
                $('ul.tabs').tabs('select_tab', 'GeneralStats');
            });

            $('.dropdown-button').dropdown({
                    inDuration: 300,
                    outDuration: 225,
                    constrainWidth: false, // Does not change width of dropdown to that of the activator
                    hover: true, // Activate on hover
                    gutter: 0, // Spacing from edge
                    belowOrigin: false, // Displays dropdown below the button
                    alignment: 'left', // Displays dropdown with edge aligned to the left of button
                    stopPropagation: false // Stops event propagation
                }
            );
        }
    }

    return {
        templateUrl: 'client/components/personal-zone/personal-zone.view.html',
        controller: ['personalZoneService', PersonalZoneCtrl],
        bindings: {
            user: '<'
        }
    }
});