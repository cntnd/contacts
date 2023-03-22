/* cntnd_contacts */
$(document).ready(function () {
  $(".new_contact").click(function () {
    $("#editor").addClass('active');
  });

  $(".add_contact").click(function () {
    let contact = $(this).data("contact");
    let action = $(this).data("action");
    let source = $(this).data("source");
    let index = $(this).data("index");
    let data = $("#" + contact).val();
    let json = JSON.parse(window.atob(data));
    $('form[name="editor_form"] .editor_form_remove').show();

    $('form[name="editor_form"] :input').each(function () {
      $(this).removeClass("highlight");
      let input = $(this).attr("name");
      let type = $(this).attr("type");
      let value = json[input];
      if (value !== undefined) {
        if (type === "checkbox" || type === "radio") {
          $(this).prop("checked", value);
        } else {
          $(this).val(value);
        }
      }
    });

    if (json["_diff"] !== undefined) {
      $.each(json["_diff"], function (key) {
        $('form[name="editor_form"] input[name="' + key + '"]').addClass("highlight");
      });
    }

    $('form[name="editor_form"] input[name="editor_form_action"]').val(action);
    $('form[name="editor_form"] input[name="editor_form_source"]').val(source);
    $('form[name="editor_form"] input[name="editor_form_index"]').val(index);
    $("#editor").addClass('active');
  });

  $(".editor_form_remove").click(function () {
    $('form[name="editor_form"] input[name="editor_form_action"]').val("delete");
    $('form[name="editor_form"]').submit();
  });

  $(".remove_contact").click(function () {
    let source = $(this).data("source");
    let index = $(this).data("index");
    $('form[name="delete_form"] input[name="editor_form_source"]').val(source);
    $('form[name="delete_form"] input[name="editor_form_index"]').val(index);

    $('form[name="delete_form"]').submit();
  });

  $(".popup .close").click(function () {
    $('form[name="editor_form"] .editor_form_remove').hide();
    $('form[name="editor_form"]').trigger("reset");
    $('form[name="editor_form"] :input').each(function () {
      $(this).removeClass("highlight");
    });

    let overlay = $(this).parent(".popup").parent(".overlay");
    closePopup(overlay);
  });

  $('form[name="editor_form"]').submit(function () {
    let action = $('input[name="editor_form_action"]').val();
    if (action === undefined) {
      $('input[name="editor_form_action"]').val("new");
    }
    // set action for later post
    // validation?
    let overlay = $(this).parent(".popup").parent(".overlay");
    closePopup(overlay);
    return true;
  });

  function closePopup(overlay) {
    if (overlay.hasClass("active")) {
      overlay.toggleClass('active');
    }
  }

  $.getJSON("https://cdn.jsdelivr.net/npm/jexcel@4.6.1/lang/de_DE.json", function (text) {
    let options = {
      data: data,
      columns: columns,
      tableOverflow: true,
      tableWidth: $(".tabs__content").innerWidth(),
      tableHeight: '600px',
      defaultColWidth: 120,
      defaultColAlign: 'left',
      filters: true,
      allowManualInsertColumn: false,
      allowInsertColumn: false,
      allowInsertRow: false,
      allowManualInsertRow: false,
      allowDeleteColumn: false,
      allowManualDeleteColumn: false,
      search: true,
      csvFileName: 'Adressen',
      text: text,
      toolbar: [{
        type: 'i',
        content: 'note_add',
        onclick: function () {
          $('#editor').toggleClass('active');
        }
      }, {
        type: 'i',
        content: 'save',
        onclick: function () {
          store(table.getData());
        }
      }, {
        type: 'i',
        content: 'download',
        onclick: function () {
          table.download();
        }
      }, {
        type: 'i',
        content: 'undo',
        onclick: function () {
          table.undo();
        }
      }, {
        type: 'i',
        content: 'redo',
        onclick: function () {
          table.redo();
        }
      }],
      onchange: function (instance, cell, x, row, value) {
        changeEntry(row)
      },
      ondeleterow: deleteEntry
    }

    let table = $('#spreadsheet').jspreadsheet(options);
  });

  function store(data) {
    let identifier = $('form[name=addresses_form] input[name="addresses_form_update"]');
    let records = JSON.parse(window.atob(identifier.val()));

    let map = [];
    $(records).each(function () {
      map.push(data[this]);
    });

    let b64 = window.btoa(JSON.stringify(map));
    $('form[name=addresses_form] input[name="addresses_form_data"]').val(b64);
    $('form[name=addresses_form]').submit();
  }

  function changeEntry(row) {
    let identifier = $('form[name=addresses_form] input[name="addresses_form_update"]');
    let map = [];
    if (identifier.val() !== "") {
      map = JSON.parse(window.atob(identifier.val()));
    }
    map.push(row);
    let b64 = window.btoa(JSON.stringify(map));
    identifier.val(b64);
  }

  var deleteEntry = function deleteEntry(instance, row, amount, value) {
    let json = [];
    $.each(value, function () {
      let email = this[9].innerHTML;
      let mobile = this[8].innerHTML;
      let telefon = this[7].innerHTML;
      json.push([{"email": email}, {"mobile": mobile}, {"telefon": telefon}]);
    });

    let b64 = window.btoa(JSON.stringify(json));
    $('form[name=delete_form] input[name="editor_form_delete"]').val(b64);
    $('form[name=delete_form]').submit();
  }
});
