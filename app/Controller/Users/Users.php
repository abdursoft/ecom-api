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
use App\Model\Project\Brand as ProjectBrand;
use Core\Files\Upload;
use System\Handle;
use System\Validation\Input;

class Brand extends Controller{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get Brand
     * @param token token number of the brand
     * will return a brand info with a valid token
     */
    public function get($param){
        if($param['token'] != ''){
            try {
                $single = ProjectBrand::model()->select()->where([
                    'token' => $param['token']
                ])->last();
                $this->response([
                    'status' => 1,
                    'message' => "all brands",
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
                $brands = ProjectBrand::model()->select()->where([
                    'token' => '!null'
                ])->all();
                $this->response([
                    'status' => 1,
                    'message' => "all brands",
                    'data' => $brands
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
     * Brand creation
     * @param brand Name of the brand
     * @param logo logo of the brand
     * @param website website of the brand company
     * @param chairman Chairman of the company
     * @param country Origin of the company
     * @param short_description Short description about the brand
     * @param full_description Full description about the brand
     * @param address mail address of the brand company
     * @param helpline Helpline/Hotline of the brand
     * @param rank Rank of the brand
     */
    public function create($param){
        $input = new Input();
        $input->field('brand')->required();
        $input->field('logo')->mime('png')->maxSize(5)->required();
        $input->field('website')->url();
        $input->field('country')->required();
        $input->field('address')->required();
        $input->field('helpline')->required();

        $errors = $input->validation();

        if(!is_array($errors)){
            $single = ProjectBrand::model()->select()->where([
                'brand' => strtolower($param['brand'])
            ])->first();
            if($single){
                $this->response([
                    'status' => 0,
                    'message' => 'Brand name already exist'
                ],200);
            }else{
                try {
                    $location = "brand";
                    $file = Upload::putImage($_FILES['logo'],$location,8);
                    ProjectBrand::model()->create([
                        "token" => $this->generateRandomString(32),
                        "brand" => strtolower($param['brand']),
                        "logo"  => $file,
                        "logo_path" => BASE_URL.trim($file,'/'),
                        "website" => $param['website'],
                        "country" => $param['country'],
                        "address" => $param['address'],
                        "helpline" => $param['helpline'],
                        "ranks" => $param['rank'] ?? 0,
                        "chairman"   => $param['chairman'] ?? "",
                        "short_description" => $param['short_description'] ?? "",
                        "full_description" => $param['full_description'] ?? ""
                    ]);
                    $this->response([
                        'status' => 1,
                        'message' => "Brand successfully created"
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
                'message' => $errors
            ],200);
        }

    }


    /**
     * Brand Update
     * @param token Brand token value
     * @param brand Name of the brand
     * @param logo logo of the brand
     * @param website website of the brand company
     * @param chairman Chairman of the company
     * @param country Origin of the company
     * @param short_description Short description about the brand
     * @param full_description Full description about the brand
     * @param address mail address of the brand company
     * @param helpline Helpline/Hotline of the brand
     * @param rank Rank of the brand
     */
    public function update($param){        
        $input = new Input();
        $input->field('token')->required();
        $input->field('brand')->required();
        $input->field('website')->url();
        $input->field('country')->required();
        $input->field('address')->required();
        $input->field('helpline')->required();
        if(isset($_FILES['logo']) && !empty($_FILES['logo'])){
            $input->field('logo')->mime('png')->maxSize(5);
        }


        $errors = $input->validation();

        if(!is_array($errors)){
            $exist = ProjectBrand::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($exist){
                $single = ProjectBrand::model()->select()->where([
                    'token' => "!".$param['token'],
                    'brand' => strtolower($param['brand']),
                ])->first();
                if($single){
                    $this->response([
                        'status' => 0,
                        'message' => 'Brand name already exist'
                    ],200);
                }else{
                    try {
                        $is_file = false;
                        $location = "brand";
                        if(isset($_FILES['logo']) && !empty($_FILES['logo'])){
                            $is_file = true;
                            $file = Upload::putImage($_FILES['logo'],$location,8);
                        }else{
                            $file = $exist->logo;
                        }
                        ProjectBrand::model()->where([
                            'token' => $param['token']
                        ])->update([
                            "brand" => strtolower($param['brand']),
                            "logo"  => $file,
                            "logo_path" => BASE_URL.trim($file,'/'),
                            "website" => $param['website'],
                            "country" => $param['country'],
                            "address" => $param['address'],
                            "helpline" => $param['helpline'],
                            "ranks" => $param['rank'] ?? 0,
                            "chairman"   => $param['chairman'] ?? "",
                            "short_description" => $param['short_description'] ?? "",
                            "full_description" => $param['full_description'] ?? ""
                        ]);
                        $is_file ? Handle::unlinkFile($exist->logo): true;
                        $this->response([
                            'status' => 1,
                            'message' => "Brand successfully updated"
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
     * Delete Brands
     * @param token token number of the brand
     * will delete the brand if token was valid
     */
    public function delete($param){
        $input = new Input();
        $input->field('token')->required();
        $error = $input->validation();

        if(!is_array($error)){
            $singel = ProjectBrand::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($singel){
                try {
                    ProjectBrand::model()->where([
                        'token' => $param['token']
                    ])->delete();
                    Handle::unlinkFile($singel->logo);
                    $this->response([
                        'status' => 1,
                        'message' => "Brand successfully deleted"
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
                    'message' => "Invalid brand token"
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