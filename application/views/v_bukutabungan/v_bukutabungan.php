<div class="main-page">
    <div class="container-fluid">
        <div class="row page-title-div">
            <div class="col-sm-6">
                <h2 class="title">Buku Tabungan</h2>
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
                    <li class="active">Buku Tabungan</li>
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
                                <h5>Data Buku Tabungan</h5>
                            </div>
                        </div>
                        <div class="panel-body p-20">
                        <?php if ($akses['add'] == 1) { ?>
                            <?php if ($akses['add'] == 1) { ?>
                                <a href="<?= base_url('bukutabungan/tambah')  ?>" class="btn btn-primary mb-20">
                                    <i class="fa fa-plus text-white"></i>
                                    Tambah Data Buku Tabungan
                                </a>    
                            <?php  } ?>
                            <?php  } ?>
                            <!--     <a href="<?= base_url('siswa-grad/')  ?>" class="btn btn-info mb-20">
                                <i class="fa fa-check text-white"></i>
                                Siswa Lulus
                            </a> -->
                            <table id="dataSiswaIndex" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Nasabah</th>
                                        <th>No Rekening</th>
                                        <th>Nama Nasabah</th>
                                        <th>Nomor Seri</th>
                                        <th>Tanggal Cetak</th>
                                        <th><center>Aksi</center></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($bukutabungan as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data->jenis_nasabah; ?></td>
                                            <td><?= $data->id_nasabah; ?></td>
                                            <?php
                                                if ($data->jenis_nasabah=='siswa'){
                                                    $nama_nasabah = $this->M_Bukutabungan->getnamasiswa($data->id_nasabah);
                                                    ?>
                                            <td><?= $nama_nasabah; ?></td>
                                            <?php
                                                } else if ($data->jenis_nasabah=="staf"){
                                                    $nama_nasabah = $this->M_Bukutabungan->getnamastaf($data->id_nasabah);
                                                    ?>
                                            <td><?= $nama_nasabah; ?></td>
                                            <?php
                                                }
                                                ?>
                                            <td><?= $data->nomor_seri; ?></td>
                                            <td><?= $data->tgl_buku_tabungan; ?></td>
                                            <td style="min-width: 175px;">
                                            <center>
                                                <div class="btn-group">
                                                    <?php if ($akses['view'] == 1) { ?>
                                                        <a href="<?= base_url('bukutabungan/cetak/') . $data->id;  ?>" class="btn btn-success" target="_blank"><i class="fa fa-print"></i></a>
                                                    <?php  } ?>
                                                    <?php if ($akses['edit'] == 1) { ?>
                                                        <a href="<?= base_url('bukutabungan/edit/') . $data->id;  ?>" class="btn btn-warning"><i class="fa fa-pencil"></i></a>
                                                    <?php  } ?>
                                                    <?php if ($akses['delete'] == 1) { ?>
                                                        <a href="<?= base_url('bukutabungan/hapus/') . $data->id;  ?>" class="btn btn-danger" onclick="return confirm('Yakin untuk menghapus?')"><i class="fa fa-trash"></i></a>
                                                    <?php  } ?>                                                        
                                                </div>
                                            </center>
                                            </td>
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
</div>
</div>