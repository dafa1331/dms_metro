<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 4px; text-align: center; }
        .text-left { text-align: left; }
    </style>
</head>
<body>

<h3 style="text-align:center;">
BADAN KEPEGAWAIAN DAN PENGEMBANGAN SDM<br>
KOTA METRO
</h3>

<h4 style="text-align:center;">
LAPORAN DOKUMEN PEGAWAI<br>
OPD: {{ $opd->nama_opd }}
</h4>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>DRH</th>
            <th>SPMT</th>
            <th>Pertek</th>
            <th>SK CPNS</th>
            <th>SK PNS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pegawai as $index => $p)
        <tr>
            <td>{{ $index+1 }}</td>
            <td class="text-left">{{ $p->nama_lengkap }}</td>

            <td>{{ $p->dokumen->where('type','DRH')->count() ? 'ada' : '-' }}</td>
            <td>{{ $p->dokumen->where('type','SPMT')->count() ? 'ada' : '-' }}</td>
            <td>{{ $p->dokumen->where('type','PERTEK_NIP')->count() ? 'ada' : '-' }}</td>
            <td>{{ $p->dokumen->where('type','SK_CPNS')->count() ? 'ada' : '-' }}</td>
            <td>{{ $p->dokumen->where('type','SK_PNS')->count() ? 'ada' : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
