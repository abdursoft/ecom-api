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
use System\Plugins\SMTP;
use System\Validation\Input;

class Subadmin extends Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Sub admin registration
     * @param name name of the admin
     * @param email admin email address
     * @param mobile admin's mobile number
     */
    public function create( $param ) {
        $input = new Input();
        $input->field( 'name' )->min( 4 )->max( 15 )->required();
        $input->field( 'email' )->email()->unique( 'admins', 'email' )->required();
        $input->field( 'mobile' )->number()->min( 8 )->max( 14 );
        $errors = $input->validation();

        if ( !is_array( $errors ) ) {
            $token    = $this->generateRandomString( 25 );
            $pass_key = $this->generateRandomNumber( 6 );
            try {
                Admins::model()->create( [
                    'token'    => $token,
                    'name'     => $param['name'],
                    'email'    => $param['email'],
                    'mobile'   => $param['mobile'],
                    'pass_key' => $pass_key,
                    'pass_sent' => date('Y-m-d H:i:s'),
                    'role'     => 'sub_admin',
                ], 200 );
                
                $message = "<h2>Dear " . $param['name'] . "</h2>";
                $message .= "<p>Use this <strong style='color:red;'>$pass_key</strong> OTP to set your password</p>";
                $message .= "It's valid for 24 hours";
                $message .= $this->mailFooter();
                $mail = new SMTP();
                $mail->send( $param['email'], 'Set Your Password', $message );

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
    }

    /**
     * Sub admin registration
     * @param OTP OTP number from email
     * @param new_pass New password for sub_admin
     * @param con_pass Confirm password for sub_admin
     */
    public function setPass( $param ) {
        $input = new Input();
        $input->field('otp')->min(6)->required();
        $input->field('new_pass')->password()->required();
        $input->field('con_pass')->password()->required();
        $error = $input->validation();

        if(!is_array($error)){
            $single = Admins::model()->select()->where([
                'pass_key' => $param['otp']
            ])->last();
            if($single && $single->pass_sent != NULL){
                $date_compare = $this->dateObject($single->pass_sent);
                if($date_compare->d < 1 && $date_compare->h < 24){
                    if($param['new_pass'] == $param['con_pass']){
                        try {
                            Admins::model()->where([
                                'pass_key' => $param['otp']
                            ])->update([
                                'password' => password_hash($param['new_pass'],PASSWORD_DEFAULT, $opt = ['key' => "ABS_pharmacy"]),
                                'pass_sent' => NULL,
                                'pass_key' => NULL
                            ]);
                            $this->response([
                                'status' => 1,
                                'message' => "Password successufully changed"
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
                            'message' => 'Both password must be same'
                        ],200);
                    }
                }else{
                    $this->response([
                        'status' => 0,
                        'message' => "Your OTP session was expired"
                    ],200);
                }
            }else{
                $this->response([
                    'status' => 0,
                    'message' => "Invalid OTP or Session"
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