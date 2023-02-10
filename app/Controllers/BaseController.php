<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


use App\Models\Modelkonfigurasi;
use App\Models\Modeluser;

use App\Models\Modelstaf;
use App\Models\Modelsupplier;
use App\Models\Modelpelanggan;

use App\Models\Modelsparepart;

use App\Models\Modelpurchase;
use App\Models\Modelpurchasetemp;
use App\Models\Modelpurchasedetail;

use App\Models\Modelsale;
use App\Models\Modelsaletemp;
use App\Models\Modelsaledetail;

use App\Models\Modeldataproduk;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
	/**
	 * Instance of the main Request object.
	 *
	 * @var CLIRequest|IncomingRequest
	 */
	protected $request;

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['form', 'url', 'Tgl_indo'];

	/**
	 * Constructor.
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		// Preload any models, libraries, etc, here.

		// E.g.:
		$this->session = \Config\Services::session();
		$this->db = db_connect();
		//konfigurasi
		$this->konfigurasi = new Modelkonfigurasi;
		//user,staf & supplier
		$this->staf = new Modelstaf;
		$this->user = new Modeluser;
		$this->supplier = new Modelsupplier($request);
		$this->pelanggan = new Modelpelanggan($request);
		//produk
		$this->sparepart = new Modelsparepart();
		//purchase
		$this->purchase = new Modelpurchase($request);
		$this->purchasetemp = new Modelpurchasetemp();
		$this->purchasedetail = new Modelpurchasedetail();
		//sale
		$this->sale = new Modelsale($request);
		$this->saletemp = new Modelsaletemp();
		$this->saledetail = new Modelsaledetail();

		//sale & produk
		$this->produk = new Modeldataproduk($request);
	}
}
