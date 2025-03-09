<?php

namespace App\Controllers;

use App\Models\KomikModel;
use Kint\Value\FunctionValue;

class Komik extends BaseController
{
    protected $komikModel;
    public function __construct()
    {
        $this->komikModel = new KomikModel();
    }
    public function index()
    {
        $komik = $this->komikModel->findAll();
        $data = [
            'title' => 'Daftar Komik',
            'komik' => $this->komikModel->getKomik()
            // 'alamat' => [
            //     [
            //         'tipe' => 'Rumah',
            //         'alamat' => ' di bubulak dongs',
            //         'kota' => 'Karawang'
            //     ],
            //     [
            //         'tipe' => 'Kantor',
            //         'alamat' => ' di Gs Battery',
            //         'kota' => 'Karawang'
            //     ]

            // ]
        ];
        // cara konek ke db tanpa Model
        // $db = \Config\Database::connect();
        // $komik = $db->query("SELECT * FROM komik");
        // foreach ($komik->getResultArray() as $row) {
        //     d($row);
        // }
        // $komikModel = new \App\Models\KomikModel();


        return view('komik/index', $data);
    }
    public function detail($slug)
    {
        $komik = $this->komikModel->getKomik($slug);
        $data = [
            'title' => 'Detail Komik ',
            'komik' => $this->komikModel->getKomik($slug)
        ];
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('judul Komik ' . $slug . 'Tidak Ditemukan');
        }
        return view('/komik/detail', $data);
    }
    public function create()
    {
        $data = [
            'title' => 'Form Tambah Data Komik',
            'validation' => session('validation')
        ];
        return view('komik/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required'   => '{field} komik harus diisi.',
                    'is_unique'  => '{field} komik sudah terdaftar.'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,2048]|is_image[sampul]|mime_in[sampul,image/png,image/jpg,image/jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran file terlalu besar (max 1MB).',
                    'is_image' => 'Yang diunggah harus berupa gambar.',
                    'mime_in'  => 'Format gambar harus PNG, JPEG, atau JPG.'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
        }

        // Ambil file sampul
        $fileSampul = $this->request->getFile('sampul');

        //apakah tidak ada gambar yang di upload 
        if ($fileSampul->getError() == 4) {
            $namaSampul = 'default.png';
        } else {
            // Generate nama unik untuk file sampul
            $namaSampul = $fileSampul->getRandomName();
            // Pindahkan file ke folder public/img
            $fileSampul->move('img', $namaSampul);
        }


        // Simpan data ke database
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => url_title($this->request->getVar('judul'), '-', true),
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul // Simpan nama file
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');
        return redirect()->to('/komik');
    }

    public function delete($id)
    {
        //cari gambar pada db berdasarkan id
        $komik = $this->komikModel->find($id);
        //kondisi agar default.png tidak terrhapus
        $filePath = FCPATH . 'img/' . $komik['sampul'];
        if ($komik['sampul'] != 'default.png' && file_exists($filePath)) {
            unlink($filePath);
        }

        // Cek apakah data dengan ID tertentu ada
        $komik = $this->komikModel->find($id);
        if (!$komik) {
            return redirect()->to('/komik')->with('error', 'Data tidak ditemukan');
        }

        // Hapus data dari database
        $this->komikModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/komik')->with('success', 'Data berhasil dihapus');
    }
    public function edit($slug)
    {
        $data = [
            'title' => 'Form Ubah Data Komik',
            'validation' => session('validation'),
            'komik' => $this->komikModel->getKomik($slug)
        ];
        return view('komik/edit', $data);
    }
    public function update($id)
    {
        // Cek judul
        $komikLama = $this->komikModel->getKomik($this->request->getVar('slug'));
        $sampulLama = $this->komikModel->find($id)['sampul'];

        if (!$this->validate([
            'judul' => [
                'rules' => 'required',
                'errors' => [
                    'required'   => '{field} komik harus diisi.',
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,2048]|is_image[sampul]|mime_in[sampul,image/png,image/jpg,image/jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran file terlalu besar (max 2MB).',
                    'is_image' => 'Yang diunggah harus berupa gambar.',
                    'mime_in'  => 'Format gambar harus PNG, JPEG, atau JPG.'
                ]
            ]
        ])) {
            return redirect()->to('/komik/edit/' . $komikLama['slug'])->withInput();
        }

        $fileSampul = $this->request->getFile('sampul');

        // Jika tidak ada gambar baru yang diunggah, gunakan gambar lama
        if ($fileSampul->getError() == 4) {
            $namaSampul = $sampulLama;
        } else {
            // Generate nama unik dan pindahkan file baru
            $namaSampul = $fileSampul->getRandomName();
            $fileSampul->move('img', $namaSampul);

            // Hapus file lama jika bukan default.png
            if ($sampulLama != 'default.png') {
                unlink('img/' . $sampulLama);
            }
        }

        // Simpan perubahan ke database
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => url_title($this->request->getVar('judul'), '-', true),
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');
        return redirect()->to('/komik');
    }
}
