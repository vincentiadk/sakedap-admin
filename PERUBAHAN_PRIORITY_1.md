# Priority 1: Perbaikan Urutan Upload & Error Detail

## File Baru
- `app/Jobs/ProcessZipJournalJobV2.php` - Job dengan urutan perbaikan
- `app/Http/Controllers/DigitalStorageHandover/JournalUploadControllerV2.php` - Controller untuk V2

## Perubahan Utama

### 1. URUTAN PROSES (Flow Change)

#### ❌ Old Flow (ProcessZipJournalJob.php - Line 181-201)
```php
// 1. CREATE collection di database DULU
$createdCollection = QueryAPI::create('e_collections', $payload);  // ← Baru execute query
if (!$createdCollection) throw ...

// 2. UPLOAD file belakangan
$uploadResult = QueryAPI::uploadFile([...]);
if (!$uploadResult) throw ...  // ← Jika fail, collection sudah tersimpan (ORPHAN)
```

**Efek Buruk:**
- Collection tersimpan di database tapi file GAGAL upload
- Database penuh orphan records (metadata tanpa file)
- Query jadi lambat (scan orphan records)
- Cleanup sulit: harus manual delete dari database

---

#### ✅ New Flow (ProcessZipJournalJobV2.php - Line 173-225)

**1. Hash Check & Cek Duplikasi (Line 173-180)**
```php
$hash = md5_file($pdfPath);
$existingFile = QueryAPI::get("SELECT * FROM CATALOGFILES WHERE hash = ...");
if ($existingFile) throw ...  // ← Gagal, jangan lanjut
```

**2. UPLOAD FILE LEBIH DULU (Line 190-210)**
```php
$uploadResult = QueryAPI::uploadFile([
    'type' => 'konten_digital',
    'id' => $tempCollectionId,  // akan diisi nanti
    'hash' => $hash,
    'file' => $file,
]);

if (!$uploadResult) throw ...  // ← STOP di sini, database masih bersih
```

**3. CREATE COLLECTION SETELAH UPLOAD SUKSES (Line 220-235)**
```php
$createdCollection = QueryAPI::create('e_collections', $payload);
if (!$createdCollection) throw ...

// ← Hanya sampai sini kalau upload udah sukses
```

**Efek Baik:**
- Database BERSIH: hanya ada collection dengan file yang valid
- Jika upload fail → tidak ada orphan collection
- Jika create collection fail → file ada (orphan file, bukan orphan collection)
- Orphan file lebih mudah cleanup (tinggal delete file di storage)

---

### 2. ERROR DETAIL YANG LEBIH JELAS

#### ❌ Old (Line 183-186)
```php
try {
    $createdCollection = QueryAPI::create('e_collections', $payload);
} catch (\Throwable $e) {
    throw new \Exception('Gagal menyimpan ke tabel e_collections');  // ← GENERIC, tidak jelas
}
```

**Masalah:**
- Tidak tahu error apa dari API
- Sulit debug kalau ada masalah
- User lihat "Gagal menyimpan" tapi tidak tahu kenapa

---

#### ✅ New (Line 216-226)
```php
try {
    $createdCollection = QueryAPI::create('e_collections', $payload);
} catch (\Throwable $e) {
    // Log detail error dari API
    Log::channel('zip-upload')->error('QueryAPI::create e_collections failed', [
        'history_id' => $this->historyId,
        'row_number' => $rowNumber,
        'error' => $e->getMessage(),        // ← Actual error message
        'error_code' => $e->getCode(),      // ← Error code
        'payload' => $payload,              // ← Data yang dikirim
    ]);
    throw new \Exception('Gagal menyimpan ke tabel e_collections: ' . $e->getMessage());
}
```

**Manfaat:**
- Log berisi full error detail
- Bisa lihat di file `storage/logs/zip-upload.log`
- Lebih mudah debug kalau ada API error
- User lihat pesan error yang lebih detail

---

### 3. ERROR HANDLING VERIFICATION

