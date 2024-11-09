function getServerTime() {
    return $.ajax({ async: false }).getResponseHeader("Date");
}
function realtimeClock() {
    var currentTime = new Date();
    var endTime = new Date(
        currentTime.toDateString() + " " + "{{$ujian->waktu_selesai}}"
    );

    var timeRemaining = endTime - currentTime;
    if (timeRemaining <= 0) {
        // Jika waktu berakhir, Anda dapat menambahkan logika atau tindakan yang sesuai di sini.
        // Misalnya, menghentikan penghitungan waktu atau mengirimkan data ke server.
        document.getElementById("clock").innerHTML = "Waktu telah berakhir";
        return;
    }

    var hours = Math.floor((timeRemaining / (1000 * 60 * 60)) % 24);
    var minutes = Math.floor((timeRemaining / 1000 / 60) % 60);
    var seconds = Math.floor((timeRemaining / 1000) % 60);

    hours = ("0" + hours).slice(-2);
    minutes = ("0" + minutes).slice(-2);
    seconds = ("0" + seconds).slice(-2);

    document.getElementById("clock").innerHTML =
        hours + " : " + minutes + " : " + seconds;

    setTimeout(realtimeClock, 500);
}
