<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = array(
            array('id' => '1','slug' => 'template-email-activation','content' => '<p style="text-align: center;">Hai {{name}}, harap verifikasi akun anda, klik link dibawah ini :&nbsp;</p>

            <p style="text-align: center;"><em><strong><u><span style="font-size:11px">link</span></u></strong></em></p>','created_at' => '2020-10-20 14:56:11','updated_at' => '2020-10-20 15:39:29'),
            array('id' => '2','slug' => 'template-email-collection-problem','content' => '<p style="text-align:center"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:16px"><strong>Verifikasi Penyerahan Koleksi</strong></span></span></p>

                        <p style="text-align:center">&nbsp;</p>

                        <p><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:12px">Kepada Yth. Pimpinan {{publisher}}</span></span></p>

                        <p>&nbsp;</p>

                        <p><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:12px">Data digital dengan judul <strong>{{title}}</strong>&nbsp;</span>sudah&nbsp;kami&nbsp;verifikasi&nbsp;dan&nbsp;masih&nbsp;terdapat&nbsp;masalah,&nbsp;mohon&nbsp;lengkapi&nbsp;datanya&nbsp;dan&nbsp;silahkan&nbsp;lakukkan&nbsp;deposit&nbsp;ulang.</span></p>

                        <p>&nbsp;</p>

                        <p>&nbsp;</p>

                        <p style="text-align:center"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif"><em>Email&nbsp;ini&nbsp;hanya&nbsp;untuk&nbsp;pengiriman&nbsp;notifikasi&nbsp;verifikasi&nbsp;saja,&nbsp;untuk&nbsp;surat&nbsp;menyurat&nbsp;harap&nbsp;menggunakan&nbsp;alamat&nbsp;email&nbsp;</em><span style="color:#0000FF"><em>edeposit@perpusnas.go.id</em></span></span></span></p>

                        <p style="text-align:center"><span style="font-size:11px"><span style="font-family:arial,helvetica,sans-serif"><em>Salam,&nbsp;Tim&nbsp;e-Deposit&nbsp;</em></span></span></p>','created_at' => '2020-10-20 15:13:16','updated_at' => '2020-10-20 15:13:55'),
            array('id' => '3','slug' => 'template-email-collection-success','content' => '<p style="text-align:center"><span style="font-family:arial,helvetica,sans-serif"><strong>Verifikasi Penyerahan Koleksi</strong></span></p>

            <p>&nbsp;</p>

            <p><span style="font-family:arial,helvetica,sans-serif">Kepada Yth. Pimpinan {{publisher}}</span></p>

            <p>&nbsp;</p>

            <p><span style="font-family:arial,helvetica,sans-serif">Data digital dengan judul <strong>{{title}}</strong>&nbsp;</span>sudah&nbsp;kami&nbsp;verifikasi,&nbsp;silahkan&nbsp;download&nbsp;data&nbsp;terkait.</p>

            <p>&nbsp;</p>

            <p>&nbsp;</p>

            <p>Detail : {{depositid}}, {{mimes}}, {{hash}}, {{size}}</p>

            <p>&nbsp;</p>

            <p style="text-align:center"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px"><em>Email&nbsp;ini&nbsp;hanya&nbsp;untuk&nbsp;pengiriman&nbsp;notifikasi&nbsp;verifikasi&nbsp;saja,&nbsp;untuk&nbsp;surat&nbsp;menyurat&nbsp;harap&nbsp;menggunakan&nbsp;alamat&nbsp;email&nbsp;</em><span style="color:#0000FF"><em>edeposit@perpusnas.go.id</em></span></span></span></p>

            <p style="text-align:center"><span style="font-family:arial,helvetica,sans-serif"><span style="font-size:11px"><em>Salam,&nbsp;Tim&nbsp;e-Deposit&nbsp;</em></span></span></p>','created_at' => '2020-10-20 15:15:57','updated_at' => '2020-11-09 15:35:59'),
            array('id' => '4','slug' => 'template-email-collection-submitted','content' => '<p style="text-align:center"><strong>Penyerahan Koleksi</strong></p>

            <p>&nbsp;</p>

            <p>Kepada Yth. Pimpinan {{publisher}}</p>

            <p>&nbsp;</p>

            <p>Data digital dengan judul <strong>{{title}}</strong>&nbsp;sudah kami terima, harap menunggu kabar verifikasi dari kami</p>

            <p>&nbsp;</p>

            <p>Detail : {{size}}, {{mimes}}, {{hash}}</p>

            <p>&nbsp;</p>

            <p style="text-align:center"><span style="font-size:11px"><em>Email&nbsp;ini&nbsp;hanya&nbsp;untuk&nbsp;pengiriman&nbsp;notifikasi&nbsp;verifikasi&nbsp;saja,&nbsp;untuk&nbsp;surat&nbsp;menyurat&nbsp;harap&nbsp;menggunakan&nbsp;alamat&nbsp;email&nbsp;<span style="color:#0000FF">edeposit@perpusnas.go.id</span></em></span></p>

            <p style="text-align:center"><span style="font-size:11px"><em>Salam,&nbsp;Tim&nbsp;e-Deposit&nbsp;</em></span></p>','created_at' => '2020-10-20 15:20:31','updated_at' => '2020-11-09 15:36:26'),
            array('id' => '5','slug' => 'template-email-publisher-rejected','content' => '<p style="text-align: center;"><strong><span style="font-size:16px">Pendaftaran&nbsp;Publisher&nbsp;e-deposit</span></strong></p>

                        <p style="text-align: center;">&nbsp;</p>

                        <p>Kepada&nbsp;Yth.&nbsp;Pimpinan&nbsp;{{publisher}}</p>

                        <p>&nbsp;</p>

                        <p>Pendaftaran&nbsp;Wajib&nbsp;Serah&nbsp;anda&nbsp;masih&nbsp;terdapat&nbsp;masalah,&nbsp;mohon&nbsp;lengkapi&nbsp;datanya&nbsp;dan&nbsp;silahkan&nbsp;lakukkan&nbsp;pendaftaran&nbsp;ulang.&nbsp;Masalah&nbsp;yang&nbsp;ditemukan adalah&nbsp;<em><strong>{{problem}}</strong></em></p>

                        <p>&nbsp;</p>

                        <p>&nbsp;</p>

                        <p style="text-align: center;"><span style="font-size:11px"><em>Email&nbsp;ini&nbsp;hanya&nbsp;untuk&nbsp;pengiriman&nbsp;notifikasi&nbsp;verifikasi&nbsp;saja,&nbsp;untuk&nbsp;surat&nbsp;menyurat&nbsp;harap&nbsp;menggunakan&nbsp;alamat&nbsp;email&nbsp;</em><span style="color:#0000FF"><em>edeposit@perpusnas.go.id</em></span></span></p>

                        <p style="text-align: center;"><span style="font-size:11px"><em>Salam,&nbsp;Tim&nbsp;e-Deposit&nbsp;</em></span></p>','created_at' => '2020-10-20 15:27:00','updated_at' => '2020-10-20 15:27:00'),
            array('id' => '6','slug' => 'template-email-publisher-submission','content' => '<p style="text-align: center;"><span style="font-size:16px"><strong>Pendaftaran&nbsp;Publisher&nbsp;e-deposit</strong></span></p>

                        <p>&nbsp;</p>

                        <p>Kepada Yth. Pimpinan {{publisher}}</p>

                        <p>&nbsp;</p>

                        <p>Pendaftaran&nbsp;Wajib&nbsp;Serah&nbsp;anda sudah kami terima harap menunggu verifikasi dari kami!</p>

                        <p>&nbsp;</p>

                        <p>&nbsp;</p>

                        <p style="text-align: center;"><span style="font-size:11px"><em>Email&nbsp;ini&nbsp;hanya&nbsp;untuk&nbsp;pengiriman&nbsp;notifikasi&nbsp;verifikasi&nbsp;saja,&nbsp;untuk&nbsp;surat&nbsp;menyurat&nbsp;harap&nbsp;menggunakan&nbsp;alamat&nbsp;email&nbsp;<span style="color:#0000FF">edeposit@perpusnas.go.id</span></em></span></p>

                        <p style="text-align: center;"><span style="font-size:11px"><em>Salam,&nbsp;Tim&nbsp;e-Deposit&nbsp;</em></span></p>','created_at' => '2020-10-20 15:28:48','updated_at' => '2020-10-20 15:28:48'),
            array('id' => '7','slug' => 'template-email-change-password','content' => '<p>Hai {{name}}, password anda telah diganti</p>','created_at' => '2020-10-20 15:38:32','updated_at' => '2020-10-20 15:38:32'),
            array('id' => '8','slug' => 'template-email-publisher-success','content' => '<p style="text-align: center;"><span style="font-size:16px"><strong>Pendaftaran&nbsp;Publisher&nbsp;e-deposit</strong></span></p>

                        <p>&nbsp;</p>

                        <p>Kepada&nbsp;Yth.&nbsp;Pimpinan&nbsp;{{publisher}}</p>

                        <p>&nbsp;</p>

                        <p>Pendaftaran&nbsp;Wajib&nbsp;Serah anda sudah kami verifikasi, berikut prosedur login di sistem kami :</p>

                        <p>1. Buka website <em>(e-deposit.perpusnas.go.id)</em></p>

                        <p><em>2.&nbsp;</em>Pilih login</p>

                        <p>3. Masukan username {{username}} dan password <em>(diwaktu ada anda mendaftar)</em></p>

                        <p>4. Klik login</p>

                        <p>&nbsp;</p>

                        <p style="text-align: center;"><span style="font-size:11px"><em>Email&nbsp;ini&nbsp;hanya&nbsp;untuk&nbsp;pengiriman&nbsp;notifikasi&nbsp;verifikasi&nbsp;saja,&nbsp;untuk&nbsp;surat&nbsp;menyurat&nbsp;harap&nbsp;menggunakan&nbsp;alamat&nbsp;email&nbsp;</em><span style="color:#0000FF"><em>edeposit@perpusnas.go.id</em></span></span></p>

                        <p style="text-align: center;"><span style="font-size:11px"><em>Salam,&nbsp;Tim&nbsp;e-Deposit&nbsp;</em></span></p>','created_at' => '2020-10-20 15:46:13','updated_at' => '2020-10-20 15:46:13'),
            array('id' => '9','slug' => 'template-email-request-file-original','content' => '<p style="text-align: center;"><span style="font-size:16px"><strong>Request&nbsp;File&nbsp;Original</strong></span></p>

                        <p>&nbsp;</p>

                        <p>Kepada&nbsp;Yth.&nbsp;Pimpinan&nbsp;{{publisher}}</p>

                        <p>&nbsp;</p>

                        <p>Berikuta adalah link download file original {{title}}:</p>

                        <p><a href="{{url}}">Download Disini</a></p>

                        <p>&nbsp;</p>

                        <p style="text-align: center;"><span style="font-size:11px"><em>Email&nbsp;ini&nbsp;hanya&nbsp;untuk&nbsp;pengiriman&nbsp;notifikasi&nbsp;verifikasi&nbsp;saja,&nbsp;untuk&nbsp;surat&nbsp;menyurat&nbsp;harap&nbsp;menggunakan&nbsp;alamat&nbsp;email&nbsp;</em><span style="color:#0000FF"><em>edeposit@perpusnas.go.id</em></span></span></p>

                        <p style="text-align: center;"><span style="font-size:11px"><em>Salam,&nbsp;Tim&nbsp;e-Deposit&nbsp;</em></span></p>','created_at' => '2020-10-20 15:46:13','updated_at' => '2020-10-20 15:46:13'),
            array('id' => '10','slug' => 'template-email-collection-submitted-bulk','content' => '<p style="text-align:center"><strong>Penyerahan Koleksi</strong></p>

                        <p>&nbsp;</p>

                        <p>Kepada Yth. Pimpinan {{publisher}}</p>

                        <p>&nbsp;</p>

                        <p>Data digital seperti berikut:&nbsp;</p>

                        <p>{{item}}</p>

                        <p>Harap menunggu kabar verifikasi dari kami</p>

                        <p>&nbsp;</p>

                        <p>&nbsp;</p>

                        <p style="text-align:center"><span style="font-size:11px"><em>Email&nbsp;ini&nbsp;hanya&nbsp;untuk&nbsp;pengiriman&nbsp;notifikasi&nbsp;verifikasi&nbsp;saja,&nbsp;untuk&nbsp;surat&nbsp;menyurat&nbsp;harap&nbsp;menggunakan&nbsp;alamat&nbsp;email&nbsp;<span style="color:#0000FF">edeposit@perpusnas.go.id</span></em></span></p>

                        <p style="text-align:center"><span style="font-size:11px"><em>Salam,&nbsp;Tim&nbsp;e-Deposit&nbsp;</em></span></p>','created_at' => '2020-10-20 15:46:13','updated_at' => '2020-10-20 15:46:13'),
            array('id' => '11','slug' => 'template-link-password-reset','content' => '<p>Hai {{name}}, berikut adalah link password reset anda</p>

                <p>{{link}}</p>','created_at' => '2020-10-20 15:46:13','updated_at' => '2020-10-20 15:46:13')

        );

        DB::connection('mysql')->table('settings')->insert($settings);
    }
}
