<?php

namespace App\Controllers;

use Config\Services;

class Purchase extends BaseController
{
    //purchase
    public function index()
    {
        if (session()->get('level') <> 2) {
            return redirect()->to('/dashboard');
        }
        $data = [
            'title' => 'Purchase'
        ];
        return view('auth/purchase/index', $data);
    }

    public function getdata()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'List Purchase',
                'list' => $this->purchase->list(),
            ];
            $msg = [
                'data' => view('auth/purchase/list', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function buatFaktur()
    {
        $tgl = date('Y-m-d');
        $query = $this->db->query("SELECT MAX(beli_faktur) AS nofaktur FROM purchase WHERE DATE_FORMAT(beli_date, '%Y-%m-%d') = '$tgl'");
        $hasil = $query->getRowArray();
        $data = $hasil['nofaktur'];
        //nomor urut tanggal+4 string contoh J080520220001
        $lastNoUrut = substr($data, -4);

        //nomor urut ditambah 1
        $nextNoUrut = intval($lastNoUrut) + 1;
        //membuat format nomor transaksi berikutnya
        $fakturPurchase = 'P' . date('dmy', strtotime($tgl)) . sprintf('%04s', $nextNoUrut);

        return $fakturPurchase;
    }

    public function formtambah()
    {
        $data = [
            'title' => 'Purchase',
            'nofaktur' => $this->buatFaktur()
        ];
        return view('auth/purchase/tambah', $data);
    }

    //temp purchase stock in

    public function viewDataProduk()
    {

        if ($this->request->isAJAX()) {
            $keyword = $this->request->getPost('keyword');
            $data = [
                'title' => 'List Sparepart',
                'keyword' => $keyword
            ];
            $msg = [
                'data' => view('auth/purchase/viewproduk', $data)
            ];
            echo json_encode($msg);
        }
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
                    $row[] = number_format($list->harga_beli, 0, ',', '.',);
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
            $queryTemp = $this->purchasetemp;
            $dataTemp = $queryTemp->dataDetail($nofaktur);

            $data = [
                'datadetail' => $dataTemp->get()
            ];

            $msg = [
                'data' => view('auth/purchase/viewdetail', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function tempPurchase()
    {
        if ($this->request->isAJAX()) {
            $kodebarcode = $this->request->getPost('kodebarcode');
            $qty = $this->request->getPost('qty');
            $nofaktur = $this->request->getPost('nofaktur');

            $cekData = $this->sparepart->tempPurchase($kodebarcode)->get();

            $totalData = $cekData->getNumRows();

            if ($totalData > 1) {
                $msg = [
                    'totaldata' => 'banyak'
                ];
            } else {
                //insert temp purchase
                $tblTempPurchase = $this->db->table('purchase_temp');
                $rowProduk = $cekData->getRowArray();
                $insertData = [
                    'det_belifaktur' => $nofaktur,
                    'det_belikodebarcode' => $rowProduk['kodebarcode'],
                    'det_hargabeli' => $rowProduk['harga_beli'],
                    'det_beliqty' => $qty,
                    'det_belitotal' => floatval($rowProduk['harga_beli']) * $qty
                ];
                $tblTempPurchase->insert($insertData);

                $msg = [
                    'sukses' => 'berhasil'
                ];
            }
            echo json_encode($msg);
        }
    }

    public function hapusItem()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');
            $tblTempPurchase = $this->db->table('purchase_temp');
            $queryHapus = $tblTempPurchase->delete(['purchase_det' => $id]);

            if ($queryHapus) {
                $msg = [
                    'sukses' => 'Data Purchase Berhasil Dihapus'
                ];
            }

            echo json_encode($msg);
        }
    }

    public function batalPurchase()
    {
        if ($this->request->isAJAX()) {
            $tblTempPurchase = $this->purchasetemp;
            $hapusData = $tblTempPurchase->emptyTable();

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
            $tblTempPurchase = $this->purchasetemp;
            $queryTotal = $tblTempPurchase->hitungTotalBayar($nofaktur)
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
            $supplier_id = $this->request->getPost('supplier_id');
            $nama_supplier = $this->request->getPost('nama_supplier');

            $tblTempPurchase = $this->purchasetemp;
            $cekDataTempPurchase = $tblTempPurchase->getWhere(['det_belifaktur' => $nofaktur]);
            $queryTotal = $tblTempPurchase->select('SUM(det_belitotal) as totalbayar')
                ->where('det_belifaktur', $nofaktur)
                ->get();

            $rowTotal = $queryTotal->getRowArray();

            $dataTemp = $tblTempPurchase->dataDetail($nofaktur);

            if ($cekDataTempPurchase->getNumRows() > 0) {
                //Modal Pembayaran
                $data = [
                    'title' => 'Cek Data Purchase',
                    'nofaktur' => $nofaktur,
                    'supplier_id' => $supplier_id,
                    'nama_supplier' => $nama_supplier,
                    'datadetail' => $dataTemp->get(),
                    'totalbayar' => $rowTotal['totalbayar']
                ];

                $msg = [
                    'data' => view('auth/purchase/modalpembayaran', $data)
                ];
            } else {
                $msg = [
                    'error' => 'Maaf Itemnya Belum Ada'
                ];
            }
            echo json_encode($msg);
        }
    }

    public function simpanPurchase()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('nofaktur');
            $supplier_id = $this->request->getPost('supplier_id');

            $total = str_replace(",", "", $this->request->getPost('total'));

            //tabel
            $tblPurchase = $this->db->table('purchase')
                ->join('supplier', 'supplier.supplier_id = purchase.supplier_id')
                ->join('purchase_detail', 'purchase_detail.detail_purchase=purchase.detail_purchase');
            $tblPurchaseDetail = $this->db->table('purchase_detail');
            $tblTempPurchase = $this->db->table('purchase_temp');

            //insert table purchase
            $dataInserPurchase = [
                'beli_faktur' => $nofaktur,
                'supplier_Id' => $supplier_id,
                'beli_total' => $total
            ];
            $tblPurchase->insert($dataInserPurchase);

            //insert table purchase detail
            $ambilDataTemp = $tblTempPurchase->getWhere(['det_belifaktur' => $nofaktur]);
            $fieldPurchase = [];
            foreach ($ambilDataTemp->getResultArray() as $row) {
                $fieldPurchase[] = [
                    'det_belifaktur' => $nofaktur,
                    'det_belikodebarcode' => $row['det_belikodebarcode'],
                    'det_hargabeli' => $row['det_hargabeli'],
                    'det_beliqty' => $row['det_beliqty'],
                    'det_belitotal' => $row['det_belitotal']
                ];
            }
            $tblPurchaseDetail->insertBatch($fieldPurchase);

            //hapus temp purchase
            $tblTempPurchase->emptyTable();

            $msg = [
                'sukses' => 'berhasil'
            ];
            echo json_encode($msg);
        }
    }

    public function detailItem()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('beli_faktur');
            $tblDetailPurchase =  $this->purchasedetail;
            $queryDetItem = $tblDetailPurchase->detailItem($nofaktur);
            $data = [
                'title' => 'List Purchase Sparepart',
                'tampildetitem' => $queryDetItem->get()

            ];

            $msg = [
                'data' => view('auth/purchase/detailitem', $data)
            ];

            echo json_encode($msg);
        }
    }

    //Edit data Purchase
    public function edit($faktur)
    {
        $cekFaktur = $this->purchase->cekFaktur($faktur);
        if ($cekFaktur->getNumRows() > 0) {
            $row = $cekFaktur->getRowArray();

            $data = [
                'title' => 'Purchase',
                'nofaktur' => $row['beli_faktur'],
                'tanggal' => $row['beli_date']
            ];
            return view('auth/purchase/edit', $data);
        } else {
            exit('Data tidak ditemukan');
        }
    }
    
    public function dataDetailPurchase()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('nofaktur');
            $queryDetail = $this->purchasedetail;

            $data = [
                'datadet' => $queryDetail->dataDetailPurchase($nofaktur),
            ];
            $totalHargaFaktur = number_format($queryDetail->ambilTotalHarga($nofaktur), 0, ",", ".");
            $msg = [
                'data' => view('auth/purchase/datadetail', $data),
                'totalharga' => $totalHargaFaktur
            ];
            echo json_encode($msg);
        }
    }

    public function editItem()
    {
        if ($this->request->isAJAX()) {
            $iddetail = $this->request->getPost('iddetail');

            $queryDetail = $this->purchasedetail;
            $ambilData = $queryDetail->ambilDetailBerdasarkanID($iddetail);
            $row = $ambilData->getRowArray();
            $data = [
                'kodebarang' => $row['det_belikodebarcode'],
                'nama_sparepart' => $row['nama_sparepart'],
                'stok' => $row['stok'],
                'qty' => $row['det_beliqty']
            ];

            $msg = [
                'sukses' => $data
            ];
            echo json_encode($msg);
        }
    }

    public function detailPurchase()
    {
        if ($this->request->isAJAX()) {
            $nofaktur = $this->request->getPost('nofaktur');
            $kodebarcode = $this->request->getPost('kodebarcode');
            $qty = $this->request->getPost('qty');

            $queryDetail = $this->purchasedetail;
            $queryPurchase = $this->purchase;
            $cekData = $this->sparepart->detailPurchase($kodebarcode)->get();

            $totalData = $cekData->getNumRows();
            if ($totalData > 1) {
                $msg = [
                    'totaldata' => 'banyak'
                ];
            } else {
                //insert detail purchase
                $tblDetailPurchase = $this->db->table('purchase_detail');
                $rowProduk = $cekData->getRowArray();

                $tblDetailPurchase->insert([
                    'det_belifaktur' => $nofaktur,
                    'det_belikodebarcode' => $rowProduk['kodebarcode'],
                    'det_hargabeli' => $rowProduk['harga_beli'],
                    'det_beliqty' => $qty,
                    'det_belitotal' => floatval($rowProduk['harga_beli']) * $qty
                ]);

                $ambilTotalHarga = $queryDetail->ambilTotalHarga($nofaktur);
                $queryPurchase->update($nofaktur, [
                    'beli_total' => $ambilTotalHarga
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

            $queryDetail = $this->purchasedetail;
            $queryPurchase = $this->purchase;

            $rowData = $queryDetail->find($iddetail);
            $nofaktur = $rowData['det_belifaktur'];
            $hargabeli = $rowData['det_hargabeli'];

            $queryDetail->update($iddetail, [
                'det_beliqty' => $qty,
                'det_belitotal' => floatval($hargabeli) * $qty
            ]);

            $ambilTotalHarga = $queryDetail->ambilTotalHarga($nofaktur);
            $queryPurchase->update($nofaktur, [
                'beli_total' => $ambilTotalHarga
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

            $queryPurchase = $this->purchase;
            $queryDetail = $this->purchasedetail;
            $tblDetailPurchase = $this->db->table('purchase_detail');
            $queryHapus = $tblDetailPurchase->delete(['detail_purchase' => $id]);

            if ($queryHapus) {

                $ambilTotalHarga = $queryDetail->ambilTotalHarga($nofaktur);
                $queryPurchase->update($nofaktur, [
                    'beli_total' => $ambilTotalHarga
                ]);
                $msg = [
                    'sukses' => 'Data Purchase Berhasil Dihapus'
                ];
            }

            echo json_encode($msg);
        }
    }

    //Hapus data purchase
    public function hapus()
    {
        if ($this->request->isAJAX()) {
            $faktur = $this->request->getPost('faktur');
            $db = \Config\Database::connect();
            $db->table('purchase_detail')->delete(['det_belifaktur' => $faktur]);
            $this->purchase->delete($faktur);

            $msg = [
                'sukses' => 'Data Transaksi '
            ];

            echo json_encode($msg);
        }
    }
}
