@section('page-title', 'Dashboard')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
    {{-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://unpkg.com/bs-brain@2.0.4/components/charts/chart-1/assets/controller/chart-1.js"></script> --}}
    <link rel="stylesheet" href="{{ asset('charts/bar.chart.min.css') }}">
    <style>
        /* Use the same compact, border-led workspace as the lead list. */
        #dashboard-page { min-height:calc(100vh - 40px); padding:16px 24px 24px; background:#fff; font-family:'Outfit',sans-serif; }
        #dashboard-page fieldset { min-width:0; margin:0; padding:0; }
        #dashboard-page .dashboard-kpi-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
        #dashboard-page .dashboard-kpi { display:block; height:100%; text-decoration:none; }
        #dashboard-page .dashboard-kpi .card { height:100%; min-height:100px; margin:0; padding:14px !important; border:1px solid #e2e8f0; border-radius:8px; box-shadow:none !important; transition:border-color .15s ease, transform .15s ease; }
        #dashboard-page .dashboard-kpi:hover .card { border-color:#cbd5e1; transform:translateY(-1px); }
        #dashboard-page .dashboard-kpi .mt-4 { margin-top:0 !important; }
        #dashboard-page .dashboard-kpi .py-4 { padding-top:0 !important; padding-bottom:0 !important; }
        #dashboard-page .dashboard-kpi .total-lead-status { margin:0 !important; color:#475569; font-size:12px; }
        #dashboard-page .dashboard-kpi .total-lead-status b { color:#475569; font-weight:500; }
        #dashboard-page .dashboard-kpi .total-lead-status span { margin-top:8px !important; color:#1e293b; font-size:28px !important; font-weight:600; }
        #dashboard-page .dashboard-kpi img { height:42px !important; opacity:.75; }
        #dashboard-page .dashboard-section { margin-top:18px; border-top:1px solid #e8edf3; padding-top:16px; }
        #dashboard-page .dashboard-section-title { margin:0 0 12px; color:#1e293b; font-size:14px; font-weight:600; }
        #dashboard-page .dashboard-lower-grid { display:grid; grid-template-columns:minmax(300px,.9fr) minmax(420px,1.1fr); gap:18px; align-items:start; }
        #dashboard-page .dashboard-lower-grid > div { width:auto !important; padding:0 !important; }
        #dashboard-page .daily-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        #dashboard-page .c-dashboardInfo { width:auto !important; padding:0; }
        #dashboard-page .c-dashboardInfo .wrap { min-height:78px; padding:12px; border:1px solid #e2e8f0; border-radius:7px; background:#fff; }
        #dashboard-page .c-dashboardInfo__title { margin:0 !important; padding:0 !important; color:#64748b; font-size:12px !important; font-weight:500; }
        #dashboard-page .c-dashboardInfo__count { display:block; margin-top:7px; color:#1e293b; font-size:22px !important; font-weight:600; }
        #dashboard-page .graph { min-height:300px; margin:0; border:1px solid #e2e8f0 !important; border-radius:8px; box-shadow:none !important; }
        #dashboard-page .graph .card-body { padding:14px !important; }
        #dashboard-page #yearSelect { height:32px; margin-left:5px; border:1px solid #dbe3ee; border-radius:5px; color:#475569; background:#fff !important; font-size:12px; }
        #dashboard-page #chtAnimatedBarChart { min-height:240px; }
        @media(max-width:900px) { #dashboard-page .dashboard-kpi-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } #dashboard-page .dashboard-lower-grid { grid-template-columns:1fr; } }
        @media(max-width:640px) { #dashboard-page { padding:12px; } #dashboard-page .dashboard-section { margin-top:16px; } #dashboard-page .daily-grid { grid-template-columns:1fr; } }
    </style>

@endsection
@section('content')
    <div id="dashboard-page"><div class="div LEAD-STATUS">
        <fieldset>
            {{-- <legend><b>Total Lead Status :</b></legend> --}}
            <div class="dashboard-kpi-grid">
                <div>
                    <a href="{{ url('lead_list') }}" class="dashboard-kpi small-box-footer text-end text-decoration-none">
                        <div class="card p-4 shadow-md mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="mt-4 total-lead-status">
                                    <b class="fw-semibold">Total Leads </b>
                                    <span class="d-block position-relative fw-bold text-start mt-md-3"
                                        style="font-size: 28px; line-height: 1;">
                                        {{ $allleads }}
                                    </span>
                                </div>
                                <div class="d-none d-md-block py-4">
                                    <img src="{{ asset('images/total-leads.png') }}" style="height: 60px; "
                                        alt="total leads">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- ./col -->
                <div>
                    <a href="{{ route('lead_list') }}/Open" class="dashboard-kpi small-box-footer text-end text-decoration-none">
                        <div class="card p-4 shadow-md">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="mt-4 total-lead-status">
                                    <b class="fw-semibold">Leads Open</b>
                                    <span class="d-block position-relative fw-bold text-start mt-md-3"
                                        style="font-size: 28px; line-height: 1;">
                                        {{ $allOpenLeads - $allInProcessLeads }}
                                    </span>
                                </div>
                                <div class="d-none d-md-block py-4">
                                    <img src="{{ asset('images/lead-open.png') }}" style="height: 60px; " alt="total leads">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div>
                    <a href="{{ route('lead_list') }}/InProcess" class="dashboard-kpi small-box-footer text-end text-decoration-none">
                        <div class="card p-4 shadow-md">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="mt-4 total-lead-status">
                                    <b class="fw-semibold">Leads In Process </b>
                                    <span class="d-block position-relative fw-bold text-start mt-md-3"
                                        style="font-size: 28px; line-height: 1;">
                                        {{ $allInProcessLeads }}
                                    </span>
                                </div>
                                <div class="d-none d-md-block py-4">
                                    <img src="{{ asset('images/progress.png') }}" style="height: 60px; " alt="total leads">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div>
                    <a href="{{ route('lead_list') }}/Closed" class="dashboard-kpi small-box-footer text-end text-decoration-none">
                        <div class="card p-4 shadow-md">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="mt-4 total-lead-status">
                                    <b class="fw-semibold">Leads Closed</b>
                                    <span class="d-block position-relative fw-bold text-start mt-md-3"
                                        style="font-size: 28px; line-height: 1;">
                                        {{ $allCompletedLeads }}
                                    </span>
                                </div>
                                <div class="d-none d-md-block py-4">

                                    <img src="{{ asset('images/closed.png') }}" style="height: 60px; " alt="total leads">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </fieldset>


        <!-- Small boxes (Stat box) CURRENT DATE-->
        <fieldset class="dashboard-section">
            <!-- Info boxes -->
            <div class="dashboard-lower-grid">
                <div class="col-md-6">
                    <h5 class="dashboard-section-title">Today's Lead Status</h5>
                    <div class="daily-grid">
                        <div class="c-dashboardInfo col-12 col-lg-6 col-md-6">
                            <a href="{{ route('lead_list') }}/Today" class="text-decoration-none">
                                <div class="wrap">
                                    <h4
                                        class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title pt-4 total-lead-status">
                                        Total</h4><span
                                        class="hind-font caption-12 c-dashboardInfo__count">{{ $Todayallleads }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="c-dashboardInfo col-12 col-lg-6 col-md-6">
                            <a href="{{ route('lead_list') }}/TodayPending" class="text-decoration-none">
                                <div class="wrap">
                                    <h4
                                        class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title pt-4 total-lead-status">
                                        Pending</h4><span
                                        class="hind-font caption-12 c-dashboardInfo__count">{{ $TodayallInProcessLeads }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="c-dashboardInfo col-12 col-lg-6 col-md-6">
                            <a href="{{ route('lead_list') }}/TodayActivity" class="text-decoration-none">
                                <div class="wrap">
                                    <h4
                                        class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title pt-4 total-lead-status">
                                        Today Activity</h4><span
                                        class="hind-font caption-12 c-dashboardInfo__count">{{ $TodayallActivityLeads }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="c-dashboardInfo col-12 col-lg-6 col-md-6">
                            <a href="{{ route('lead_list') }}/TodayClosed" class="text-decoration-none">
                                <div class="wrap">
                                    <h4
                                        class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title pt-4 total-lead-status">
                                        Closed</h4><span
                                        class="hind-font caption-12 c-dashboardInfo__count">{{ $TodayallCompletedLeads }}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3 mb-sm-0">
                        <h5 class="dashboard-section-title">Leads Overview</h5>
                    </div>
                    <div class="card widget-card border-light shadow-sm graph overflow-hidden">
                        <div class="card-body p-0">
                            <label for="yearSelect" class="p-2 text-dark">Year:</label>
                            {{--<select id="yearSelect" class="btn-primary p-1 bg-transparent text-dark">
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                            </select>--}}
							<select id="yearSelect" class="btn-primary p-1 bg-transparent text-dark">
								@for ($year = 2023; $year <= date('Y'); $year++)
									<option value="{{ $year }}">{{ $year }}</option>
								@endfor
							</select>
                            {{-- <div id="chart" style="width: 100%; height: 200px;"></div> --}}
                            <div id="chtAnimatedBarChart" class="bcBar"></div>
                            <div id="loaderOverlay"
                                class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25 d-flex justify-content-center align-items-center"
                                style="visibility: hidden; opacity: 0;">
                                <div class="spinner-border text-light spinner-xl" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div></div>
    @endsection
    @section('js')
        <script src="https://d3js.org/d3.v4.min.js"></script>
        <script src="{{ asset('charts/jquery.bar.chart.min.js') }}"></script>

        <script>
            $(function() {
                // var data = getData();
                const yearSelect = document.getElementById("yearSelect");
                // Default year as current year
                const currentYear = new Date().getFullYear();
                yearSelect.value = currentYear;

                generateChart(currentYear);
                yearSelect.addEventListener("change", function() {
                    const selectedYear = this.value;
                    generateChart(selectedYear);
                });

            });
            let chartInstance = null;

            function showLoader() {
                $("#loaderOverlay").css({
                    "visibility": "visible",
                    "opacity": 1,
                });
            }

            // Hide the loader
            function hideLoader() {
                $("#loaderOverlay").css({
                    "visibility": "hidden",
                    "opacity": 0,
                });
            }

            generateChart = function(year) {
                destroyChart();
                showLoader();
                getChartData(year).done(function(data) {
                    initilizeChartData(data); // Pass the AJAX response data to the chart
                    hideLoader();
                }).fail(function() {
                    console.error("Error fetching chart data.");
                    hideLoader();
                });
            }

            function destroyChart() {
                if (chartInstance) {
                    // Assuming the library supports a `destroy` method
                    // This is for example purposes, you may need to adjust this depending on the library you're using
                    if (typeof chartInstance.destroy === "function") {
                        chartInstance.destroy();
                    } else {
                        // If there is no `destroy` method, reset the chart container
                        $("#chtAnimatedBarChart").empty(); // Clear the chart container
                    }
                }
            }

            initilizeChartData = function(chart_data) {
                var options = {
                    data: chart_data, // data for chart rendering
                    params: {
                        // columns from data array for rendering graph
                        group_name: "leads", // title for group name to be shown in legend
                        name: "month", // name for xaxis
                        value: "value", // value for yaxis
                    },
                    horizontal_bars: false, // default chart orientation
                    chart_height: 250, // default chart height in px
                    colors: ['#ffa726', '#67bb6a'], // colors for chart
                    show_legend: true, // show chart legend
                    legend: {
                        // default legend settings
                        position: LegendPosition.bottom, // legend position (bottom/top/right/left)
                        width: 200, // legend width in pixels for left/right
                    },
                    x_grid_lines: false, // show x grid lines
                    y_grid_lines: true, // show y grid lines
                    tweenDuration: 300, // speed for tranistions
                    bars: {
                        // default bar settings
                        padding: 0.075, // padding between bars
                        opacity: 1, // default bar opacity
                        opacity_hover: 0.45, // default bar opacity on mouse hover
                        disable_hover: false, // disable animation and legend on hover
                        hover_name_text: "month", // text for name column for label displayed on bar hover
                        hover_value_text: "leads", // text for value column for label displayed on bar hover
                    },
                    number_format: {
                        // default locale for number format
                        format: ",.2f", // default number format
                        decimal: ".", // decimal symbol
                        thousands: ",", // thousand separator symbol
                        grouping: [3], // thousand separator grouping
                        currency: ["$"], // currency symbol
                    },
                    margin: {
                        // margins for chart rendering
                        top: 0, // top margin
                        right: 35, // right margin
                        bottom: 20, // bottom margin
                        left: 70, // left margin
                    },
                    rotate_x_axis_labels: {
                        // rotate xaxis label params
                        process: true, // process xaxis label rotation
                        minimun_resolution: 720, // minimun_resolution for label rotating
                        bottom_margin: 15, // bottom margin for label rotation
                        rotating_angle: 90, // angle for rotation,
                        x_position: 9, // label x position after rotation
                        y_position: -3, // label y position after rotation
                    },
                };
                chartInstance = $("#chtAnimatedBarChart").animatedBarChart(options);
            }



            function getChartData(year) {
                let graphDataUrl = `${base_url}/get-lead-graph-data?year=${year}`;
                return $.ajax({
                    url: graphDataUrl,
                    type: 'GET',
                    dataType: 'json'
                });
            }


            // document.addEventListener("DOMContentLoaded", function () {
            //     // Define data for each year
            //     const yearlyData = {
            //         "2023": {
            //             totalLeads: [440, 505, 414, 671, 227, 413, 201, 352, 752, 320, 257, 160],
            //             leadsClosed: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16]
            //         },
            //         "2024": {
            //             totalLeads: [480, 450, 420, 680, 230, 420, 210, 340, 760, 330, 260, 170],
            //             leadsClosed: [20, 40, 30, 25, 40, 20, 15, 30, 20, 20, 10, 15]
            //         },
            //         "2025": {
            //             totalLeads: [500, 510, 400, 690, 240, 430, 220, 360, 770, 340, 270, 180],
            //             leadsClosed: [25, 45, 38, 28, 45, 23, 19, 33, 24, 24, 14, 18]
            //         }
            //     };

            //     // Chart options configuration
            //     var options = {
            //         chart: {
            //             height: 380,
            //             type: "line",
            //             zoom: { enabled: false }
            //         },
            //         series: [
            //             { name: "Total leads", type: "column", data: yearlyData["2023"].totalLeads },
            //             { name: "Leads closed", type: "column", data: yearlyData["2023"].leadsClosed }
            //         ],
            //         stroke: {
            //             width: [0, 4],
            //             curve: 'smooth'
            //         },
            //         title: {
            //             // text: "Traffic Sources (Jan - Dec)"
            //         },
            //         xaxis: {
            //             categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            //             labels: { style: { fontSize: '12px' } }
            //         },
            //         yaxis: [
            //             { title: { text: "Total leads" } },
            //             { opposite: true, title: { text: "Leads closed" } }
            //         ]
            //     };

            //     // Initialize and render the chart
            //     var chart = new ApexCharts(document.querySelector("#chart"), options);
            //     chart.render();

            //     // Update chart data when year changes
            //     document.getElementById("yearSelect").addEventListener("change", function () {
            //         const selectedYear = this.value;
            //         chart.updateSeries([
            //             { name: "Total leads", type: "column", data: yearlyData[selectedYear].totalLeads },
            //             { name: "Leads closed", type: "column", data: yearlyData[selectedYear].leadsClosed }
            //         ]);
            //     });
            // });

            // document.addEventListener("DOMContentLoaded", function() {
            //     const chartElement = document.querySelector("#chart");
            //     const yearSelect = document.getElementById("yearSelect");

            //     // Default year as current year
            //     const currentYear = new Date().getFullYear();
            //     yearSelect.value = currentYear;

            //     // Chart options configuration
            //     const options = {
            //         chart: {
            //             height: 380,
            //             type: "line",
            //             zoom: {
            //                 enabled: false
            //             }
            //         },
            //         series: [{
            //                 name: "Total leads",
            //                 type: "column",
            //                 data: []
            //             },
            //             {
            //                 name: "Leads closed",
            //                 type: "column",
            //                 data: []
            //             }
            //         ],
            //         stroke: {
            //             width: [0, 4],
            //             curve: 'smooth'
            //         },
            //         xaxis: {
            //             categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov",
            //                 "Dec"
            //             ],
            //             labels: {
            //                 style: {
            //                     fontSize: '12px'
            //                 }
            //             }
            //         },
            //         yaxis: [{
            //                 title: {
            //                     text: "Total leads"
            //                 }
            //             },
            //             {
            //                 opposite: false,
            //                 title: {
            //                     text: "Leads closed"
            //                 }
            //             }

            //         ]
            //     };

            //     const chart = new ApexCharts(chartElement, options);
            //     chart.render();

            //     function fetchData(year) {
            //         let graphDataUrl = '{{ url('/get-lead-graph-data?year=') }}' + year;
            //         fetch(graphDataUrl)
            //             .then(response => response.json())
            //             .then(data => {
            //                 chart.updateSeries([{
            //                         name: "Total leads",
            //                         type: "column",
            //                         data: data.totalLeads
            //                     },
            //                     {
            //                         name: "Leads closed",
            //                         type: "column",
            //                         data: data.leadsClosed
            //                     }
            //                 ]);
            //             })
            //             .catch(error => console.error('Error fetching data:', error));
            //     }

            //     // Load data for the current year on page load
            //     fetchData(currentYear);

            //     // Update data when the year is changed
            //     yearSelect.addEventListener("change", function() {
            //         const selectedYear = this.value;
            //         fetchData(selectedYear);
            //     });
            // });



            // document.addEventListener("DOMContentLoaded", function() {
            //     const chartElement = document.querySelector("#chart");
            //     const yearSelect = document.getElementById("yearSelect");

            //     // Default year as current year
            //     const currentYear = new Date().getFullYear();
            //     yearSelect.value = currentYear;

            //     // Chart options configuration
            //     const options = {
            //         chart: {
            //             height: 280,
            //             type: "bar",
            //             zoom: {
            //                 enabled: false
            //             },
            //             toolbar: {
            //                 show: false
            //             }
            //         },
            //         series: [{
            //                 name: "Total leads",
            //                 type: "column",
            //                 data: [] // Filled dynamically
            //             },
            //             {
            //                 name: "Leads closed",
            //                 type: "column",
            //                 data: [] // Filled dynamically
            //             }
            //         ],
            //         xaxis: {
            //             categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov",
            //                 "Dec"
            //             ],
            //             labels: {
            //                 style: {
            //                     fontSize: '12px'
            //                 }
            //             }
            //         },
            //         yaxis: {
            //             title: {
            //                 text: "Leads"
            //             }
            //         },
            //         plotOptions: {
            //             bar: {
            //                 columnWidth: '100%',
            //             }
            //         },
            //         dataLabels: {
            //             enabled: true,
            //             formatter: function(val) {
            //                 return val ? val.toFixed(0) : '';
            //             },
            //             offsetY: -10,
            //             style: {
            //                 fontSize: '12px',
            //                 colors: ["#304758"]
            //             }
            //         },
            //         colors: ['#4CAF50', '#FF9800'] // Customize colors if needed
            //     };
            //     console.log('chartElement', chartElement)
            //     const chart = new ApexCharts(chartElement, options);
            //     chart.render();

            //     function fetchData(year) {
            //         const graphDataUrl = `{{ url('/get-lead-graph-data?year=') }}${year}`;

            //         fetch(graphDataUrl)
            //             .then(response => response.json())
            //             .then(data => {
            //                 const totalLeads = data.totalLeads;
            //                 const leadsClosed = data.leadsClosed.map((closed, i) => {
            //                     const closedInt = parseInt(closed, 10) || 0;
            //                     const totalLeadCount = totalLeads[i] || 1; // Avoid division by zero
            //                     return (closedInt / totalLeadCount) * totalLeadCount;
            //                 });

            //                 chart.updateSeries([{
            //                         name: "Total leads",
            //                         data: totalLeads // Directly use total leads
            //                     },
            //                     {
            //                         name: "Leads closed",
            //                         data: leadsClosed // Closed leads as percentage of total leads
            //                     }
            //                 ]);
            //             })
            //             .catch(error => console.error('Error fetching data:', error));
            //     }

            //     // Load data for the current year on page load
            //     fetchData(currentYear);

            //     // Update data when the year is changed
            //     yearSelect.addEventListener("change", function() {
            //         const selectedYear = this.value;
            //         fetchData(selectedYear);
            //     });
            // });
        </script>




        {{-- <script type="text/javascript" src="{{ URL::asset('js/apexcharts.js') }}"></script>
     <script type="text/javascript" src="{{ URL::asset('js/dashboards-analytics.js') }}"></script> --}}
    @endsection
