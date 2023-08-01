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
                                <h5>Data Tanggal laporan </h5>
                            </div>


                        </div>
                        <div class="panel-body p-20">

                            <table id="tb_tanggallaporan" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>Nama Produk</th>

                                        <th>Jumlah </th>
                                        <th>Laba </th>
                                        <th>Nilai </th>

                                        <!-- <th>Tanggal Update</th> -->

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($datalaporan as $data) : ?>

                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td>
                                                <?= $data['nama_kategori']; ?>
                                            </td>
                                            <td>
                                                <?= $data['nama_produk']; ?>
                                            </td>
                                            <td>
                                                <?= $data['jumlah']; ?>
                                            </td>
                                            <td>
                                                <?= $data['laba']; ?>
                                            </td>
                                            <td>
                                                <?= $data['nilai']; ?>
                                            </td>


                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="panel-body p-20">

                            <table id="tb_tanggallaporan" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>%5</th>
                                        <th>Omset</th>
                                        <th>Pembagian</th>




                                        <!-- <th>Tanggal Update</th> -->

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($datalaporanby as $data) : ?>

                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td>
                                                <?= $data['nama_kategori']; ?>
                                            </td>
                                            <td align="right">
                                                <?= number_format($data['persen'], 0); ?>
                                            </td>
                                            <td>
                                                <?= number_format($data['omset'], 0); ?>
                                            </td>
                                            <td>
                                                <?= number_format($data['tot'], 0); ?>
                                            </td>



                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>

                                    <tr>
                                        <th colspan="2" class="text-center">Total</th>
                                        <th class="text-center"><?= $datatotpersen; ?></th>
                                        <th class="text-center"><?= $datatotomset; ?></th>
                                        <th class="text-center"><?= $datatotpembagian; ?></th>
                                    </tr>

                                </tfoot>
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