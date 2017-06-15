define(function () {
    function ChartsFactory($q) {

        var isFirstTimeDraw = true;
        var isFirstTimeTableDraw = true;

        function drawPieChart(analyzedData, titleStr, activeTab, col1, col2) {

            var chartData = [[col1, col2]];
            angular.forEach(analyzedData, function (value, key) {
                chartData.push([key, value]);
            }, this);


            var data = google.visualization.arrayToDataTable(chartData);

            var options = {
                title: titleStr,
                width: 900,
                height: 500
            };

            var chart = new google.visualization.PieChart(document.getElementById(activeTab));
            chart.draw(data, options);
        }

        function drawLineChart(analyzedData, titleStr, activeTab, col1, col2) {
            var chartData = [[col1, col2]];
            angular.forEach(analyzedData, function (value, key) {
                chartData.push([key, value]);
            }, this);

            var data = google.visualization.arrayToDataTable(chartData);

            var options = {
                title: titleStr,
                curveType: 'function',
                legend: { position: 'bottom' },
                width: 900,
                height: 500
            };

            var chart = new google.visualization.LineChart(document.getElementById(activeTab));

            chart.draw(data, options);
        }

        function drawHistogramChart(analyzedData, titleStr, activeTabmm, col1, col2) {
            
        }

        function drawBarChart(analyzedData, titleStr, activeTab, col1, col2) {
            var chartData = [[col1, col2, { role: 'style' }]];
            angular.forEach(analyzedData, function (value, key) {
                chartData.push([key, value, 'color: #e5e4e2']);
            }, this);

            var data = google.visualization.arrayToDataTable(chartData);

            var view = new google.visualization.DataView(data);
            view.setColumns([0, 1,
                             {
                                 calc: "stringify",
                                 sourceColumn: 1,
                                 type: "string",
                                 role: "annotation"
                             },
                             2]);

            var options = {
                title: titleStr,
                //width: 900,
                //height: 500,
                bar: { groupWidth: "95%" },
                legend: { position: "none" },
            };
            var chart = new google.visualization.BarChart(document.getElementById(activeTab));
            chart.draw(view, options);
        }

        function drawMaterialBarChart(analyzedData, titleStr, activeTab, cols) {
            analyzedData.unshift(cols);
            var data = google.visualization.arrayToDataTable(analyzedData);

            var options = {
                chart: {
                    title: titleStr
                },
                bars: 'horizontal' // Required for Material Bar Charts.
            };

            var chart = new google.charts.Bar(document.getElementById(activeTab));

            chart.draw(data, options);
        }

        function drawMultiLineChart(analyzedData, titleStr, activeTab, cols) {
            analyzedData.unshift(cols);
             var data = google.visualization.arrayToDataTable(analyzedData);

             var options = {
                 title: titleStr,
                 curveType: 'function',
                 legend: { position: 'bottom' },
                 //width: 1000,
                 //height: 500
             };

             var chart = new google.visualization.LineChart(document.getElementById(activeTab));

             chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        function drawStackedBarChart(analyzedData, titleStr, activeTab, cols) {
            analyzedData.unshift(cols);
            var data = google.visualization.arrayToDataTable(analyzedData);

            var options = {
                legend: { position: 'top', maxLines: 3 },
                bar: { groupWidth: '75%' },
                isStacked: true 
            };

            var options_fullStacked = {
                isStacked: 'percent',
                legend: { position: 'top', maxLines: 3 },
                hAxis: {
                    minValue: 0,
                    ticks: [0, .3, .6, .9, 1]
                }
            };


            var chart = new google.visualization.BarChart(document.getElementById(activeTab));

            chart.draw(data, options);
        }

        function drawColumnChart(analyzedData, titleStr, activeTab, cols, xAxis, yAxis) {
            var data = new google.visualization.DataTable();
            cols.forEach(function (col) {
                data.addColumn(col.type, col.name);
            });

            data.addRows(analyzedData);

            var options = {
                title: titleStr,
                hAxis: {
                    title: xAxis,
                    viewWindow: {
                        min: [7, 30, 0],
                        max: [17, 30, 0]
                    }
                },
                vAxis: {
                    title: yAxis
                },
                width: 800,
                height: 300
            };

            var chart = new google.visualization.ColumnChart(
              document.getElementById(activeTab));

            chart.draw(data, options);
        }

        return {
            drawChart: function (analyzedData, titleStr, activeTab, chartType, col1, col2, yAxis) {
                return $q(function (resolve, reject) {
                    if (isFirstTimeDraw) {
                        google.charts.load("current", { packages: ["corechart", "bar"] });
                        google.charts.setOnLoadCallback(drawChart);
                    } else {
                        drawChart();
                    }

                    function drawChart() {
                        switch (chartType) {
                            case "pie":
                                drawPieChart(analyzedData, titleStr, activeTab, col1, col2);
                                break;
                            case "histogram":
                                drawHistogramChart(analyzedData, titleStr, activeTab, col1, col2);
                                break;
                            case "bar":
                                drawBarChart(analyzedData, titleStr, activeTab, col1, col2);
                                break;
                            case "line":
                                drawLineChart(analyzedData, titleStr, activeTab, col1, col2);
                                break;
                            case "multiline":
                                drawMultiLineChart(analyzedData, titleStr, activeTab, col1);
                                break;
                            case "materialBar":
                                drawMaterialBarChart(analyzedData, titleStr, activeTab, col1);
                                break;
                            case "stackedBar":
                                drawStackedBarChart(analyzedData, titleStr, activeTab, col1);
                                break;
                            case "columnChart":
                                drawColumnChart(analyzedData, titleStr, activeTab, col1, col2, yAxis);
                                break;

                        }

                        isFirstTimeDraw = false;
                        resolve();
                    }
                });

                
            }
        }
    }

    return ChartsFactory;
});