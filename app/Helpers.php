<?php


namespace App;


class Helpers
{
    /**
     * Перенаправляем пользователя
     *
     * @param $path string
     */
    public function redirect_to($path) {
        header("Location: $path");
        exit();
    }
}