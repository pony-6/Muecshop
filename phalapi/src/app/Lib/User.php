<?php

namespace App\Lib;
use App\Lib\Auth as Auth;
use App\Model\User as UserModel;
use App\Model\Address as AddressModel;

class User
{
    protected $user_model;
    protected $address_model;
    public function __construct()
    {
        $this->user_model = new UserModel();
        $this->address_model = new AddressModel();
        Auth::check_user();
    }

    public function getAddressList($user_id)
    {
        $data = $this->address_model->getUserAddress($user_id);
        return $data;
    }

    public function defaultAddress($user_id)
    {
        $data = $this->address_model->getUserDefaultAddress($user_id);
        return $data;
    }

    public function deleteAddress($user_id , $address_id)
    {
        Auth::check_user_address($user_id,$address_id);
        $data = $this->address_model->deleteUserAddress($address_id);
        return $data;
    }

    public function saveAddress($save_data)
    {
        return $this->address_model->saveAddress($save_data);
    }
    
    public function check_mobile_register($mobile_phone){
        if($mobile_phone == ''){
            $this->msg = 'mobile error';
            return false;
        }
        $di = \PhalApi\DI()->notorm->users;
        $user_data = $di->where('mobile_phone',$mobile_phone)->fetchOne();
        if($user_data){
            $this->msg = 'mobile error';
            return false;
        }
        return true;
    }
}
