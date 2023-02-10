<?php

namespace App\Controllers;

class Sparepart extends BaseController
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
            'title' => 'Sparepart',
        ];
        return view('auth/sparepart/index', $data);
    }

    public function getdata()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'List Sparepart',
                'list' => $this->sparepart->orderBy('kodebarcode', 'ASC')->findAll()

            ];
            $msg = [
                'data' => view('auth/sparepart/list', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function formtambah()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'Tambah Sparepart'
            ];
            $msg = [
                'data' => view('auth/sparepart/tambah', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function simpan()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'kodebarcode' => [
                    'label' => 'Barcode',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'nama_sparepart' => [
                    'label' => 'Nama Sparepart',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'harga_beli' => [
                    'label' => 'Harga Beli',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'harga_jual' => [
                    'label' => 'Harga Jual',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ]
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'kodebarcode' => $validation->getError('kodebarcode'),
                        'nama_sparepart' => $validation->getError('nama_sparepart'),
                        'harga_beli' => $validation->getError('harga_beli'),
                        'harga_jual' => $validation->getError('harga_jual'),
                    ]
                ];
            } else {
                $simpandata = [
                    'kodebarcode'  => $this->request->getVar('kodebarcode'),
                    'nama_sparepart'  => $this->request->getVar('nama_sparepart'),
                    'harga_beli' => str_replace(',', '', $this->request->getVar('harga_beli')),
                    'harga_jual' => str_replace(',', '', $this->request->getVar('harga_jual')),
                ];

                $this->sparepart->insert($simpandata);
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
            $kodebarcode = $this->request->getVar('kodebarcode');
            $list =  $this->sparepart->find($kodebarcode);
            $data = [
                'title'             => 'Edit Sparepart',
                'kodebarcode'      => $list['kodebarcode'],
                'nama_sparepart'    => $list['nama_sparepart'],
                'harga_beli'        => $list['harga_beli'],
                'harga_jual'        => $list['harga_jual'],
            ];
            $msg = [
                'sukses' => view('auth/sparepart/edit', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function update()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'kodebarcode' => [
                    'label' => 'Barcode',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'nama_sparepart' => [
                    'label' => 'Nama Sparepart',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'harga_beli' => [
                    'label' => 'Harga Beli',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'harga_jual' => [
                    'label' => 'Harga Jual',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ]
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'kodebarcode' => $validation->getError('kodebarcode'),
                        'nama_sparepart' => $validation->getError('nama_sparepart'),
                        'harga_beli' => $validation->getError('harga_beli'),
                        'harga_jual' => $validation->getError('harga_jual'),
                    ]
                ];
            } else {
                $updatedata = [
                    'kodebarcode'  => $this->request->getVar('kodebarcode'),
                    'nama_sparepart'  => $this->request->getVar('nama_sparepart'),
                    'harga_beli' => str_replace(',', '', $this->request->getVar('harga_beli')),
                    'harga_jual' => str_replace(',', '', $this->request->getVar('harga_jual')),
                ];

                $kodebarcode = $this->request->getVar('kodebarcode');
                $this->sparepart->update($kodebarcode, $updatedata);
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

            $kodebarcode = $this->request->getVar('kodebarcode');
            $this->sparepart->delete($kodebarcode);
            $msg = [
                'sukses' => 'Data Sparepart Berhasil Dihapus'
            ];

            echo json_encode($msg);
        }
    }

    public function hapusall()
    {
        if ($this->request->isAJAX()) {
            $kodebarcode = $this->request->getVar('kodebarcode');
            $jmldata = count($kodebarcode);
            for ($i = 0; $i < $jmldata; $i++) {
                $this->sparepart->delete($kodebarcode[$i]);
            }

            $msg = [
                'sukses' => "$jmldata Data berhasil dihapus"
            ];
            echo json_encode($msg);
        }
    }
}
