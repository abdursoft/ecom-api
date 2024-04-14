<?php
/**
 * ABS MVC Framework
 *
 * @created      2023
 * @version      1.0.1
 * @author       abdursoft <support@abdursoft.com>
 * @copyright    2024 abdursoft
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
*/

namespace App\Controller\Controls;

use App\Controller\Controller;
use App\Model\Project\Suppliers as ProjectSuppliers;
use Core\Files\Upload;
use System\Handle;
use System\Validation\Input;

class Suppliers extends Controller{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get Supplier
     * @param token token number of the supplier
     * will return a supplier info with a valid token
     */
    public function get($param){
        if($param['token'] != ''){
            try {
                $single = ProjectSuppliers::model()->select()->where([
                    'token' => $param['token']
                ])->last();
                $this->response([
                    'status' => 1,
                    'message' => "Sub category retrieved",
                    'data' => $single
                ],200);
            } catch (\Throwable $th) {
                $this->response([
                    'status' => 0,
                    'message' =>$th->getMessage(),
                ],200);
            }            
        }else{
            try {
                $categories = ProjectSuppliers::model()->select()->where([
                    'token' => '!null'
                ])->all();
                $this->response([
                    'status' => 1,
                    'message' => "all sub categories",
                    'data' => $categories,
                    "method" => $_SERVER['REQUEST_METHOD']
                ],200);
            } catch (\Throwable $th) {
                $this->response([
                    'status' => 0,
                    'message' => $th->getMessage()
                ],200);
            }            
        }
    }

    /**
     * Supplier creation
     * @param brand Name of the brand/Company
     * @param name name of the suppliers
     * @param profile image/photo of the suppliers
     * @param mobile Contact number of the suppliers
     * @param email Mail address of the suppliers
     * @param address mailing address of the suppliers
     * @param status Suppliers current presentence status
     */
    public function create($param){
        $input = new Input();
        $input->field('name')->required();
        $input->field('brand')->exist('brand','brand');
        $input->field('profile')->mime('png')->maxSize(5)->required();
        $input->field('mobile')->min(8)->max(14)->required();
        $input->field('email')->email()->required();
        $input->field('status')->required();

        $errors = $input->validation();

        if(!is_array($errors)){
            try {
                $location = "sub_category";
                $file = Upload::putImage($_FILES['profile'],$location,8);
                ProjectSuppliers::model()->create([
                    "token" => $this->generateRandomString(32),
                    "brand" => $param['brand'],
                    "name" => $param['name'],
                    "mobile" => $param['mobile'],
                    "email" => $param['email'],
                    "is_active"  => $param['status'],
                    "address" => $param['address'] ?? '',
                    "profile" => $file,
                    "profile_path" => BASE_URL.trim($file,'/')
                ]);
                $this->response([
                    'status' => 1,
                    'message' => "Supplier successfully created"
                ],200);
            } catch (\Throwable $th) {
                $this->response([
                    'status' => 0,
                    'message' => $th->getMessage(),
                    'data' => $param
                ],200);
            }
        }else{
            $this->response([
                'status' => 0,
                'message' => $errors
            ],200);
        }

    }


    /**
     *  Supplier Update
     * @param token supplier token value
     * @param brand Name of the brand/Company
     * @param name name of the suppliers
     * @param profile image/photo of the suppliers
     * @param mobile Contact number of the suppliers
     * @param email Mail address of the suppliers
     * @param address mailing address of the suppliers
     * @param status Suppliers current presentence status
     */
    public function update($param){        
        $input = new Input();
        $input->field('token')->required();
        $input->field('name')->required();
        $input->field('brand')->exist('brand','brand');
        $input->field('mobile')->min(8)->max(14)->required();
        $input->field('email')->email()->required();
        $input->field('status')->required();
        if(isset($_FILES['profile']) && !empty($_FILES['profile'])){
            $input->field('profile')->mime('png')->maxSize(5);
        }


        $errors = $input->validation();

        if(!is_array($errors)){
            $exist = ProjectSuppliers::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($exist){
                try {
                    $is_file = false;
                    $location = "sub_category";
                    if(isset($_FILES['profile']) && !empty($_FILES['profile'])){
                        $is_file = true;
                        $file = Upload::putImage($_FILES['profile'],$location,8);
                    }else{
                        $file = $exist->profile;
                    }
                    ProjectSuppliers::model()->where([
                        'token' => $param['token']
                    ])->update([
                        "brand" => $param['brand'],
                        "name" => $param['name'],
                        "mobile" => $param['mobile'],
                        "email" => $param['email'],
                        "is_active"  => $param['status'],
                        "address" => $param['address'] ?? '',
                        "profile" => $file,
                        "profile_path" => BASE_URL.trim($file,'/')
                    ]);
                    $is_file ? Handle::unlinkFile($exist->profile): true;
                    $this->response([
                        'status' => 1,
                        'message' => "Supplier successfully updated"
                    ],200);
                } catch (\Throwable $th) {
                    $this->response([
                        'status' => 0,
                        'message' => $th->getMessage(),
                        'data' => $param
                    ],200);
                }
            }else{
                $this->response([
                    'status' => 0,
                    'message' => 'Invalid token ID'
                ],200);
            }
        }else{
            $this->response([
                'status' => 0,
                'message' => $errors
            ],200);
        }

    }


    /**
     * Delete supplier
     * @param token token number of the supplier
     * will delete the supplier if token was valid
     */
    public function delete($param){
        $input = new Input();
        $input->field('token')->required();
        $error = $input->validation();

        if(!is_array($error)){
            $single = ProjectSuppliers::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($single){
                try {
                    ProjectSuppliers::model()->where([
                        'token' => $param['token']
                    ])->delete();
                    Handle::unlinkFile($single->profile);
                    $this->response([
                        'status' => 1,
                        'message' => "Supplier successfully deleted"
                    ],200);
                } catch (\Throwable $th) {
                    $this->response([
                        'status' => 0,
                        'message' => $th->getMessage()
                    ],200);
                }

            }else{
                $this->response([
                    'status' => 0,
                    'message' => "Invalid supplier token"
                ],200);
            }
        }else{
            $this->response([
                'status' => 0,
                'message' => $error
            ],200);
        }
    }
}