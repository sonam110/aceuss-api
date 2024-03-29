<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/*Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});*/

Broadcast::channel('notification-for-all', function ($data) {
    return true;
});

Broadcast::channel('bank-id-verified.{userId}-{uniqueId}', function ($data, $userId, $uniqueId, $action=null) {
    return true;
});

Broadcast::channel('notifications.{userId}-{uniqueId}', function ($data, $userId, $uniqueId, $action=null, $pnr=null, $error=null) {
    return true;
});