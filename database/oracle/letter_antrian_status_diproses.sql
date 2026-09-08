-- ============================================================
-- Menambah kode 'diproses' ke CK_LETTER_ANTRIAN_STATUS
--
-- ALTERNATIF, BUKAN KEHARUSAN. Aplikasi saat ini memakai
-- 'diterima_kckr' untuk menandai dus yang sedang dibuka petugas
-- verifikasi, dan itu sudah berjalan tanpa perlu mengubah apa pun
-- di database. Jalankan skrip ini hanya kalau "diserahkan ke KCKR"
-- dan "sedang dibuka petugas" memang perlu dibedakan.
--
-- PERHATIAN: LETTER_ANTRIAN dipakai bersama aplikasi lain di skema
-- INLIS (aplikasi satpam menulis 'diterima_satpam'). Kode baru akan
-- terbaca oleh aplikasi-aplikasi itu juga. Pastikan mereka menampilkan
-- status yang tidak dikenal apa adanya, bukan error atau baris hilang.
--
-- Setelah skrip ini dijalankan, ubah juga di aplikasi:
--   app/Helpers/AntrianFisik.php
--     - STATUS_DIPROSES  : 'diterima_kckr' -> 'diproses'
--     - STATUS_SAH       : tambahkan 'diproses'
--     - labelStatus()    : 'diproses' => 'Diproses'
--     - warnaStatus()    : sesuaikan kalau 'diterima_kckr' perlu
--                          warna sendiri
--   Tanpa perubahan itu, kode baru tidak akan pernah tertulis.
--
-- JALANKAN BERURUTAN. Langkah 1-2 hanya SELECT.
-- ============================================================


-- ------------------------------------------------------------
-- 1) Lihat definisi constraint yang berlaku sekarang
--    Diharapkan: STATUS IN ('menunggu','diterima_satpam','transit',
--                           'diterima_kckr','selesai')
-- ------------------------------------------------------------
SELECT constraint_name, status, validated
FROM user_constraints
WHERE table_name = 'LETTER_ANTRIAN'
  AND constraint_name = 'CK_LETTER_ANTRIAN_STATUS';


-- ------------------------------------------------------------
-- 2) Pastikan tidak ada nilai di luar daftar
--    Kalau ada baris tak terduga, hentikan dan telusuri dulu --
--    constraint baru dibuat VALIDATE, jadi data lama yang menyimpang
--    akan menggagalkan langkah 3.
-- ------------------------------------------------------------
SELECT status, COUNT(*) AS jml
FROM letter_antrian
GROUP BY status
ORDER BY jml DESC;


-- ------------------------------------------------------------
-- 3) Pasang constraint baru LEBIH DULU, dengan nama sementara.
--
--    Sengaja tidak DROP-lalu-ADD: di antara dua perintah itu tabel
--    berjalan tanpa constraint sama sekali, dan aplikasi lain yang
--    sedang menulis bisa menyelipkan status apa pun. Urutan
--    tambah -> hapus -> ganti nama tidak pernah meninggalkan celah.
-- ------------------------------------------------------------
ALTER TABLE letter_antrian
    ADD CONSTRAINT ck_letter_antrian_status2 CHECK (
        status IN ('menunggu', 'diterima_satpam', 'transit',
                   'diterima_kckr', 'diproses', 'selesai')
    ) ENABLE VALIDATE;


-- ------------------------------------------------------------
-- 4) Baru lepas yang lama
-- ------------------------------------------------------------
ALTER TABLE letter_antrian
    DROP CONSTRAINT ck_letter_antrian_status;


-- ------------------------------------------------------------
-- 5) Kembalikan ke nama semula, supaya pesan ORA-02290 di log
--    tetap menyebut nama yang sama seperti sebelumnya
-- ------------------------------------------------------------
ALTER TABLE letter_antrian
    RENAME CONSTRAINT ck_letter_antrian_status2 TO ck_letter_antrian_status;


-- ------------------------------------------------------------
-- 6) Verifikasi: harus ada satu constraint bernama
--    CK_LETTER_ANTRIAN_STATUS, ENABLED dan VALIDATED
-- ------------------------------------------------------------
SELECT constraint_name, status, validated
FROM user_constraints
WHERE table_name = 'LETTER_ANTRIAN'
  AND constraint_name LIKE 'CK_LETTER_ANTRIAN_STATUS%';


-- ============================================================
-- PEMBATALAN (rollback)
--
-- Hanya bisa dijalankan kalau belum ada baris berstatus 'diproses'.
-- Kalau sudah ada, kembalikan dulu:
--
--   UPDATE letter_antrian SET status = 'diterima_kckr'
--   WHERE status = 'diproses';
--   COMMIT;
--
-- lalu:
--
--   ALTER TABLE letter_antrian
--       ADD CONSTRAINT ck_letter_antrian_status2 CHECK (
--           status IN ('menunggu', 'diterima_satpam', 'transit',
--                      'diterima_kckr', 'selesai')
--       ) ENABLE VALIDATE;
--   ALTER TABLE letter_antrian DROP CONSTRAINT ck_letter_antrian_status;
--   ALTER TABLE letter_antrian
--       RENAME CONSTRAINT ck_letter_antrian_status2 TO ck_letter_antrian_status;
--
-- Jangan lupa kembalikan juga STATUS_DIPROSES di AntrianFisik.php.
-- ============================================================
