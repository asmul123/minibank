<div class="main-page" style="height: 100%;">
    <div class="container-fluid">
        <div class="row page-title-div">
            <div class="col-sm-6">
                <h2 class="title">Export Data Siswa</h2>
                <p class="sub-title">SIMBMS (Sistem Informasi Bank Mini Sekolah)</p>
            </div>
            <!-- /.col-sm-6 -->
            <!-- <div class="col-sm-6 right-side">
                <a class="btn bg-black toggle-code-handle tour-four" role="button">Toggle Code!</a>
            </div> -->
            <!-- /.col-sm-6 text-right -->
        </div>
        <!-- /.row -->
        <div class="row breadcrumb-div">
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url('/') ?>"><i class="fa fa-home"></i>Home</a></li>
                    <li>Data Master</li>
                    <li class="active">Siswa</li>
                </ul>
            </div>
            <!-- /.col-sm-6 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    <section class="section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-9">
                    <?= $this->session->flashdata('alert'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h5>Data Siswa</h5>
                            </div>
                            <a href="<?= base_url('laporan/') ?>" class="btn btn-primary ml-15"><i class="fa fa-arrow-left"></i>Kembali</a>
                        </div>
                        <div class="panel-body p-20">
                            <p>Tampilkan Berdasar tanggal</p>
                            <div class="row">
                                <div class="col-lg-4 mb-20">
                                    <select class="form-control js-states" id="laporanTanggalLaporan">
                                        <option>Pilih Kelas</option>
                                        <?php foreach ($tanggallaporan as $datatanggallaporan) : ?>
                                            <option value="<?= $datatanggallaporan['id_tanggallaporan']; ?>"> <?= $datatanggallaporan['tgl']; ?> </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-info btn-grad" disabled><i class="fa fa-edit"></i>Export Ke Excel</button>
                                </div>
                            </div>
                            <table class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>
                                            <center>No</center>
                                        </th>
                                        <!-- <th><center>RFID</center></th> -->
                                        <th>
                                            <center>Nama Produk</center>
                                        </th>
                                        <th>
                                            <center>Nama Kategori</center>
                                        </th>
                                        <th>
                                            <center>Jumlah</center>
                                        </th>
                                        <th>
                                            <center>Laba</center>
                                        </th>
                                        <th>
                                            <center>Nilai</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="dataLaporanTanggalLaporan">
                                    <tr>
                                        <td colspan="7" align="center">Silahkan Cari Data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.section -->
</div>



<!-- .prop('checked', this.checked) -->