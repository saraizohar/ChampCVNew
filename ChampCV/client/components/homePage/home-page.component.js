define(['angular'], function (angular) {

    function HomePageCtrl($rootScope, homePageService) {
        this.homePageService = homePageService;
        this.$rootScope = $rootScope;
        this.tasksList = [];
        this.isLoading = true;
        this.isError = false;
    }

    HomePageCtrl.prototype = {
        getTasksList: function () {
            return this.homePageService.getTasksList(this.user.cid);
        },
        $onInit: function () {
            //PDFJS.disableWorker = true;
            this.getTasksList().then(function (response) {
                this.tasksList = response.data.tasksList;
                this.createTasksDesc();
                this.readTasks();
            }.bind(this)).catch(function () {
                this.isError = true;
            }.bind(this)).finally(function () {
                this.isLoading = false;
            }.bind(this));
            
        },
        createTasksDesc: function () {
            var task, keyWords, desc = '';

            for (var i = 0; i < this.tasksList.length; i++) {
                task = this.tasksList[i];
                keyWords = task.keywords;
                keyWords.join(', ');
                keyWords += "...";
                task.taskDesc = keyWords;
            }
        },
        readTasks: function () {
            var task;
            if (this.tasksList) {
                
                for (var i = 0; i < this.tasksList.length; i++) {
                    task = this.tasksList[i];
                    (function (i) {
                        PDFJS.getDocument(task.url).then(function (pdf) {
                            pdf.getPage(1).then(function getPageHelloWorld(page) {
                                var scale = 1;
                                var viewport = page.getViewport(scale);
                                //
                                // Prepare canvas using PDF page dimensions
                                //
                                var canvas = document.getElementById('canvas' + i);
                                if (!canvas) {
                                    return;
                                }

                                var context = canvas.getContext('2d');
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;
                                //
                                // Render PDF page into canvas context
                                //
                                var task2 = page.render({ canvasContext: context, viewport: viewport })
                                task2.promise.then(function () {
                                    //console.log(canvas.toDataURL('image/jpeg'));
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
        gradeCV: function (index) {
            var data = {
                list: this.tasksList,
                index: index
            }

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