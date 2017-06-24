define(['angular'], function (angular) {
    'use strict';
   
    /*
        Handles file upload 
        Checks file type and size
    */
    function ImageUploadFactory($http, $q) {
        var acceptedTypes = {
                'application/pdf': true
            },
            maxFileSize = 3 * 1024 * 1024, // 3MB
            errors = {
                unSupportedBrowser: 'unSupportedBrowser',
                noFiles: 'noFiles',
                unAccepedFileType: 'unAccepedFileType',
                maxFileSizeExceed: 'maxFileSizeExceed',
                failedToUpload: 'failedToUpload'
            };

        function validate(files, config) {
            var maxSize, types;
       
            config = config || {};
            types = config.acceptedTypes || acceptedTypes;
            maxSize = config.maxFileSize || maxFileSize;

            for (var i = 0, l = files.length; i < l; i++) {
                if (!types[files[i].type]) {
                    return { 'success': false, 'error_message': errors.unAccepedFileType };
                }

                if (maxSize < files[i].size) {
                    return { 'success': false, 'error_message': errors.maxFileSizeExceed };
                }
            }

            return { 'success': true, 'error_message': null };
        }

        function uploadFiles(files, url, params, config) {
            var deferred = $q.defer(),
                formData;

            formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append('file', files[i]);
            }

            angular.forEach(params, function (value, key) {
                formData.append(key, value);
            });
            


            $http.post(url, formData, {
                headers: { 'Content-Type': undefined },
                transformRequest: angular.identity
            }).then(function (data) {
                deferred.resolve(data);
            }).error(function (data, status) {
                if (status === 413) {
                    deferred.reject({ 'success': false, 'error': errors.maxFileSizeExceed });
                    return;
                }

                deferred.reject({ 'success': false, 'error': errors.failedToUpload });
            });

            return deferred.promise;
        }
        return {
            acceptedTypes: acceptedTypes,
            uploadFiles: uploadFiles,
            validate: validate
        };

    }
    
    return ImageUploadFactory;
});