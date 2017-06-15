define(function () {
    /**
    * responsible got handling server requests
    **/
    function GradeResumeService($http, $q, homePageService, $rootScope) {

        function isError(response) {
            return (!response.data) || (response.data == "") || (response.data && response.data["error_message"]);
        }

        return {
            grade: function (cid, answers) {
                var dataStr = JSON.stringify(answers);

                return $q(function (resolve, reject) {
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/gradeResume.php',
                        data: 'action=grade&cid=' + cid + '&data='+dataStr,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).then(function successCallback(response) {
                        if (isError(response)) {
                            reject(response);
                            return;
                        }
                        resolve(response);

                    }, function errorCallback(response) {
                        reject(response);
                    });
                });
            },
            set resumesList(list) {
                this._resumesList = list;
            },
            set firstResumeIndex(index) {
                this.cycle = false;
                this.firstIndex = index;
                this.currentIndex = index;
            },
            next: function () {
                return $q(function (resolved, reject) {
                    if (!this.cycle && this.currentIndex < this._resumesList.length - 1) {
                        this.currentIndex++;
                        resolved(this._resumesList[this.currentIndex]);
                    } else if (!this.cycle && 0 < this.firstIndex) {
                        this.cycle = true;
                        this.currentIndex = 0;
                        resolved(this._resumesList[this.currentIndex]);
                    } else if (this.cycle && this.currentIndex < this.firstIndex - 1) {
                        this.currentIndex++;
                        resolved(this._resumesList[this.currentIndex]);
                    } else {
                        this.clean();
                        homePageService.getTasksList($rootScope.user.cid).then(function (response) {
                            this._resumesList = response.data.tasksList;
                            if (this._resumesList.length == 0) {
                                reject({error_message:'No more resumes'});
                                return;
                            }
                            resolved(this._resumesList[this.currentIndex]);
                        }.bind(this)).catch(function () {
                            reject({ error_message: 'Error' });
                        });
                    }
                }.bind(this));
            },
            clean: function () {
                this.currentIndex = 0;
                this.cycle = false;
                this.firstIndex = 0;
                this._resumesList.length = 0;
            },
            report: function (cid, resumeID) {
                return $q(function (resolve, reject) {
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/gradeResume.php',
                        data: 'action=report&cid=' + cid + '&resumeID=' + resumeID,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).then(function successCallback(response) {
                        if (isError(response)) {
                            reject(response);
                            return;
                        }
                        resolve(response);

                    }, function errorCallback(response) {
                        reject(response);
                    });
                });
            }

        }

    }

    return GradeResumeService;
});