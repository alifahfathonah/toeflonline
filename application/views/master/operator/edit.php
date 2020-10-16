<?= form_open('operator/save', array('id' => 'formoperator'), array('method' => 'edit', 'id_operator' => $data->id_operator)); ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Form <?= $subjudul ?></h3>
        <div class="box-tools pull-right">
            <a href="<?= base_url() ?>operator" class="btn btn-sm btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <div class="form-group">
                    <label for="no_identitas">No Identitas</label>
                    <input value="<?= $data->no_identitas ?>" autofocus="autofocus" onfocus="this.select()" type="number" id="no_identitas" class="form-control" name="no_identitas" placeholder="No Identitas">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="nama_operator">Nama Operator</label>
                    <input value="<?= $data->nama_operator ?>" type="text" class="form-control" name="nama_operator" placeholder="Nama Operator">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="email">Email Operator</label>
                    <input value="<?= $data->email ?>" type="text" class="form-control" name="email" placeholder="Email Operator">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="mataujian">Mata Ujian</label>
                    <select name="mataujian" id="mataujian" class="form-control select2" style="width: 100%!important">
                        <option value="" disabled selected>Pilih Mata Ujian</option>
                        <?php foreach ($mataujian as $row) : ?>
                            <option <?= $data->mataujian_id === $row->id_mataujian ? "selected" : "" ?> value="<?= $row->id_mataujian ?>"><?= $row->nama_mataujian ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-block"></small>
                </div>
                <div class="form-group pull-right">
                    <button type="reset" class="btn btn-flat btn-default">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>
                    <button type="submit" id="submit" class="btn btn-flat bg-purple">
                        <i class="fa fa-pencil"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= form_close(); ?>

<script src="<?= base_url() ?>assets/dist/js/app/master/operator/edit.js"></script>