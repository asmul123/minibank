<?php

header("Content-type: application/vnd-ms-excel");

header("Content-Disposition: attachment; filename=" . $tgl->tgl . "-" . date('d-m-Y') . ".xls");

header("Pragma: no-cache");

header("Expires: 0");

?>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/Theme/css/bootstrap.min.css" media="screen">
<table class="table table-bordered" border="1">
    <thead>
        <tr>
            <th colspan="12">
                <center>
                    Data Siswa Kelas <?= $kelas->kelas; ?>
                </center>
            </th>
        </tr>
        <tr>
            <th colspan="12">
                <center>
                    SMKN 1 GARUT
                </center>
            </th>
        </tr>
        <tr>
            <th colspan="12">

            </th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama produk</th>
            <th>nama kategori</th>
            <th>jumlah</th>
            <th>nilai</th>
            <th>laba</th>
            <th>laba</th>
            <th>laba</th>
            <th>laba</th>
            <th>laba</th>
            <th>laba</th>
            <th>laba</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($data as $laporan) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $laporan['nama_produk']; ?></td>
                <td><?= $laporan['nama_kategori']; ?></td>
                <td><?= $laporan['jumlah']; ?></td>
                <td><?= $laporan['nilai']; ?></td>

                <td><?= $laporan['laba']; ?></td>
                <td><?= $laporan['laba']; ?></td>
                <td><?= $laporan['laba']; ?></td>
                <td><?= $laporan['laba']; ?></td>
                <td><?= $laporan['laba']; ?></td>
                <td><?= $laporan['laba']; ?></td>

                <td><?= $laporan['status']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>