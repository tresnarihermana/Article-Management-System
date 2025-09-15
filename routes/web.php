<?php

use App\Models\User;
use Inertia\Inertia;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use Spatie\Permission\Contracts\Permission;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MainPageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Middleware\EnsureProfileComplete;
use App\Http\Controllers\ManageArticleController;
use App\Http\Controllers\ApproveArticleController;
use App\Http\Controllers\Settings\ProfileController;
use App\Models\Category;
use Illuminate\Session\Middleware\AuthenticateSession;

Route::get('/', [MainPageController::class, 'index'], function () {})->name('home');


Route::get('dashboard', [DashboardController::class, 'index'], function () {
    return Inertia::render('Dashboard')->with('message', 'Selamat datang');
})->middleware(['auth', 'verified'])->name('dashboard');
Auth::routes(['verify' => true]);
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.redirect');
Route::get('/clear-message', function () {
    session()->forget('message');
    return redirect()->back();
});
Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();
    $user = User::firstOrCreate(
        [
            'email' => $googleUser->getEmail(),
        ]

    );
    $user->assignRole('user');

    Auth::login($user);
    return redirect('/dashboard')->with("message", "Selamat datang");
});
Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
Route::post('profile/cover', [ProfileController::class, 'updateCover'])->name('profile.cover.update');

// Main Page
Route::middleware(['auth', 'web', 'verified'])->group(function () {


    Route::get('/article/read/{slug}', [MainPageController::class, 'show'])
        ->name('article.show');

    Route::get('/articles', [MainPageController::class, 'articles'])
        ->name('articles.list');

    Route::get('/profile/@{username}', [MainPageController::class, 'profile'])
        ->name('profile.show');

    Route::get('/search', [MainPageController::class, 'search'])
        ->name('search.list');

    Route::get('/articles/category/{slug}', [MainPageController::class, 'category'])
        ->name('category.show');

    Route::get('/categories', [MainPageController::class, 'categories'])
        ->name('categories.list');

    Route::post('/article/read/{article}/like', [MainPageController::class, 'like'])->middleware('auth');

    Route::post('/articles/read/{article}/comments', [CommentController::class, 'store'])
        ->middleware(['throttle:comments', 'auth'])
        ->name('comments.store');

    Route::delete('/articles/comment-delete/{id}', [CommentController::class, 'destroy'])
        ->name('comments.delete');
});

