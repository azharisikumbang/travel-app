CREATE TABLE m_penyimpanan (
    id int primary key auto_increment,
    nama varchar(255) not null,
    lokasi varchar(255) not null,
    kategori varchar(255) null
);

CREATE TABLE m_kategori_pelanggan (
    id int primary key auto_increment,
    kategori varchar(64) not null
);

CREATE TABLE m_akun (
    id int primary key auto_increment,
    username varchar(255) not null,
    password varchar(255) not null,
    role enum('PELANGGAN', 'DRIVER', 'ADMIN') default 'PELANGGAN'
);

CREATE TABLE m_supir(
    id int primary key auto_increment,
    nama varchar(255) not null,
    kontak varchar(255) not null,
    akun_id int not null,
    photo_id int,
    INDEX (akun_id, photo_id),
    FOREIGN KEY (akun_id) REFERENCES m_akun(id),
    FOREIGN KEY (photo_id) REFERENCES m_penyimpanan(id)
);

CREATE TABLE m_pelanggan(
    id int primary key auto_increment,
    nama varchar(255) not null,
    kontak varchar(255) not null,
    akun_id int not null,
    photo_id int null,
    kategori_id int not null,
    photo_identitas int null,
    INDEX (akun_id, photo_id, kategori_id, photo_identitas),
    FOREIGN KEY (akun_id) REFERENCES m_akun(id),
    FOREIGN KEY (photo_id) REFERENCES m_penyimpanan(id),
    FOREIGN KEY (photo_identitas) REFERENCES m_penyimpanan(id)
);

CREATE TABLE m_jadwal_keberangkatan (
    id int primary key auto_increment,
    jam varchar(16) not null,
    alias varchar(32) null
);

CREATE TABLE m_mobil (
    id int primary key AUTO_INCREMENT,
    merk varchar(128) not null,
    jumlah_kursi int default 1,
    plat_nomor varchar(16) null,
    gambar_id int null,
    supir_id int null,
    INDEX (gambar_id, supir_id),
    FOREIGN KEY (gambar_id) REFERENCES m_penyimpanan(id),
    FOREIGN KEY (supir_id) REFERENCES m_supir(id)
);

CREATE TABLE m_daerah_operasional (
    id int primary key auto_increment,
    nama_kota varchar(255) not null
);

CREATE TABLE m_rute(
    id int primary key auto_increment,
    asal_id int not null,
    tujuan_id int not null,
    INDEX (asal_id, tujuan_id),
    FOREIGN KEY (asal_id) REFERENCES m_daerah_operasional(id),
    FOREIGN KEY (tujuan_id) REFERENCES m_daerah_operasional(id)
);

CREATE TABLE m_tiket(
    id int primary key AUTO_INCREMENT,
    rute_id int not null,
    kategori_penumpang_id int,
    tarif decimal(15,2),
    INDEX (rute_id, kategori_penumpang_id),
    FOREIGN KEY (rute_id) REFERENCES m_rute(id),
    FOREIGN KEY (kategori_penumpang_id) REFERENCES m_kategori_pelanggan(id)
);

CREATE TABLE m_keberangkatan(
    id int primary key AUTO_INCREMENT,
    provinsi_id int not null,
    mobil_id int NOT NULL,
    jam_keberangkatan_id int NOT NULL,
    last_updated datetime DEFAULT CURRENT_TIMESTAMP,
    INDEX (provinsi_id, mobil_id, jam_keberangkatan_id),
    FOREIGN KEY (mobil_id) REFERENCES m_mobil(id),
    FOREIGN KEY (jam_keberangkatan_id) REFERENCES m_jadwal_keberangkatan(id)
);

/* Opersional */

CREATE TABLE pesanan (
    id int primary key AUTO_INCREMENT,
    nomor_pemesanan varchar(255) unique,
    nomor_iterasi_pemesanan int,
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
    nama_pembayaran varchar(255),
    bank_pembayaran varchar(255),
    pemesan_id int,
    mobil varchar(255) DEFAULT NULL,
    driver varchar(255) DEFAULT NULL,
    FOREIGN KEY (pemesan_id) REFERENCES m_akun(id)
);

CREATE TABLE pesanan_detail(
    id int primary key AUTO_INCREMENT,
    pesanan_id int NOT NULL,
    nomor_kursi int NOT NULL,
    harga_tiket decimal(15,2) not null,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id)
);