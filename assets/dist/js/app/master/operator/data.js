var table;

$(document).ready(function () {
  ajaxcsrf();

  table = $("#operator").DataTable({
    initComplete: function () {
      var api = this.api();
      $("#operator_filter input")
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
        exportOptions: { columns: [1, 2, 3, 4] },
      },
      {
        extend: "print",
        exportOptions: { columns: [1, 2, 3, 4] },
      },
      {
        extend: "excel",
        exportOptions: { columns: [1, 2, 3, 4] },
      },
      {
        extend: "pdf",
        exportOptions: { columns: [1, 2, 3, 4] },
      },
    ],
    oLanguage: {
      sProcessing: "loading...",
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + "operator/data",
      type: "POST",
    },
    columns: [
      {
        data: "id_operator",
        orderable: false,
        searchable: false,
      },
      { data: "no_identitas" },
      { data: "nama_operator" },
      { data: "email" },
      { data: "nama_mataujian" },
    ],
    columnDefs: [
      {
        searchable: false,
        targets: 5,
        data: {
          id_operator: "id_operator",
          ada: "ada",
        },
        render: function (data, type, row, meta) {
          let btn;
          if (data.ada > 0) {
            btn = "";
          } else {
            btn = `<button type="button" class="btn btn-aktif btn-primary btn-xs" data-id="${data.id_operator}">
								<i class="fa fa-user-plus"></i> Aktif
							</button>`;
          }
          return `<div class="text-center">
							<a href="${base_url}operator/edit/${data.id_operator}" class="btn btn-xs btn-warning">
								<i class="fa fa-pencil"></i> Edit
							</a>
							${btn}
						</div>`;
        },
      },
      {
        targets: 6,
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

  table.buttons().container().appendTo("#operator_wrapper .col-md-6:eq(0)");

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

  $("#operator tbody").on("click", "tr .check", function () {
    var check = $("#operator tbody tr .check").length;
    var checked = $("#operator tbody tr .check:checked").length;
    if (check === checked) {
      $(".select_all").prop("checked", true);
    } else {
      $(".select_all").prop("checked", false);
    }
  });

  $("#bulk").on("submit", function (e) {
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
  });

  $("#operator").on("click", ".btn-aktif", function () {
    let id = $(this).data("id");

    $.ajax({
      url: base_url + "operator/create_user",
      data: "id=" + id,
      type: "GET",
      success: function (response) {
        if (response.msg) {
          var title = response.status ? "Berhasil" : "Gagal";
          var type = response.status ? "success" : "error";
          Swal({
            title: title,
            text: response.msg,
            type: type,
          });
        }
        reload_ajax();
      },
    });
  });
});

function bulk_delete() {
  if ($("#operator tbody tr .check:checked").length == 0) {
    Swal({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      type: "error",
    });
  } else {
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
