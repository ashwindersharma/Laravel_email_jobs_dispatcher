<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactImportController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\QueueMonitorController;

Route::get('/', function () {
    return view('welcome');
});

Route::post(
    '/contacts/import',
    [ContactImportController::class, 'import']
)->name('contacts.import');

Route::get('/contacts', [ContactImportController::class, 'index'])->name('contacts.index');

Route::get('/contacts/upload', function () {
    return view('contacts_import.contacts_import');
})->name('contacts.upload');

Route::resource('templates', TemplateController::class)->names('templates');
Route::resource(
    'campaigns',
    CampaignController::class,
)->names('campaigns');


Route::get('/queue-monitor', [QueueMonitorController::class, 'index']);
Route::post('/queue-monitor/retry/{id}', [QueueMonitorController::class, 'retry']);
Route::post('/queue-monitor/delete-failed/{id}', [QueueMonitorController::class, 'deleteFailed']);
