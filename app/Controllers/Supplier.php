<?php

namespace App\Controllers;

use Config\Services;

class Supplier extends BaseController
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
            'title' => 'Supplier',
        ];
        return view('auth/supplier/index', $data);
    }

    public function getdata()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'List Supplier',
                'list' => $this->supplier->orderBy('supplier_id', 'ASC')->findAll()
            ];
            $msg = [
                'data' => view('auth/supplier/list', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function formtambah()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'Tambah Supplier'
            ];
            $msg = [
                'data' => view('auth/supplier/tambah', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function simpan()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'nama_supplier' => [
                    'label' => 'Nama Supplier',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'alamat' => [
                    'label' => 'Alamat',
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
                        'nama_supplier' => $validation->getError('nama_supplier'),
                        'alamat' => $validation->getError('alamat'),
                        'telephone' => $validation->getError('telephone')
                    ]
                ];
            } else {
                $simpandata = [
                    'nama_supplier' => $this->request->getVar('nama_supplier'),
                    'alamat' => $this->request->getVar('alamat'),
                    'telephone' => $this->request->getVar('telephone'),
                    'foto' => $this->request->getVar('foto'),
                ];

                $this->supplier->insert($simpandata);
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
            $supplier_id = $this->request->getVar('supplier_id');
            $list =  $this->supplier->find($supplier_id);
            $data = [
                'title'         => 'Edit Supplier',
                'supplier_id'   => $list['supplier_id'],
                'nama_supplier' => $list['nama_supplier'],
                'alamat'        => $list['alamat'],
                'telephone'     => $list['telephone'],
            ];
            $msg = [
                'sukses' => view('auth/supplier/edit', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function update()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'nama_supplier' => [
                    'label' => 'Nama Supplier',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                    ]
                ],
                'alamat' => [
                    'label' => 'Alamat',
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
                        'nama_supplier' => $validation->getError('nama_supplier'),
                        'alamat' => $validation->getError('alamat'),
                        'telephone' => $validation->getError('telephone')
                    ]
                ];
            } else {
                $updatedata = [
                    'nama_supplier' => $this->request->getVar('nama_supplier'),
                    'alamat' => $this->request->getVar('alamat'),
                    'telephone' => $this->request->getVar('telephone'),
                ];

                $supplier_id = $this->request->getVar('supplier_id');
                $this->supplier->update($supplier_id, $updatedata);
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

            $supplier_id = $this->request->getVar('supplier_id');
            //check
            $cekdata = $this->supplier->find($supplier_id);
            $fotolama = $cekdata['foto'];
            if ($fotolama != 'default.png') {
                unlink('img/supplier/' . $fotolama);
                unlink('img/supplier/thumb/' . 'thumb_' . $fotolama);
            }
            $this->supplier->delete($supplier_id);
            $msg = [
                'sukses' => 'Data Supplier Berhasil Dihapus'
            ];

            echo json_encode($msg);
        }
    }

    public function hapusall()
    {
        if ($this->request->isAJAX()) {
            $supplier_id = $this->request->getVar('supplier_id');
            $jmldata = count($supplier_id);
            for ($i = 0; $i < $jmldata; $i++) {
                //check
                $cekdata = $this->supplier->find($supplier_id[$i]);
                $fotolama = $cekdata['foto'];
                if ($fotolama != 'default.png') {
                    unlink('img/supplier/' . $fotolama);
                    unlink('img/supplier/thumb/' . 'thumb_' . $fotolama);
                }
                $this->supplier->delete($supplier_id[$i]);
            }

            $msg = [
                'sukses' => "$jmldata Data berhasil dihapus"
            ];
            echo json_encode($msg);
        }
    }

    public function formupload()
    {
        if ($this->request->isAJAX()) {
            $supplier_id = $this->request->getVar('supplier_id');
            $list =  $this->supplier->find($supplier_id);
            $data = [
                'title' => 'Upload Foto Supplier',
                'list'  => $list,
                'supplier_id' => $supplier_id
            ];
            $msg = [
                'sukses' => view('auth/supplier/upload', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function doupload()
    {
        if ($this->request->isAJAX()) {

            $supplier_id = $this->request->getVar('supplier_id');

            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'foto' => [
                    'label' => 'Upload foto',
                    'rules' => 'uploaded[foto]|mime_in[foto,image/png,image/jpg,image/jpeg]|is_image[foto]',
                    'errors' => [
                        'uploaded' => 'Masukkan gambar',
                        'mime_in' => 'Harus gambar!'
                    ]
                ]
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'foto' => $validation->getError('foto')
                    ]
                ];
            } else {

                //check
                $cekdata = $this->supplier->find($supplier_id);
                $fotolama = $cekdata['foto'];
                if ($fotolama != 'default.png') {
                    unlink('img/supplier/' . $fotolama);
                    unlink('img/supplier/thumb/' . 'thumb_' . $fotolama);
                }

                $filefoto = $this->request->getFile('foto');

                $updatedata = [
                    'foto' => $filefoto->getName()
                ];

                $this->supplier->update($supplier_id, $updatedata);

                \Config\Services::image()
                    ->withFile($filefoto)
                    ->fit(250, 250, 'center')
                    ->save('img/supplier/thumb/' . 'thumb_' .  $filefoto->getName());
                $filefoto->move('img/supplier');
                $msg = [
                    'sukses' => 'Foto berhasil diupload!'
                ];
            }
            echo json_encode($msg);
        }
    }


    //data untuk ke purchase
    public function modalData()
    {
        if ($this->request->isAJAX()) {
            $msg = [
                'data' => view('auth/purchase/viewsupplier')
            ];

            echo json_encode($msg);
        }
    }

    //ambil data supplier
    public function cariDataSupplier()
    {
        if ($this->request->isAJAX()) {
            $request = Services::request();
            $modalsupplier = $this->supplier;
            if ($request->getMethod()) {
                $lists = $modalsupplier->get_datatables();
                $data = [];
                $no = $request->getPost("start");
                foreach ($lists as $list) {
                    $no++;
                    $row = [];

                    $btnPilih = "<button type=\"button\" class=\"btn-sm btn-primary\" onclick=\"pilihsupplier('" . $list->supplier_id . "','" . $list->nama_supplier . "')\"><i class=\"fa fa-check\"></i> Pilih</button>";

                    $row[] = $no;
                    $row[] = $list->nama_supplier;
                    $row[] = $list->alamat;
                    $row[] = $list->telephone;
                    $row[] = $btnPilih;
                    $data[] = $row;
                }
                $output = [
                    "draw" => $request->getPost('draw'),
                    "recordTotal" => $modalsupplier->count_all(),
                    "recordsFiltered" => $modalsupplier->count_filtered(),
                    "data" => $data
                ];

                echo json_encode($output);
            }
        }
    }
}
