<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_sales extends CI_Controller {
    private $dataAdmin;

    function __construct() {
        parent::__construct();

        if(!$this->session->auth) {
            redirect(base_url("auth/login"));
        }

        $this->load->model("user_model");
        $this->load->model("datatables");
        $this->load->model("transaction_model");

        $this->dataAdmin = $this->user_model->get(["id" => $this->session->auth['id']])->row();
    }


	public function index()
	{

        $push = [
            "pageTitle" => "Riwayat Service",
            "dataAdmin" => $this->dataAdmin 
        ];

		$this->load->view('header',$push);
		$this->load->view('service_sales',$push);
		$this->load->view('footer',$push);
    }

    public function print($id = 0) {
        $query = $this->transaction_model->get($id);
        if($query->num_rows() > 0) {
            $push["fetch"] = $query->row();
            $push["details"] = $this->transaction_model->get_details($id)->result();

            $title = "Invoice";

            $this->load->library("pdf");

            $this->pdf->setPaper('A4', 'portrait');
            $this->pdf->filename = $title;

            $this->pdf->load_view("service_sales_pdf",$push);
        }
    }
    
    public function json() {
        $this->datatables->setTable("transactions");
        $this->datatables->setWhere("type","service");
        $this->datatables->setColumn([
            '<index>',
            '[reformat_date=<get-date>]',
            '<get-customer>',
            '<get-plat>',
            '[rupiah=<get-total>]',
            '<get-kondisi>',
            '<div class="text-center">
                <button type="button" class="btn btn-sm btn-success btn-edit" data-kondisi="<get-kondisi>" data-total="<get-total>"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-sm btn-warning btn-view" data-id="<get-id>" data-total="<get-total>"><i class="fa fa-eye"></i></button>
                <a href="[base_url=service_sales/print/<get-id>]" class="btn btn-sm btn-primary"><i class="fa fa-print"></i></a>
            </div>'
        ]);
        $this->datatables->setOrdering(["id","date","customer","plat","total","kondisi",NULL]);
        $this->datatables->setSearchField("date");
        $this->datatables->generate();
    }

    
    function insert() {
        $this->process();
    }
    

    function update($id) {
        $this->process("edit",$id);
    }

    private function process($action = "add",$id = 0) {
        $name = $this->input->post("name");
        $plat = $this->input->post("plat");
    

        if(!$name OR !$plat) {
            $response['status'] = FALSE;
            $response['msg'] = "Periksa kembali data yang anda masukkan";
        } else {
            $insertData = [
                "id" => NULL,
                "name" => $name,
                "plat" => $plat,
                "type" => "service_sales",

            ];

            
            $response['status'] = TRUE;

            if($action == "add") {
                $response['msg'] = "Data berhasil ditambahkan";
                $this->product_model->post($insertData);
            } else {
                unset($insertData['id']);
                unset($insertData['type']);
                unset($insertData['stock']);

                $response['msg'] = "Data berhasil diedit";
                $this->product_model->put($id,$insertData);
            }

        }

        echo json_encode($response);
    }

    private function proces($action = "edit",$id = 0) {
        $kondisi = $this->input->post("kondisi");

        if(!$kondisi) {
            $response['status'] = FALSE;
            $response['msg'] = "Periksa kembali data yang anda masukkan";
        } else {
            $insertData = [
                "id" => NULL,
                "kondisi" => $kondisi,
                "type" => "service_sales",

            ];
            $response['status'] = TRUE;

            if($action == "edit") {
                $response['msg'] = "Data berhasil ditambahkan";
                $this->product_model->post($insertData);
            } else {
                unset($insertData['id']);
                unset($insertData['kondisi']);
                unset($insertData['type']);


                $response['msg'] = "Data berhasil diedit";
                $this->product_model->put($id,$insertData);
        }
        echo json_encode($response);
    }
    }

    public function json_details($id = 0) {
        $this->datatables->setTable("details");
        $this->datatables->setWhere("transaction_id",$id);
        $this->datatables->setColumn([
            '<index>',
            '<get-name>',
            '[rupiah=<get-price>]',
            '<get-qty>',
            '[math=<get-qty> * <get-price>]'
        ]);
        $this->datatables->setOrdering(["id","name","price", NULL]);
        $this->datatables->setSearchField("name");
        $this->datatables->generate();
    }
    
}
