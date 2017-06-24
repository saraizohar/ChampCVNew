define(['angular'], function (angular) {

    function HomePageCtrl($rootScope, homePageService) {
        this.homePageService = homePageService;
        this.$rootScope = $rootScope;
        this.tasksList = [];
        this.isLoading = true;
        this.isError = false;
    }

    HomePageCtrl.prototype = {
        /*
         Called when all bindings are ready
        */
        $onInit: function () {
            // Get resumes list to grade
            this.getTasksList().then(function (response) {
                this.tasksList = response.data.tasksList;
                // uploaed resumed
                this.readTasks();
            }.bind(this)).catch(function () {
                this.isError = true;
            }.bind(this)).finally(function () {
                this.isLoading = false;
            }.bind(this));

        },
        /*
            get resumes to grade list
        */
        getTasksList: function () {
            return this.homePageService.getTasksList(this.user.cid);
        },
        /*
            upload resumes
            convert the first page to picutes in order to show them in cards
        */
        readTasks: function () {
            var task;
            if (this.tasksList) {
                
                for (var i = 0; i < this.tasksList.length; i++) {
                    task = this.tasksList[i];
                    (function (i) {
                        // Upload PDF
                        PDFJS.getDocument(task.url).then(function (pdf) {
                            pdf.getPage(1).then(function (page) {
                                // convert PDF's first page to png
                                var scale = 1;
                                var viewport = page.getViewport(scale);
                                
                                // Prepare canvas using PDF page dimensions
                                
                                var canvas = document.getElementById('canvas' + i);
                                if (!canvas) {
                                    return;
                                }

                                var context = canvas.getContext('2d');
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;
                                
                                // Render PDF page into canvas context
                                
                                var task2 = page.render({ canvasContext: context, viewport: viewport })
                                task2.promise.then(function () {
                                    $(document).ready(function () {
                                        $('.materialboxed').materialbox();
                                    });
                                });
                            });
                        });
                    })(i)
                }
            }
        },
        /*
            user clicked "Grade Me" button. Need to move to Grade resume page
        */
        gradeCV: function (index) {
            var data = {
                list: this.tasksList,
                index: index
            }

            // Notify champCvCtrl that a resume for grade was chosen.
            this.$rootScope.$emit('resume:chosen', data);
        }
        
    }

    return {
        templateUrl: 'client/components/homePage/home-page.view.html',
        controller: ['$rootScope', 'homePageService', HomePageCtrl],
        bindings: {
            user: '<'
        }
    }
});