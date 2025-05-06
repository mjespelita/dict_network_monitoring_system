$(document).ready(function () {
    var lineChartOptions = {
        chart: {
            type: 'line',
        },
        colors: ["#FF1654", "#247BA0"],
        series: [
            {
                name: 'sales',
                data: [30,40,35,50,49,60,70,91,125]
            },
            {
                name: 'sales 1',
                data: [20,31,35,66,77,34,78,77,100]
            }
        ],
        xaxis: {
            categories: [1991,1992,1993,1994,1995,1996,1997, 1998,1999]
        }
    }

    var lineChart = new ApexCharts(document.querySelector("#lineChart"), lineChartOptions);

    lineChart.render();

    var barChartOptions = {
        chart: {
            type: 'bar'
        },
        series: [{
            name: 'sales',
            data: [30,40,35,50,49,60,70,91,125]
        }],
        xaxis: {
            categories: [1991,1992,1993,1994,1995,1996,1997, 1998,1999]
        }
    }

    var barChart = new ApexCharts(document.querySelector("#barChart"), barChartOptions);

    barChart.render();

    // Line Chart Statistics - User Counts
    var lineChartStatisticsUserCountsOptions = {
        chart: {
            type: 'line',
            height: 300,
            background: '#ffffff',  // White background for the entire chart area
            borderRadius: 10,        // Rounded corners for the chart
            dropShadow: {
                enabled: true,
                top: 1,
                left: 1,
                blur: 3,
                opacity: 0.1
            }
        },
        stroke: {
            curve: 'smooth'  // This makes the lines smooth
        },
        colors: ["#5C78FB"],  // Consistent color for user counts chart
        series: [
            {
                name: 'Active Users',  // Series name
                data: [50, 60, 55, 23, 65, 34, 90, 100, 130]  // Hardcoded data
            }
        ],
        xaxis: {
            categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]  // X-axis categories
        },
        grid: {
            show: true, // Show grid lines
            borderColor: '#ddd',  // Light border color for grid lines
            strokeDashArray: 0,   // Solid grid lines
            position: 'back',      // Place the grid behind the chart
            xaxis: {
                lines: {
                    show: true,   // Show grid lines for x-axis
                }
            },
            yaxis: {
                lines: {
                    show: true,   // Show grid lines for y-axis
                }
            }
        },
    }

    var lineChartStatisticsUserCounts = new ApexCharts(document.querySelector("#lineChartStatisticsUserCounts"), lineChartStatisticsUserCountsOptions);
    lineChartStatisticsUserCounts.render();

    // ==========================================================================================

    // Line Chart Statistics - Usage
    var lineChartStatisticsUsageOptions = {
        chart: {
            type: 'line',
            height: 300,
            background: '#ffffff',
            borderRadius: 10,
            dropShadow: {
                enabled: true,
                top: 1,
                left: 1,
                blur: 3,
                opacity: 0.1
            }
        },
        stroke: {
            curve: 'smooth'
        },
        colors: ["#FFA01B", "#FFDB1B"],  // Consistent color for usage chart
        series: [
            {
                name: 'CPU',  // Series name
                data: [20, 30, 35, 45, 55, 60, 65, 80, 95]  // Hardcoded data
            },
            {
                name: 'Memory',  // Series name
                data: [25, 40, 40, 60, 72, 52, 85, 92, 110]  // Hardcoded data
            }
        ],
        xaxis: {
            categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]  // X-axis categories
        },
        grid: {
            show: true, 
            borderColor: '#ddd',
            strokeDashArray: 0,   
            position: 'back',      
            xaxis: {
                lines: {
                    show: true,  
                }
            },
            yaxis: {
                lines: {
                    show: true,   
                }
            }
        }
    }

    var lineChartStatisticsUsage = new ApexCharts(document.querySelector("#lineChartStatisticsUsage"), lineChartStatisticsUsageOptions);
    lineChartStatisticsUsage.render();

    // ==========================================================================================

    // Line Chart Statistics - Traffic
    // var lineChartStatisticsTrafficOptions = {
    //     chart: {
    //         type: 'line',
    //         height: 300,
    //         background: '#ffffff',
    //         borderRadius: 10,
    //         dropShadow: {
    //             enabled: true,
    //             top: 1,
    //             left: 1,
    //             blur: 3,
    //             opacity: 0.1
    //         }
    //     },
    //     stroke: {
    //         curve: 'smooth'
    //     },
    //     colors: ["#FFD700", "#4B0082"],  // Consistent color for traffic chart
    //     series: [
    //         {
    //             name: 'Received',  // Series name
    //             data: [334, 232, 343, 545, 444, 343, 223, 232, 343]  // Hardcoded data
    //         },
    //         {
    //             name: 'Transmitted',  // Series name
    //             data: [444, 343, 343, 454, 343, 334, 343, 343, 343]  // Hardcoded data
    //         }
    //     ],
    //     xaxis: {
    //         categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]  // X-axis categories
    //     },
    //     grid: {
    //         show: true, 
    //         borderColor: '#ddd',
    //         strokeDashArray: 0,   
    //         position: 'back',      
    //         xaxis: {
    //             lines: {
    //                 show: true,  
    //             }
    //         },
    //         yaxis: {
    //             lines: {
    //                 show: true,   
    //             }
    //         }
    //     }
    // }

    // var lineChartStatisticsTraffic = new ApexCharts(document.querySelector("#lineChartStatisticsTraffic"), lineChartStatisticsTrafficOptions);
    // lineChartStatisticsTraffic.render();

    
    
    // ==========================================================================================

    // Line Chart Statistics - Packets
    var lineChartStatisticsPacketsOptions = {
        chart: {
            type: 'line',
            height: 300,
            background: '#ffffff',
            borderRadius: 10,
            dropShadow: {
                enabled: true,
                top: 1,
                left: 1,
                blur: 3,
                opacity: 0.1
            }
        },
        stroke: {
            curve: 'smooth'
        },
        colors: ["#00FF00", "#8A2BE2"],  // Consistent color for packets chart
        series: [
            {
                name: 'Sent Packets',  // Series name
                data: [100, 300, 234, 111, 433, 343, 444, 232, 111]  // Hardcoded data
            },
            {
                name: 'Received Packets',  // Series name
                data: [112, 211, 233, 443, 334, 333, 232, 434, 544]  // Hardcoded data
            }
        ],
        xaxis: {
            categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]  // X-axis categories
        },
        grid: {
            show: true, 
            borderColor: '#fff',
            strokeDashArray: 0,   
            position: 'back',      
            xaxis: {
                lines: {
                    show: true,  
                }
            },
            yaxis: {
                lines: {
                    show: true,   
                }
            }
        }
    }

    var lineChartStatisticsPackets = new ApexCharts(document.querySelector("#lineChartStatisticsPackets"), lineChartStatisticsPacketsOptions);
    lineChartStatisticsPackets.render();
})
