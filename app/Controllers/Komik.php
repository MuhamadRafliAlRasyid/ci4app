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
        // Atur validasi
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required'   => '{field} komik harus diisi.',
                    'is_unique'  => '{field} komik sudah terdaftar.'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
        }

        // Simpan data jika validasi berhasil
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => url_title($this->request->getVar('judul'), '-', true),
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $this->request->getVar('sampul')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');
        return redirect()->to('/komik');
    }
    public function delete($id)
    {
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
        $rule_judul = ($komikLama['judul'] == $this->request->getVar('judul'))
            ? 'required'
            : 'required|is_unique[komik.judul]';

        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required'   => '{field} komik harus diisi.',
                    'is_unique'  => '{field} komik sudah terdaftar.'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput()->with('validation', $validation);
        }
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => url_title($this->request->getVar('judul'), '-', true),
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $this->request->getVar('sampul')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');
        return redirect()->to('/komik');
    }
}