#### ✅ New (Line 247-255)
```php
try {
    $verifikasi = QueryAPI::verificationCollection($createdCollection->ID, ...);
    if (!$verifikasi) {
        throw new \Exception('Gagal verifikasi artikel menjadi katalog');
    }
} catch (\Throwable $e) {
    Log::channel('zip-upload')->error('Verification failed', [
        'history_id' => $this->historyId,
        'collection_id' => $createdCollection->ID,
        'error' => $e->getMessage(),
    ]);
    throw $e;
}
```

**Manfaat:**
- Verification error juga tercatat dengan detail
- Bisa tracking masalah verification di log

---

## Comparison Table

| Aspek | Old Flow | New Flow |
|-------|----------|----------|
| **Order Proses** | Create Collection → Upload File | Hash Check → Upload File → Create Collection |
| **Orphan Collection** | ❌ BANYAK (jika upload fail) | ✅ MINIMAL (hampir tidak ada) |
| **Orphan File** | N/A | ✅ SEDIKIT (hanya jika create fail) |
| **Database Integrity** | ❌ Jelek (banyak orphan records) | ✅ Baik (data konsisten) |
| **Error Visibility** | ❌ Generic "Gagal menyimpan" | ✅ Detail error + log |
| **Cleanup** | ❌ Susah (harus query database) | ✅ Mudah (tinggal delete file) |
| **Performance** | Bisa jelek (scan orphan) | ✅ Baik (database bersih) |
| **Log Detail** | ❌ Minimal | ✅ Lengkap dengan stack trace |

---

## Testing Priority 1

### Scenario 1: Upload Sukses
```
Row 1: Hash check ✓ → Upload file ✓ → Create collection ✓ 
Result: SUCCESS, metadata ada + file ada
```

### Scenario 2: Hash Duplikat
```
Row 2: Hash check ✗ (sudah ada di CATALOGFILES)
Result: FAILED, tidak ada yang tersimpan (baik)
```

### Scenario 3: Upload Gagal
```
Row 3: Hash check ✓ → Upload file ✗ (misal disk penuh)
Result: FAILED, collection belum terciptakan, database bersih
Cleanup: Tinggal delete orphan file di storage
```

### Scenario 4: Create Collection Gagal
```
Row 4: Hash check ✓ → Upload file ✓ → Create collection ✗ (misal API timeout)
Result: FAILED, file sudah ada di storage tapi collection belum
Cleanup: Ada orphan file di storage, tapi database tidak tercemar
```

---

## Migration Guide

Untuk menggunakan V2, cukup ubah routing:

### Old Route
```php
Route::post('/journal-upload', [JournalUploadController::class, 'store']);
```

### New Route (V2)
```php
Route::post('/journal-upload', [JournalUploadControllerV2::class, 'store']);
```

Atau buat route baru untuk testing parallel:
```php
// Existing (v1)
Route::prefix('v1')->group(function () {
    Route::post('/journal-upload', [JournalUploadController::class, 'store']);
});

// Baru (v2)
Route::prefix('v2')->group(function () {
    Route::post('/journal-upload', [JournalUploadControllerV2::class, 'store']);
});
```

---

## Efek Jangka Panjang

### Positif ✅
1. Database lebih bersih (no orphan collections)
2. Monitoring lebih mudah (lihat error di log)
3. Cleanup lebih sederhana
4. Data integrity lebih baik
5. Performance tidak jelek (bersih ≠ lambat)

### Trade-off
- Bisa ada orphan FILE (tapi lebih mudah handle)
- Butuh monitoring storage untuk orphan file cleanup

---

## Next Steps (Priority 2 & 3)

Setelah Priority 1 stable, bisa lanjut:

**Priority 2:** Tambah UNIQUE constraint + database lock
- `ALTER TABLE CATALOGFILES ADD UNIQUE(hash)`
- Lock mechanism untuk mencegah race condition

**Priority 3:** Auto cleanup orphan files
- Background job untuk delete orphan file yang tua
- Query file di storage tapi tidak ada di e_collections
