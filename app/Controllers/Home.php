<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		$staf = $this->staf->selectCount('staf_id')->first();
		$konfigurasi = $this->konfigurasi->orderBy('konfigurasi_id')->first();
		$list_staf = $this->staf->orderBy('staf_id')->get()->getResultArray();
		$data = [
			'title' => 'Selamat Datang!',
			'staf' => $staf,
			'konfigurasi' => $konfigurasi,
			'list_staf' => $list_staf,
		];
		return view('front/layout/menu', $data);
	}
}
