<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Form <?= $judul ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-offset-3 col-sm-6">
                <div class="my-2">
                    <div class="form-horizontal form-inline">
                        <a href="<?= base_url('kelompok') ?>" class="btn btn-default btn-xs">
                            <i class="fa fa-arrow-left"></i> Batal
                        </a>
                        <div class="pull-right">
                            <span> Jumlah : </span><label for=""><?= count($kelompok) ?></label>
                        </div>
                    </div>
                </div>
                <?= form_open('kelompok/save', array('id' => 'kelompok'), array('mode' => 'edit')) ?>
                <table id="form-table" class="table text-center table-condensed">
                    <thead>
                        <tr>
                            <th># No</th>
                            <th>Kelompok</th>
                            <th>Angkatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($kelompok as $row) : ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td>
                                    <div class="form-group">
                                        <?= form_hidden('id_kelompok[' . $i . ']', $row->id_kelompok); ?>
                                        <input required="required" autofocus="autofocus" onfocus="this.select()" value="<?= $row->nama_kelompok ?>" type="text" name="nama_kelompok[<?= $i ?>]" class="form-control">
                                        <span class="d-none">DON'T DELETE THIS</span>
                                        <small class="help-block text-right"></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select required="required" name="angkatan_id[<?= $i ?>]" class="input-sm form-control select2" style="width: 100%!important">
                                            <option value="" disabled>-- Pilih --</option>
                                            <?php foreach ($angkatan as $j) : ?>
                                                <option <?= $row->angkatan_id == $j->id_angkatan ? "selected='selected'" : "" ?> value="<?= $j->id_angkatan ?>"><?= $j->nama_angkatan ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="help-block text-right"></small>
                                    </div>
                                </td>
                            </tr>
                        <?php $i++;
                        endforeach; ?>
                    </tbody>
                </table>
                <button id="submit" type="submit" class="mb-4 btn btn-block btn-flat bg-purple">
                    <i class="fa fa-edit"></i> Simpan Perubahan
                </button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url() ?>assets/dist/js/app/master/kelompok/edit.js"></script>