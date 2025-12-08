<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  /**
   * @return object auth api
   */
  protected function guard()
  {
    return Auth::guard();
  }

  /**
   * @return object auth web
   */
  public function guardWeb()
  {
    return Auth::guard('web');
  }

  /**
   * @return object auth api
   */
  public function guardApi()
  {
    return Auth::guard('api');
  }

  /**
   * digunakan untuk mendapatkan data user
   */
  public function getDataUser()
  {
    return $this->guard()->user();
  }

  /**
   * digunakan untuk mendapatkan data user
   */
  public function getDataDosen($attribute = null)
  {
    $dosen = $this->guard()->user()->dosen;

    if (is_null($attribute)) {
      return $dosen;
    } else {
      return $dosen[$attribute];
    }
  }

  /**
   * digunakan untuk mendapatkan userid
   */
  public function getUserid()
  {
    return $this->guard()->user()->id;
  }

  /**
   * digunakan untuk mendapatkan username
   */
  public function getUsername()
  {
    return $this->guard()->user()->username;
  }

  /**
   * digunakan untuk mendapatkan username
   */
  public function getUsernameOld()
  {
    return $this->guard()->user()->username_old;
  }

  /**
   * digunakan untuk mendapatkan nama user
   */
  public function getUserFullName()
  {
    return $this->guard()->user()->name;
  }

  /**
   * @return boolean roles of user in array
   */
  public function getRoleNames()
  {
    return $this->guard()->user()->getRoleNames()->toArray();
  }

  /**
   * @return boolean remove roles of user
   */
  public function removeRole($role)
  {
    return $this->guard()->user()->removeRole($role);
  }

  /**
   * @return boolean roles of user
   */
  public function getDefaultRole()
  {
    return $this->guard()->user()->default_role;
  }

  /**
   * @return boolean has role
   */
  public function hasRole($name)
  {
    return $this->guard()->user()->hasRole($name);
  }

  /**
   * @return object auth api
   */
  public function hasPermissionTo($permission)
  {
    $user = Auth::guard()->user();
    if ($this->guard()->guest()) {
      return true;
    } elseif ($user->hasPermissionTo($permission) || $user->hasRole('superadmin')) {
      return true;
    } else {
      abort(403, "Terlarang: Anda tidak memiliki permission untuk mengeksekusi proses ini [$permission]");
    }
  }

  /**
   * @return object auth api
   */
  public function hasAnyPermission($permission)
  {
    $user = Auth::guard('api')->user();
    if ($this->guard()->guest()) {
      return true;
    } elseif ($user->hasAnyPermission($permission) || $user->hasRole('superadmin')) {
      return true;
    } else {
      abort(403, "Terlarang: Anda tidak memiliki permission untuk mengeksekusi proses ini [$permission]");
    }
  }

  /**
   * Display the specified user permissions.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function getUserPermissions($id)
  {
    $user = User::find($id);
    $permissions = is_null($user) ? [] : $user->permissions;
    return $permissions;
  }
}
