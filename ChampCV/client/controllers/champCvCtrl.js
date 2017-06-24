define(['angular'], function (angular) {
    /*
        control index.php file
    */
    function ChampCvCtrl($rootScope, gradeResumeService) {
        this.gradeResumeService = gradeResumeService;
        this.clean();
        
        // listen to login success event
        $rootScope.$on("login:success", function (e, data) {
            this.user = data.user;
            $rootScope.user = this.user;
            this.isLoggedIn = true;
            // Move to home page
            this.nextPage(2);
        }.bind(this));

        // listen to registration clicked event
        $rootScope.$on("registration:clicked", function () {
            // Move to registration page
            this.nextPage(1);
        }.bind(this));

        // listen to registration success event
        $rootScope.$on("registration:success", function (e, data) {
            this.user = data.user;
            $rootScope.user = this.user;
            this.isLoggedIn = true;
            // Move to home page
            this.nextPage(2);
        }.bind(this));

        // listen to resume chosen in home page event. 
        $rootScope.$on("resume:chosen", function (e, data) {
            this.resumesList = data.list;
            this.firstIndex = data.index;
            this.currentIndex = data.index;
            this.currentResume = this.resumesList[this.currentIndex];

            this.gradeResumeService.resumesList = data.list;
            this.gradeResumeService.firstResumeIndex = data.index;

            // Move to grade resume page
            this.nextPage(3);
        }.bind(this));

        // listen to move to home page event
        $rootScope.$on("movePage:homePage", function () {
            // Move to home page
            this.nextPage(2);
        }.bind(this));
    }

    ChampCvCtrl.prototype = {
        /*
            move to next page or a specific page 'pageNum'
        */
        nextPage: function (pageNum) {
            this.removePage();
            this.currentPage = pageNum || pageNum === 0 ? pageNum : this.currentPage + 1;
            this.pages[this.currentPage] = 1;
        },
        /*
            move to prev page
        */
        prevPage: function () {
            this.removePage();
            this.currentPage--;
            this.pages[this.currentPage] = 1;
        },
        /*
            clear page
        */
        removePage: function () {
            this.pages[this.currentPage] = 0;
        },
        /*
            user clicked the Logo:
            if user is logged in - move to home page
            if user is not logged in - move to login page
        */
        logoClicked: function () {
            if (this.isLoggedIn) {
                // Move to home page
                this.nextPage(2);
            } else {
                // Move to login page
                this.nextPage(0);
            }
        },
        /*
            User clicked the settings button
        */
        settingsClicked: function () {
            this.nextPage(4);
        },
        /*
            User clicked the personal zone button
        */
        personalZoneClicked: function () {
            this.nextPage(5);
        },
        /*
            User clicked logout button
        */
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

