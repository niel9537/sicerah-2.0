<?php

namespace App\Traits;

trait HasFlashMessage
{
    protected function flashSuccess(string $message): \Illuminate\Http\RedirectResponse
    {
        return back()->with('success', $message);
    }

    protected function flashError(string $message): \Illuminate\Http\RedirectResponse
    {
        return back()->with('error', $message);
    }

    protected function redirectSuccess(string $route, string $message, array $params = []): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route($route, $params)->with('success', $message);
    }
}