<div class="main-page">
    <div class="container-fluid">
        <div class="row page-title-div">
            <div class="col-sm-6">
                <h2 class="title">Tanggal Laporan</h2>
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
                    <li><a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                    <li class="active">Data Master</li>
                    <li class="active">Tanggal Laporan</li>
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
                    <?= $this->session->flashdata('message'); ?>
                </div>
            </div>
            <div class="row ">

                <div class="col-md-12">

                    <div class="panel">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h5>Data Tanggal laporan</h5>
                            </div>

                            <?php if ($akses['add'] == 1) { ?>
                                <a href="<?= base_url('tanggallaporan-add') ?>" class="btn btn-primary ml-15"><i class="fa fa-plus"></i> Tambah Tanggal Laporan</a>
                            <?php } ?>
                        </div>
                        <div class="panel-body p-20">
                            <table id="tb_tanggallaporan" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal </th>

                                        <!-- <th>Tanggal Update</th> -->
                                        <th width="200px">
                                            <center>Aksi</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($tanggallaporan as $data) : ?>

                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td>
                                                <?php $tgl = date_create($data['tgl']);
                                                echo date_format($tgl, "d-m-Y"); ?>
                                            </td>

                                            <td style="min-width: 175px;">
                                                <center>
                                                    <div class="btn btn-group">
                                                        <?php if ($akses['view'] == 1) { ?>
                                                            <!-- <a href="<?= base_url('tanggallaporan-det/') . $data['id_tanggallaporan'] ?>" class="btn btn-success"><i class="fa fa-search"></i></a> -->
                                                        <?php } ?>
                                                        <?php if ($akses['edit'] == 1) { ?>
                                                            <a href="<?= base_url('tanggallaporan-ubah/') . $data['id_tanggallaporan'] ?>" class="btn btn-warning"><i class="fa fa-pencil"></i></a>
                                                        <?php } ?>
                                                        <?php if ($akses['delete'] == 1) { ?>
                                                            <a href="<?= base_url('tanggallaporan/hapus/') . $data['id_tanggallaporan'] ?> " class="btn btn-danger" onclick="return confirm('Yakin Mau Dihapus ?')"><i class="fa fa-trash"></i></a>
                                                        <?php } ?>
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
<!-- /.main-page -->
<!-- /.right-sidebar -->

</div>
<!-- /.content-container -->
</div>