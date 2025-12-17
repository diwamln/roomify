<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Pemberitahuan Penggunaan Ruangan</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 14px;
            line-height: 1.5;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header b {
            font-size: 16px;
        }

        .line {
            border-bottom: 2px solid #000;
            margin-top: 5px;
            margin-bottom: 25px;
        }

        .indent {
            text-indent: 40px;
        }

        ol {
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .ttd-section {
            margin-top: 40px;
            width: 100%;
        }

        .left,
        .right {
            width: 50%;
            float: left;
            text-align: center;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>

    <!-- KOP SURAT -->
    <div>
        <img src="{{ public_path('images/pens.png') }}" width="120" style="float:left; margin-right:20px;">

        <div style="float:left;">
            <b>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,
                RISET, DAN TEKNOLOGI</b><br>

            <b>POLITEKNIK ELEKTRONIKA NEGERI SURABAYA</b><br>
            Jalan Raya ITS, Sukolilo, Surabaya, 60111<br>
            Telepon: +62-31-5947280 (hunting); Fax: +62-31-5946114<br>
            Laman: https://www.pens.ac.id; E-mail: info@pens.ac.id
        </div>

        <div style="clear: both;"></div>
    </div>

    <br>
    <div class="line"></div>
    <br>

    <!-- ISI SURAT -->
    <p class="indent">
        Dengan hormat, melalui surat ini disampaikan bahwa kegiatan
        <b>{{ $booking->title }}</b> telah dijadwalkan dan memperoleh persetujuan penggunaan ruangan.
        Informasi rinci terkait penggunaan ruangan adalah sebagai berikut:
    </p>

    <ol>
        <li>
            {{ $booking->room->name }}
            ({{ $booking->room->gedung }} Lt. {{ $booking->room->lantai }}),
            pada tanggal
            {{ \Carbon\Carbon::parse($booking->start_time)->translatedFormat('d F Y') }}
            pukul
            {{ \Carbon\Carbon::parse($booking->start_time)->translatedFormat('H:i') }}
            sampai
            {{ \Carbon\Carbon::parse($booking->end_time)->translatedFormat('H:i') }} WIB.
        </li>
    </ol>

    @if($booking->description)
        <p>
            Keterangan tambahan terkait kegiatan tersebut adalah sebagai berikut:
            <br>
            <b>{{ $booking->description }}</b>
        </p>
    @endif

    <p>
        Surat pemberitahuan ini diterbitkan oleh Politeknik Elektronika Negeri Surabaya sebagai bukti resmi
        bahwa penggunaan ruangan untuk kegiatan yang dimaksud telah mendapat persetujuan sesuai data yang tercantum.
    </p>

    <!-- TANDA TANGAN -->
    <div class="ttd-section">

        <div class="left">
            Mengetahui,<br>
            Wakil Direktur Bidang Kerjasama dan Teknologi<br><br><br><br><br>

            <img src="{{ public_path('images/ttd.png') }}" width="120"><br><br>

            <b>Ganjar Pranowo</b><br>
            NIP. 123123123123
        </div>

        <div class="right">
            Dibuat oleh,<br>
            Penanggung Jawab Kegiatan<br><br><br><br><br>

            <b>{{ auth()->user()->name ?? 'Nama Pengaju' }}</b><br>
            NIM/NIP
        </div>

        <div class="clear"></div>
    </div>

</body>

</html>