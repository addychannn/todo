<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\SampleController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\LaptopController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
$middleAuth = (bool)env('APP_AUTH_TOKEN', false) ? 'auth:api' : 'auth:sanctum';

Broadcast::routes(["middleware" => [$middleAuth]]);
Route::middleware([$middleAuth, 'throttle:60,1', 'disabled'])->group(function () {
    Route::middleware('verified', 'throttle:60,1')->group(function () {
        #region Account Management
        Route::post("/user/image", [ProfileController::class, "saveAvatar"]);
        Route::patch("/user/image", [ProfileController::class, "changeAvatar"]);
        Route::patch("/user/password", [ProfileController::class, "changePassword"]);
        Route::patch("/user/profile/name", [ProfileController::class, "updateName"]);
        Route::patch("/user/profile/birthdate", [ProfileController::class, "updateBirthdate"]);
        Route::patch("/user/profile/gender", [ProfileController::class, "updateGender"]);
        Route::put("/user/profile/address/{address}", [ProfileController::class, "setMainAddress"]);
        Route::patch("/user/profile/address/{address?}", [ProfileController::class, "setAddress"]);
        Route::delete("/user/profile/address/{address}", [
            ProfileController::class,
            "deleteAddress",
        ]);
        Route::post("/user/profile", [ProfileController::class, "changeProfile"]);

        Route::patch("/user/account/username", [ProfileController::class, "updateUsername"]);
        Route::patch("/user/account/email", [ProfileController::class, "updateEmail"]);
        Route::get("/user/activites", [ProfileController::class, "searchActivities"]);
        // Route::patch('/user/account', [ProfileController::class, 'changeAccount']);
        // Route::delete("/user/image", [ProfileController::class, "deleteImage"]);
        #endregion

        #region Permissions
        Route::get("permissions", [PermissionsController::class, "permissionSearch"]);
        Route::post("/permissions", [PermissionsController::class, "createPermission"]);
        Route::patch("/permissions/{permission}", [PermissionsController::class, "updatePermission"]);
        Route::delete("/permissions/{permission}", [PermissionsController::class, "permissionDelete"]);
        #endregion

        #region Roles
        Route::get("/roles", [PermissionsController::class, "roleSearch"]);
        Route::get("/roles/permissions", [PermissionsController::class, "rolePermissionSearch"]);
        Route::post("/roles", [PermissionsController::class, "createRole"]);
        Route::patch("/roles/{role}", [PermissionsController::class, "updateRole"]);
        Route::delete("/roles/{role}", [PermissionsController::class, "deleteRole"]);
        #endregion

        #region Users
        Route::get("/users", [UsersController::class, "userSearch"]);
        Route::get("/users/stathistory/{id}", [UsersController::class, "getUserStatusHistory"]);
        Route::get("/users/roles", [UsersController::class, "getRoles"]);
        Route::get("/users/i/{user}", [UsersController::class, "getUser"]);
        Route::get("/users/permissions", [UsersController::class, "searchPermissions"]);

        Route::post("/users", [UsersController::class, "addUser"]);

        Route::patch("/users/email/{user}", [UsersController::class, "updateEmail"]);
        Route::patch("/users/username/{user}", [UsersController::class, "updateUsername"]);
        Route::patch("/users/name/{user}", [UsersController::class, "updateName"]);
        Route::patch("/users/password/{user}", [UsersController::class, "updatePassword"]);
        Route::patch("/users/avatar/{user}", [UsersController::class, "changeAvatar"]);
        Route::patch("/users/birthdate/{user}", [UsersController::class, "updateBirthdate"]);
        Route::patch("/users/gender/{user}", [UsersController::class, "updateGender"]);
        Route::patch("/users/verify/{user}", [UsersController::class, "verifyEmail"]);
        Route::patch("/users/disable/{user}", [UsersController::class, "toggleUserActive"]);
        Route::patch("/users/permissions/{user}", [UsersController::class, "setPermissions"]);
        Route::put("/users/address/{user}/{address}", [UsersController::class, "setMainAddress"]);
        Route::patch("/users/address/{user}/{address?}", [UsersController::class, "setAddress"]);
        Route::delete("/users/address/{user}/{address}", [UsersController::class, "deleteAddress"]);

        // Route::post("/users/profile/{id}", [UsersController::class, "updateUserProfile"]);
        // Route::patch("/users/account/{id}", [UsersController::class, "updateAccount"]);
        #endregion

        #region Genders
            Route::get('/users/genders', [GenderController::class, 'searchGenders']);
            Route::post('/users/gender', [GenderController::class, 'addGender']);
            Route::put('/users/gender/{gender}', [GenderController::class, 'updateGender']);
            Route::patch('/users/gender/{gender}', [GenderController::class, 'toggleGender']);
        #endregion

        #region Logs
            Route::get('/logsy/{year}/{month?}/{day?}', [LogsController::class, 'getLogs']);
            Route::get("/log/levels", [LogsController::class, "getLevels"]);
        #endregion
    });

    Route::get('/auth/permissions', [AuthenticationController::class, 'getPermissions']);
    Route::post('/auth/logout', [AuthenticationController::class, 'Logout']);

    #region Authentication
        Route::post('/email/resend', [VerificationController::class, 'resend']);
        Route::get('/email/isVerified', [VerificationController::class, 'checkifverified']);
    #endregion

    #region Task

    Route::get('/tasks',[TaskController::class,'getAllTasks']);

    Route::post('/create/task',[TaskController::class,'createTask']);

    Route::post('/add/task',[TaskController::class,'addNewTask']);

    Route::patch('/update/task/{id}',[TaskController::class,'updateTask']);

    Route::patch('/update/task/{id}',[TaskController::class,'updateTaskStatus']);

    Route::delete('/delete/task/{id}',[TaskController::class,'deleteTask']);

    

    #endregion


    #region List

    Route::get('/lists',[ListController::class,'getAllLists']);

    Route::patch('/update/list/{id}',[ListController::class,'updateList']);

    Route::delete('/delete/list/{id}',[ListController::class,'deleteList']);

    #endregion

});


