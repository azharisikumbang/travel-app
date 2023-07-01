CREATE TABLE users (
    id int primary key auto_increment,
    nama_lengkap varchar(255) not null,
    username varchar(255) not null,
    password varchar(255) not null,
    level enum('PELANGGAN', 'DRIVER', 'ADMIN') default 'PELANGGAN',
    kontak varchar(16) null,
    photo int,
    FOREIGN KEY (photo) REFERENCES files(id)
);

CREATE TABLE files (
    id int primary key auto_increment,
    nama varchar(255) not null,
    lokasi varchar(255) not null,
    kategori varchar(255) null
);

CREATE TABLE jadwal_keberangkatan (
    id int primary key auto_increment,
    jam varchar(16) not null,
    alias varchar(32) null
);

CREATE TABLE mobil (
    id int primary key AUTO_INCREMENT,
    merk varchar(128) not null,
    jumlah_kursi int default 1,
    plat_nomor varchar(16) null,
    gambar int,
    FOREIGN KEY (gambar) REFERENCES files(id)
);

CREATE TABLE daerah_operasional (
    id int primary key auto_increment,
    nama_kota varchar(255) not null,
    gambar int,
    FOREIGN KEY (gambar) REFERENCES files(id)
);

CREATE TABLE tipe_penumpang (
    id int primary key AUTO_INCREMENT,
    tipe_penumpang varchar(64) not null
);

CREATE TABLE tarif (
    id int primary key AUTO_INCREMENT,
    kota_asal int,
    kota_tujuan int,
    tipe_penumpang int,
    tarif decimal(15,2),
    FOREIGN KEY (kota_asal) REFERENCES daerah_operasional(id),
    FOREIGN KEY (kota_tujuan) REFERENCES daerah_operasional(id),
    FOREIGN KEY (tipe_penumpang) REFERENCES tipe_penumpang(id)
);

CREATE TABLE pesanan (
    id int primary key AUTO_INCREMENT,
    nama_pemesan varchar(255) not null,
    kontak_pemesan varchar(32) not null,
    tanggal_pemesanan datetime default CURRENT_TIMESTAMP,
    tanggal_keberangkatan datetime not null,
    jam_keberangkatan time not null,
    kota_asal varchar(64) not null,
    kota_tujuan varchar(64) not null,
    titik_jemput text,
    tipe_penumpang varchar(64),
    total_tarif decimal(15,2) not null,
    status_bukti_pembayaran varchar(64),
    status_pemesanan varchar(64),
    total_uang_muka decimal(15,2),
    total_dibayarkan decimal(15,2),
    bukti_pembayaran varchar(255),
    pemesan_id int,
    mobil_id int,
    FOREIGN KEY (pemesan_id) REFERENCES users(id),
    FOREIGN KEY (mobil_id) REFERENCES mobil(id)
);

CREATE TABLE pesanan_detail(
    id int primary key AUTO_INCREMENT,
    pesanan_id int NOT NULL,
    nomor_kursi int NOT NULL,
    harga_tiket decimal(15,2) not null,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id),
);