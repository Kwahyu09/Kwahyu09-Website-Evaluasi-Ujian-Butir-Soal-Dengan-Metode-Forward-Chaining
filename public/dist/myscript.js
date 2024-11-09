const flasData = $(".flash-data").data("flashdata");

if (flasData) {
    Swal.fire("Berhasil!", flasData, "success");
}

const flasDataeror = $(".flash-dataeror").data("flashdataeror");

if (flasDataeror) {
    Swal.fire({
        icon: "error",
        title: "Gagal",
        text: flasDataeror,
    });
}

//tombol hapus
$(".tombol-hapus").on("click", function (e) {
    e.preventDefault();
    const href = $(this).attr("href");

    Swal.fire({
        title: "Apakah Anda Yakin?",
        text: "Akan Menghapus Data Ini!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "Tidak",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Hapus",
    }).then((result) => {
        if (result.value) {
            document.location.href = href;
        }
    });
});

document.querySelectorAll('.tombol-hapusdosen').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const form = this.closest('form');

        Swal.fire({
            title: "Apakah Anda Yakin?",
            text: "Akan Menghapus Data Ini!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Tidak",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

//menampilkan review gambar
function previewImage(){
    const gambar = document.querySelector('#gambar');
    const imgPreview = document.querySelector('.img-preview');

    imgPreview.style.display = 'block';

    const oFReader = new FileReader();
    oFReader.readAsDataURL(gambar.files[0]);
    
    oFReader.onload = function(oFREvent){
        imgPreview.src = oFREvent.target.result;
    }
}

function previewImagea(){
    const opsi_a = document.querySelector('#opsi_a');
    const imgPreview = document.querySelector('.img-previewa');

    imgPreview.style.display = 'block';

    const oFReader = new FileReader();
    oFReader.readAsDataURL(opsi_a.files[0]);
    
    oFReader.onload = function(oFREvent){
        imgPreview.src = oFREvent.target.result;
    }
}
function previewImageb(){
    const opsi_b = document.querySelector('#opsi_b');
    const imgPreview = document.querySelector('.img-previewb');

    imgPreview.style.display = 'block';

    const oFReader = new FileReader();
    oFReader.readAsDataURL(opsi_b.files[0]);
    
    oFReader.onload = function(oFREvent){
        imgPreview.src = oFREvent.target.result;
    }
}
function previewImagec(){
    const opsi_c = document.querySelector('#opsi_c');
    const imgPreview = document.querySelector('.img-previewc');

    imgPreview.style.display = 'block';

    const oFReader = new FileReader();
    oFReader.readAsDataURL(opsi_c.files[0]);
    
    oFReader.onload = function(oFREvent){
        imgPreview.src = oFREvent.target.result;
    }
}
function previewImaged(){
    const opsi_d = document.querySelector('#opsi_d');
    const imgPreview = document.querySelector('.img-previewd');

    imgPreview.style.display = 'block';

    const oFReader = new FileReader();
    oFReader.readAsDataURL(opsi_d.files[0]);
    
    oFReader.onload = function(oFREvent){
        imgPreview.src = oFREvent.target.result;
    }
}
function previewImagee(){
    const opsi_e = document.querySelector('#opsi_e');
    const imgPreview = document.querySelector('.img-previewe');

    imgPreview.style.display = 'block';

    const oFReader = new FileReader();
    oFReader.readAsDataURL(opsi_e.files[0]);
    
    oFReader.onload = function(oFREvent){
        imgPreview.src = oFREvent.target.result;
    }
}
