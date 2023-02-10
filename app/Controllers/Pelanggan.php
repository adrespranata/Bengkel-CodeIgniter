<?php

namespace App\Controllers;

use Config\Services;

class Pelanggan extends BaseController
{
    public function password()
    {
        echo (password_hash('123', PASSWORD_BCRYPT));
    }

    public function index()
    {
        if (session()->get('level') <> 2) {
            return redirect()->to('dashboard');
        }
        $data = [
            'title' => 'Pelanggan',
        ];
        return view('auth/pelanggan/index', $data);
    }

    public function getdata()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'List Pelanggan',
                'list' => $this->pelanggan->orderBy('pelanggan_id', 'ASC')->findAll()
            ];
            $msg = [
                'data' => view('auth/pelanggan/list', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function formtambah()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'Tambah Pelanggan'
            ];
            $msg = [
                'data' => view('auth/pelanggan/tambah', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function simpan()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'nama_pelanggan' => [
                    'label' => 'Nama Pelanggan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'telephone' => [
                    'label' => 'Telephone',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ]
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'nama_pelanggan' => $validation->getError('nama_pelanggan'),
                        'telephone' => $validation->getError('telephone')
                    ]
                ];
            } else {
                $simpandata = [
                    'nama_pelanggan' => $this->request->getVar('nama_pelanggan'),
                    'telephone' => $this->request->getVar('telephone'),
                ];
                $this->pelanggan->insert($simpandata);

                $msg = [
                    'sukses' => 'Data berhasil disimpan'
                ];
            }
            echo json_encode($msg);
        }
    }

    public function formedit()
    {
        if ($this->request->isAJAX()) {
            $pelanggan_id = $this->request->getVar('pelanggan_id');
            $list =  $this->pelanggan->find($pelanggan_id);
            $data = [
                'title'         => 'Edit Pelanggan',
                'pelanggan_id'   => $list['pelanggan_id'],
                'nama_pelanggan' => $list['nama_pelanggan'],
                'telephone'     => $list['telephone'],
            ];
            $msg = [
                'sukses' => view('auth/pelanggan/edit', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function update()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'nama_pelanggan' => [
                    'label' => 'Nama Pelanggan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'telephone' => [
                    'label' => 'Telephone',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ]
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'nama_pelanggan' => $validation->getError('nama_pelanggan'),
                        'telephone' => $validation->getError('telephone')
                    ]
                ];
            } else {
                $updatedata = [
                    'nama_pelanggan' => $this->request->getVar('nama_pelanggan'),
                    'telephone' => $this->request->getVar('telephone'),
                ];

                $pelanggan_id = $this->request->getVar('pelanggan_id');
                $this->pelanggan->update($pelanggan_id, $updatedata);
                $msg = [
                    'sukses' => 'Data berhasil diupdate'
                ];
            }
            echo json_encode($msg);
        }
    }

    public function hapus()
    {
        if ($this->request->isAJAX()) {
            $pelanggan_id = $this->request->getVar('pelanggan_id');
            $this->pelanggan->delete($pelanggan_id);
            $msg = [
                'sukses' => 'Data Pelanggan Berhasil Dihapus'
            ];

            echo json_encode($msg);
        }
    }

    public function hapusall()
    {
        if ($this->request->isAJAX()) {
            $pelanggan_id = $this->request->getVar('pelanggan_id');
            $jmldata = count($pelanggan_id);
            for ($i = 0; $i < $jmldata; $i++) {
                $this->pelanggan->delete($pelanggan_id[$i]);
            }

            $msg = [
                'sukses' => "$jmldata Data berhasil dihapus"
            ];
            echo json_encode($msg);
        }
    }

    //data untuk ke purchase
    public function modalData()
    {
        if ($this->request->isAJAX()) {
            $msg = [
                'data' => view('auth/sale/viewpelanggan')
            ];

            echo json_encode($msg);
        }
    }

    //ambil data pelanggan
    public function cariDataPelanggan()
    {
        if ($this->request->isAJAX()) {
            $request = Services::request();
            $modalpelanggan = $this->pelanggan;
            if ($request->getMethod()) {
                $lists = $modalpelanggan->get_datatables();
                $data = [];
                $no = $request->getPost("start");
                foreach ($lists as $list) {
                    $no++;
                    $row = [];

                    $btnPilih = "<button type=\"button\" class=\"btn-sm btn-primary\" onclick=\"pilihpelanggan('" . $list->pelanggan_id . "','" . $list->nama_pelanggan . "')\"><i class=\"fa fa-check\"></i> Pilih</button>";

                    $row[] = $no;
                    $row[] = $list->nama_pelanggan;
                    $row[] = $list->telephone;
                    $row[] = $btnPilih;
                    $data[] = $row;
                }
                $output = [
                    "draw" => $request->getPost('draw'),
                    "recordTotal" => $modalpelanggan->count_all(),
                    "recordsFiltered" => $modalpelanggan->count_filtered(),
                    "data" => $data
                ];

                echo json_encode($output);
            }
        }
    }
}
