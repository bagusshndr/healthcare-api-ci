<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Struktur_Organisasi extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->token = 'Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6ImZhZWE3Y2Q2YWFhYjM1YmIyYmE4MjE3ZTgyNWNkODE5I';
        $this->load->model('MSudi');
    }
    public function index_get()
    {
        if (isset($_GET['id_structure'])) {
            $query = $this->MSudi->GetDataWhere('structure', 'id_structure', $_GET['id_structure'])->result();
            $this->response([
                'status' => 200,
                'message' => 'success',
                'data' => $query
            ], 200);
        } else {
            $query = $this->MSudi->GetData('structure');
            $this->response([
                'status' => 200,
                'message' => 'success',
                'data' => $query
            ], 200);
        }
    }

    public function index_post()
    {
        if (isset($_POST['id_structure'])) {
            $result = $this->update();
        } else {
            $header = $this->input->request_headers();
            if (isset($header['Authorization'])) {
                if ($header['Authorization'] == $this->token) {


                    // Check form submit or not
                    if (isset($_POST['image'])) {
                        $data['filenames'] = $_POST['image'];
                        // array_push( $data['filenames'],$_POST['image']);
                    }
                    if (isset($_FILES['files'])) {

                        $data = array();
                        $data['filenames'] = [];
                        // Count total files
                        $countfiles = count($_FILES['files']['name']);

                        // Looping all files
                        for ($i = 0; $i < $countfiles; $i++) {

                            if (!empty($_FILES['files']['name'][$i])) {

                                // Define new $_FILES array - $_FILES['file']
                                $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                                $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                                $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                                $_FILES['file']['size'] = $_FILES['files']['size'][$i];

                                // Set preference
                                $config['upload_path'] = 'uploads/';
                                $config['allowed_types'] = 'jpg|jpeg|png|gif|JPG';
                                // $config['max_size'] = '500000000'; // max_size in kb
                                $config['file_name'] = $_FILES['files']['name'][$i];

                                //Load upload library
                                $this->load->library('upload', $config);

                                try {

                                    // File upload
                                    if ($this->upload->do_upload('file')) {
                                        // Get data about the file
                                        $uploadData = $this->upload->data();
                                        $filename = site_url('uploads/') . $uploadData['file_name'];
                                        $replcate = str_replace("index.php/", "", $filename);
                                        $filename = $replcate;

                                        // Initialize array
                                        array_push($data['filenames'], $filename);
                                    }
                                }

                                //catch exception
                                catch (Exception $e) {
                                    echo 'Message: ' . $e->getMessage();
                                }
                            }
                        }
                    }
                    if (isset($_FILES['files'])) {

                        $image = json_encode($data['filenames']);
                        $replcate = str_replace("\/", "/", $image);
                        $data['filenames'] = $replcate;
                    } else if (isset($_POST['image'])) {
                    } else {
                        $data['filenames'] = "[]";
                    }
                    $insert = array(
                        'nama_structure' => $this->input->post('nama_structure'),
                        'jabatan_structure' => $this->input->post('jabatan_structure'),
                        'gambar_structure' => $data['filenames'],
                    );

                    $query = $this->MSudi->AddData('structure', $insert);
                    $this->response([
                        'status' => 200,
                        'message' => 'success',
                        'data' => $query
                    ], 200);
                } else {
                    $this->response([
                        'status' => 401,
                        'message' => 'UnAuthorize',
                        'data' => []
                    ], 401);
                }
            } else {
                $this->response([
                    'status' => 401,
                    'message' => 'UnAuthorize',
                    'data' => []
                ], 401);
            }
        }
    }

    public function update()
    {
        $header = $this->input->request_headers();
        if (isset($header['Authorization'])) {
            if ($header['Authorization'] == $this->token) {

                // Check form submit or not
                if (isset($_FILES['files'])) {

                    $data = array();
                    $data['filenames'] = [];
                    // Count total files
                    $countfiles = count($_FILES['files']['name']);

                    // Looping all files
                    for ($i = 0; $i < $countfiles; $i++) {

                        if (!empty($_FILES['files']['name'][$i])) {

                            // Define new $_FILES array - $_FILES['file']
                            $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                            $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                            $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                            $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                            $_FILES['file']['size'] = $_FILES['files']['size'][$i];

                            // Set preference
                            $config['upload_path'] = 'uploads/';
                            $config['allowed_types'] = 'jpg|jpeg|png|gif';
                            // $config['max_size'] = '500000000'; // max_size in kb
                            $config['file_name'] = $_FILES['files']['name'][$i];

                            //Load upload library
                            $this->load->library('upload', $config);

                            // File upload
                            if ($this->upload->do_upload('file')) {
                                // Get data about the file
                                $uploadData = $this->upload->data();
                                $filename = site_url('uploads/') . $uploadData['file_name'];
                                $replcate = str_replace("index.php/", "", $filename);
                                $filename = $replcate;

                                // Initialize array
                                array_push($data['filenames'], $filename);
                            }
                        }
                    }
                }
                if (isset($_FILES['files'])) {

                    $image = json_encode($data['filenames']);
                    $replcate = str_replace("\/", "/", $image);
                    $data['filenames'] = $replcate;
                } else {
                    $data['filenames'] = "[]";
                }

                $update = array(
                    'id_structure' => $this->input->post('id_structure'),
                    'nama_structure' => $this->input->post('nama_structure'),
                    'jabatan_structure' => $this->input->post('jabatan_structure'),
                    'gambar_structure' => $data['filenames'],
                );

                $query = $this->MSudi->UpdateData('structure', 'id_structure', $update['id_structure'], $update);
                $this->response([
                    'status' => 200,
                    'message' => 'success',
                    'data' => $query
                ], 200);
            } else {
                $this->response([
                    'status' => 401,
                    'message' => 'UnAuthorize',
                    'data' => []
                ], 401);
            }
        } else {
            $this->response([
                'status' => 401,
                'message' => 'UnAuthorize',
                'data' => []
            ], 401);
        }
    }
    public function index_delete()
    {
        $header = $this->input->request_headers();
        if (isset($header['Authorization'])) {
            if ($header['Authorization'] == $this->token) {
                $delete = array(
                    'id_structure' => $_GET['id_structure']
                );

                $query = $this->MSudi->DeleteData('structure', 'id_structure', $delete['id_structure'], $delete);
                $this->response([
                    'status' => 200,
                    'message' => 'success',
                    'data' => $query
                ], 200);
            } else {
                $this->response([
                    'status' => 401,
                    'message' => 'UnAuthorize',
                    'data' => []
                ], 401);
            }
        } else {
            $this->response([
                'status' => 401,
                'message' => 'UnAuthorize',
                'data' => []
            ], 401);
        }
    }
}
