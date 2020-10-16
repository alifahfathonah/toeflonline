<?php if ($this->ion_auth->is_admin()) : ?>
    <div class="row">
        <?php foreach ($info_box as $info) : ?>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-<?= $info->box ?>">
                    <div class="inner">
                        <h3><?= $info->total; ?></h3>
                        <p><?= $info->title; ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-<?= $info->icon ?>"></i>
                    </div>
                    <a href="<?= base_url() . strtolower($info->title); ?>" class="small-box-footer">
                        More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php elseif ($this->ion_auth->in_group('operator')) : ?>

    <div class="row">
        <div class="col-sm-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Informasi Akun</h3>
                </div>
                <table class="table table-hover">
                    <tr>
                        <th>Nama</th>
                        <td><?= $operator->nama_operator ?></td>
                    </tr>
                    <tr>
                        <th>No Identitas</th>
                        <td><?= $operator->no_identitas ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $operator->email ?></td>
                    </tr>
                    <tr>
                        <th>Mata Ujian</th>
                        <td><?= $operator->nama_mataujian ?></td>
                    </tr>
                    <tr>
                        <th>Daftar Kelompok</th>
                        <td>
                            <ol class="pl-4">
                                <?php foreach ($kelompok as $k) : ?>
                                    <li><?= $k->nama_kelompok ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="box box-solid">
                <div class="box-header bg-purple">
                    <h3 class="box-title">Pemberitahuan</h3>
                </div>
                <div class="box-body">
                    <p>Bagi peserta untuk mengikuti alur ujian yang ada pada sistem ini:</p>
                    <ul class="pl-4">
                        <li>Ujian TOEFL ini dibatasi oleh waktu yang telah ditentukan pada setiap ujian.</li>
                        <li>Untuk mengikuti ujian diharapkan dengan menyiapkan akun, token dan jaringan serta akses menggunakan google chrome pada PC/Laptop.</li>
                        <li>Pada saat ujian, silahkan menjawab soal boleh dengan acak ataupun berurutan.</li>
                        <li>Opsi jawaban pada tiap ujian hanya opsi A,B,C dan D.</li>
                        <li>Pemberitahuan hasil ujian akan diumumkan oleh panitia pada waktu yang telah ditentukan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php else : ?>

    <div class="row">
        <div class="col-sm-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Informasi Akun</h3>
                </div>
                <table class="table table-hover">
                    <tr>
                        <th>No Identitas</th>
                        <td><?= $peserta->no_identitas ?></td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td><?= $peserta->nama ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td><?= $peserta->jenis_kelamin === 'L' ? "Laki-laki" : "Perempuan"; ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $peserta->email ?></td>
                    </tr>
                    <tr>
                        <th>Angkatan</th>
                        <td><?= $peserta->nama_angkatan ?></td>
                    </tr>
                    <tr>
                        <th>Kelompok</th>
                        <td><?= $peserta->nama_kelompok ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="box box-solid">
                <div class="box-header bg-purple">
                    <h3 class="box-title">Pemberitahuan</h3>
                </div>
                <div class="box-body">
                    <p>Bagi peserta untuk mengikuti alur ujian yang ada pada sistem ini:</p>
                    <ul class="pl-4">
                        <li>Ujian TOEFL ini dibatasi oleh waktu yang telah ditentukan pada setiap ujian.</li>
                        <li>Untuk mengikuti ujian diharapkan dengan menyiapkan akun, token dan jaringan serta akses menggunakan google chrome pada PC/Laptop.</li>
                        <li>Pada saat ujian, silahkan menjawab soal boleh dengan acak ataupun berurutan.</li>
                        <li>Opsi jawaban pada tiap ujian hanya opsi A,B,C dan D.</li>
                        <li>Pemberitahuan hasil ujian akan diumumkan oleh panitia pada waktu yang telah ditentukan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>