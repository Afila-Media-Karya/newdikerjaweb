class Control {
  constructor(type = null) {
    this.type = type;
    this.table = $("#kt_table_data");
    // this.formData = new FormData();
  }

  searchTable(data) {
    this.table.DataTable().search(data).draw();
  }

  overlay_form(type, module, url = null, role = null) {
    $(".title_side_form").html(`${type} ${module}`);
    $(".text-danger").html("");
    let this_tmt = this;

    if (type == "Tambah") {
      $(".form-data")[0].reset();
      if (module !== "Sasaran Kinerja") {
        $(".form-select").val(null).trigger("change");
      }

      $(".form-data").attr("data-type", "add");
      $(".drop-zone__prompt").css("display", "block");
      $(".img-promt").css("display", "block");
      $(".drop-zone__thumb").remove();
      if (module == "Layanan Cuti") {
        $("#keterangan-perubahan").css("display", "none");
      }
    } else {
      $(".form-data").attr("data-type", "update");

      if (module == "Layanan Cuti") {
        $("#jenis_layanan").prop("disabled", true);
      }

      if (module == "Kehadiran") {
        $("#id_satuan_kerja").prop("disabled", true);
        $("#id_pegawai").prop("disabled", true);
        $("#tanggal_absen").prop("disabled", true);
      }

      $.ajax({
        url: url,
        method: "GET",
        success: function (res) {
          if (res.success == true) {
            console.log(res);
            if (module == "Layanan Cuti") {
              if (res.data.status == "2") {
                $("#keterangan-perubahan").css("display", "block");
                $("#keterangan-perubahan p").html(`* ${res.data.keterangan}`);
              }
            }

            $.each(res.data, function (x, y) {
              if (x == "aspek_skp") {
                $(".repeater-items").remove();
                $.each(y, function (i, b) {
                  $("#tambah-output-repeater").trigger("click");
                  setTimeout(function () {
                    $(`input[name='repeater_iki[${i}][iki]']`).val(b.iki);
                    $(`input[name='repeater_iki[${i}][satuan]']`).val(b.satuan);
                    $(`input[name='repeater_iki[${i}][target]']`).val(b.target);
                  }, 500);
                });
              }

              if (x == "id_unit_kerja") {
                setTimeout(function () {
                  $("#id_unit_kerja").val(y);
                  $("#id_unit_kerja").trigger("change");
                }, 800);
              }

              if (x == "id_jabatan_masuk") {
                setTimeout(function () {
                  $("#id_jabatan_masuk").val(y);
                  $("#id_jabatan_masuk").trigger("change");
                }, 800);
              }

              if (x == "id_master_jabatan") {
                setTimeout(function () {
                  $("#id_master_jabatan").val(y);
                  $("#id_master_jabatan").trigger("change");
                }, 800);
              }

              if (x === "id_kelompok_jabatan") {
                setTimeout(function () {
                  $("#kelompok_jabatan").val(y);
                  $("#kelompok_jabatan").trigger("change");
                }, 200);
              }

              if (x === "id_lokasi_kerja") {
                setTimeout(function () {
                  $("#id_lokasi_kerja").val(y);
                  $("#id_lokasi_kerja").trigger("change");
                }, 800);
              }

              if (x == "id_satuan_kerja") {
                setTimeout(function () {
                  $("#id_satuan_kerja").val(y);
                  $("#id_satuan_kerja").trigger("change");
                }, 200);
              }

              if (x === "id_lokasi_apel") {
                setTimeout(function () {
                  $("#id_lokasi_apel").val(y);
                  $("#id_lokasi_apel").trigger("change");
                }, 1000);
              }

              if (x === "id_parent") {
                setTimeout(function () {
                  // alert(y);
                  $("#id_parent").val(y);
                  $("#id_parent").trigger("change");
                }, 1500);
              }

              if (x == "id_pegawai") {
                setTimeout(function () {
                  console.log("pegawai");
                  $("#id_pegawai").val(y);
                  $("#id_pegawai").trigger("change");
                }, 1000);
              }

              // if (x == "atasan_langsung_trigger") {
              //   setTimeout(function () {
              //     alert("tes");
              //     this_tmt.push_select_atasan_langsung(
              //       `/master-jabatan/master-jabatan/option-atasan-langsung?jenis_jabatan=${y.jenis_jabatan}&satuan_kerja=${y.id_satuan_kerja}`,
              //       "#id_parent"
              //     );
              //   }, 1000);
              // }

              if (x == "status_kepegawaian") {
                let checklist = y.replace(/ /g, "_");
                $(`#${checklist}`).prop("checked", true);
              }

              if (x == "gambar" || x == "icon") {
                updateThumbnail(x, y);
              }

              // Fungsi updateThumbnail tidak perlu menerima dropZoneElement sebagai argumen
              function updateThumbnail(x, y) {
                const dropZoneElement = document.querySelector(
                  `.dropzone-${x}`
                );
                let thumbnailElement =
                  dropZoneElement.querySelector(".drop-zone__thumb");
                // First time - remove the prompt
                if (dropZoneElement.querySelector(".drop-zone__prompt")) {
                  dropZoneElement.querySelector(".drop-zone__prompt").remove();
                }

                if (dropZoneElement.querySelector(".img-promt")) {
                  dropZoneElement.querySelector(".img-promt").remove();
                }
                // thumbnailElement.dataset.label = y.name;
                // First time - there is no thumbnail element, so lets create it
                if (!thumbnailElement) {
                  thumbnailElement = document.createElement("div");
                  thumbnailElement.classList.add("drop-zone__thumb");
                  dropZoneElement.appendChild(thumbnailElement);
                }

                // Set the background image of the thumbnail
                thumbnailElement.style.backgroundImage = `url('/storage/${y}')`;

                // Show name
                thumbnailElement.dataset.label = y;
              }

              if (
                x !== "gambar" &&
                x !== "jenis_kelamin" &&
                x !== "validation" &&
                x !== "jenis" &&
                x !== "dokumen" &&
                x !== "status" &&
                x !== "foto_ijazah" &&
                x !== "foto_buku_nikah" &&
                x !== "sertifikat" &&
                x !== "memperoleh_tunjangan" &&
                x !== "foto_kartu_keluarga" &&
                x !== "dokumen_pendukung"
              ) {
                if (x == "pelatihan") {
                  let checklist = y.replace(/ /g, "_");
                  $(`#${checklist}`).prop("checked", true);
                }

                $("input[name='" + x + "']").val(y);
                $("select[name='" + x + "']").val(y);
                $("textarea[name='" + x + "']").val(y);
                $("select[name='" + x + "']").trigger("change");
              } else {
                if (x == "memperoleh_tunjangan") {
                  let checklist = y.replace(/ /g, "_");

                  $(`#${checklist}`).attr("checked", true);
                }

                if (x == "jenis_kelamin") {
                  let checklist = y.replace(/ /g, "_");
                  $(`#${checklist}`).prop("checked", true);
                }

                if (x == "validation") {
                  if (y == 1) {
                    $(`#validation`).attr("checked", true);
                  }
                }

                if (x == "jenis") {
                  if (typeof y === "string") {
                    let checklist = y.replace(/ /g, "_");
                    $(`#${checklist}`).attr("checked", true);
                  }
                }

                if (x == "status") {
                  // Periksa apakah y adalah string
                  if (typeof y === "string") {
                    let checklist = y.replace(/ /g, "_");
                    $(`#${checklist}`).attr("checked", true);
                  }
                  // Jika y adalah integer, tambahkan logika tambahan jika diperlukan
                  else if (typeof y === "number") {
                    $(`#${y}`).attr("checked", true);
                  }
                }
              }
            });
          }
        },
        error: function (xhr) {
          alert("gagal");
        },
      });
    }
    // this._offcanvasObject.show();
  }

  submitFormMultipart(url, role_data = null, module = null, method) {
    let this_ = this;
    let table_ = this.table;

    $(".btn-submit").prop("disabled", true);
    $(".btn-submit").html(
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
    );

    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
    });

    $.ajax({
      type: method,
      url: url,
      data: $(".form-data").serialize(),
      success: function (response) {
        console.log(response);
        $(".text-danger").html("");
        if (response.success == true) {
          swal
            .fire({
              text: `${module} berhasil di ${role_data}`,
              icon: "success",
              showConfirmButton: false,
              timer: 1500,
            })
            .then(function () {
              $("#side_form_close").trigger("click");
              table_.DataTable().ajax.reload();
              $("form")[0].reset();
              $("#from_select").val(null).trigger("change");
            });
        } else {
          $("form")[0].reset();
          $("#from_select").val(null).trigger("change");
          Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
        }
      },
      error: function (xhr) {
        console.log(xhr);
        if (xhr.statusText == "Method Not Allowed") {
          Swal.fire(
            "Gagal Memproses data!",
            "Silahkan Hubungi Admin",
            "warning"
          );
        }

        $(".text-danger").html("");
        $.each(xhr.responseJSON["errors"], function (key, value) {
          $(`.${key}_error`).html(" " + value);
          window.location.hash = key;
        });
      },
      complete: function () {
        $(".btn-submit").prop("disabled", false);
        $(".btn-submit").html('<i class="bi bi-file-earmark-diff"></i> Simpan');
      },
    });
  }

  submitFormMultipartData(url, role_data = null, module = null, method) {
    let this_ = this;
    let table_ = this.table;

    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
    });

    $.ajax({
      type: method,
      url: url,
      data: new FormData($(".form-data")[0]),
      contentType: false,
      processData: false,
      success: function (response) {
        $(".text-danger").html("");
        if (response.success == true) {
          swal
            .fire({
              text: `${module} berhasil di ${role_data}`,
              icon: "success",
              showConfirmButton: false,
              timer: 1500,
            })
            .then(function () {
              $("#side_form_close").trigger("click");
              table_.DataTable().ajax.reload();
              $("form")[0].reset();
              $("#from_select").val(null).trigger("change");
            });
        } else {
          $("form")[0].reset();
          $("#from_select").val(null).trigger("change");
          Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
        }
      },
      error: function (xhr) {
        $(".text-danger").html("");
        $.each(xhr.responseJSON["errors"], function (key, value) {
          $(`.${key}_error`).html(value);
        });
      },
    });
  }

  ajaxDelete(url, label) {
    let token = $("meta[name='csrf-token']").attr("content");
    let table_ = this.table;
    Swal.fire({
      title: `Apakah anda yakin akan menghapus data ${label} ?`,
      text: "Anda tidak akan dapat mengembalikan ini!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya, hapus itu!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: "DELETE",
          data: {
            id: $(this).attr("data-id"),
            _token: token,
          },
          success: function (res) {
            swal.fire({
              title: "Menghapus!",
              text: "Data Anda telah dihapus.",
              icon: "success",
              showConfirmButton: false,
              timer: 1500,
            });
            table_.DataTable().ajax.reload();
          },
          error: function (xhr) {
            if (xhr.statusText == "Unprocessable Content") {
              Swal.fire(
                `${xhr.responseJSON.data}`,
                `${xhr.responseJSON.message}`,
                "warning"
              );
            }
          },
        });
      }
    });
  }

  push_select(url, element) {
    $.ajax({
      url: url,
      method: "GET",
      success: function (res) {
        var select2Element = $(element);
        select2Element.html('<option value=""></option>');
        $.each(res.data, function (index, item) {
          var option = new Option(item.text, item.id, false, false);
          select2Element.append(option);
        });
        select2Element.trigger("change.select2");
      },
      error: function (xhr) {
        alert("gagal");
      },
    });
  }

  push_select_pegawai(url, element) {
    //
    $.ajax({
      url: url,
      method: "GET",
      success: function (res) {
        $(element).html("");
        let html = "<option value=''></option>";
        $.each(res.data, function (x, y) {
          html += `<option value="${y.id}" data-tipe="${y.tipe_pegawai}">${y.text}</option>`;
        });
        $(element).html(html);
      },
      error: function (xhr) {
        alert("gagal");
      },
    });
  }

  push_select2(url, element) {
    $.ajax({
      url: url,
      method: "GET",
      success: function (res) {
        $(element).html("");
        let html = "<option value='-'>-</option>";
        $.each(res.data, function (x, y) {
          html += `<option value="${y.id}">${y.text}</option>`;
        });
        $(element).html(html);
      },
      error: function (xhr) {
        alert("gagal");
      },
    });
  }

  push_select_atasan_langsung(url, element) {
    $.ajax({
      url: url,
      method: "GET",
      success: function (res) {
        console.log(res);
        let value = "";
        $(element).html("");
        let html = "<option selected disabled>Pilih</option>";
        $.each(res.data, function (x, y) {
          y.nama !== null
            ? (value = `${y.nama} - ${y.text}`)
            : (value = y.text);
          html += `<option value="${y.id}">${value}</option>`;
        });
        $(element).html(html);
      },
      error: function (xhr) {
        alert("gagal");
      },
    });
  }

  push_select_laporan(url, element) {
    $.ajax({
      url: url,
      method: "GET",
      success: function (res) {
        var select2Element = $(element);
        select2Element.html('<option value="all">Semua</option>');
        $.each(res.data, function (index, item) {
          var option = new Option(item.text, item.id, false, false);
          select2Element.append(option);
        });
        select2Element.trigger("change");
      },
      error: function (xhr) {
        alert("gagal");
      },
    });
  }

  async initDatatable(url, columns, columnDefs) {
    this.table.DataTable().clear().destroy();

    // await this.table.dataTable().clear().draw();
    await this.table.dataTable().fnClearTable();
    await this.table.dataTable().fnDraw();
    await this.table.dataTable().fnDestroy();
    this.table.DataTable({
      responsive: true,
      pageLength: 10,
      order: [[0, "desc"]],
      processing: true,
      // serverSide: true,
      ajax: url,
      columns: columns,
      columnDefs: columnDefs,
      rowCallback: function (row, data, index) {
        var api = this.api();
        var startIndex = api.context[0]._iDisplayStart;
        var rowIndex = startIndex + index + 1;
        $("td", row).eq(0).html(rowIndex);
      },
    });
  }

  dragDrop() {
    document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
      const dropZoneElement = inputElement.closest(".drop-zone");

      dropZoneElement.addEventListener("click", (e) => {
        inputElement.click();
      });

      inputElement.addEventListener("change", (e) => {
        if (inputElement.files.length) {
          console.log(inputElement.files);
          updateThumbnail(dropZoneElement, inputElement.files[0]);
        }
      });

      dropZoneElement.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropZoneElement.classList.add("drop-zone--over");
      });

      ["dragleave", "dragend"].forEach((type) => {
        dropZoneElement.addEventListener(type, (e) => {
          dropZoneElement.classList.remove("drop-zone--over");
        });
      });

      dropZoneElement.addEventListener("drop", (e) => {
        e.preventDefault();
        if (e.dataTransfer.files.length) {
          inputElement.files = e.dataTransfer.files;
          updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
        }

        dropZoneElement.classList.remove("drop-zone--over");
      });
    });

    /**
     * Updates the thumbnail on a drop zone element.
     *
     * @param {HTMLElement} dropZoneElement
     * @param {File} file
     */
    function updateThumbnail(dropZoneElement, file) {
      let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");

      // First time - remove the prompt
      if (dropZoneElement.querySelector(".drop-zone__prompt")) {
        // dropZoneElement.querySelector(".drop-zone__prompt").remove();
        const promptElement =
          dropZoneElement.querySelector(".drop-zone__prompt");
        promptElement.style.display = "none";
      }

      if (dropZoneElement.querySelector(".img-promt")) {
        // dropZoneElement.querySelector(".img-promt").remove();
        const promptElement = dropZoneElement.querySelector(".img-promt");
        promptElement.style.display = "none";
      }

      // First time - there is no thumbnail element, so lets create it
      if (!thumbnailElement) {
        thumbnailElement = document.createElement("div");
        thumbnailElement.classList.add("drop-zone__thumb");
        dropZoneElement.appendChild(thumbnailElement);
      }

      // Show thumbnail for image files
      if (file.type.startsWith("image/")) {
        const reader = new FileReader();
        thumbnailElement.dataset.label = file.name;

        reader.readAsDataURL(file);
        reader.onload = () => {
          thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
        };
      } else {
        thumbnailElement.style.backgroundImage = null;
      }
    }
  }

  resetPassword(url, label, data) {
    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
    });

    Swal.fire({
      title: `Apakah anda yakin akan meng-reset password ${label} ?`,
      text: "Anda tidak akan dapat mengembalikan ini!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya, Reset!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: "POST",
          data: {
            uuid: data,
          },
          success: function (res) {
            swal.fire({
              title: "Reset!",
              text: "Password telah di-reset.",
              icon: "success",
              showConfirmButton: false,
              timer: 1500,
            });
            table_.DataTable().ajax.reload();
          },
          error: function (xhr) {
            if (xhr.statusText == "Unprocessable Content") {
              Swal.fire(
                `${xhr.responseJSON.data}`,
                `${xhr.responseJSON.message}`,
                "warning"
              );
            }
          },
        });
      }
    });
  }

  data_dashboard_pegawai(bulan) {
    $.ajax({
      url: `/dashboard-pegawai-data?bulan=${bulan}`,
      method: "GET",
      success: function (res) {
        let data = res.data;
        let pegawai_dinilai = data.pegawai_dinilai;
        console.log(data);
        $(".info_nama").text(data.pegawai.nama);
        $(".info_nip").text(data.pegawai.nip);
        $(".info_golongan").text(data.pegawai.golongan);
        $(".info_nama_jabatan").text(data.pegawai.nama_jabatan);
        $(".info_nama_satuan_kerja").text(data.pegawai.nama_satuan_kerja);

        if (data.atasan !== null) {
          $(".atasan_nama").text(data.atasan.nama);
          $(".atasan_nip").text(data.atasan.nip);
          $(".atasan_golongan").text(data.atasan.golongan);
          $(".atasan_nama_jabatan").text(data.atasan.nama_jabatan);
          $(".atasan_nama_satuan_kerja").text(data.atasan.nama_satuan_kerja);
        }

        $(".tpp_prestasi_kerja").text(data.tpp.kinerja_maks);
        $(".tpp_kehadiran_kerja").text(data.tpp.kehadiran_maks);
        $(".tpp_pph21").text(data.tpp.pphPsl);
        $(".tpp_jumlah").text(data.tpp.tppNetto);

        $(".count_aktivitas").text(data.persentase_kinerja.total_aktivitas);
        $(".jumlah_pegawai_dinilai").text(data.jumlah_pegawai_dinilai);

        $(".skp_sasaran").text(data.persentase_skp.sasaran);
        $(".skp_realisasi").text(data.persentase_skp.realisasi);
        $(".skp_kinerja").text(`${data.persentase_skp.kinerja} %`);

        $(".kinerja_target").text(data.persentase_kinerja.target);
        $(".kinerja_capaian").text(data.persentase_kinerja.capaian);
        $(".kinerja_prestasi").text(`${data.persentase_kinerja.prestasi} %`);

        $(".kehadiran_hadir").text(data.tpp.jml_hadir);
        $(".kehadiran_apel").text(data.tpp.jml_tidak_apel);
        $(".kehadiran_tanpa_keterangan").text(data.tpp.tanpa_keterangan);
        $(".kehadiran_dinas_luar").text(data.tpp.jml_dinas_luar);
        $(".kehadiran_sakit").text(data.tpp.jml_sakit);
        $(".kehadiran_cuti").text(data.tpp.jml_cuti);
        $(".kehadiran_hari_kerja").text(data.tpp.jml_hari_kerja);
        $(".kehadiran_potongan").text(`${data.tpp.persen_kehadiran_maks} %`);

        $(".title-kinerja").text(
          `Kinerja (${data.tpp.pembagi_nilai_kinerja}%)`
        );
        $(".title-kehadiran").text(
          `Kehadiran (${data.tpp.pembagi_nilai_kehadiran}%)`
        );

        $(".maksimal-kinerja").text(data.tpp.kinerja_maks);
        $(".maksimal-kehadiran").text(data.tpp.kehadiran_maks);

        $(".capaian-kinerja").text(`Rp. ${data.tpp.capaian}`);
        $(".potongan-kehadiran").text(data.tpp.potongan_kehadiran);
        $(".total-potongan-kehadiran").text(data.tpp.jumlahKehadiran);

        $(".box-tpp h1").text(data.tpp.tpp_bulan_ini);
        $(".progress-bar__value").text(`Maksimal ${data.tpp.nilaiPaguTpp}`);

        $(".box-tpp p").text(
          `Belum Termasuk Potongan (JKN + Pph21) ${data.tpp.potongan_jkn_pph_tmt}`
        );
        // potongan_jkn_pph_tmt;

        // tb_pegawai_dinilai;
        let row = "";
        if (pegawai_dinilai.length > 0) {
          $.each(pegawai_dinilai, function (x, y) {
            row += `
            <tr>
              <td>${x + 1}</td>
              <td>${y.nip}</td>
              <td>${y.nama}</td>
              <td>${y.nama_jabatan}</td>
            </tr>
          `;
          });
        } else {
          row += `
            <tr class="text-center">
              <td colspan="4">belum ada data</td>
            </tr>
          `;
        }

        $("#tb_pegawai_dinilai tbody").html(row);
      },
      error: function (xhr) {
        console.log(xhr);
        alert("gagal");
      },
    });
  }

  make_skeleton() {
    var output = '<div class="row">';

    for (let i = 0; i < 10; i++) {
      output += '<div class="col-lg-4">';
      output += '<div class="ph-item">';
      output += '<div class="ph-col-2">';
      output += '<div class="ph-picture"></div>';
      output += "</div>";
      output += "<div>";
      output += '<div class="ph-row">';
      output += '<div class="ph-col-12 big"></div>';
      output += '<div class="ph-col-12"></div>';
      output += '<div class="ph-col-12"></div>';
      output += '<div class="ph-col-12"></div>';
      output += '<div class="ph-col-12"></div>';
      output += '<div class="ph-col-12"></div>';
      output += "</div>";
      output += "</div>";
      output += "</div>";
      output += "</div>";
    }

    output += "</div>";
    return output;
  }

  async persentase_dashboard(bulan) {
    let role = "";
    const persentase_pegawai = await $.ajax({
      url: `/get-dashboard/persentase-pegawai?bulan=${bulan}`,
      method: "GET",
      success: function (res) {
        let data = res.data;
        role = data.role;

        $(".persentase_skp").text(`${data.persentase_skp.toFixed(2)} %`);
        $(".persentase_kinerja").text(
          `${data.persentase_kinerja.toFixed(2)} %`
        );
        $(".persentase_kehadiran").text(
          `${data.persentase_kehadiran.toFixed(2)} %`
        );

        $(".golongan1").text(data.golongan1);
        $(".golongan2").text(data.golongan2);
        $(".golongan3").text(data.golongan3);
        $(".golongan4").text(data.golongan4);
        $(".golongan5").text(data.golongan5);
        $(".pendidikan_menengah").text(data.pendidikan_menengah);
        $(".pendidikan_tinggi").text(data.pendidikan_tinggi);
        $(".jenis_kelamin_l").text(data.jenis_kelamin_l);
        $(".jenis_kelamin_p").text(data.jenis_kelamin_p);
        $(".jumlah_pegawai").text(data.jumlah_pegawai);
        $(".jml_definitif").text(data.jml_definitif);
        $(".jml_plt").text(data.jml_plt);
        $(".jabatan_kosong").text(data.jabatan_kosong);
        $(".pegawai_keluar").text(data.pegawai_keluar);
        $(".pegawai_masuk").text(data.pegawai_masuk);
        $(".pegawai_pensiun").text(data.pegawai_pensiun);

        $(".customProgressBar").css("--value", data.persentase_laki);
      },
      error: function (xhr) {
        console.log(xhr);
        alert("gagal");
      },
    });

    if (role == "administrator") {
      const skeletonHtml = this.make_skeleton();
      $("#kontent-rank-opd").html(skeletonHtml);

      const rank_opd = await $.ajax({
        url: `/get-dashboard/rank-opd?bulan=${bulan}`,
        method: "GET",
        success: function (res) {
          if (res && res.data && Array.isArray(res.data)) {
            let data = res.data;
            let html = "";
            let jml_data = data.length;
            let count_persentase_skp = 0;
            let count_persentase_kinerja = 0;
            let count_persentase_kehadiran = 0;

            $.each(data, function (x, y) {
              if (y.nama_satuan_kerja !== null) {
                let nama_opd = y.nama_satuan_kerja;
                let truncatedNamaOpd =
                  nama_opd.length > 26
                    ? `${nama_opd.slice(0, 26)} ...`
                    : nama_opd;

                html += `<div class="col-lg-4 mb-5">
                  <div class="card">
                      <div class="card-header align-items-center border-0 mt-4">
                          <h3 class="card-title align-items-start flex-column">
                              <span class="fw-bolder mb-2 text-dark">
                              ${truncatedNamaOpd}</span>
                              <div class="row">
                                      <span class="line-header line-blue"></span>
                                      <span class="line-header line-ocean"></span>
                              </div>
                          </h3>
                          <img src="admin/assets/media/icons/arrow-right.svg" alt="" srcset="">

                      </div>
                      <div class="card-body rank-widget">
                          <div class="box-rank">
                              <img src="admin/assets/media/icons/dashboard/rank/pegawai.svg" alt="">
                              <div class="content-icon-pegawai">
                                  <span>Jumlah Pegawai</span>
                                  <p>${y.jumlah_pegawai}</p>
                              </div>
                          </div>
                          <div class="content-general-rank">

                              <div class="d-flex flex-column flex-center">
                                  <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/kinerja.png" style="width:40px" alt="">
                                  <div class="badge badge-primary mb-2">${
                                    y.persentase_skp
                                  } %</div>
                                  <span>Kinerja</span>
                              </div>
                              <div class="d-flex flex-column flex-center">
                                  <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/aktivitas.png" style="width:40px" alt="">
                                  <div class="badge badge-primary mb-2">${
                                    y.persentase_kinerja
                                  } %</div>
                                  <span>Aktivitas</span>
                              </div>
                              <div class="d-flex flex-column flex-center">
                                  <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/kehadiran.svg" style="width:40px" alt="">
                                  <div class="badge badge-primary mb-2">${
                                    y.persentase_kehadiran
                                  } %</div>
                                  <span>Kehadiran</span>
                              </div>
                          </div>
                          <div class="content-general-rank">

                              <div class="d-flex flex-column flex-center">
                                  <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/definitif.svg" style="width:40px" alt="">
                                  <div class="badge badge-primary mb-2">${
                                    y.jml_definitif
                                  }</div>
                                  <span>Definitif</span>
                              </div>
                              <div class="d-flex flex-column flex-center">
                                  <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/plt.svg" style="width:40px" alt="">
                                  <div class="badge badge-primary mb-2">${
                                    y.jml_plt
                                  }</div>
                                  <span>PLT</span>
                              </div>
                              <div class="d-flex flex-column flex-center">
                                  <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/jabatan_kosong.svg" style="width:40px; text-center" alt="">
                                  <div class="badge badge-primary mb-2">${
                                    y.jabatan_kosong
                                  }</div>
                                  <span>Kosong</span>
                              </div>
                          </div>
                          <div class="d-flex mt-5" style="gap:8px;">
                              <div class="box-sm box-sm-success">
                                  <span>Pend. Menengah</span>
                                  <span>${
                                    y.pendidikan_menengah !== null
                                      ? y.pendidikan_menengah
                                      : 0
                                  }</span>
                              </div>
                              <div class="box-sm box-sm-primary">
                                  <span>Pend. Tinggi</span>
                                  <span>${
                                    y.pendidikan_tinggi !== null
                                      ? y.pendidikan_tinggi
                                      : 0
                                  }</span>
                              </div>
                          </div>
                          <div class="d-flex mt-5" style="gap:8px;">
                              <div class="box-xs box-sm-success">
                                  <span>Gol.I</span>
                                  <span>${
                                    y.golongan1 !== null ? y.golongan1 : 0
                                  }</span>
                              </div>
                              <div class="box-xs box-sm-success">
                                  <span>Gol.II</span>
                                  <span>${
                                    y.golongan2 !== null ? y.golongan2 : 0
                                  }</span>
                              </div>
                              <div class="box-xs box-sm-primary">
                                  <span>Gol.III</span>
                                  <span>${
                                    y.golongan3 !== null ? y.golongan3 : 0
                                  }</span>
                              </div>
                              <div class="box-xs box-sm-primary">
                                  <span>Gol.IV</span>
                                  <span>${
                                    y.golongan4 !== null ? y.golongan4 : 0
                                  }</span>
                              </div>
                          </div>
                          <div class="content-general-rank2">

                              <div class="d-flex flex-column">
                                  <div class="box-lg box-sm-primary">
                                      <img src="admin/assets/media/icons/dashboard/man.svg" alt="" srcset="">
                                              <div>
                                                  <span>Laki Laki</span>
                                                  <h1 class="jenis_kelamin_l">
                                                  ${
                                                    y.jml_laki !== null
                                                      ? y.jml_laki
                                                      : 0
                                                  }
                                                  </h1>
                                              </div>
                                  </div>
                              </div>
                              <div class="d-flex flex-column">
                                  <div class="box-lg box-sm-success">
                                      <img src="admin/assets/media/icons/dashboard/women.svg" alt="" srcset="">
                                          <div>
                                              <span>Perempuan</span>
                                              <h1 class="jenis_kelamin_l">${
                                                y.jml_perempuan !== null
                                                  ? y.jml_perempuan
                                                  : 0
                                              }</h1>
                                          </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>`;

                count_persentase_skp += parseInt(y.persentase_skp);
                count_persentase_kinerja += parseInt(y.persentase_kinerja);
                count_persentase_kehadiran += parseInt(y.persentase_kehadiran);
              }
            });

            let result_persentase_skp = count_persentase_skp / jml_data;
            let result_persentase_kinerja = count_persentase_kinerja / jml_data;
            let result_persentase_kehadiran =
              count_persentase_kehadiran / jml_data;
            $(".persentase_skp").html(`${result_persentase_skp.toFixed(2)} %`);
            $(".persentase_kinerja").html(
              `${result_persentase_kinerja.toFixed(2)} %`
            );
            $(".persentase_kehadiran").html(
              `${result_persentase_kehadiran.toFixed(2)} %`
            );
            $("#kontent-rank-opd").html(html);
          }
        },
        error: function (xhr) {
          console.log(xhr);
        },
      });

      // const rank_opd = await $.ajax({
      //   url: `/get-dashboard/rank-opd?bulan=${bulan}`,
      //   method: "GET",
      // })
      //   .then((res) => {
      //     console.log(res);
      //     if (res && res.data && Array.isArray(res.data)) {
      //       let data = res.data;
      //       let html = "";
      //       let jml_data = data.length;
      //       let count_persentase_skp = 0;
      //       let count_persentase_kinerja = 0;
      //       let count_persentase_kehadiran = 0;

      //       $.each(data, function (x, y) {
      //         let nama_opd = y.nama_satuan_kerja;
      //         let truncatedNamaOpd =
      //           nama_opd.length > 26
      //             ? `${nama_opd.slice(0, 26)} ...`
      //             : nama_opd;

      //         html += `<div class="col-lg-4 mb-5">
      //       <div class="card">
      //           <div class="card-header align-items-center border-0 mt-4">
      //               <h3 class="card-title align-items-start flex-column">
      //                   <span class="fw-bolder mb-2 text-dark">
      //                   ${truncatedNamaOpd}</span>
      //                   <div class="row">
      //                           <span class="line-header line-blue"></span>
      //                           <span class="line-header line-ocean"></span>
      //                   </div>
      //               </h3>
      //               <img src="admin/assets/media/icons/arrow-right.svg" alt="" srcset="">

      //           </div>
      //           <div class="card-body rank-widget">
      //               <div class="box-rank">
      //                   <img src="admin/assets/media/icons/dashboard/rank/pegawai.svg" alt="">
      //                   <div class="content-icon-pegawai">
      //                       <span>Jumlah Pegawai</span>
      //                       <p>${y.jumlah_pegawai}</p>
      //                   </div>
      //               </div>
      //               <div class="content-general-rank">

      //                   <div class="d-flex flex-column flex-center">
      //                       <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/kinerja.png" style="width:40px" alt="">
      //                       <div class="badge badge-primary mb-2">${
      //                         y.persentase_skp
      //                       } %</div>
      //                       <span>Kinerja</span>
      //                   </div>
      //                   <div class="d-flex flex-column flex-center">
      //                       <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/aktivitas.png" style="width:40px" alt="">
      //                       <div class="badge badge-primary mb-2">${
      //                         y.persentase_kinerja
      //                       } %</div>
      //                       <span>Aktivitas</span>
      //                   </div>
      //                   <div class="d-flex flex-column flex-center">
      //                       <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/kehadiran.svg" style="width:40px" alt="">
      //                       <div class="badge badge-primary mb-2">${
      //                         y.persentase_kehadiran
      //                       } %</div>
      //                       <span>Kehadiran</span>
      //                   </div>
      //               </div>
      //               <div class="content-general-rank">

      //                   <div class="d-flex flex-column flex-center">
      //                       <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/definitif.svg" style="width:40px" alt="">
      //                       <div class="badge badge-primary mb-2">${
      //                         y.jml_definitif
      //                       }</div>
      //                       <span>Definitif</span>
      //                   </div>
      //                   <div class="d-flex flex-column flex-center">
      //                       <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/plt.svg" style="width:40px" alt="">
      //                       <div class="badge badge-primary mb-2">${
      //                         y.jml_plt
      //                       }</div>
      //                       <span>PLT</span>
      //                   </div>
      //                   <div class="d-flex flex-column flex-center">
      //                       <img class="mb-2" src="admin/assets/media/icons/dashboard/rank/jabatan_kosong.svg" style="width:40px; text-center" alt="">
      //                       <div class="badge badge-primary mb-2">${
      //                         y.jabatan_kosong
      //                       }</div>
      //                       <span>Kosong</span>
      //                   </div>
      //               </div>
      //               <div class="d-flex mt-5" style="gap:8px;">
      //                   <div class="box-sm box-sm-success">
      //                       <span>Pend. Menengah</span>
      //                       <span>${
      //                         y.pendidikan_menengah !== null
      //                           ? y.pendidikan_menengah
      //                           : 0
      //                       }</span>
      //                   </div>
      //                   <div class="box-sm box-sm-primary">
      //                       <span>Pend. Tinggi</span>
      //                       <span>${
      //                         y.pendidikan_tinggi !== null
      //                           ? y.pendidikan_tinggi
      //                           : 0
      //                       }</span>
      //                   </div>
      //               </div>
      //               <div class="d-flex mt-5" style="gap:8px;">
      //                   <div class="box-xs box-sm-success">
      //                       <span>Gol.I</span>
      //                       <span>${
      //                         y.golongan1 !== null ? y.golongan1 : 0
      //                       }</span>
      //                   </div>
      //                   <div class="box-xs box-sm-success">
      //                       <span>Gol.II</span>
      //                       <span>${
      //                         y.golongan2 !== null ? y.golongan2 : 0
      //                       }</span>
      //                   </div>
      //                   <div class="box-xs box-sm-primary">
      //                       <span>Gol.III</span>
      //                       <span>${
      //                         y.golongan3 !== null ? y.golongan3 : 0
      //                       }</span>
      //                   </div>
      //                   <div class="box-xs box-sm-primary">
      //                       <span>Gol.IV</span>
      //                       <span>${
      //                         y.golongan4 !== null ? y.golongan4 : 0
      //                       }</span>
      //                   </div>
      //               </div>
      //                <div class="content-general-rank2">

      //                   <div class="d-flex flex-column">
      //                       <div class="box-lg box-sm-primary">
      //                           <img src="admin/assets/media/icons/dashboard/man.svg" alt="" srcset="">
      //                                   <div>
      //                                       <span>Laki Laki</span>
      //                                       <h1 class="jenis_kelamin_l">
      //                                       ${
      //                                         y.jml_laki !== null
      //                                           ? y.jml_laki
      //                                           : 0
      //                                       }
      //                                       </h1>
      //                                   </div>
      //                       </div>
      //                   </div>
      //                   <div class="d-flex flex-column">
      //                       <div class="box-lg box-sm-success">
      //                           <img src="admin/assets/media/icons/dashboard/women.svg" alt="" srcset="">
      //                               <div>
      //                                   <span>Perempuan</span>
      //                                   <h1 class="jenis_kelamin_l">${
      //                                     y.jml_perempuan !== null
      //                                       ? y.jml_perempuan
      //                                       : 0
      //                                   }</h1>
      //                               </div>
      //                       </div>
      //                   </div>
      //               </div>
      //           </div>
      //       </div>
      //   </div>`;

      //         count_persentase_skp += parseInt(y.persentase_skp);
      //         count_persentase_kinerja += parseInt(y.persentase_kinerja);
      //         count_persentase_kehadiran += parseInt(y.persentase_kehadiran);
      //       });

      //       let result_persentase_skp = count_persentase_skp / jml_data;
      //       let result_persentase_kinerja = count_persentase_kinerja / jml_data;
      //       let result_persentase_kehadiran =
      //         count_persentase_kehadiran / jml_data;
      //       $(".persentase_skp").html(`${result_persentase_skp.toFixed(2)} %`);
      //       $(".persentase_kinerja").html(
      //         `${result_persentase_kinerja.toFixed(2)} %`
      //       );
      //       $(".persentase_kehadiran").html(
      //         `${result_persentase_kehadiran.toFixed(2)} %`
      //       );
      //       $("#kontent-rank-opd").html(html);
      //     } else {
      //       console.log("Respons tidak sesuai yang diharapkan", res);
      //       alert("Gagal mendapatkan data yang sesuai");
      //     }
      //   })
      //   .catch((xhr) => {
      //     console.log("Gagal melakukan permintaan AJAX", xhr);
      //     alert("Gagal melakukan permintaan AJAX");
      //   });
    }
  }

  data_rank_opd(bulan) {}
}
