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
use App\Model\Project\Sub_category as ProjectSub_category;
use Core\Files\Upload;
use System\Handle;
use System\Validation\Input;

class Sub_category extends Controller{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get sub-category
     * @param token token number of the sub-category
     * will return a sub-category info with a valid token
     */
    public function get($param){
        if($param['token'] != ''){
            try {
                $single = ProjectSub_category::model()->select()->where([
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
                $categories = ProjectSub_category::model()->select()->where([
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
     * Sub-category creation
     * @param category Name of the sub-category
     * @param logo logo of the sub-category
     * @param status sub-category active status 1|0
     */
    public function create($param){
        $input = new Input();
        $input->field('category')->unique('sub_category','category')->required();
        $input->field('primary_category')->exist('category','category');
        $input->field('logo')->mime('png')->maxSize(5)->required();
        $input->field('status')->required();

        $errors = $input->validation();

        if(!is_array($errors)){
            try {
                $location = "sub_category";
                $file = Upload::putImage($_FILES['logo'],$location,8);
                ProjectSub_category::model()->create([
                    "token" => $this->generateRandomString(32),
                    "primary_category" => $param['primary_category'],
                    "category" => strtolower($param['category']),
                    "is_active"  => $param['status'],
                    "logo" => $file,
                    "logo_path" => BASE_URL.trim($file,'/')
                ]);
                $this->response([
                    'status' => 1,
                    'message' => "Sub category successfully created"
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
     *  Sub-category Update
     * @param token Sub-ctegory token value
     * @param category Name of the sub-category
     * @param logo logo of the sub-category
     * @param status sub-category active status 1|0
     */
    public function update($param){        
        $input = new Input();
        $input->field('token')->required();
        $input->field('category')->required();
        $input->field('primary_category')->exist('category','category');
        $input->field('status')->required();
        if(isset($_FILES['logo']) && !empty($_FILES['logo'])){
            $input->field('logo')->mime('png')->maxSize(5);
        }


        $errors = $input->validation();

        if(!is_array($errors)){
            $exist = ProjectSub_category::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($exist){
                $single = ProjectSub_category::model()->select()->where([
                    'token' => "!".$param['token'],
                    'category' => strtolower($param['category']),
                ])->first();
                if($single){
                    $this->response([
                        'status' => 0,
                        'message' => 'Sub category name already exist'
                    ],200);
                }else{
                    try {
                        $is_file = false;
                        $location = "sub_category";
                        if(isset($_FILES['logo']) && !empty($_FILES['logo'])){
                            $is_file = true;
                            $file = Upload::putImage($_FILES['logo'],$location,8);
                        }else{
                            $file = $exist->logo;
                        }
                        ProjectSub_category::model()->where([
                            'token' => $param['token']
                        ])->update([
                            "primary_category" => $param['primary_category'],
                            "category" => strtolower($param['category']),
                            "is_active"  => $param['status'],
                            "logo" => $file,
                            "logo_path" => BASE_URL.trim($file,'/')
                        ]);
                        $is_file ? Handle::unlinkFile($exist->logo): true;
                        $this->response([
                            'status' => 1,
                            'message' => "Sub category successfully updated"
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
     * Delete sub-category
     * @param token token number of the sub-category
     * will delete the sub-category if token was valid
     */
    public function delete($param){
        $input = new Input();
        $input->field('token')->required();
        $error = $input->validation();

        if(!is_array($error)){
            $single = ProjectSub_category::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($single){
                try {
                    ProjectSub_category::model()->where([
                        'token' => $param['token']
                    ])->delete();
                    Handle::unlinkFile($single->logo);
                    $this->response([
                        'status' => 1,
                        'message' => "Sub category successfully deleted"
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
                    'message' => "Invalid sub category token"
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