<div class="main-page">
    <div class="container-fluid bg-white">
        <div class="row page-title-div">
            <div class="col-sm-6">
                <h2 class="title">Tambah Data Siswa</h2>
                <p class="sub-title">SIMBMS (Sistem Informasi Bank Mini Sekolah)</p>
            </div>
            <!-- /.col-sm-6 -->
            <!-- <div class="col-sm-6 right-side">
                                    <a class="btn bg-black toggle-code-handle tour-four" role="button">Toggle Code!</a>
                                </div> -->
            <!-- /.col-sm-6 text-right -->
        </div>
        <form method="post" action="<?= base_url('bukutabungan/add_process')  ?>">
            <div class="row panel">
                <div class="panel-body">
                    <div class="col-md-12">
                        <i>( * ) Wajib di Isi</i>
                        <div class="form-group has-feedback">
                              <label for="name5" style="font-size: 15px;">Nomor Seri Buku Tabungan</label>
                              <input type="text" class="form-control inpCus" disabled value="<?=$noseribaru?>">
                              <span class="fa fa-pencil form-control-feedback"></span>
                              <span class="help-block">Digenerate secara otomatis</span>
                          </div>
                        <div class="form-group has-feedback">
                            <label for="exampleInputPassword5">Jenis Nasabah*</label>
                            <select class="form-control js-states" name="jenis_nasabah" id="jenis" required>
                                <option value="">Pilih Jenis Nasabah</option>
                                <option value="siswa">Siswa</option>
                                <option value="staf">Staf</option>
                            </select>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="exampleInputPassword5">Nasabah*</label>
                            <select class="form-control s_kota js-states" name="id_nasabah" disabled id="selectNasabah" required>

                            </select>
                            <!-- <span class="fa fa-map form-control-feedback"></span> -->
                        </div>
                        <div class="form-group has-feedback">
                            <label for="exampleInputEmail5">Tanggal Buku Tabungan*</label>
                            <input type="date" class="form-control" id="exampleInputEmail5" name="tgl_buku_tabungan" value="<?=date('Y-m-d')?>" required>
                            <span class="help-block">Masukan Tanggal Dikeluarkan Buku Tabungan</span>
                        </div>
                        <div class="form-group has-feedback">
                            <a href="<?= base_url('bukutabungan/') ?>" class="btn btn-primary btn-labeled"><i class="fa fa-arrow-left"></i>Kembali</a>
                            <button type="Submit" class="btn btn-success btn-labeled">
                                <i class="fa fa-plus"></i> Tambah Data Buku Tabungan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /.row -->
    </div>
</div>
<!-- /.main-page -->
<!-- /.right-sidebar -->
</div>
<!-- /.content-container -->
</div>