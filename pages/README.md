ShopEase - Sistem POS & E-Commerce
ShopEase adalah aplikasi toko online modern yang dirancang untuk memudahkan transaksi antara Pembeli dan Penjual secara praktis, responsif, dan terorganisir.

Fitur Utama Website:
1. Fitur Pembeli (Customer Experience)
- Discovery & Browsing: Penelusuran produk berdasarkan kategori dengan informasi stok, harga, dan rating yang akurat.
- Advanced Checkout System: Fitur Buy Now untuk transaksi instan dan Cart Management untuk pengelolaan barang di keranjang.
- Security & Profile: Manajemen akun (Sign Up, Sign In, Sign Out), edit profil, ganti foto profil (PFP), dan sistem ganti kata sandi.
- Post-Purchase Interaction: Sistem ulasan (Review) dan rating bintang, riwayat transaksi (Order History), serta notifikasi real-time.
- Flexible Payment: Pengaturan metode pembayaran yang dinamis.

2. Fitur Penjual (Seller Centre)
- Store Dashboard: Panel khusus untuk memantau performa toko dan pesanan masuk.
- Inventory Control: Menambah, mengedit, mengelola stok, serta fitur Restore & Delete produk.
- Voucher & Marketing: Pembuatan dan pengelolaan voucher promo toko.
- Order Fulfillment: Manajemen pesanan pelanggan secara mendetail dan update status pesanan.
- Customer Engagement: Merespons ulasan pembeli melalui fitur Official Reply.

3. Fitur Administrator (Admin Panel)
- Central Control Dashboard: Pengawasan menyeluruh terhadap seluruh aktivitas transaksi di platform.
- Account Oversight: Manajemen pengguna (Buyer & Seller) serta fitur aktivasi dan non-aktivasi akun secara manual.
- Shop & Product Monitoring: Monitoring daftar toko yang terdaftar dan pengawasan produk per toko.
- Global Promotion: Pengelolaan voucher promo berskala sistem.

Panduan penggunaan fitur-fitur:
1. Akses dan Keamanan Akun
- Sign Up & Sign In: Pengguna harus mendaftar dan masuk untuk mulai bertransaksi atau berjualan.
- Sign Out: Keluar dari sesi akun untuk keamanan data.
- Ganti PW: Fitur untuk memperbarui kata sandi lama dengan yang baru di halaman pengaturan.
- Edit Profile & Ganti PFP: Pengguna bisa mengubah nama, informasi kontak, dan mengunggah foto profil baru.

2. Alur Belanja Pembeli
- Pilih Produk: Cari barang melalui home page atau produk per kategori.
- Add Keranjang: Masukkan barang ke keranjang untuk dikumpulkan sebelum membayar.
- CO Keranjang: Memproses pembayaran untuk semua barang yang ada di keranjang.
- CO Langsung (Buy Now): Memotong jalur keranjang untuk langsung menuju proses pembayaran satu produk.
- Set Payment: Memilih metode pembayaran yang tersedia sebelum pesanan diproses.
- Proses Bayar & Proses CO: Tahap akhir konfirmasi transaksi agar pesanan masuk ke sistem penjual.

3. Interaksi Setelah Pembelian
- History: Melihat daftar transaksi yang sudah selesai atau sedang diproses.
- Nulis Review: Memberikan feedback berupa bintang dan komentar setelah barang diterima.
- Filter Review: Memudahkan calon pembeli lain memfilter ulasan berdasarkan bintang tertentu di halaman liat produk.
- Notifikasi: Melihat pemberitahuan jika pesanan dikemas, dikirim, atau jika ada promo baru.

4. Operasional Toko (Seller)
- Add Product & Add Product Process: Menambahkan item dagangan baru ke katalog toko.
- Edit & Delete Product: Memperbarui data barang atau menghapusnya jika sudah tidak dijual.
- Restore Product: Mengembalikan produk yang pernah dihapus ke status aktif kembali.
- Orders & Update Status: Memantau pesanan masuk dan mengubah statusnya (misal: dari dikemas menjadi dikirim).
- My Voucher & Add Promo Seller: Membuat kode promo khusus yang hanya berlaku di toko tersebut.
- Process Reply: Membalas komentar ulasan pembeli agar hubungan penjual dan pembeli terjaga.

5. Kendali Manajemen (Admin)
- Kelola Buyer & Kelola Promo: Menangani daftar pengguna serta membuat event diskon besar di website.
- Liat Toko & Produk Per Toko: Mengecek legalitas toko dan barang yang dijual agar sesuai aturan.
- Proses Nonaktifkan & Aktifkan: Membekukan akun buyer atau seller yang melanggar aturan, serta menonaktifkan produk tertentu.
- Update Produk Status: Mengubah status produk di seluruh platform secara massal atau individu.

Struktur Penting Sistem
- Includes: Header-Main (Pembeli), Header-Seller (Penjual), Header-Admin (Admin), Header-Auth (Login/Register), dan Footer.
- Backend Processors: File khusus (proses-...) yang menangani logika database seperti add keranjang, ganti pfp, bayar, dan update status tanpa mengganggu tampilan UI.
- Notification System: Pencatatan otomatis ke tabel notifikasi setiap kali terjadi transaksi atau perubahan status akun.

Link github: https://github.com/archerriesh/onlinePOS-ShopEase