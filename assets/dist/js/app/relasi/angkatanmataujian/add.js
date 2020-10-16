function loadAngkatan(id) {
  $("#angkatan option").remove();
  $.getJSON(base_url + "angkatanmataujian/getAngkatanId/" + id, function (
    data
  ) {
    console.log(data);
    let opsi;
    $.each(data, function (key, val) {
      opsi = `
                    <option value="${val.id_angkatan}">${val.nama_angkatan}</option>
                `;
      $("#angkatan").append(opsi);
    });
  });
}

$(document).ready(function () {
  $('[name="mataujian_id"]').on("change", function () {
    loadAngkatan($(this).val());
  });

  $("form#angkatanmataujian select").on("change", function () {
    $(this).closest(".form-group").removeClass("has-error");
    $(this).nextAll(".help-block").eq(0).text("");
  });

  $("form#angkatanmataujian").on("submit", function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    var btn = $("#submit");
    btn.attr("disabled", "disabled").text("Wait...");

    $.ajax({
      url: $(this).attr("action"),
      data: $(this).serialize(),
      method: "POST",
      success: function (data) {
        btn.removeAttr("disabled").text("Simpan");
        console.log(data);
        if (data.status) {
          Swal({
            title: "Sukses",
            text: "Data Berhasil disimpan",
            type: "success",
          }).then((result) => {
            if (result.value) {
              window.location.href = base_url + "angkatanmataujian";
            }
          });
        } else {
          if (data.errors) {
            let j;
            $.each(data.errors, function (key, val) {
              j = $('[name="' + key + '"]');
              j.closest(".form-group").addClass("has-error");
              j.nextAll(".help-block").eq(0).text(val);
              if (val == "") {
                j.parent().addClass("has-error");
                j.nextAll(".help-block").eq(0).text("");
              }
            });
          }
        }
      },
    });
  });
});
