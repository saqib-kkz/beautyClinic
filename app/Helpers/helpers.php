<?php
    /* use App\Model\Admin\Core\Module;
    use App\Model\Admin\Core\Role;
    use App\Model\Admin\Core\Permission;
    use App\Model\Admin\Core\RolePermission;
    use App\Model\Admin\Core\ModulePermission;

    use App\Model\Admin\Core\User;
    use App\Model\Admin\HousingSociety\MemberShip;

    use App\Model\Admin\Core\LoginHistory;
    use App\Model\Admin\Core\RequestSource;

    use Carbon\Carbon;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth; */

    use Illuminate\Support\Facades\Session;

    function getBg()
    {
        return url('assets/images/logo/bg.jpg');
    }

    function getLogo()
    {
        return url('assets/images/logo/logo.png');
    }

    function gettitle()
    {
        return config('app.name', 'Beauty Clinic');
    }

    function getadminasset($str)
    {
        return url('assets/' . $str);
    }

    function getUserProfilePic($photo = "")
    {
        if(empty($photo)) {
            $photo = "blank.png";
        }

        if (file_exists(public_path('assets/images/uploads/users/profile' . $photo))) {
            return getadminasset('images/uploads/users/profile/' . $photo);
        } else {
            return getadminasset('images/avatar/' . $photo);
        }
    }

    function displayAlert()
    {
        if (Session::has('message')) {
            list($type, $message) = explode('|', Session::get('message'));

            $type = $type == 'error' ?: 'danger';
            $type = $type == 'message' ?: 'info';
            $type = $type == 'success' ?: 'success';

            return sprintf('<div class="alert alert-%s alert-dismissible fade show" role="alert">%s
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>', $type, $message);
        }

        return '';
    }
?>