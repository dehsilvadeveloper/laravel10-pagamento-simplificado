<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTypeController;

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

/*
|-------------------------------------------
| Application Routes
|-------------------------------------------
*/
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    });

    Route::prefix('/document-types')->name('document-type.')->group(function () {
        Route::get('/', [DocumentTypeController::class, 'index'])->name('index');
        Route::get('/{id}', [DocumentTypeController::class, 'show'])->name('show');
    });

    Route::prefix('/user-types')->name('user-type.')->group(function () {
        Route::get('/', [UserTypeController::class, 'index'])->name('index');
        Route::get('/{id}', [UserTypeController::class, 'show'])->name('show');
    });

    Route::prefix('/users')->name('user.')->group(function () {
        Route::post('/', [UserController::class, 'create'])->name('create');
        Route::patch('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'delete'])->name('delete');
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
    });
});

/*
|-------------------------------------------
| Preview Routes
|-------------------------------------------
*/
Route::prefix('/previews/mailables')->name('previews.mailables')->group(function () {
    Route::get('/user/welcome', function () {
        $user = \App\Domain\User\Models\User::find(1);

        if (!$user) {
            return 'User not found. Cannot preview email.';
        }

        return new \App\Domain\User\Mails\WelcomeMailable($user);
    })->name('user.welcome');
});

/*
|-------------------------------------------
| Mock Routes
|-------------------------------------------
*/

// Temporary route
Route::get('/test-authorizer', function () {
    /**
     * @var \App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto
     */
    $dto = \App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto::from([
        'transfer_id' => 1,
        'payer_id' => 5,
        'payee_id' => 6,
        'amount' => 25.50
    ]);

    /**
     * @var \App\Domain\TransferAuthorization\Services\Interfaces\TransferAuthorizerServiceInterface
     */
    $service = app(\App\Domain\TransferAuthorization\Services\Interfaces\TransferAuthorizerServiceInterface::class);
    $result = $service->authorize($dto);

    print 'Here is the result: <br><br>';
    print '<pre>';
    dd($result);
});
