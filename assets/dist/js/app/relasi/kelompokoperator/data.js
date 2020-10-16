var save_label;
var table;

$(document).ready(function () {
  ajaxcsrf();

  table = $("#kelompokoperator").DataTable({
    initComplete: function () {
      var api = this.api();
      $("#kelompokoperator_filter input")
        .off(".DT")
        .on("keyup.DT", function (e) {
          api.search(this.value).draw();
        });
    },
    dom:
      "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [
      {
        extend: "copy",
        exportOptions: { columns: [1, 2, 3] },
      },
      {
        extend: "print",
        exportOptions: { columns: [1, 2, 3] },
      },
      {
        extend: "excel",
        exportOptions: { columns: [1, 2, 3] },
      },
      {
        extend: "pdf",
        exportOptions: { columns: [1, 2, 3] },
      },
    ],
    oLanguage: {
      sProcessing: "loading...",
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + "kelompokoperator/data",
      type: "POST",
    },
    columns: [
      {
        data: "id",
        orderable: false,
        searchable: false,
      },
      { data: "no_identitas" },
      { data: "nama_operator" },
    ],
    columnDefs: [
      {
        targets: 3,
        searchable: false,
        orderable: false,
        title: "kelompok",
        data: "kelompok",
        render: function (data, type, row, meta) {
          let kelompok = data.split(",");
          let badge = [];
          $.each(kelompok, function (i, val) {
            var newkelompok = `<span class="badge bg-maroon">${val}</span>`;
            badge.push(newkelompok);
          });
          return badge.join(" ");
        },
      },
      {
        targets: 4,
        searchable: false,
        orderable: false,
        data: "id_operator",
        render: function (data, type, row, meta) {
          return `<div class="text-center">
									<a href="${base_url}kelompokoperator/edit/${data}" class="btn btn-warning btn-xs">
										<i class="fa fa-pencil"></i>
									</a>
								</div>`;
        },
      },
      {
        targets: 5,
        searchable: false,
        orderable: false,
        data: "id_operator",
        render: function (data, type, row, meta) {
          return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
        },
      },
    ],
    order: [[1, "asc"]],
    rowId: function (a) {
      return a;
    },
    rowCallback: function (row, data, iDisplayIndex) {
      var info = this.fnPagingInfo();
      var page = info.iPage;
      var length = info.iLength;
      var index = page * length + (iDisplayIndex + 1);
      $("td:eq(0)", row).html(index);
    },
  });

  table
    .buttons()
    .container()
    .appendTo("#kelompokoperator_wrapper .col-md-6:eq(0)");

  $(".select_all").on("click", function () {
    if (this.checked) {
      $(".check").each(function () {
        this.checked = true;
        $(".select_all").prop("checked", true);
      });
    } else {
      $(".check").each(function () {
        this.checked = false;
        $(".select_all").prop("checked", false);
      });
    }
  });

  $("#kelompokoperator tbody").on("click", "tr .check", function () {
    var check = $("#kelompokoperator tbody tr .check").length;
    var checked = $("#kelompokoperator tbody tr .check:checked").length;
    if (check === checked) {
      $(".select_all").prop("checked", true);
    } else {
      $(".select_all").prop("checked", false);
    }
  });

  $("#bulk").on("submit", function (e) {
    if ($(this).attr("action") == base_url + "kelompokoperator/delete") {
      e.preventDefault();
      e.stopImmediatePropagation();

      $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        type: "POST",
        success: function (respon) {
          if (respon.status) {
            Swal({
              title: "Berhasil",
              text: respon.total + " data berhasil dihapus",
              type: "success",
            });
          } else {
            Swal({
              title: "Gagal",
              text: "Tidak ada data yang dipilih",
              type: "error",
            });
          }
          reload_ajax();
        },
        error: function () {
          Swal({
            title: "Gagal",
            text: "Ada data yang sedang digunakan",
            type: "error",
          });
        },
      });
    }
  });
});

function bulk_delete() {
  if ($("#kelompokoperator tbody tr .check:checked").length == 0) {
    Swal({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      type: "error",
    });
  } else {
    $("#bulk").attr("action", base_url + "kelompokoperator/delete");
    Swal({
      title: "Anda yakin?",
      text: "Data akan dihapus!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Hapus!",
    }).then((result) => {
      if (result.value) {
        $("#bulk").submit();
      }
    });
  }
}
