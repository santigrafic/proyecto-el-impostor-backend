<?php

Broadcast::channel('room.{id}', function ($user, $id) {
    return true;
});