var app_tables_pagination_limit = 10;
var length_options = [10, 25, 50, 100];
var length_options_names = [10, 25, 50, 100];
function returnButtons(selector, url = "") {
  var table =
    typeof selector == "string" ? $("body").find("table" + selector) : selector;

  app_tables_pagination_limit = parseFloat(app_tables_pagination_limit);
  if ($.inArray(app_tables_pagination_limit, length_options) == -1) {
    length_options.push(app_tables_pagination_limit);
    length_options_names.push(app_tables_pagination_limit);
  }

  length_options.sort(function (a, b) {
    return a - b;
  });

  length_options_names.sort(function (a, b) {
    return a - b;
  });

  length_options.push(-1);
  length_options_names.push("All");

  var buttons = [
    // {
    //   text: '<i class="fa-solid fa-angles-left"></i>',
    //   className: "btn btn-primary",
    //   action: function (e, dt, node, config) {
    //     if (backUrl) {
    //       window.location.href = backUrl;
    //     } else {
    //       window.history.back();
    //     }
    //   },
    //   titleAttr: "All Leads",
    // },
    // {
    //   extend: "pageLength",
    //   text: "Show 10 rows",
    //   className: "btn btn-default-dt-options dropdown-toggle",
    //   titleAttr: "All Leads",
    // },
    {
      extend: "collection",
      text: "Export",
      className: "exportCollectionBtn btn btn-default-dt-options dropdown-toggle",
      attr: {
        title: "Export data in various formats", // Tooltip text
      },
      buttons: [
        {
          extend: "excel",
          text: "Excel",
          footer: true,
          exportOptions: {
            columns: ":not(.action-column):not(.checkbox-column)",
          },
        },
        {
          extend: "csvHtml5",
          text: "CSV",
          footer: true,
          exportOptions: {
            columns: ":not(.action-column):not(.checkbox-column)",
          },
        },
      ],
    },
  ];
  var bulkActionButtons = [];

  var tableButtons = $("body").find(".table-btn");
  $.each(tableButtons, function () {
    var b = $(this);

    if (b.length && b.attr("data-table")) {
      if ($(table).is(b.attr("data-table"))) {
        bulkActionButtons.push({
          text: b.text(),
          className: "dtBulkActionBtn btn-default-dt-options",
          action: function (e, dt, node, config) {
            b.click();
          },
        });
      }
    }
  });
  if (bulkActionButtons.length) {
    var bulkAction = {
      extend: "collection",
      text: "Bulk Action",
      className: "dtBulkActionBtn btn btn-default-dt-options dropdown-toggle",
      buttons: bulkActionButtons,
    };
    buttons.push(bulkAction);
  }
  console.log("url", url);
  if (url != "") {
    buttons.push({
      text: '<i class="fas fa-sync text-secondary"></i>',
      className: "dtRefreshBtn btn btn-default-dt-options",
      action: function (e, dt, node, config) {
        dt.ajax.reload(null, false); // false to keep the current page
      },
    });
  }

  return buttons;
}

function initDataTable(
  selector,
  url = "",
  order = [],
  columns = null,
  datas = null,
  otherOptions = null,
  method = "POST"
) {
  var tableElement =
    typeof selector === "string"
      ? $("body").find("table" + selector)
      : selector;

  if (tableElement.length === 0) {
    console.error(`Table with selector "${selector}" not found.`);
    return;
  }

  if ($.fn.DataTable.isDataTable(tableElement)) {
    tableElement.DataTable().clear().destroy();
  }
  // console.log('url',url)

  // Basic options setup without redundant properties
  let options = {
    statesave: true,
    pageLength: app_tables_pagination_limit,
    lengthMenu: [length_options, length_options_names],
    autoWidth: false,
    columnDefs: [
      {
        targets: "no-sort",
        orderable: false,
      },
    ],
    dom:
      "<'row'<'col-md-12'<'row'<'col-md-6 d-flex gap-2 ps-2'lB><'col-md-6 pe-2 'f>>>t" +
      "<'col-md-12 d-flex justify-content-between align-items-center'<'info'i><'pagination'p>>",
    processing: true,
    language: {
      emptyTable: "No entries found",
      info: "Showing _START_ to _END_ of _TOTAL_ entries",
      infoEmpty: "Showing 0 to 0 of 0 entries",
      infoFiltered: "(filtered from _MAX_ total entries)",
      lengthMenu: "_MENU_",
      loadingRecords: "Loading...",
      processing: '<div class="dt-loader"></div>',
      search:
        '<div class="input-group"><span class="input-group-addon"><span class="fa fa-search"></span></span>',
      searchPlaceholder: "Search...",
      zeroRecords: "No matching records found",
      paginate: {
        next: "Next",
        previous: "Previous",
      },
      aria: {
        sortAscending: " activate to sort column ascending",
        sortDescending: " activate to sort column descending",
      },
    },
    paging: true,
    scrollCollapse: true,
    scrollX: "100%",
    responsive: false,
    order: order,
    buttons: returnButtons(selector, url),
    initComplete: function () {
      $(".exportCollectionBtn").attr("title", "Select All from Length to export all data").tooltip();
      $(".dtRefreshBtn").attr("title", "Refresh").tooltip();
      $(".dtBulkActionBtn").attr("title", "Bulk Action").tooltip();
      $(".dt-length select").attr("title", "Length").tooltip();
      
    },
  };

  // Setting the AJAX options if a URL is provided
  if (url) {
    options.ajax = {
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      url: url,
      type: method,
      data: function (d) {
        if (datas) {
          for (var key in datas) {
            d[key] = $(datas[key]).val();
          }
        }
      },
      error: function (error) {
        if (error.responseText !== "") {
          alert(error.responseText);
        }
      },
    };
    options.serverSide = true;
  }

  // If columns are provided, add them to options
  if (columns) {
    options.columns = columns;
  }

  // Merge otherOptions without overwriting existing keys
  if (otherOptions) {
    options = { ...options, ...otherOptions };
  }
  options.drawCallback = function () {
    this.api().columns.adjust();
  };

  // if (indexing) {
  //   options.drawCallback = function (settings) {
  //     let api = this.api();
  //     let startIndex = api.page.info().start;

  //     // Update serial numbers in the first column
  //     api
  //       .column(0, {
  //         page: "current",
  //       })
  //       .nodes()
  //       .each(function (cell, i) {
  //         cell.innerHTML = startIndex + i + 1;
  //       });
  //   };
  // }
  // Initialize the DataTable instance
  return tableElement.DataTable(options);
}
