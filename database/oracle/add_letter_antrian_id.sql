-- ============================================================
-- Menautkan judul ke dus: LETTER_DETAIL.LETTER_ANTRIAN_ID
--
-- Satu resi bisa terdiri dari beberapa dus (LETTER_ANTRIAN), dan
-- sampai sekarang tidak ada catatan judul mana berada di dus mana.
-- Kolom ini mengisi kekosongan itu.
--
-- Diisi otomatis oleh aplikasi saat penerimaan disimpan:
--   - resi berdus tunggal  -> langsung terisi, tanpa aksi petugas
--   - resi berdus banyak   -> diambil dari "dus aktif" yang dipilih
--                             petugas saat membuka kardus
--
-- Boleh NULL. Tiga sebab NULL yang berbeda:
--   1. data lama, sebelum kolom ini ada
--   2. resinya belum pernah tercatat di antrian (dus belum didata satpam)
--   3. resi berdus banyak dan petugas belum memilih dus aktif
-- Nomor 3 seharusnya tidak terjadi -- aplikasi menolak simpan dalam
-- keadaan itu -- tapi tetap mungkin muncul dari jalur lain.
--
-- JALANKAN BERURUTAN. Langkah 1-2 hanya SELECT.
-- ============================================================


-- ------------------------------------------------------------
-- 1) Pastikan kolomnya belum ada
-- ------------------------------------------------------------
SELECT column_name, data_type FROM user_tab_columns
WHERE table_name = 'LETTER_DETAIL' AND column_name = 'LETTER_ANTRIAN_ID';


-- ------------------------------------------------------------
-- 2) Berapa yang bisa diisi surut tanpa ambigu?
--    Hanya resi yang dusnya tepat satu -- di situ tidak ada pilihan
--    lain yang mungkin, jadi pengisiannya pasti benar.
-- ------------------------------------------------------------
SELECT
    COUNT(*) AS judul_bisa_diisi,
    COUNT(DISTINCT ld.letter_id) AS jml_resi
FROM letter_detail ld
WHERE EXISTS (
    SELECT 1 FROM letter_antrian a
    WHERE a.letter_id = ld.letter_id AND a.delete_date IS NULL
    GROUP BY a.letter_id
    HAVING COUNT(*) = 1
);

-- Sebaran jumlah dus per resi -- angka ini yang menentukan seberapa
-- sering petugas harus memilih dus aktif secara manual.
SELECT jml_dus, COUNT(*) AS jml_resi
FROM (
    SELECT letter_id, COUNT(*) AS jml_dus
    FROM letter_antrian
    WHERE delete_date IS NULL AND letter_id IS NOT NULL
    GROUP BY letter_id
)
GROUP BY jml_dus ORDER BY jml_dus;


-- ------------------------------------------------------------
-- 3) Tambah kolom
-- ------------------------------------------------------------
ALTER TABLE letter_detail ADD (letter_antrian_id NUMBER);

COMMENT ON COLUMN letter_detail.letter_antrian_id IS
    'Dus asal judul ini (LETTER_ANTRIAN.ID). NULL bila belum tercatat.';


-- ------------------------------------------------------------
-- 4) Index -- dipakai untuk menghitung isi tiap dus
-- ------------------------------------------------------------
CREATE INDEX ix_letter_detail_antrian ON letter_detail (letter_antrian_id);

-- Sekalian yang ini: dibutuhkan penautan antrian ke resi, dan sampai
-- sekarang LETTER_ANTRIAN.LETTER_ID belum berindeks sama sekali.
CREATE INDEX ix_letter_antrian_letter ON letter_antrian (letter_id);


-- ------------------------------------------------------------
-- 5) Isi surut untuk resi berdus tunggal saja.
--    Resi berdus banyak sengaja dibiarkan kosong -- menebak akan
--    menghasilkan data yang terlihat benar padahal karangan.
-- ------------------------------------------------------------
UPDATE letter_detail ld
SET ld.letter_antrian_id = (
    SELECT MIN(a.id) FROM letter_antrian a
    WHERE a.letter_id = ld.letter_id AND a.delete_date IS NULL
)
WHERE ld.letter_antrian_id IS NULL
  AND EXISTS (
      SELECT 1 FROM letter_antrian a
      WHERE a.letter_id = ld.letter_id AND a.delete_date IS NULL
      GROUP BY a.letter_id
      HAVING COUNT(*) = 1
  );


-- ------------------------------------------------------------
-- 6) Simpan
-- ------------------------------------------------------------
-- COMMIT;


-- ------------------------------------------------------------
-- 7) Verifikasi
-- ------------------------------------------------------------
SELECT
    COUNT(*) AS total_judul,
    SUM(CASE WHEN letter_antrian_id IS NOT NULL THEN 1 ELSE 0 END) AS sudah_tertaut
FROM letter_detail;
