<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Form <?= $judul ?></h3>
        <div class="box-tools pull-right">
            <a href="<?= base_url() ?>kelompokoperator" class="btn btn-warning btn-flat btn-sm">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <?= form_open('kelompokoperator/save', array('id' => 'kelompokoperator'), array('method' => 'edit', 'operator_id' => $id_operator)) ?>
                <div class="form-group">
                    <label>Operator</label>
                    <input type="text" readonly="readonly" value="<?= $operator->nama_operator ?>" class="form-control">
                    <small class="help-block text-right"></small>
                </div>
                <div class="form-group">
                    <label>Kelompok</label>
                    <select id="kelompok" multiple="multiple" name="kelompok_id[]" class="form-control select2" style="width: 100%!important">
                        <?php
                        $sk = [];
                        foreach ($kelompok as $key => $val) {
                            $sk[] = $val->id_kelompok;
                        }
                        foreach ($all_kelompok as $m) : ?>
                            <option <?= in_array($m->id_kelompok, $sk) ? "selected" : "" ?> value="<?= $m->id_kelompok ?>"><?= $m->nama_kelompok ?> - <?= $m->nama_angkatan ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-block text-right"></small>
                </div>
                <div class="form-group pull-right">
                    <button type="reset" class="btn btn-flat btn-default">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>
                    <button id="submit" type="submit" class="btn btn-flat bg-purple">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url() ?>assets/dist/js/app/relasi/kelompokoperator/edit.js"></script>