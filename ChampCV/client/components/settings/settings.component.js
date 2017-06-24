define(['angular'], function (angular) {

    function SettingsCtrl($rootScope, settingsService, fieldsService, fileUploadService) {
        this.settingsService = settingsService;
        this.fieldsService = fieldsService;
        this.$rootScope = $rootScope;
        this.fileUploadService = fileUploadService;
        this.isLoading = true;
        this.isRemoveResume = false;
        this.isError = false;
    }

    SettingsCtrl.prototype = {
        /*
            Called when all bindings are ready
        */
        $onInit: function () {
            // Get user details from server
            this.settingsService.getUserDetails(this.user.cid, this.user.isRecruiter).then(function (result) {
                this.contactDetails = result.data.contactDetails;

                this.resume = angular.extend({}, result.data.resume);
                this.setIsUploadedResume();

                if (!this.user.isRecruiter && this.resume.isUploadedResume) {
                    this.resume.isSendContactDetails = this.resume.isSendContactDetails == 1 ? true : false
                    
                    if (this.resume.isUploadedResume) {
                        this.readResume();
                    }
                }

                this.initFields(result);
                
                $(document).ready(function () {
                    $('ul.tabs').tabs();
                });
            }.bind(this)).catch(function () {
                this.isError = true;
            }.bind(this)).finally(function () {
                this.isLoading = false;
            }.bind(this));
        },
        /*
            Get all possible fields in resume 
            Mark fields that are already in resume 
        */
        initFields: function (result) {
            this.fieldsToGrade = this.fieldsService.getFieldsList();
            this.fieldsToGrade.forEach(function (field) {
                field.model = {};
                field.model['grade'] = false;
                field.model['resume'] = false;

                if (result.data.fieldsToGrade[field.id] == '1') {
                    field.model['grade'] = true;
                }

                if (!this.user.isRecruiter && this.resume.isUploadedResume && result.data.resume.fieldsInResume[field.id] == '1') {
                    field.model['resume'] = true;
                }
            }.bind(this));
        },
        /*
            Called when user chose a new file
        */
        fileChosenHandler: function (event) {
            var ctrl = this.$ctrl;
            ctrl.$rootScope.$evalAsync(function () {
                var file = event.target.files[0];
                if (file) {
                    ctrl.fileChosen = file;
                    ctrl.isFileChosen = true;
                }
                else {
                    ctrl.fileChosen = undefined;
                    ctrl.isFileChosen = false;
                }
            });
        },
        /*
            user clicked "Update" button
        */
        submit: function (tab) {
            var data, atLeastOne, fileValidate;

            this.isError1 = false;
            this.isError2 = false;

            switch (tab) {
                case "contactDetails":
                    data = {
                        cid: this.user.cid,
                        isRecruiter: this.user.isRecruiter,
                        email: this.contactDetails.email === '' ? null : this.setVal(this.contactDetails.email),
                        phoneNumber: this.contactDetails.phoneNumber === '' ? null : this.setVal(this.contactDetails.phoneNumber),
                        city: this.contactDetails.city === '' ? null : this.setVal(this.contactDetails.city),
                        companyName: this.contactDetails.companyName === '' ? null : this.setVal(this.contactDetails.companyName),
                        isSendContactDetails: this.user.isRecruiter ? null : this.resume.isSendContactDetails,
                    };

                    this.settingsService.updateContactDetails(data).then(function () {
                        Materialize.toast('Your contact details were updated.', 3000);
                    }.bind(this)).catch(function () {
                        Materialize.toast('An error occured. Please try again later.', 3000);
                    }.bind(this));
                    break;
                case "gradingSettings":
                    data = {
                        cid: this.user.cid,
                        isRecruiter: this.user.isRecruiter,
                        fieldsToGrade: {}
                    };

                    atLeastOne = false;

                    this.fieldsToGrade.forEach(function (field) {
                        data.fieldsToGrade[field.id] = field.model.grade ? 1 : 0;
                        atLeastOne = atLeastOne || field.model.grade
                    }.bind(this));

                    if (!atLeastOne) {
                        this.isError1 = true;
                        this.errorText1 = 'You muse choose a least one field';
                        return;
                    }

                    this.settingsService.updateFieldsToGrade(data).then(function () {
                        Materialize.toast('The fields you can grade were updated.', 3000);
                    }.bind(this)).catch(function () {
                        Materialize.toast('An error occured. Please try again later.', 3000);
                    }.bind(this));

                    break;
                case "resumeDetails":
                    if (this.user.isRecruiter) {
                        return;
                    }

                    data = {
                        cid: this.user.cid,
                        resumeId: this.setVal(this.resume.id),
                        openQuestion: this.resume.openQuestion === '' ? null : this.setVal(this.resume.openQuestion),
                        fieldsInResume: {},
                        isFileChosen: this.isRemoveResume ? false : this.isFileChosen,
                        isRemoveResume: this.isRemoveResume
                    }

                    if (!this.isRemoveResume) {
                        atLeastOne = false;

                        this.fieldsToGrade.forEach(function (field) {
                            data.fieldsInResume[field.id] = field.model.resume ? 1 : 0;
                            atLeastOne = atLeastOne || field.model.resume;
                        }.bind(this));

                        if (!atLeastOne) {
                            this.isError2 = true;
                            this.errorText2 = 'You muse choose a least one field';
                            return;
                        }
                    }
                    else {
                        this.fileChosen = null;
                        this.isFileChosen = false;
                    }

                    if (this.fileChosen) {
                        // check file type and size
                        fileValidate = this.fileUploadService.validate([this.fileChosen]);
                        if (!fileValidate.success) {
                            switch (fileValidate.error_message) {
                                case 'unAccepedFileType':
                                    this.isError2 = true;
                                    this.errorText2 = "The resume file's type is not supported. Please upload only a PDF file"
                                    break;
                                case 'maxFileSizeExceed':
                                    this.isError2 = true;
                                    this.errorText2 = "The resume file's size exceed the maximum size which is 3MB"
                                    break;
                            }
                            return;
                        }
                    }
                    

                    this.settingsService.updateResumeDetails(data, this.fileChosen).then(function (result) {
                        Materialize.toast('Your resume settings were updated.', 3000);
                        if (result.data.resume && result.data.resume.id) {
                            this.resume.url = result.data.resume.url;
                            this.resume.id = result.data.resume.id;
                            this.setIsUploadedResume();
                            this.readResume();
                        } else {
                            if (this.isRemoveResume) {
                                this.isRemoveResume = false;
                                this.resume = {};
                                this.setIsUploadedResume();
                            }
                        }
                        
                    }.bind(this)).catch(function () {
                        Materialize.toast('An error occured. Please try again later.', 3000);
                    }.bind(this));

                    break;
            }
        },
        /*
            Upload PDF and convert to picture to show it.
        */
        readResume: function (resume) {
            PDFJS.getDocument(resume || this.resume.url).then(function (pdf) {
                pdf.getPage(1).then(function getPageHelloWorld(page) {
                    var scale = 1;
                    var viewport = page.getViewport(scale);
                    //
                    // Prepare canvas using PDF page dimensions
                    //
                    var canvas = document.getElementById('canvas');

                    var context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    //
                    // Render PDF page into canvas context
                    //
                    var task = page.render({ canvasContext: context, viewport: viewport })
                    task.promise.then(function () {
                        $(document).ready(function () {
                            $('.materialboxed').materialbox();
                        });
                    });
                });
            });
            
        },
        /*
            Check whether the user has uploaded a resume 
        */
        setIsUploadedResume: function () {
            this.resume.isUploadedResume = false;
            if(!this.resume || this.resume.id > 0){
                this.resume.isUploadedResume = true;
            }
        },
        /*
            when the val is 'undefined' the JSON.encode remove the property from the object.
            Therefore, we need to put NULL instead of undefined.
        */
        setVal: function (val) {
            return val ? val : null;
        }
      
        
    }

    return {
        templateUrl: 'client/components/settings/settings.view.html',
        controller: ['$rootScope', 'settingsService', 'fieldsService', 'fileUploadService', SettingsCtrl],
        bindings: {
            user: '<'
        }
    }
});