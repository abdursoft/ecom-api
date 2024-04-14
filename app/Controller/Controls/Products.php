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
use App\Model\Project\Products as ProjectProducts;
use Core\Files\Upload;
use System\Handle;
use System\Validation\Input;

class Products extends Controller{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get products
     * @param token token number of the product
     * will return a product info with a valid token
     */
    public function get($param){
        if($param['token'] != ''){
            try {
                $single = ProjectProducts::model()->select()->where([
                    'token' => $param['token']
                ])->last();
                $this->response([
                    'status' => 1,
                    'message' => "Product retrieved",
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
                $products = ProjectProducts::model()->select()->where([
                    'token' => '!null'
                ])->all();
                $this->response([
                    'status' => 1,
                    'message' => "all products",
                    'data' => $products,
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
     * Category creation
     * @param name name of the product
     * @param brand name of the product brand
     * @param category Name of the category
     * @param sub_category sub category name
     * @param types type of the player physical|digital
     * @param packing type of the product package
     * @param quantity products quantity
     * @param is_stock products stock status 1|0
     * @param unit_price single unit price of the product
     * @param short_description product short_description
     * @param full_description prodcut full description
     * @param expired_at Prodcut expiration date
     * @param logo logo of the category
     * @param feature_image feature image of the product
     */
    public function create($param){
        $input = new Input();
        $input->field('name')->unique('products','name')->max(150)->required();
        $input->field('brand')->exist('brand','brand')->required();
        $input->field('category')->exist('category','category')->required();
        $input->field('logo')->mime('png')->maxSize(5)->required();
        $input->field('types')->required();
        $input->field('packing')->required();
        $input->field('quantity')->number()->required();
        $input->field('is_stock')->required();
        $input->field('package_price')->required();
        $input->field('unit_price')->required();
        $input->field('short_descrption')->max(250)->required();
        $input->field('full_description')->required();
        $input->field('expired_at')->required();

        $errors = $input->validation();

        if(!is_array($errors)){
            try {
                $location = "products";
                $file = Upload::putImage($_FILES['logo'],$location,8);
                if(isset($_FILES['feature_image']) && !empty($_FILES['feature_image'])){
                    $feature = Upload::putManyImage('feature_image',"feature",8);
                }
                ProjectProducts::model()->create([
                    "token" => $this->generateRandomString(32),
                    "category" => strtolower($param['category']),
                    "name"  => $param['name'],
                    "brand"  => $param['brand'],
                    "types"  => $param['types'],
                    "packing"  => $param['packing'],
                    "quantity"  => $param['quantity'],
                    "is_stock"  => $param['is_stock'],
                    "package_price"  => $param['package_price'],
                    "unit_price"  => $param['unit_price'],
                    "short_descrption"  => $param['short_descrption'],
                    "full_description"  => $param['full_description'],
                    "logo" => $file,
                    "logo_path" => BASE_URL.trim($file,'/'),
                    "feature_image" => $feature ?? '',
                    "expired_at" => $param['expired_at']
                ]);
                $this->response([
                    'status' => 1,
                    'message' => "Prodcut successfully created"
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
     *  Product Update
     * @param token product token value
     * @param name name of the product
     * @param brand name of the product brand
     * @param category Name of the category
     * @param sub_category sub category name
     * @param types type of the player physical|digital
     * @param packing type of the product package
     * @param quantity products quantity
     * @param is_stock products stock status 1|0
     * @param unit_price single unit price of the product
     * @param short_description product short_description
     * @param full_description prodcut full description
     * @param expired_at Prodcut expiration date
     * @param logo logo of the category
     * @param feature_image feature image of the product
     */
    public function update($param){        
        $input = new Input();
        $input->field('token')->required();
        $input->field('name')->required();
        $input->field('brand')->exist('brand','brand')->required();
        $input->field('category')->exist('category','category')->required();
        $input->field('logo')->mime('png')->maxSize(5)->required();
        $input->field('types')->required();
        $input->field('packing')->required();
        $input->field('quantity')->number()->required();
        $input->field('is_stock')->required();
        $input->field('package_price')->required();
        $input->field('unit_price')->required();
        $input->field('short_descrption')->max(250)->required();
        $input->field('full_description')->required();
        $input->field('expired_at')->required();
        if(isset($_FILES['logo']) && !empty($_FILES['logo'])){
            $input->field('logo')->mime('png')->maxSize(5);
        }
        if(isset($_FILES['feature_image']) && !empty($_FILES['feature_image'])){
            $input->field('feature_image')->mime('png')->maxSize(5);
        }


        $errors = $input->validation();

        if(!is_array($errors)){
            $exist = ProjectProducts::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($exist){
                $single = ProjectProducts::model()->select()->where([
                    'token' => "!".$param['token'],
                    'name' => $param['name'],
                ])->first();
                if($single){
                    $this->response([
                        'status' => 0,
                        'message' => 'Product name already exist'
                    ],200);
                }else{
                    try {
                        $is_file = false;
                        $location = "products";
                        if(isset($_FILES['logo']) && !empty($_FILES['logo'])){
                            $is_file = true;
                            $file = Upload::putImage($_FILES['logo'],$location,8);
                        }else{
                            $file = $exist->logo;
                        }
                        ProjectProducts::model()->where([
                            'token' => $param['token']
                        ])->update([
                            "category" => strtolower($param['category']),
                            "name"  => $param['name'],
                            "brand"  => $param['brand'],
                            "types"  => $param['types'],
                            "packing"  => $param['packing'],
                            "quantity"  => $param['quantity'],
                            "is_stock"  => $param['is_stock'],
                            "package_price"  => $param['package_price'],
                            "unit_price"  => $param['unit_price'],
                            "short_descrption"  => $param['short_descrption'],
                            "full_description"  => $param['full_description'],
                            "logo" => $file,
                            "logo_path" => BASE_URL.trim($file,'/'),
                            "feature_image" => $feature ?? '',
                            "expired_at" => $param['expired_at']
                        ]);
                        $is_file ? Handle::unlinkFile($exist->logo): true;
                        $this->response([
                            'status' => 1,
                            'message' => "Product successfully updated"
                        ],200);
                    } catch (\Throwable $th) {
                        $this->response([
                            'status' => 0,
                            'message' => $th->getMessage(),
                            'data' => $param
                        ],200);
                    }
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
     * Delete Category
     * @param token token number of the category
     * will delete the category if token was valid
     */
    public function delete($param){
        $input = new Input();
        $input->field('token')->required();
        $error = $input->validation();

        if(!is_array($error)){
            $single = ProjectProducts::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($single){
                try {
                    ProjectProducts::model()->where([
                        'token' => $param['token']
                    ])->delete();
                    Handle::unlinkFile($single->logo);
                    $this->response([
                        'status' => 1,
                        'message' => "Product successfully deleted"
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
                    'message' => "Invalid product token"
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