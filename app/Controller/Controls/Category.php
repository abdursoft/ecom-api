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
use App\Model\Project\Category as ProjectCategory;
use Core\Files\Upload;
use System\Handle;
use System\Validation\Input;

class Category extends Controller{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get Category
     * @param token token number of the category
     * will return a category info with a valid token
     */
    public function get($param){
        if($param['token'] != ''){
            try {
                $single = ProjectCategory::model()->select()->where([
                    'token' => $param['token']
                ])->last();
                $this->response([
                    'status' => 1,
                    'message' => "Category retrieved",
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
                $categories = ProjectCategory::model()->select()->where([
                    'token' => '!null'
                ])->all();
                $this->response([
                    'status' => 1,
                    'message' => "all categories",
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
     * Category creation
     * @param category Name of the category
     * @param logo logo of the category
     * @param status category active status 1|0
     */
    public function create($param){
        $input = new Input();
        $input->field('category')->unique('category','category')->required();
        $input->field('logo')->mime('png')->maxSize(5)->required();
        $input->field('status')->required();

        $errors = $input->validation();

        if(!is_array($errors)){
            try {
                $location = "category";
                $file = Upload::putImage($_FILES['logo'],$location,8);
                ProjectCategory::model()->create([
                    "token" => $this->generateRandomString(32),
                    "category" => strtolower($param['category']),
                    "is_active"  => $param['status'],
                    "logo" => $file,
                    "logo_path" => BASE_URL.trim($file,'/')
                ]);
                $this->response([
                    'status' => 1,
                    'message' => "Category successfully created"
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
     *  Category Update
     * @param token Category token value
     * @param category Name of the category
     * @param logo logo of the category
     * @param status category active status 1|0
     */
    public function update($param){        
        $input = new Input();
        $input->field('token')->required();
        $input->field('category')->unique('category','category')->required();
        $input->field('status')->required();
        if(isset($_FILES['logo']) && !empty($_FILES['logo'])){
            $input->field('logo')->mime('png')->maxSize(5);
        }


        $errors = $input->validation();

        if(!is_array($errors)){
            $exist = ProjectCategory::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($exist){
                $single = ProjectCategory::model()->select()->where([
                    'token' => "!".$param['token'],
                    'category' => strtolower($param['category']),
                ])->first();
                if($single){
                    $this->response([
                        'status' => 0,
                        'message' => 'Category name already exist'
                    ],200);
                }else{
                    try {
                        $is_file = false;
                        $location = "category";
                        if(isset($_FILES['logo']) && !empty($_FILES['logo'])){
                            $is_file = true;
                            $file = Upload::putImage($_FILES['logo'],$location,8);
                        }else{
                            $file = $exist->logo;
                        }
                        ProjectCategory::model()->where([
                            'token' => $param['token']
                        ])->update([
                            "category" => strtolower($param['category']),
                            "is_active"  => $param['status'],
                            "logo" => $file,
                            "logo_path" => BASE_URL.trim($file,'/')
                        ]);
                        $is_file ? Handle::unlinkFile($exist->logo): true;
                        $this->response([
                            'status' => 1,
                            'message' => "Category successfully updated"
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
            $single = ProjectCategory::model()->select()->where([
                'token' => $param['token']
            ])->last();
            if($single){
                try {
                    ProjectCategory::model()->where([
                        'token' => $param['token']
                    ])->delete();
                    Handle::unlinkFile($single->logo);
                    $this->response([
                        'status' => 1,
                        'message' => "Category successfully deleted"
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
                    'message' => "Invalid category token"
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