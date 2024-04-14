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

namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\Admins\Admins;
use System\Auth as SystemAuth;
use System\Validation\Input;

class Auth extends Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Root admin registration
     * @param name name of the admin
     * @param email admin email address
     * @param mobile admin's mobile number
     * @param password admin's password
     */
    public function register( $param ) {
        $admins = Admins::model()->count( 'id', 'admin' );
        if ( $admins === 0 ) {
            $validation = new Input();
            $validation->field( 'name' )->min( 10 );
            $validation->field( 'email' )->email()->unique( 'admins', 'email' )->required();
            $validation->field( 'mobile' )->min( 8 )->max( 14 )->number();
            $validation->field( 'password' )->min( 8 )->password();

            $errors = $validation->validation();
            if ( !is_array( $errors ) ) {
                try {
                    Admins::model()->create( [
                        'token'       => "ad_" . $this->generateRandomString( 32 ),
                        'name'        => $param['name'],
                        'email'       => $param['email'],
                        'mobile'      => $param['mobile'],
                        'role'        => 'admin',
                        'is_active'   => 1,
                        'is_approved' => 1,
                        'password'    => password_hash( $param['password'], PASSWORD_DEFAULT, $opt = ["key" => "ABS_pharmacy"] ),
                    ] );
                    $this->response( [
                        'status'  => 1,
                        'message' => 'Account successfully created',
                    ], 200 );
                } catch ( \Throwable $th ) {
                    $this->response( [
                        'status'  => 0,
                        'message' => $th->getMessage(),
                    ], 200 );
                }
            } else {
                $this->response( [
                    'status'  => 0,
                    'message' => $errors,
                ], 200 );
            }
        } else {
            $this->response( [
                'status'  => 0,
                'message' => 'Admin Account registration over',
            ], 200 );
        }
    }

    /**
     * root admin login
     * @param email admin login email
     * @param password admin login password
     */
    public function login( $param ) {
        $validation = new Input();
        $validation->field( 'email' )->email()->required();
        $errors = $validation->validation();

        if ( !is_array( $errors ) ) {
            $single = Admins::model()->select()->where( [
                'email' => $param['email'],
            ] )->last();
            if ( $single ) {
                if ( password_verify( $param['password'], $single->password ) ) {
                    $token = SystemAuth::jwtAUTH( $single, $single->role,1800 );
                    $this->response( [
                        'status'     => 1,
                        'message'    => 'Login successful',
                        'role'       => $single->role,
                        'token'      => $token,
                        'token_type' => 'Bearer',
                    ], 200 );
                } else {
                    $this->response( [
                        'status'  => 0,
                        'message' => 'Password not match',
                    ], 200 );
                }
            } else {
                $this->response( [
                    'status'  => 0,
                    'message' => 'Invalid email address or password!',
                ], 200 );
            }
        } else {
            $this->response( [
                'status'  => 0,
                'message' => $errors,
            ], 200 );
        }
    }

    public function subadmin($param){
        echo "OK";
    }
}