define(function () {
    /**
    * responsible for handling server requests
    **/
    function SettingsService($http, $q) {

        function isError(response) {
            return (!response.data) || (response.data == "") || (response.data && response.data["error_message"]);
        }

        return {
            /*
               get user deails from server
            */
            getUserDetails: function (cid, isRecruiter) {
                return $q(function (resolve, reject) {
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/settings.php',
                        data: 'action=getUserDetails&cid=' + cid + '&isRecruiter=' + isRecruiter,
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
            /*
                Update user contact details
            */
            updateContactDetails: function (data) {
                var dataStr = JSON.stringify(data);

                return $q(function (resolve, reject) {
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/settings.php',
                        data: 'action=updateContactDetails&data=' + dataStr,
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
            /*
                Update user fields to grade 
            */
            updateFieldsToGrade: function (data) {
                var dataStr = JSON.stringify(data);

                return $q(function (resolve, reject) {
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/settings.php',
                        data: 'action=updateFieldsToGrade&data=' + dataStr,
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
            /*
                Update resume details. Contains:
                - resume
                - fields in resume
                - open question
            */
            updateResumeDetails: function (data, file) {
                var deferred = $q.defer(),
                    formData = new FormData();

                formData.append('file', file);
                formData.append('data', JSON.stringify(data));
                formData.append('action', 'updateResumeDetails');

                $http.post('./server/clientAPI/settings.php', formData, {
                    headers: { 'Content-Type': undefined },
                    transformRequest: angular.identity
                }).then(function (response) {
                    if (isError(response)) {
                        deferred.reject(response);
                        return;
                    }
                    deferred.resolve(response);
                }).catch(function (response, status) {
                    if (status === 413) {
                        deferred.reject();
                        return;
                    }

                    deferred.reject();
                });

                return deferred.promise;
            }


        }

    }

    return SettingsService;
});