$(document).ready(function () {
  $("#formoperator").on("submit", function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    var btn = $("#submit");

    btn.attr("disabled", "disabled").text("Wait...");

    $.ajax({
      url: $(this).attr("action"),
      data: $(this).serialize(),
      type: "POST",
      success: function (response) {
        btn.removeAttr("disabled").text("Update");
        console.log(response);
        if (response.status) {
          Swal("Sukses", "Data Berhasil diupdate", "success").then((result) => {
            if (result.value) {
              window.location.href = base_url + "operator";
            }
          });
        } else {
          $.each(response.errors, function (key, val) {
            $('[name="' + key + '"]')
              .closest(".form-group")
              .addClass("has-error");
            $('[name="' + key + '"]')
              .nextAll(".help-block")
              .eq(0)
              .text(val);
            if (val === "") {
              $('[name="' + key + '"]')
                .closest(".form-group")
                .removeClass("has-error")
                .addClass("has-success");
              $('[name="' + key + '"]')
                .nextAll(".help-block")
                .eq(0)
                .text("");
            }
          });
        }
      },
    });
  });

  $("#formoperator input, #formoperator select").on("change", function () {
    $(this).closest(".form-group").removeClass("has-error has-success");
    $(this).nextAll(".help-block").eq(0).text("");
  });
});
