define(['angular'], function (angular) {

    function ChampCvCtrl($rootScope, gradeResumeService) {
        this.gradeResumeService = gradeResumeService;
        this.clean();
        
        $rootScope.$on("login:success", function (e, data) {
            this.user = data.user;
            $rootScope.user = this.user;
            this.isLoggedIn = true;
            this.nextPage(2);
        }.bind(this));

        $rootScope.$on("registration:clicked", function () {
            this.nextPage(1);
        }.bind(this));

        $rootScope.$on("registration:success", function (e, data) {
            this.user = data.user;
            $rootScope.user = this.user;
            this.isLoggedIn = true;
            this.nextPage(2);
        }.bind(this));

        $rootScope.$on("resume:chosen", function (e, data) {
            this.resumesList = data.list;
            this.firstIndex = data.index;
            this.currentIndex = data.index;
            this.currentResume = this.resumesList[this.currentIndex];

            this.gradeResumeService.resumesList = data.list;
            this.gradeResumeService.firstResumeIndex = data.index;

            this.nextPage(3);
        }.bind(this));

        $rootScope.$on("movePage:homePage", function () {
            this.nextPage(2);
        }.bind(this));
    }

    ChampCvCtrl.prototype = {
        // move to next page or a specific page 'pageNum'
        nextPage: function (pageNum) {
            this.removePage();
            this.currentPage = pageNum || pageNum === 0 ? pageNum : this.currentPage + 1;
            this.pages[this.currentPage] = 1;
        },
        // move to prev page
        prevPage: function () {
            this.removePage();
            this.currentPage--;
            this.pages[this.currentPage] = 1;
        },
        // clear page
        removePage: function () {
            this.pages[this.currentPage] = 0;
        }/*,
        // return to choose musician page
        startOver: function () {
            this.nextPage(1);
            this._initParams();
        }*/,
        logoClicked: function () {
            if (this.isLoggedIn) {
                this.nextPage(2);
            } else {
                this.nextPage(0);
            }
        },
        settingsClicked: function () {
            this.nextPage(4);
        },
        personalZoneClicked: function () {
            this.nextPage(5);
        },
        logoutClicked: function () {
            this.clean();
            this.nextPage(0);
        },
        clean: function () {
            this.currentPage = 0;
            this.pages = [1, 0, 0, 0, 0, 0];
            this.isLoggedIn = false;
        }
    }

    return ChampCvCtrl;
});

