<?php

namespace App\Controllers;

use Config\Services;

class Sale extends BaseController
{
    //sale
    public function index()
    {
        if (session()->get('level') <> 2) {
            return redirect()->to('/dashboard');
        }
        $data = [
            'title' => 'Sale'
        ];
        return view('auth/sale/index', $data);
    }

    public function getdata()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'List Sale',
                'list' => $this->sale->list()
            ];
            $msg = [
                'data' => view('auth/sale/list', $data)
            ];
            echo json_encode($msg);
        }
    }


    public function formtambah()
    {
        $data = [
            'title' => 'Sale',
            'nofaktur' => $this->buatFaktur()
        ];
        return view('auth/sale/tambah', $data);
    }

    public function detailItem()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('jual_faktur');
            $tblDetailSale =  $this->saledetail;
            $queryDetItem = $tblDetailSale->detailItem($nofaktur);

            $data = [
                'title' => 'List Sale Sparepart',
                'tampildetitem' => $queryDetItem->get()
            ];

            $msg = [
                'data' => view('auth/sale/detailitem', $data)
            ];

            echo json_encode($msg);
        }
    }

    //temp sale stock in

    public function viewDataProduk()
    {

        if ($this->request->isAJAX()) {
            $keyword = $this->request->getPost('keyword');
            $data = [
                'title' => 'List Sparepart',
                'keyword' => $keyword
            ];
            $msg = [
                'data' => view('auth/sale/viewproduk', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function buatFaktur()
    {
        $tgl = date('Y-m-d');
        $query = $this->db->query("SELECT MAX(jual_faktur) AS nofaktur FROM sale WHERE DATE_FORMAT(jual_date, '%Y-%m-%d') = '$tgl'");
        $hasil = $query->getRowArray();
        $data = $hasil['nofaktur'];

        //nomor urut tanggal+4 string contoh J080520220001
        $lastNoUrut = substr($data, -4);

        //nomor urut ditambah 1
        $nextNoUrut = intval($lastNoUrut) + 1;

        //membuat format nomor transaksi berikutnya
        $fakturSale = 'S' . date('dmy', strtotime($tgl)) . sprintf('%04s', $nextNoUrut);

        return $fakturSale;
    }

    public function listDataProduk()
    {
        if ($this->request->isAJAX()) {
            $keyData = $this->request->getPost('keyData');
            $request = Services::request();
            $modalProduk = $this->produk;
            if ($request->getMethod()) {
                $lists = $modalProduk->get_datatables($keyData);
                $data = [];
                $no = $request->getPost("start");
                foreach ($lists as $list) {
                    $no++;
                    $row = [];
                    $row[] = $no;
                    $row[] = $list->kodebarcode;
                    $row[] = $list->nama_sparepart;
                    $row[] = number_format($list->harga_jual, 0, ',', '.',);
                    $row[] = $list->stok;
                    $row[] = "<button type=\"button\" class=\"btn-sm btn-primary\" onclick=\"pilihsparepart('" . $list->kodebarcode . "','" . $list->nama_sparepart . "','" . $list->stok . "')\"><i class=\"fa fa-check\"></i> Pilih</button>";
                    $data[] = $row;
                }
                $output = [
                    "draw" => $request->getPost('draw'),
                    "recordTotal" => $modalProduk->count_all($keyData),
                    "recordsFiltered" => $modalProduk->count_filtered($keyData),
                    "data" => $data
                ];

                echo json_encode($output);
            }
        }
    }

    public function dataDetail()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('nofaktur');
            $queryTemp = $this->saletemp;
            $dataTemp = $queryTemp->dataDetail($nofaktur);

            $data = [
                'datadetail' => $dataTemp->get()
            ];

            $msg = [
                'data' => view('auth/sale/viewdetail', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function tempSale()
    {
        if ($this->request->isAJAX()) {
            $kodebarcode = $this->request->getPost('kodebarcode');
            $qty = $this->request->getPost('qty');
            $nofaktur = $this->request->getPost('nofaktur');

            $cekData = $this->sparepart->tempSale($kodebarcode)->get();
            $totalData = $cekData->getNumRows();

            if ($totalData > 1) {
                $msg = [
                    'totaldata' => 'banyak'
                ];
            } else if ($totalData == 1) {
                //insert temp sale
                $tblTempSale = $this->db->table('sale_temp');
                $rowProduk = $cekData->getRowArray();

                $stokProduk = $rowProduk['stok'];
                if (intval($stokProduk) == 0) {
                    $msg = [
                        'error' => 'Maaf stok habis!'
                    ];
                } else if ($qty > intval($stokProduk)) {
                    $msg = [
                        'error' => 'Maaf stok tidak mencukupi!'
                    ];
                } else {
                    $insertData = [
                        'det_jualfaktur' => $nofaktur,
                        'det_jualkodebarcode' => $rowProduk['kodebarcode'],
                        'det_hargajual' => $rowProduk['harga_jual'],
                        'det_jualqty' => $qty,
                        'det_jualtotal' => floatval($rowProduk['harga_jual']) * $qty
                    ];

                    $tblTempSale->insert($insertData);

                    $msg = [
                        'sukses' => 'berhasil'
                    ];
                }
            } else {
                $msg = [
                    'error' => 'Maaf produk tidak ditemukan!'
                ];
            }
            echo json_encode($msg);
        }
    }

    public function hapusItem()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getPost('id');
            $tblTempSale = $this->db->table('sale_temp');
            $queryHapus = $tblTempSale->delete(['sale_det' => $id]);

            if ($queryHapus) {
                $msg = [
                    'sukses' => 'Data Sale Berhasil Dihapus'
                ];
            }

            echo json_encode($msg);
        }
    }

    public function batalSale()
    {
        if ($this->request->isAJAX()) {
            $tblTempSale = $this->db->table('sale_temp');
            $hapusData = $tblTempSale->emptyTable();

            if ($hapusData) {
                $msg = [
                    'sukses' => 'berhasil'
                ];
            }

            echo json_encode($msg);
        }
    }

    public function hitungTotalBayar()
    {

        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('nofaktur');
            $tblTempSale = $this->saletemp;
            $queryTotal = $tblTempSale->hitungTotalBayar($nofaktur)
                ->get();
            $rowTotal = $queryTotal->getRowArray();

            $msg = [
                'totalbayar' => number_format($rowTotal['totalbayar'], 0, ",", ".")
            ];

            echo json_encode($msg);
        }
    }

    public function pembayaran()
    {
        if ($this->request->isAJAX()) {

            $nofaktur = $this->request->getPost('nofaktur');
            $pelanggan_id = $this->request->getPost('pelanggan_id');
            $nama_pelanggan = $this->request->getPost('nama_pelanggan');

            $tblTempSale = $this->saletemp;
            $cekDataTempSale = $tblTempSale->getWhere(['det_jualfaktur' => $nofaktur]);
            $queryTotal = $tblTempSale->select('SUM(det_jualtotal) as totalbayar')
                ->where('det_jualfaktur', $nofaktur)
                ->get();
            $rowTotal = $queryTotal->getRowArray();

            if ($cekDataTempSale->getNumRows() > 0) {
                //Modal Pembayaran
                $data = [
                    'title' => 'Pembayaran',
                    'nofaktur' => $nofaktur,
                    'pelanggan_id' => $pelanggan_id,
                    'nama_pelanggan' => $nama_pelanggan,
                    'totalbayar' => $rowTotal['totalbayar']
                ];

                $msg = [
                    'data' => view('auth/sale/modalpembayaran', $data)
                ];
            } else {
                $msg = [
                    'error' => 'Maaf itemnya belum ada..'
                ];
            }
            echo json_encode($msg);
        }
    }

    public function simpanSale()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('nofaktur');
            $pelanggan_id = $this->request->getPost('pelanggan_id');
            $totalkotor = $this->request->getPost('totalkotor');
            $totalbersih = str_replace(",", "", $this->request->getPost('totalbersih'));
            $dispersen = str_replace(",", "", $this->request->getPost('dispersen'));
            $disuang = str_replace(",", "", $this->request->getPost('disuang'));
            $jumlahuang = str_replace(",", "", $this->request->getPost('jumlahuang'));
            $sisauang = str_replace(",", "", $this->request->getPost('sisauang'));

            //tabel
            $tblSale = $this->db->table('sale')
                ->join('pelanggan', 'pelanggan.pelanggan_id = sale.pelanggan_id')
                ->join('sale_detail', 'sale_detail.detail_sale=sale.detail_sale');
            $tblTempSale = $this->db->table('sale_temp');
            $tblSaleDetail = $this->db->table('sale_detail');

            //insert table sale
            $dataInsertSale = [
                'jual_faktur' => $nofaktur,
                'pelanggan_id' => $pelanggan_id,
                'jual_total' => $totalkotor,
                'jual_dispersen' => $dispersen,
                'jual_disuang' => $disuang,
                'jual_totalbersih' => $totalbersih,
                'jual_jmluang' => $jumlahuang,
                'jual_sisauang' => $sisauang
            ];
            $tblSale->insert($dataInsertSale);

            //insert table sale detail
            $ambilDataTemp = $tblTempSale->getWhere(['det_jualfaktur' => $nofaktur]);
            $fieldDetailSale = [];
            foreach ($ambilDataTemp->getResultArray() as $row) {
                $fieldDetailSale[] = [
                    'det_jualfaktur' => $nofaktur,
                    'det_jualkodebarcode' => $row['det_jualkodebarcode'],
                    'det_hargajual' => $row['det_hargajual'],
                    'det_jualqty' => $row['det_jualqty'],
                    'det_jualtotal' => $row['det_jualtotal']
                ];
            }
            $tblSaleDetail->insertBatch($fieldDetailSale);

            //hapus temp sale
            $tblTempSale->emptyTable();

            $msg = [
                'sukses' => 'Transaksi berhasil disimpan',
                'cetak' => site_url('sale/cetakfaktur/' . $nofaktur)
            ];
            echo json_encode($msg);
        }
    }

    //cetak faktur
    public function cetakfaktur($faktur)
    {
        $modelSale = $this->sale;
        $modelDetail = $this->saledetail;
        $modelPelanggan = $this->pelanggan;

        $cekData = $modelSale->find($faktur);
        $dataPelanggan = $modelPelanggan->find($cekData['pelanggan_id']);

        $namaPelanggan = ($dataPelanggan != null) ? $dataPelanggan['nama_pelanggan'] : '-';

        if ($cekData != null) {
            $data = [
                'faktur' => $faktur,
                'tanggal' => $cekData['jual_date'],
                'nama_pelanggan' => $namaPelanggan,
                'detailbarang' => $modelDetail->dataDetailSale($faktur),
                'jumlahuang' => $cekData['jual_jmluang'],
                'sisauang' => $cekData['jual_sisauang']
            ];
            return view('auth/sale/cetakfaktur', $data);
        } else {
            return redirect()->to(site_url('auth/sale/tambah'));
        }
    }

    //edit
    public function edit($faktur)
    {
        $cekFaktur = $this->sale->cekFaktur($faktur);
        if ($cekFaktur->getNumRows() > 0) {
            $row = $cekFaktur->getRowArray();

            $data = [
                'title' => 'Edit Sale',
                'nofaktur' => $row['jual_faktur'],
                'tanggal' => $row['jual_date']
            ];
            return view('auth/sale/edit', $data);
        } else {
            exit('Data tidak ditemukan');
        }
    }
    public function dataDetailSale()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('nofaktur');
            $queryDetail = $this->saledetail;

            $data = [
                'datadet' => $queryDetail->dataDetailSale($nofaktur),
            ];
            $totalHargaFaktur = number_format($queryDetail->ambilTotalHarga($nofaktur), 0, ",", ".");
            $msg = [
                'data' => view('auth/sale/datadetail', $data),
                'totalharga' => $totalHargaFaktur
            ];
            echo json_encode($msg);
        }
    }

    public function editItem()
    {
        if ($this->request->isAJAX()) {
            $iddetail = $this->request->getPost('iddetail');

            $queryDetail = $this->saledetail;
            $ambilData = $queryDetail->ambilDetailBerdasarkanID($iddetail);
            $row = $ambilData->getRowArray();
            $data = [
                'kodebarang' => $row['det_jualkodebarcode'],
                'nama_sparepart' => $row['nama_sparepart'],
                'stok' => $row['stok'],
                'qty' => $row['det_jualqty']
            ];

            $msg = [
                'sukses' => $data
            ];
            echo json_encode($msg);
        }
    }

    public function detailSale()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('nofaktur');
            $kodebarcode = $this->request->getPost('kodebarcode');
            $qty = $this->request->getPost('qty');

            $queryDetail = $this->saledetail;
            $querySale = $this->sale;
            $cekData = $this->sparepart->detailSale($kodebarcode)->get();

            $totalData = $cekData->getNumRows();
            if ($totalData > 1) {
                $msg = [
                    'totaldata' => 'banyak'
                ];
            } else {
                //insert detail sale
                $tblDetailSale = $this->db->table('sale_detail');
                $rowProduk = $cekData->getRowArray();

                $tblDetailSale->insert([
                    'det_jualfaktur' => $nofaktur,
                    'det_jualkodebarcode' => $rowProduk['kodebarcode'],
                    'det_hargajual' => $rowProduk['harga_jual'],
                    'det_jualqty' => $qty,
                    'det_jualtotal' => floatval($rowProduk['harga_jual']) * $qty
                ]);

                $ambilTotalHarga = $queryDetail->ambilTotalHarga($nofaktur);
                $querySale->update($nofaktur, [
                    'jual_total' => $ambilTotalHarga
                ]);
                $msg = [
                    'sukses' => 'berhasil'
                ];
            }
            echo json_encode($msg);
        }
    }

    public function updateItem()
    {
        if ($this->request->isAJAX()) {
            $qty = $this->request->getPost('qty');
            $iddetail = $this->request->getPost('iddetail');

            $queryDetail = $this->saledetail;
            $querySale = $this->sale;

            $rowData = $queryDetail->find($iddetail);
            $nofaktur = $rowData['det_jualfaktur'];
            $hargajual = $rowData['det_hargajual'];

            $queryDetail->update($iddetail, [
                'det_jualqty' => $qty,
                'det_jualtotal' => floatval($hargajual) * $qty
            ]);

            $ambilTotalHarga = $queryDetail->ambilTotalHarga($nofaktur);
            $querySale->update($nofaktur, [
                'jual_total' => $ambilTotalHarga
            ]);
            $msg = [
                'sukses' => 'Item berhasil di update'
            ];

            echo json_encode($msg);
        }
    }

    public function hapusItemDetail()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');
            $nofaktur = $this->request->getPost('nofaktur');

            $querySale = $this->sale;
            $queryDetail = $this->saledetail;
            $tblDetailSale = $this->db->table('sale_detail');
            $queryHapus = $tblDetailSale->delete(['detail_sale' => $id]);

            if ($queryHapus) {

                $ambilTotalHarga = $queryDetail->ambilTotalHarga($nofaktur);
                $querySale->update($nofaktur, [
                    'jual_total' => $ambilTotalHarga
                ]);
                $msg = [
                    'sukses' => 'Data Sale Berhasil Dihapus'
                ];
            }

            echo json_encode($msg);
        }
    }
    //hapus data sale
    public function hapus()
    {
        if ($this->request->isAJAX()) {
            $faktur = $this->request->getPost('faktur');
            $db = \Config\Database::connect();
            $db->table('sale_detail')->delete(['det_jualfaktur' => $faktur]);
            $this->sale->delete($faktur);

            $msg = [
                'sukses' => 'Data Transaksi '
            ];

            echo json_encode($msg);
        }
    }
}
