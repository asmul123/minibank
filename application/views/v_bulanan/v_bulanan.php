<div class="main-page">
    <div class="container-fluid">
        <div class="row page-title-div">
            <div class="col-sm-6">
                <h2 class="title">Data Bulanan</h2>
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
                    <li class="active">Guru dan Anggota</li>
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
                <div class="col-md-15">

                    <div class="panel">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h5>Data Guru dan Anggota</h5>
                            </div>

                            <?php if ($akses['add'] == 1) { ?>
                                <a href="<?= base_url('bulanan-add') ?>" class="btn btn-primary ml-15"><i class="fa fa-plus"></i>Tambah Data Bulanan</a>
                            <?php } ?>
                        </div>
                        <div class="panel-body p-20">
                            <table id="dataTableSiswa" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bulanan</th>
                                        <th>

                                            <center>Aksi</center>
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($bulanan as $data) :
                                        if ($data['status'] == 'aktif') {
                                    ?>

                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= $data['bulananlaporan'] ?></td>
                                                <td style="min-width: 175px;">
                                                    <center>
                                                        <div class="btn btn-group">
                                                            <?php if ($akses['view'] == 1) { ?>
                                                                <a href="<?= base_url('bulanan-det/') . $data['id_bulananlaporan'] ?>" class="btn btn-success"><i class="fa fa-search"></i></a>
                                                            <?php } ?>
                                                            <?php if ($akses['edit'] == 1) { ?>
                                                                <a href="<?= base_url('bulanan-ubah/') . $data['id_bulananlaporan'] ?>" class="btn btn-warning"><i class="fa fa-pencil"></i></a>
                                                            <?php } ?>
                                                            <?php if ($akses['delete'] == 1) { ?>
                                                                <a href="<?= base_url('bulanan/hapus/') . $data['id_bulananlaporan'] ?>" class="btn btn-danger" onclick="return confirm('Yakin Mau Dihapus ?')"><i class="fa fa-trash"></i></a>
                                                            <?php } ?>
                                                        </div>
                                                    </center>
                                                </td>

                                            </tr>
                                    <?php
                                        }
                                    endforeach;
                                    ?>
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