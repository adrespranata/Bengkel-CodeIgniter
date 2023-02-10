<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan extends BaseController
{
    public function index()
    {
        if (session()->get('level') <> 2) {
            return redirect()->to('/dashboard');
        }
        $data = [
            'title' => 'Laporan Purchase'
        ];
        return view('auth/laporanpurchase/index', $data);
    }

    public function cetakpurchase()
    {
        $btnCetak = $this->request->getPost('btnCetak');
        $btnExport = $this->request->getPost('btnExport');
        $tglawal = $this->request->getPost('tglawal');
        $tglakhir = $this->request->getPost('tglakhir');

        $dataLaporan = $this->purchase
            ->laporanPerPeriode($tglawal, $tglakhir);
        if (isset($btnCetak)) {
            $data = [
                'datalaporan' => $dataLaporan,
                'tglawal' => $tglawal,
                'tglakhir' => $tglakhir
            ];
            return view('auth/laporanpurchase/cetakPurchase', $data);
        }

    }

    public function tampilGrafikPurchase()
    {
        $bulan = $this->request->getPost('bulan');

        $query = $this->db->query("SELECT `beli_date` AS tgl, `beli_total` FROM `purchase` WHERE DATE_FORMAT(`beli_date`, '%Y-%m') = '$bulan' ORDER BY `beli_date` ASC")
            ->getResult();
        $data = [
            'grafik' => $query
        ];
        $msg = [
            'data' => view('auth/laporanpurchase/grafikPurchase', $data)
        ];

        echo json_encode($msg);
    }

    //laporan sale
    public function laporansale()
    {
        if (session()->get('level') <> 2) {
            return redirect()->to('/dashboard');
        }
        $data = [
            'title' => 'Laporan Sale'
        ];
        return view('auth/laporansale/index', $data);
    }

    public function cetakSale()
    {
        $btnCetak = $this->request->getPost('btnCetak');
        $btnExport = $this->request->getPost('btnExport');
        $tglawal = $this->request->getPost('tglawal');
        $tglakhir = $this->request->getPost('tglakhir');

        $dataLaporan = $this->sale
            ->laporanPerPeriode($tglawal, $tglakhir);
        if (isset($btnCetak)) {
            $data = [
                'datalaporan' => $dataLaporan,
                'tglawal' => $tglawal,
                'tglakhir' => $tglakhir
            ];
            return view('auth/laporansale/cetakSale', $data);
        }

    }

    public function tampilGrafikSale()
    {
        $bulan = $this->request->getPost('bulan');

        $query = $this->db->query("SELECT `jual_date` AS tgl, `jual_total` FROM `sale` WHERE DATE_FORMAT(`jual_date`, '%Y-%m') = '$bulan' ORDER BY `jual_date` ASC")
            ->getResult();
        $data = [
            'grafik' => $query
        ];

        $msg = [
            'data' => view('auth/laporansale/grafikSale', $data)
        ];

        echo json_encode($msg);
    }
}