Route::middleware('api', 'throttle:60,1')->group(function (){
    #region Authentication
        Route::post('/auth/login', [AuthenticationController::class, 'login']);
        Route::post('/auth/register/{user?}', [AuthenticationController::class, 'register']);
        Route::post('/password/forgot', [PasswordController::class, 'forgot_password'])->name('password.email');
        Route::post('/password/reset', [PasswordController::class, 'change_password'])->name('password.update');
    #endregion

    #region Public
        Route::get('/logo/{size?}', [ImagesController::class, 'logo'])->name('app.logo');
    #endregion

    #region Email Verification
    Route::get("/email/verify/{user}", [VerificationController::class, "verify"])->name(
        "verification.verify"
    );
    #endregion

    #region Genders
        Route::get('/genders', [GenderController::class, 'getGenders']);
    #endregion

    #region Address
        Route::get('/address/initial/barangay/{Code}', [AddressController::class, 'getBarangayAddress']);
        Route::get('/address/initial/city/{Code}', [AddressController::class, 'getCityAddress']);
        Route::get('/address/regions', [AddressController::class, 'Regions']);
        Route::get('/address/provinces/{regionCode}', [AddressController::class, 'Provinces']);
        Route::get('/address/cities/{provinceCode}', [AddressController::class, 'Cities']);
        Route::get('/address/barangays/{cityCode}', [AddressController::class, 'Barangays']);
        Route::get('/address/types', [AddressController::class, 'getAddressTypes']);
    #endregion

});

Route::middleware('api', 'throttle:120,1')->group(function (){
});

// unthrottled routes
Route::middleware([$middleAuth, 'disabled'])->group(function () {
    Route::middleware('verified')->group(function () {
        #region Users
        Route::post("/users/avatar/{id}", [UsersController::class, "saveAvatar"]);
        #endregion

        // We'll put our upload api here to avoid our upload being throttled, which will cause our upload to fail.
        Route::post('/sample/upload', [SampleController::class, 'UploadFile']);
    });
});

// Unthrottled public routes
Route::middleware('api')->group(function (){
    # Images
        Route::get('/image/{thumbsize}/{hash}', [ImagesController::class, 'display_thumb'])->name('image.thumb');
        Route::get('/image/{hash}', [ImagesController::class, 'display'])->name('image.display');
    # End - Images

    #region - Registration: Profile image upload
    Route::post("/auth/avatar/{user}", [AuthenticationController::class, "addAvatar"]);
    #endregion
});
