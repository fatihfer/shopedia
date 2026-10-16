<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class InfoController extends Controller
{
    /** @var array<string, array{title: string, body: string[]}> */
    private const PAGES = [
        'syarat-ketentuan' => [
            'title' => 'Syarat & Ketentuan',
            'body' => [
                'Dengan berbelanja di Shopedia, Anda menyetujui seluruh ketentuan berikut. Harga yang tertera adalah harga final dalam Rupiah dan dapat berubah sewaktu-waktu tanpa pemberitahuan.',
                'Pesanan wajib dibayar maksimal 1×24 jam setelah checkout. Pesanan yang melewati batas waktu dibatalkan otomatis dan stok dikembalikan.',
                'Bukti pembayaran yang tidak valid (nominal kurang, struk tidak jelas) akan ditolak dan customer wajib mengunggah ulang dalam 1×24 jam.',
            ],
        ],
        'kebijakan-privasi' => [
            'title' => 'Kebijakan Privasi',
            'body' => [
                'Data yang kami simpan (nama, email, nomor HP, alamat) hanya digunakan untuk memproses pesanan dan layanan pelanggan. Kami tidak menjual data Anda ke pihak ketiga.',
                'Bukti pembayaran yang diunggah hanya dapat diakses oleh Anda dan admin toko untuk keperluan verifikasi.',
                'Hubungi kami melalui halaman Kontak jika ingin meminta penghapusan akun dan data pribadi Anda.',
            ],
        ],
        'pengembalian-barang' => [
            'title' => 'Pengembalian Barang',
            'body' => [
                'Garansi 7 hari sejak paket diterima untuk produk cacat produksi atau salah kirim. Sertakan foto/video unboxing sebagai bukti.',
                'Ajukan klaim via WhatsApp admin dengan menyebutkan nomor order. Setelah disetujui, barang dikirim kembali atau dana dikembalikan via transfer.',
                'Ongkos kirim pengembalian ditanggung toko untuk kesalahan kami, dan ditanggung pembeli untuk alasan selain cacat/salah kirim.',
            ],
        ],
    ];

    public function show(string $slug): View
    {
        abort_unless(isset(self::PAGES[$slug]), 404);

        return view('info.show', [
            'title' => self::PAGES[$slug]['title'],
            'paragraphs' => self::PAGES[$slug]['body'],
            'pages' => collect(self::PAGES)->map(fn ($p) => $p['title']),
        ]);
    }
}
