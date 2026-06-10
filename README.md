# Five Dance School - PHP Native

Aplikasi PHP native tanpa database. Data dummy disimpan di `$_SESSION`, sehingga CRUD berjalan selama sesi browser aktif.

## Cara menjalankan

```bash
cd five-dance-school
php -S localhost:8000
```

Buka:

```bash
http://localhost:8000
```

## Fitur

- Dashboard ringkas owner/admin
- CRUD data murid
- Validasi nomor WhatsApp dan anti duplikasi
- Jadwal kelas mingguan
- Conflict check guru/studio
- Kapasitas otomatis: Toddler 10, Kids/Teens 20
- Pembayaran SPP, diskon paket 2 bulan, denda otomatis setelah tanggal 7
- Pencatatan pengeluaran
- Laporan keuangan dan export print/PDF
- Rekap mengajar dan slip gaji guru
- Assessment 7 kriteria
- Rapor digital dan status siap naik level

## Catatan

Karena tidak memakai database, data akan kembali ke dummy awal jika session di-reset atau browser ditutup lama.