// Roles and Permissions mulai disini
// users
Route::middleware(['auth', 'web', 'verified'])->group(function () {


    Route::resource("/manage/users", UserController::class)
        ->only(['create', 'store'])
        ->middleware("permission:users.create");

    Route::resource("/manage/users", UserController::class)
        ->only(['edit', 'update'])
        ->middleware("permission:users.edit");

    Route::resource("/manage/users", UserController::class)
        ->only(['destroy'])
        ->middleware("permission:users.delete");

    Route::put('/users/{user}/toggleStatus', [UserController::class, 'toggleStatus'])
        ->name('users.toggleStatus')
        ->middleware('permission:users.toggleStatus');


    Route::resource("/manage/users", UserController::class)
        ->only(['index', 'show'])
        ->middleware("permission:users.create|users.edit|users.delete|users.view");

    Route::resource("/manage/users", UserController::class)
        ->only(['show'])
        ->middleware("permission:users.show");

    // roles
    Route::resource("/manage/roles", RoleController::class)
        ->only(['create', 'store'])
        ->middleware("permission:roles.create");

    Route::resource("/manage/roles", RoleController::class)
        ->only(['edit', 'update'])
        ->middleware("permission:roles.edit");

    Route::resource("/manage/roles", RoleController::class)
        ->only(['destroy'])
        ->middleware("permission:roles.delete");

    Route::resource("/manage/roles", RoleController::class)
        ->only(['index', 'show'])
        ->middleware("permission:roles.create|roles.edit|roles.delete|roles.view");

    // permissions
    Route::resource("/manage/permissions", PermissionController::class)
        ->only(['create', 'store'])
        ->middleware("permission:permissions.create");

    Route::resource("/manage/permissions", PermissionController::class)
        ->only(['edit', 'update'])
        ->middleware("permission:permissions.edit");

    Route::resource("/manage/permissions", PermissionController::class)
        ->only(['destroy'])
        ->middleware("permission:permissions.delete");

    Route::resource("/manage/permissions", PermissionController::class)
        ->only(['index', 'show'])
        ->middleware("permission:permissions.create|permissions.edit|permissions.delete|permissions.view");

    // Logs
    Route::resource("/manage/logs", LogController::class)
        ->only(['index', 'show'])
        ->middleware("permission:logs.view");

    // Article
    Route::resource("/manage/articles", ArticleController::class)
        ->only(['edit', 'update'])
        ->middleware("permission:articles.edit");

    Route::resource("/manage/articles", ArticleController::class)
        ->only(['create', 'store'])
        ->middleware("permission:articles.create");

    Route::resource("/manage/articles", ArticleController::class)
        ->only(['destroy'])
        ->middleware("permission:articles.delete");

    Route::resource("/manage/articles", ArticleController::class)
        ->only(['index'])
        ->middleware("permission:articles.create|articles.edit|articles.delete|articles.view");

    Route::resource("/manage/articles", ArticleController::class)
        ->only(['show'])
        ->middleware("permission:articles.show");

    // Manage Article
    Route::resource("manage/approve", ManageArticleController::class)
        ->only(['edit', 'update'])
        ->middleware("permission:approve.edit");
    Route::resource("manage/approve", ManageArticleController::class)
        ->only(['create', 'store'])
        ->middleware("permission:approve.create");

    Route::resource("manage/approve", ManageArticleController::class)
        ->only(['destroy'])
        ->middleware("permission:approve.delete");

    Route::resource("manage/approve", ManageArticleController::class)
        ->only(['index'])
        ->middleware("permission:approve.create|approve.edit|approve.delete|approve.view");

    Route::resource("manage/approve", ManageArticleController::class)
        ->only(['show'])
        ->middleware("permission:approve.show");



    // Article Categories
    Route::resource("manage/categories", CategoryController::class)
        ->only(['create', 'store'])
        ->middleware("permission:categories.create");
    Route::resource("manage/categories", CategoryController::class)
        ->only(['edit', 'update'])
        ->middleware("permission:categories.edit");
    Route::resource("manage/categories", CategoryController::class)
        ->only(['destroy'])
        ->middleware("permission:categories.delete");
    Route::resource("manage/categories", CategoryController::class)
        ->only(['index'])
        ->middleware("permission:categories.create|categories.edit|categories.delete|categories.view");
    Route::resource("manage/categories", CategoryController::class)
        ->only(['show'])
        ->middleware("permission:categories.show");

    // Article Tags
    Route::get('/manage/categories/tags/create', [TagController::class, 'create'])->name('tags.create')
        ->middleware("permission:tags.create");
    Route::resource("manage/tags", TagController::class)
        ->only(['store'])
        ->middleware("permission:tags.create");
    Route::resource("manage/tags", TagController::class)
        ->only(['edit', 'update'])
        ->middleware("permission:tags.edit");
    Route::resource("manage/tags", TagController::class)
        ->only(['destroy'])
        ->middleware("permission:tags.delete");
    Route::resource("manage/tags", TagController::class)
        ->only(['index'])
        ->middleware("permission:tags.create|tags.edit|tags.delete|tags.view");
    Route::resource("manage/tags", TagController::class)
        ->only(['show'])
        ->middleware("permission:tags.show");

    Route::post('articles/Edit', [ArticleController::class, 'uploadCover'])
        ->name('articles.uploadCover');
    Route::put('articles/{article}/approve', [ApproveArticleController::class, 'approve'])
        ->name('approve')->middleware('permission:articles.approve');
    Route::put('articles/{article}/reject', [ApproveArticleController::class, 'reject'])
        ->name('reject')->middleware('permission:articles.approve');
});

// Bulk Action
Route::delete("manage/articles/bulk-destroy/{ids}", [ArticleController::class, 'bulkDestroy'])->name('articles.bulk-destroy');
Route::delete("manage/users/bulk-destroy/{ids}", [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
Route::delete("manage/roles/bulk-destroy/{ids}", [RoleController::class, 'bulkDestroy'])->name('roles.bulk-destroy');
Route::delete("manage/permissions/bulk-destroy/{ids}", [PermissionController::class, 'bulkDestroy'])->name('permissions.bulk-destroy');
Route::delete("manage/categories/bulk-destroy/{ids}", [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
Route::delete("manage/tags/bulk-destroy/{ids}", [TagController::class, 'bulkDestroy'])->name('tags.bulk-destroy');

Route::get('/bar', function () {
    return Inertia::render('CategoriesStatsPieChart'); // TODO hello
});
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
