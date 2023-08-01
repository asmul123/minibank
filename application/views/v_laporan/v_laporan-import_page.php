<div class="main-page">
    <div class="container-fluid">
        <div class="row page-title-div">
            <div class="col-sm-6">
                <h2 class="title">Import Data laporan</h2>
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
                    <li class="active">laporan</li>
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
                                <h5>Data laporan</h5>
                            </div>
                            <a href="<?= base_url('laporan/') ?>" class="btn btn-primary ml-15"><i class="fa fa-arrow-left"></i>Kembali</a>
                        </div>
                        <div class="panel-body p-20">
                            <p>Unggah Data laporan</p>
                            <div class="row">
                                <div class="col-lg-4 mb-20">
                                    <i><span class="mb-10" id="filename"></span></i>
                                    <form method="post" action="<?php echo base_url('laporan-checkImport') ?>" enctype="multipart/form-data">
                                        <label for="file" class="btn btn-primary">
                                            <i class="fa fa-file"></i>
                                            Pilih File
                                        </label>
                                        <input type="file" name="file" id="file" style="display: none;" required="true">
                                        <button type="submit" class="btn btn-info"><i class="fa fa-check"></i>Check data</button>
                                    </form>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-4">
                                    <label for="">Tanggal Laporan <i>(Wajib di isi)</i> <?= $tglLaporan; ?></label>
                                    <select id="ta" class="form-control js-states mb-20" style="height: 45px; font-size: 17px;">
                                        <option value="salah">Pilih Tanggal Laporan</option>
                                        <?php foreach ($tanggallaporan as $row) : ?>
                                            <option value="<?= $row['id']; ?>"><?= $row['tgl']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button disabled id="btnImportS" class="btn btn-primary mt-10">Import Data</button>
                                </div>
                            </div>

                            <table class="display table table-striped table-bordered" id="tb_import" cellspacing="0" width="100%">
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
                                        <!-- <th><center>Tempat/Tgl Lahir</center></th>                                      -->
                                        <!-- <th><center>Alamat</center></th>                     -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($datalaporan as $data) : ?>
                                        <tr>
                                            <td align="center"><?= $no++; ?></td>
                                            <td align="center"><?= $data['nama_produk']; ?></td>
                                            <td align="center"><?= $data['nama_kategori']; ?></td>
                                            <td align="center"><?= $data['jumlah']; ?></td>
                                            <td align="center"><?= $data['laba']; ?></td>
                                            <td align="center"><?= $data['nilai']; ?></td>
                                            <!-- <td align="center"><?= $data['tempat_tgl_lahir']; ?></td>                                         -->
                                            <!-- <td align="center"><?= $data['alamat']; ?></td> -->
                                        </tr>
                                    <?php endforeach; ?>
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